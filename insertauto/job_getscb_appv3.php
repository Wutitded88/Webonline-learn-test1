<?php
date_default_timezone_set("Asia/Bangkok");
ini_set('max_execution_time', 60);
require_once 'config.php';
require_once 'scbClass.php';
require_once 'Service/amb.php';
$api = new AMBAPI();

$check_outstading = 100; // ตั้งค่า outstading

$q_website = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
$row_website = $q_website->fetch(PDO::FETCH_ASSOC);

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
										if($val["txnCode"]["code"] == "X1" && $val["txnDebitCreditFlag"] == "C" && strpos($val["txnCode"]["description"], "ฝากเงิน") !== false)
										{
											$txnDateTime = new DateTime($val["txnDateTime"]);
											$w_create_date = date('Y-m-d H:i:s',strtotime('-20 minutes'));
											if($w_create_date > $txnDateTime)
											{
												//ข้ามรายการที่เลยเวลามา 20 นาที
											}
											else
											{
												$t_topup_bank = "";
												if (strpos($val["txnRemark"], "รับโอนจาก SCB") !== false)
												{
													$str_number = str_replace("รับโอนจาก SCB ", "", $val["txnRemark"]);
													$arr_str_number = explode(" ", $str_number);
													if(count($arr_str_number) > 0)
													{
														$t_topup_bank = strtolower($arr_str_number[0]);
													}
												}
												else if (strpos($val["txnRemark"], "Transfer from SCB") !== false)
												{
													$str_number = str_replace("Transfer from SCB ", "", $val["txnRemark"]);
													$arr_str_number = explode(" ", $str_number);
													if(count($arr_str_number) > 0)
													{
														$t_topup_bank = strtolower($arr_str_number[0]);
													}
												}
												else
												{
													$arr_str_number = explode("/", $val["txnRemark"]);
													if(count($arr_str_number) > 1)
													{
														$t_topup_bank = strtolower($arr_str_number[1]);
													}
												}
													
												$txnDateTime = new DateTime($val["txnDateTime"]);
												$t_date_create = $txnDateTime->format('Y-m-d');
												$t_time_create = $txnDateTime->format('H:i:s');
												$t_create_date = $txnDateTime->format('Y-m-d H:i:s');

												
												$t_tx_id = $val["txnRemark"]." (".$t_create_date.")";

												$txnAmount = str_replace(",", "", $val["txnAmount"]);
												$t_amount = number_format($txnAmount, 2, '.', '');

												$q_temp = dd_q('SELECT * FROM topup_temp_db WHERE t_topup_bank = ? AND t_amount = ? AND t_time_create = ? AND t_type_system = ?', [
													$t_topup_bank, 
													$t_amount, 
													$t_time_create, 
													"scb"
												]);
												if ($q_temp->rowCount() == 0)
												{
													$current_time = date('H:i:s');
													if($current_time >= $t_time_create && $t_date_create == date("Y-m-d"))
													{
														$q_t = dd_q('SELECT * FROM topup_db WHERE t_tx_id = ?', [$t_tx_id]);
														if ($q_t->rowCount() == 0)
														{
															if (strpos($val["txnRemark"], "รับโอนจาก SCB") !== false)
															{
																$q_u = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($t_topup_bank, -4)]);
															}
															else if (strpos($val["txnRemark"], "Transfer from SCB") !== false)
															{
																$q_u = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($t_topup_bank, -4)]);
															}
															else
															{
																$q_u = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($t_topup_bank, -6)]);
															}
															if ($q_u->rowCount() == 1)
															{
																$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

																$insertFalse = false;
																$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
																if ($_GetUserCredit->success == true)
																{
																	// 2022/02/07
																	// === เช็ค outstanding ยอดล่าสุด ===
																	$sum_outstanding = 0;
																	$q_trans = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? AND t_transaction_id != ? ORDER BY t_id DESC LIMIT 1', [
																		$row_u['u_user'],
																		""
																	]);
																	if ($q_trans->rowCount() > 0)
																	{
																		$row_trans = $q_trans->fetch(PDO::FETCH_ASSOC);
																		$outs = $api->GetWinLose($row_u['u_agent_id'], $row_trans['t_transaction_id']);
																		if ($outs->success == true)
																		{
																			$outs = json_decode(json_encode($outs->data), true);
																			foreach ($outs["data"] as $val)
																			{
																				$sum_outstanding = $sum_outstanding + ($val['outstanding']);
																			}
																		}
																	}

																	$q_top = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_transaction_id != ? AND t_status = ? ORDER BY t_id DESC LIMIT 1', [
																		$row_u['u_user'],
																		"",
																		"1"
																	]);
																	if ($q_top->rowCount() > 0)
																	{
																		$row_top = $q_top->fetch(PDO::FETCH_ASSOC);
																		$outs = $api->GetWinLose($row_u['u_agent_id'], $row_top['t_transaction_id']);
																		if ($outs->success == true)
																		{
																			$outs = json_decode(json_encode($outs->data), true);
																			foreach ($outs["data"] as $val)
																			{
																				$sum_outstanding = $sum_outstanding + ($val['outstanding']);
																			}
																		}
																	}
																	// === เช็ค outstanding ยอดล่าสุด ===

																	$isResetPromotion = true;
																	$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);

																	if($sum_outstanding > $check_outstading)
																	{
																		$isResetPromotion = false;
																		write_log("SCB Topup ไม่รีโปร", $row_u['u_user']." ไม่รีโปรเนื่องจาก outstanding = ".$sum_outstanding);
																	}
																	else if($_GetUserCredit['credit'] > 5)
																	{
																		$isResetPromotion = false;
																		write_log("SCB Topup ไม่รีโปร", $row_u['u_user']." ไม่รีโปรเนื่องจากเครดิตคงเหลือ = ".$_GetUserCredit['credit']);
																	}

																	if(!$isResetPromotion) //ไม่รีโปร
																	{

																		$q_trans_add = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? AND t_active = ? ORDER BY t_id DESC LIMIT 1', [
																			$row_u['u_user'],
																			"Y"
																		]);
																		if ($q_trans_add->rowCount() > 0)
																		{
																			$row_trans = $q_trans_add->fetch(PDO::FETCH_ASSOC);

																			$_turnover = 0;
																			$q_b = dd_q('SELECT * FROM promotion_tb WHERE p_title = ?', [
																				$row_trans["t_promotion_title"]
																			]);
																			if ($q_b->rowCount() > 0)
																			{
																				$row_b = $q_b->fetch(PDO::FETCH_ASSOC);
																				$_turnover = $row_b["p_turnover"];
																			}
																			else
																			{
																				$q_bx = dd_q('SELECT * FROM promotion_fixed_deposit_tb WHERE p_title = ?', [
																					$row_trans["t_promotion_title"]
																				]);
																				if ($q_bx->rowCount() > 0)
																				{
																					$row_b = $q_bx->fetch(PDO::FETCH_ASSOC);
																					$_turnover = $row_b["p_turnover"];
																				}
																			}
																			
																			if($row_trans["t_promotion_turntype"] == "c") //เท่า
																			{
																				$summary = $t_amount * $_turnover; //คำนวนยอดเทิร์น
																			}
																			else if($row_trans["t_promotion_turntype"] == "p") //เปอร์เซ็น
																			{
																				$summary = $t_amount + $t_amount * ($_turnover / 100); //คำนวนยอดเทิร์น
																			}
																			else if($row_trans["t_promotion_turntype"] == "w") //winloss
																			{
																				$summary = $t_amount * $_turnover; //คำนวนยอดเทิร์น
																			}
																			$t_turnover = $summary + $row_trans["t_turnover"];

																			dd_q('UPDATE transfergame_tb SET t_turnover = ? WHERE t_id = ?', [
																				$t_turnover, 
																				$row_trans['t_id']
																			]);
																		}

																		$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",","",number_format($t_amount, 2, '.', '')));
																		if ($deposit->success == true)
																		{
																			$deposit = json_decode(json_encode($deposit->data), true);
																			$ExternalTransactionId = $deposit['ref'];
																			$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time, t_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u['u_id'], 
																				$row_u['u_user'], 
																				$row_u['u_agent_id'], 
																				$row_u['u_fname']." ".$row_u['u_lname'], 
																				$t_topup_bank, 
																				$t_tx_id, 
																				$t_amount, 
																				$row_u['u_bank_code'], 
																				$row_u['u_bank_number'], 
																				$row_u['u_bank_name'], 
																				$row['a_bank_code'], 
																				$row['a_bank_name'], 
																				$row['a_bank_acc_number'], 
																				'System', 
																				$t_create_date,
																				$t_date_create, 
																				$t_time_create, 
																				'1', 
																				'1', 
																				$deposit['before'], 
																				$deposit['after'], 
																				"scb",
																				date("Y-m-d"),
																				date('H:i:s'),
																				$ExternalTransactionId
																			]);
																			if($q_insert_t == true)
																			{
																				$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
																					'Vip', 
																					$row_u['u_id']
																				]);
																				if($q_update_u == true)
																				{
																					//% แนะนำเพื่อน
																					if(!empty($row_u['u_aff']))
																					{
																						if($row_website['aff_type'] == "1") //ฝากแรกของเพื่อน
																						{
																							$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
																							if ($q_t_aff->rowCount() == 1)
																							{
																								if($row_website['aff_step'] >= 1)
																								{
																									$aff_percent = $t_amount * $row_website['affpersen'];
																									dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																										$row_u['u_aff'], 
																										$row_u['u_user'], 
																										$t_amount, 
																										$aff_percent, 
																										'0',
																										1, 
																										date("Y-m-d"),
																										date('H:i:s')
																									]);
																								}
																								if($row_website['aff_step'] >= 2)
																								{
																									$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																										$row_u['u_aff']
																									]);
																									if ($q_u2->rowCount() > 0)
																									{
																										$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																										if(!empty($row_u2['u_aff']))
																										{
																											$aff_percent2 = $t_amount * $row_website['affpersen2'];
																											dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																												$row_u2['u_aff'], 
																												$row_u['u_user'], 
																												$t_amount, 
																												$aff_percent2, 
																												'0',
																												2, 
																												date("Y-m-d"),
																												date('H:i:s')
																											]);
																										}
																									}
																								}
																								if($row_website['aff_step'] == 3)
																								{
																									$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																										$row_u['u_aff']
																									]);
																									if ($q_u2->rowCount() > 0)
																									{
																										$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																										if(!empty($row_u2['u_aff']))
																										{
																											$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																												$row_u2['u_aff']
																											]);
																											if ($q_u3->rowCount() > 0)
																											{
																												$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																												if(!empty($row_u3['u_aff']))
																												{
																													$aff_percent3 = $t_amount * $row_website['affpersen3'];
																													dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																														$row_u3['u_aff'], 
																														$row_u['u_user'], 
																														$t_amount, 
																														$aff_percent3, 
																														'0',
																														3, 
																														date("Y-m-d"),
																														date('H:i:s')
																													]);
																												}
																											}
																										}
																									}
																								}
																							}
																						}
																						else if($row_website['aff_type'] == "2")  //ทุกยอดฝาก
																						{
																							if($row_website['aff_step'] >= 1)
																							{
																								$aff_percent = $t_amount * $row_website['affpersen'];
																								dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																									$row_u['u_aff'], 
																									$row_u['u_user'], 
																									$t_amount, 
																									$aff_percent, 
																									'0',
																									1, 
																									date("Y-m-d"),
																									date('H:i:s')
																								]);
																							}
																							if($row_website['aff_step'] >= 2)
																							{
																								$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																									$row_u['u_aff']
																								]);
																								if ($q_u2->rowCount() > 0)
																								{
																									$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																									if(!empty($row_u2['u_aff']))
																									{
																										$aff_percent2 = $t_amount * $row_website['affpersen2'];
																										dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																											$row_u2['u_aff'], 
																											$row_u['u_user'], 
																											$t_amount, 
																											$aff_percent2, 
																											'0',
																											2, 
																											date("Y-m-d"),
																											date('H:i:s')
																										]);
																									}
																								}
																							}
																							if($row_website['aff_step'] == 3)
																							{
																								$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																									$row_u['u_aff']
																								]);
																								if ($q_u2->rowCount() > 0)
																								{
																									$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																									if(!empty($row_u2['u_aff']))
																									{
																										$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																											$row_u2['u_aff']
																										]);
																										if ($q_u3->rowCount() > 0)
																										{
																											$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																											if(!empty($row_u3['u_aff']))
																											{
																												$aff_percent3 = $t_amount * $row_website['affpersen3'];
																												dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																													$row_u3['u_aff'], 
																													$row_u['u_user'], 
																													$t_amount, 
																													$aff_percent3, 
																													'0',
																													3, 
																													date("Y-m-d"),
																													date('H:i:s')
																												]);
																											}
																										}
																									}
																								}
																							}
																						}
																					}
																					
																					notify_message("✅ ".$row_u['u_fname']." ".$row_u['u_user']." เติมเงินผ่าน SCB แบบไม่รีโปร ".number_format($t_amount,2,',','.')." บาท",$_CONFIG['token']);
																					echo $t_tx_id." - สำเร็จ status 1 (พบข้อมูล user)";
																					echo "<br>";
																				}
																			}
																		}
																		else
																		{
																			write_log("SCB API-Deposit-ไม่รีโปร", json_encode($deposit->message));
																			$insertFalse = true;
																			echo "deposit".$deposit->message;
																		}
																	}
																	else //รีโปร
																	{
																		$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",","",number_format($t_amount, 2, '.', '')));
																		if ($deposit->success == true)
																		{
																			$deposit = json_decode(json_encode($deposit->data), true);
																			$ExternalTransactionId = $deposit['ref'];
																			dd_q('UPDATE transfergame_tb SET t_active=? WHERE t_user=?', [
																				"N", 
																				$row_u['u_user']
																			]);

																			$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time, t_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u['u_id'], 
																				$row_u['u_user'], 
																				$row_u['u_agent_id'], 
																				$row_u['u_fname']." ".$row_u['u_lname'], 
																				$t_topup_bank, 
																				$t_tx_id, 
																				$t_amount, 
																				$row_u['u_bank_code'], 
																				$row_u['u_bank_number'], 
																				$row_u['u_bank_name'], 
																				$row['a_bank_code'], 
																				$row['a_bank_name'], 
																				$row['a_bank_acc_number'], 
																				'System', 
																				$t_create_date,
																				$t_date_create, 
																				$t_time_create, 
																				'1', 
																				'1', 
																				$deposit['before'], 
																				$deposit['after'], 
																				"scb",
																				date("Y-m-d"),
																				date('H:i:s'),
																				$ExternalTransactionId
																			]);
																			if($q_insert_t == true)
																			{
																				$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
																					'Vip', 
																					$row_u['u_id']
																				]);
																				if($q_update_u == true)
																				{
																					//% แนะนำเพื่อน
																					if(!empty($row_u['u_aff']))
																					{
																						if($row_website['aff_type'] == "1") //ฝากแรกของเพื่อน
																						{
																							$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
																							if ($q_t_aff->rowCount() == 1)
																							{
																								if($row_website['aff_step'] >= 1)
																								{
																									$aff_percent = $t_amount * $row_website['affpersen'];
																									dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																										$row_u['u_aff'], 
																										$row_u['u_user'], 
																										$t_amount, 
																										$aff_percent, 
																										'0',
																										1, 
																										date("Y-m-d"),
																										date('H:i:s')
																									]);
																								}
																								if($row_website['aff_step'] >= 2)
																								{
																									$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																										$row_u['u_aff']
																									]);
																									if ($q_u2->rowCount() > 0)
																									{
																										$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																										if(!empty($row_u2['u_aff']))
																										{
																											$aff_percent2 = $t_amount * $row_website['affpersen2'];
																											dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																												$row_u2['u_aff'], 
																												$row_u['u_user'], 
																												$t_amount, 
																												$aff_percent2, 
																												'0',
																												2, 
																												date("Y-m-d"),
																												date('H:i:s')
																											]);
																										}
																									}
																								}
																								if($row_website['aff_step'] == 3)
																								{
																									$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																										$row_u['u_aff']
																									]);
																									if ($q_u2->rowCount() > 0)
																									{
																										$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																										if(!empty($row_u2['u_aff']))
																										{
																											$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																												$row_u2['u_aff']
																											]);
																											if ($q_u3->rowCount() > 0)
																											{
																												$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																												if(!empty($row_u3['u_aff']))
																												{
																													$aff_percent3 = $t_amount * $row_website['affpersen3'];
																													dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																														$row_u3['u_aff'], 
																														$row_u['u_user'], 
																														$t_amount, 
																														$aff_percent3, 
																														'0',
																														3, 
																														date("Y-m-d"),
																														date('H:i:s')
																													]);
																												}
																											}
																										}
																									}
																								}
																							}
																						}
																						else if($row_website['aff_type'] == "2")  //ทุกยอดฝาก
																						{
																							if($row_website['aff_step'] >= 1)
																							{
																								$aff_percent = $t_amount * $row_website['affpersen'];
																								dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																									$row_u['u_aff'], 
																									$row_u['u_user'], 
																									$t_amount, 
																									$aff_percent, 
																									'0',
																									1, 
																									date("Y-m-d"),
																									date('H:i:s')
																								]);
																							}
																							if($row_website['aff_step'] >= 2)
																							{
																								$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																									$row_u['u_aff']
																								]);
																								if ($q_u2->rowCount() > 0)
																								{
																									$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																									if(!empty($row_u2['u_aff']))
																									{
																										$aff_percent2 = $t_amount * $row_website['affpersen2'];
																										dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																											$row_u2['u_aff'], 
																											$row_u['u_user'], 
																											$t_amount, 
																											$aff_percent2, 
																											'0',
																											2, 
																											date("Y-m-d"),
																											date('H:i:s')
																										]);
																									}
																								}
																							}
																							if($row_website['aff_step'] == 3)
																							{
																								$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																									$row_u['u_aff']
																								]);
																								if ($q_u2->rowCount() > 0)
																								{
																									$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																									if(!empty($row_u2['u_aff']))
																									{
																										$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																											$row_u2['u_aff']
																										]);
																										if ($q_u3->rowCount() > 0)
																										{
																											$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																											if(!empty($row_u3['u_aff']))
																											{
																												$aff_percent3 = $t_amount * $row_website['affpersen3'];
																												dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																													$row_u3['u_aff'], 
																													$row_u['u_user'], 
																													$t_amount, 
																													$aff_percent3, 
																													'0',
																													3, 
																													date("Y-m-d"),
																													date('H:i:s')
																												]);
																											}
																										}
																									}
																								}
																							}
																						}
																					}

																					notify_message("✅ ".$row_u['u_fname']." ".$row_u['u_user']." เติมเงินผ่าน SCB แบบรีโปร ".number_format($t_amount,2,'.',',')." บาท",$_CONFIG['token']);
																					echo $t_tx_id." - สำเร็จ status 1 (พบข้อมูล user)";
																					echo "<br>";
																				}
																			}
																		}
																		else
																		{
																			write_log("SCB API-Deposit-รีโปร", json_encode($deposit->message));
																			$insertFalse = true;
																			echo "deposit".$deposit->message;
																		}
																	}
																}
																else
																{
																	write_log("SCB API-GetUserCredit", json_encode($_GetUserCredit));
																	$insertFalse = true;
																	echo "_GetUserCredit - ".json_encode($_GetUserCredit);
																}

																if($insertFalse == true) //api error
																{
																	$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																		$row_u['u_id'], 
																		$row_u['u_user'], 
																		$row_u['u_agent_id'], 
																		$row_u['u_fname']." ".$row_u['u_lname'], 
																		$t_topup_bank, 
																		$t_tx_id, 
																		$t_amount, 
																		$row_u['u_bank_code'], 
																		$row_u['u_bank_number'], 
																		$row_u['u_bank_name'], 
																		$row['a_bank_code'], 
																		$row['a_bank_name'], 
																		$row['a_bank_acc_number'], 
																		'System', 
																		$t_create_date,
																		$t_date_create, 
																		$t_time_create, 
																		'2', 
																		'1', 
																		$t_amount, 
																		$t_amount,
																		"scb",
																		date("Y-m-d"),
																		date('H:i:s')
																	]);
																	notify_message("❌ ".$row_u['u_user']." เติมเงินผ่าน SCB".number_format($t_amount,2,'.',',')." บาท แต่ API AMB Error กรุณายืนยันรายการฝากอีกครั้ง ตรวจสอบข้อผิดพลาดได้ที่ Log ❌",$_CONFIG['token']);
																}
															}
															else
															{
																$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																	'', 
																	'', 
																	'', 
																	'', 
																	$t_topup_bank, 
																	$t_tx_id, 
																	$t_amount, 
																	'', 
																	'', 
																	'', 
																	$row['a_bank_code'], 
																	$row['a_bank_name'], 
																	$row['a_bank_acc_number'], 
																	'System', 
																	$t_create_date,
																	$t_date_create, 
																	$t_time_create, 
																	'2', 
																	'1', 
																	0, 
																	0,
																	"scb",
																	date("Y-m-d"),
																	date('H:i:s')
																]);
																if($q_insert_t == true)
																{
																	notify_message("❌ เติมเงินผ่าน SCB".number_format($t_amount,2,'.',',')." บาท แต่ข้อมูล user ผิดพลาด กรุณาตรวจสอบรายการฝาก ❌",$_CONFIG['token']);
																	echo $t_tx_id." - สำเร็จ status 2 (ไม่พบข้อมูล user หรือ มีมากกว่า 1 user)";
																	echo "<br>";
																}
															}
														}
													}
													else
													{
														dd_q('INSERT INTO topup_temp_db (t_topup_bank, t_tx_id, t_amount, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_create_date, t_date_create, t_time_create, t_status, t_add_datetime, t_update_datetime, t_type_system) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
															$t_topup_bank, 
															$t_tx_id, 
															$t_amount, 
															$row['a_bank_code'], 
															$row['a_bank_name'], 
															$row['a_bank_acc_number'], 
															$t_create_date, 
															$t_date_create, 
															$t_time_create, 
															'2', 
															date('Y-m-d H:i:s'), 
															date('Y-m-d H:i:s'), 
															"scb"
														]);
													}
												}
											}
										}
									}
								}
							}
							dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
								date('Y-m-d H:i:s'),
								'1',
								'scb'
							]);
						}
						else
						{
							echo "ไม่พบข้อมูลรายการฝากของวันนี้ (txnList)";
							dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
								date('Y-m-d H:i:s'),
								'0',
								'scb'
							]);
						}
					}
					else
					{
						echo $data["status"]["description"];
						dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
							date('Y-m-d H:i:s'),
							'0',
							'scb'
						]);
					}
				}
				else
				{
					echo "ไม่พบข้อมูล code หรือ description";
					dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
						date('Y-m-d H:i:s'),
						'0',
						'scb'
					]);
				}
			}
			else
			{
				echo "ไม่พบข้อมูล status";
				dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
					date('Y-m-d H:i:s'),
					'0',
					'scb'
				]);
			}
		}
		else
		{
			echo "ไม่พบข้อมูล data";
			dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
				date('Y-m-d H:i:s'),
				'0',
				'scb'
			]);
		}
	}
	else
	{
		echo "ไม่พบข้อมูล device_id หรือ pin หรือ เลขบัญชีธนาคาร";
		dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
			date('Y-m-d H:i:s'),
			'0',
			'scb'
		]);
	}
}
else
{
	echo "ระบบ auto ปิดใช้งาน";
	dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
		date('Y-m-d H:i:s'),
		'0',
		'scb'
	]);
}
?>