<?php
require_once 'config.php';
require_once 'scbClass.php';

$q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['scb', 1]);
if ($q_1->rowCount() > 0)
{
	$row = $q_1->fetch(PDO::FETCH_ASSOC);

	if($row['a_bank_username'] != "" && $row['a_bank_password'] != "" && $row['a_bank_acc_number'] != "")
	{
		echo "เวลาที่ระบบออโต้ทำงาน: " . date('Y-m-d H:i:s');
		echo "<br>";
		$_deviceid = $row['a_bank_username'];
		$_pin = $row['a_bank_password'];
		$_acc_num = str_replace("-", "", $row['a_bank_acc_number']);
		$apiSCB = new SCB($_deviceid, $_pin, $_acc_num);
		$apiSCB->login();

		$q_2 = dd_q('SELECT * FROM website_tb WHERE id = ?', [1]);
		if ($q_2->rowCount() > 0)
		{
			$row_2 = $q_2->fetch(PDO::FETCH_ASSOC);

			$withdraw_open = true;
			$withdraw_all_user = true;
			if($row_2['withdraw_auto'] == "0")
			{
				$withdraw_open = false;
			}
			if($row_2['withdraw_auto_user_all'] == "0")
			{
				$withdraw_all_user = false;
			}

			if($withdraw_open)
			{
				$q_withdraw_tb = dd_q('SELECT * FROM withdraw_tb WHERE w_status = ? LIMIT 3', ['0']);
				while($row_withdraw_tb = $q_withdraw_tb->fetch(PDO::FETCH_ASSOC))
				{
					$q_w_tb = dd_q('SELECT * FROM withdraw_tb WHERE w_id = ? AND w_status = ?', [
						$row_withdraw_tb['w_id'], 
						'0'
					]);
					if ($q_w_tb->rowCount() > 0)
					{
						$row_w_tb = $q_w_tb->fetch(PDO::FETCH_ASSOC);

						if($row_w_tb['w_amount'] <= $row_2['withdraw_auto_max'])
						{
							$value = trim($row_w_tb['w_bank_name']);
							$bankcode = "";
							if ($value == "ธนาคารไทยพาณิชย์")
							{
								$bankcode = "014";
							}
							else if ($value == "ธนาคารกสิกรไทย")
							{
								$bankcode = "004";
							}
							else if ($value == "ธนาคารกรุงไทย")
							{
								$bankcode = "006";
							}
							else if ($value == "ธนาคารกรุงเทพ")
							{
								$bankcode = "002";
							}
							else if ($value == "ธนาคารทหารไทยธนชาต")
							{
								$bankcode = "011";
							}
							else if ($value == "ธนาคารออมสิน")
							{
								$bankcode = "030";
							}
							else if ($value == "ธนาคารกรุงศรีอยุธยา")
							{
								$bankcode = "025";
							}
							else if ($value == "ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร")
							{
								$bankcode = "034";
							}
							else if ($value == "ธนาคารยูโอบี")
							{
								$bankcode = "024";
							}
							else if ($value == "ธนาคารอาคารสงเคราะห์")
							{
								$bankcode = "033";
							}
							else if ($value == "ธนาคารซีไอเอ็มบี")
							{
								$bankcode = "022";
							}
							else if ($value == "ธนาคารซิตี้แบงค์")
							{
								$bankcode = "017";
							}
							else if ($value == "ธนาคารเกียรตินาคิน")
							{
								$bankcode = "069";
							}
							else if ($value == "ธนาคารแลนด์ แอนด์ เฮ้าส์")
							{
								$bankcode = "073";
							}
							else if ($value == "ธนาคารทิสโก้")
							{
								$bankcode = "067";
							}
							else if ($value == "ธนาคารอิสลามแห่งประเทศไทย")
							{
								$bankcode = "066";
							}

							$isSuccess = true;
							// แก้ตัวถอน เช็ครายการก่อน หากมีรายการโอนไปเลข บช. นี้ จำนวนเงินเท่านี้ ภายใน 5 นาที ให้เปลี่ยนสถานะให้แอดมินมากดเอง
							$data = $apiSCB->getTransaction();
							if(array_key_exists('data', $data) && array_key_exists('status', $data))
							{
								if($data["data"] != "error")
								{
									if(array_key_exists('code', $data["status"]) && array_key_exists('description', $data["status"]))
									{
										if($data["status"]["code"] == 1000)
										{
											if(array_key_exists('txnList', $data["data"]))
											{
												foreach ($data["data"]["txnList"] as $val)
												{
													if(array_key_exists('txnCode', $val) && array_key_exists('txnDebitCreditFlag', $val))
													{
														if(array_key_exists('description', $val["txnCode"]) && array_key_exists('code', $val["txnCode"]))
														{
															if($val["txnCode"]["code"] == "X2" && $val["txnDebitCreditFlag"] == "D" && strpos($val["txnCode"]["description"], "ถอนเงิน") !== false)
															{
																$t_topup_bank = "";
																if (strpos($val["txnRemark"], "โอนไป") !== false)
																{
																	$str_number = str_replace("โอนไป ", "", $val["txnRemark"]);
																	$arr_str_number = explode(" ", $str_number);
																	if(count($arr_str_number) > 0)
																	{
																		$t_topup_bank = strtolower($arr_str_number[1]);
																		if(substr(trim($row_w_tb['w_bank_number']), -4) == substr($t_topup_bank, -4))
																		{
																			if(trim($row_w_tb['w_amount']) == str_replace(",", "", $val["txnAmount"]))
																			{
																				$txnDateTime = new DateTime($val["txnDateTime"]);
																				$t_create_date = $txnDateTime->format('Y-m-d H:i:s');
																				$w_create_date = date('Y-m-d H:i:s',strtotime('-5 minutes',strtotime($row_w_tb['w_create_date'])));
																				if($w_create_date > $t_create_date)
																				{
																					$isSuccess = true;
																				}
																				else
																				{
																					$isSuccess = false;
                     																break;
																				}
																			}
																		}
																		else
																		{
																			write_log("withdraw auto : เช็คเลขบัญชี", "log ไว้ดูค่าเฉยๆ สามารถเข้ามาลบได้ บรรทัดที่ 166 | บัญชีที่ต้องการโอน ".substr(trim($row_w_tb['w_bank_number']), -4)." บัญชีที่พบ ".substr($t_topup_bank, -4)." ไม่ตรงกัน", "");
																		}
																	}
																}
											    			}
											    		}
											    	}
											    }
											}
										}
										else
										{
											write_log("withdraw auto : เช็ครายการถอน", "error code ".$data["status"]["code"]." ดึงรายการไม่สำเร็จ รหัสรายการ ".$row_w_tb['w_id']." จะถูกดำเนินการต่ออัตโนมัติ", "");
										}
									}
									else
									{
										write_log("withdraw auto : เช็ครายการถอน", "status code empty ดึงรายการไม่สำเร็จ รหัสรายการ ".$row_w_tb['w_id']." จะถูกดำเนินการต่ออัตโนมัติ", "");
									}
								}
								else
								{
									write_log("withdraw auto : เช็ครายการถอน", "data error ดึงรายการไม่สำเร็จ รหัสรายการ ".$row_w_tb['w_id']." จะถูกดำเนินการต่ออัตโนมัติ", "");
								}
							}
							else
							{
								write_log("withdraw auto : เช็ครายการถอน", "getTransaction ดึงรายการไม่สำเร็จ รหัสรายการ ".$row_w_tb['w_id']." จะถูกดำเนินการต่ออัตโนมัติ", "");
							}

							if($withdraw_all_user)
							{
								$balance = $apiSCB->summary();
								if(array_key_exists('totalAvailableBalance', $balance) && array_key_exists('status', $balance))
								{
									if(array_key_exists('code', $balance["status"]) && array_key_exists('description', $balance["status"]))
									{
										if($balance["status"]["code"] == 1000)
										{
											if(str_replace(",", "", $balance["totalAvailableBalance"]) >= $row_w_tb['w_amount'])
											{
												if($bankcode != "")
											    {
											    	if($isSuccess)
											    	{
												    	$withdraw = $apiSCB->transfer_confrim(trim($row_w_tb['w_bank_number']), $bankcode, trim($row_w_tb['w_amount']));
													    if(array_key_exists('data', $withdraw) && array_key_exists('status', $withdraw))
													    {
													    	if(array_key_exists('code', $withdraw["status"]) && array_key_exists('description', $withdraw["status"]))
													    	{
													    		if($withdraw["status"]["code"] == 1000)
										                        {
										                            $q_up_w = dd_q('UPDATE withdraw_tb SET w_status=?, w_action_by=? WHERE w_id=?', [
														    			'1',
														    			"Auto",
														    			$row_w_tb['w_id']
														    		]);
														    		if($q_up_w = true)
														    		{
														    			write_log("withdraw : approve_auto_system", "อนุมัติรายการถอน (รหัสรายการ ".$row_w_tb['w_id'].")", "");

														    			notify_message("🤖 อนุมัติรายการถอน Auto ของยูส ".$row_w_tb['w_user']." จำนวน ".$row_w_tb['w_amount']." บาท เวลา ".date("d/m/Y H:i:s"), $row_2['tokenline']);
														    			
														    			echo "ทำรายการสำเร็จ <br>";
														    		}
														    		else
														    		{
														    			echo "ทำรายการไม่สำเร็จ <br>";
														    		}
										                        }
										                        else
										                        {
										                        	echo "withdraw error ".json_encode($withdraw["status"]["description"], JSON_UNESCAPED_UNICODE)."<br>";
										                        }
													    	}
													    	else
													    	{
													    		echo "withdraw error ".json_encode($withdraw["status"]["description"], JSON_UNESCAPED_UNICODE)."<br>";
													    	}
													    }
													    else
													    {
													    	echo "withdraw error ".json_encode($withdraw["status"]["description"], JSON_UNESCAPED_UNICODE)."<br>";
													    }
													}
													else
													{
														$q_up_w = dd_q('UPDATE withdraw_tb SET w_status=?, w_action_by=? WHERE w_id=?', [
															'99',
															"Auto",
															$row_w_tb['w_id']
														]);
														if($q_up_w = true)
														{
															write_log("withdraw : change_status_auto_system", "เปลี่ยนสถานะรายการถอนให้แอดมินดำเนินการ (รหัสรายการ ".$row_w_tb['w_id'].")", "");

															notify_message("⚠️ เปลี่ยนสถานะรายการถอนให้แอดมินดำเนินการ ของยูส ".$row_w_tb['w_user']." จำนวน ".$row_w_tb['w_amount']." บาท เวลา ".date("d/m/Y H:i:s"), $row_2['tokenline']);

															echo "ทำรายการสำเร็จ <br>";
														}
														else
														{
															echo "ทำรายการไม่สำเร็จ <br>";
														}
													}
											    }
											    else
											    {
											    	echo $row['w_bank_name']." ไม่รองรับการถอนเงินอัตโนมัติ กรุณาติดต่อผู้พัฒนา <br>";
											    }
											}
											else
											{
												echo "ยอดเงินคงเหลือไม่เพียงพอ <br>";
											}
										}
										else
										{
											echo $balance."<br>";
										}
									}
									else
									{
										echo $balance."<br>";
									}
								}
								else
								{
									echo $balance."<br>";
								}
							}
							else
							{
								$q_withdraw_auto_tb = dd_q('SELECT * FROM withdraw_auto_tb WHERE w_u_user = ? AND w_u_id = ?', [
									$row_w_tb['w_user'],
									$row_w_tb['w_u_id']
								]);
								if ($q_withdraw_auto_tb->rowCount() > 0)
								{
									$row_withdraw_auto_tb = $q_withdraw_auto_tb->fetch(PDO::FETCH_ASSOC);

									$balance = $apiSCB->summary();
									if(array_key_exists('totalAvailableBalance', $balance) && array_key_exists('status', $balance))
									{
										if(array_key_exists('code', $balance["status"]) && array_key_exists('description', $balance["status"]))
										{
											if($balance["status"]["code"] == 1000)
											{
												if(str_replace(",", "", $balance["totalAvailableBalance"]) >= $row_w_tb['w_amount'])
												{
													if($bankcode != "")
												    {
												    	if($isSuccess)
												    	{
													    	$withdraw = $apiSCB->transfer_confrim(trim($row_w_tb['w_bank_number']), $bankcode, trim($row_w_tb['w_amount']));
													    	if(array_key_exists('data', $withdraw) && array_key_exists('status', $withdraw))
													    	{
													    		if(array_key_exists('code', $withdraw["status"]) && array_key_exists('description', $withdraw["status"]))
													    		{
													    			if($withdraw["status"]["code"] == 1000)
													    			{
													    				$q_up_w = dd_q('UPDATE withdraw_tb SET w_status=?, w_action_by=? WHERE w_id=?', [
															    			'1',
															    			"Auto",
															    			$row_w_tb['w_id']
															    		]);
															    		if($q_up_w = true)
															    		{
															    			write_log("withdraw : approve_auto_system", "อนุมัติรายการถอน (รหัสรายการ ".$row_w_tb['w_id'].")", "");

															    			notify_message("🤖 อนุมัติรายการถอน Auto ของยูส ".$row_w_tb['w_user']." จำนวน ".$row_w_tb['w_amount']." บาท เวลา ".date("d/m/Y H:i:s"), $row_2['tokenline']);

															    			echo "ทำรายการสำเร็จ <br>";
															    		}
															    		else
															    		{
															    			echo "ทำรายการไม่สำเร็จ <br>";
															    		}
													    			}
													    			else
													    			{
													    				echo "withdraw error ".json_encode($withdraw["status"]["description"], JSON_UNESCAPED_UNICODE)."<br>";
													    			}
													    		}
													    		else
													    		{
													    			echo "withdraw error ".json_encode($withdraw["status"]["description"], JSON_UNESCAPED_UNICODE)."<br>";
													    		}
													    	}
													    	else
													    	{
													    		echo "withdraw error ".json_encode($withdraw["status"]["description"], JSON_UNESCAPED_UNICODE)."<br>";
													    	}
												    	}
												    	else
												    	{
												    		$q_up_w = dd_q('UPDATE withdraw_tb SET w_status=?, w_action_by=? WHERE w_id=?', [
												    			'99',
												    			"Auto",
												    			$row_w_tb['w_id']
												    		]);
															if($q_up_w = true)
															{
																write_log("withdraw : change_status_auto_system", "เปลี่ยนสถานะรายการถอนให้แอดมินดำเนินการ (รหัสรายการ ".$row_w_tb['w_id'].")", "");

																notify_message("🤖 เปลี่ยนสถานะรายการถอนให้แอดมินดำเนินการ ของยูส ".$row_w_tb['w_user']." จำนวน ".$row_w_tb['w_amount']." บาท เวลา ".date("d/m/Y H:i:s"), $row_2['tokenline']);

																echo "ทำรายการสำเร็จ <br>";
															}
															else
															{
																echo "ทำรายการไม่สำเร็จ <br>";
															}
												    	}
												    }
												    else
												    {
												    	echo $row['w_bank_name']." ไม่รองรับการถอนเงินอัตโนมัติ กรุณาติดต่อผู้พัฒนา <br>";
												    }
												}
												else
												{
													echo "ยอดเงินคงเหลือไม่เพียงพอ <br>";
												}
											}
											else
											{
												echo "balance code error ".$balance."<br>";
											}
										}
										else
										{
											echo "balance status error ".$balance."<br>";
										}
									}
									else
									{
										echo "balance totalAvailableBalance error ".$balance."<br>";
									}
								}
								else
								{
									echo "ผู้เล่นนี้ไม่อยู่ในรายชื่อถอนออโต้ กรุณาอนุมัติโดยแอดมิน <br>";
								}
							}
						}
						else
						{
							echo "ยอดถอนเกินกว่ายอดเงินสูงสุดที่ให้ถอนออโต้ กรุณาอนุมัติถอนโดยแอดมิน <br>";
						}
					}
					else
					{
						echo "รายการถอน ID ".$row_withdraw_tb['w_id']." ถูกดำเนินการแล้ว <br>";
					}
				}
			}
			else
			{
				echo "ถอนออโต้แบบไม่ต้องยืนยัน ปิดใช้งาน <br>";
			}
		}
		else
		{
			echo "ไม่พบข้อมูล website_tb <br>";
		}
	}
	else
	{
		echo "ไม่พบข้อมูล device_id หรือ pin หรือ เลขบัญชีธนาคาร <br>";
	}
}
else
{
	echo "ระบบ auto ปิดใช้งาน <br>";
}
?>