<?php
date_default_timezone_set("Asia/Bangkok");
ini_set('max_execution_time', 60);
require_once 'config.php';
require_once './kbiz/ClassKbiz.php';
require_once 'Service/amb.php';
$api = new AMBAPI();

header("Content-Type: application/json; charset=UTF-8");

$q_website = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
$row_website = $q_website->fetch(PDO::FETCH_ASSOC);

$q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['kbank', 1]);
if ($q_1->rowCount() > 0)
{
	$row = $q_1->fetch(PDO::FETCH_ASSOC);

	if($row['a_bank_username'] != "" && $row['a_bank_password'] != "" && $row['a_bank_acc_number'] != "")
	{
		echo "เวลาที่ระบบออโต้ทำงาน: " . date('Y-m-d H:i:s');
		echo "<br>";
		$_username = $row['a_bank_username'];
		$_password = $row['a_bank_password'];
		$_acc_num = str_replace("-", "", $row['a_bank_acc_number']);

		$kbank = new KbankBiz(
			array(
				'username' => $_username, //ยูสเซอร์เนม
				'password' => $_password, //รหัสผ่าน
				'accountFrom' => $_acc_num //เลขบช.
			)
		);

		$isSuccess = false;
	    $refresh = $kbank->refreshSession();
	    if(array_key_exists('status', $refresh) && $refresh['status'] == "F")
	    {
	        $kbank->validateSession($kbank->login());
	    }
	    else
	    {
	        $isSuccess = true;
	    }

	    if($isSuccess)
	    {
	    	$TransactionHistory = $kbank->getTransactionHistory();
            $data = $kbank->GetNumberOtherBank($TransactionHistory);
            if(array_key_exists('data', $data) && array_key_exists('status', $data))
            {
            	foreach ($data['data']['recentTransactionList'] as $val)
            	{
	            	if(array_key_exists('transNameTh', $val) && array_key_exists('toAccountNumber', $val) && array_key_exists('data', $val))
	            	{
	            		if($val["transNameTh"] == "รับโอนเงิน")
	            		{
	            			$serviceRefID = "";
	            			$t_topup_bank = "";
	            			$t_topup_bank_full = false;
	            			if(!empty($val["toAccountNumber"])) // รับโอนจาก kbank xxx-x-x0894-x
	            			{
	            				$t_topup_bank = trim(str_replace("-", "", str_replace("x", "", $val["toAccountNumber"])));
	            				if(array_key_exists('data', $val))
	            				{
	            					if(array_key_exists('serviceRefID', $val['data']))
	            					{
		            					$serviceRefID = $val['data']['serviceRefID'];
		            				}
	            				}
	            			}
	            			else // รับโอนจากต่างธนาคาร
	            			{
	            				if(array_key_exists('data', $val))
	            				{
	            					if(array_key_exists('serviceRefID', $val['data']))
	            					{
		            					$serviceRefID = $val['data']['serviceRefID'];
		            				}
		            				if(array_key_exists('data', $val['data']))
	            					{
	            						if(array_key_exists('toAccountNo', $val['data']['data']))
		            					{
		            						$t_topup_bank = $val['data']['data']['toAccountNo'];
		            						$t_topup_bank_full = true;
			            				}
		            				}
	            				}
	            			}

	            			$transDate = new DateTime($val["transDate"]);
	            			$t_date_create = $transDate->format('Y-m-d');
	            			$t_time_create = $transDate->format('H:i:s');
	            			$t_create_date = $transDate->format('Y-m-d H:i:s');

	            			$t_tx_id = $serviceRefID." (".$t_create_date.")";

	            			$depositAmount = str_replace(",", "", $val["depositAmount"]);
	            			$t_amount = number_format($depositAmount, 2, '.', '');

	            			$q_t = dd_q('SELECT * FROM topup_db WHERE t_tx_id = ?', [$t_tx_id]);
	            			if ($q_t->rowCount() == 0)
	            			{
	            				if ($t_topup_bank_full)
	            				{
	            					$q_u = dd_q('SELECT * FROM user_tb WHERE u_bank_number = ?', [$t_topup_bank]);
	            				}
	            				else
	            				{
	            					$q_u = dd_q('SELECT * FROM user_tb WHERE SUBSTR(u_bank_number, -5, 4) = ?', [$t_topup_bank]);
	            				}

	            				if ($q_u->rowCount() == 1)
	            				{
	            					$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

	            					$insertFalse = false;
	            					$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
	            					if ($_GetUserCredit->success == true)
	            					{
	            						// 2022/01/10
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

	            						if($sum_outstanding != 0)
	            						{
	            							$isResetPromotion = false;
	            							write_log("KBANK Topup ไม่รีโปร", $row_u['u_user']." ไม่รีโปรเนื่องจาก outstanding = ".$sum_outstanding);
	            						}
	            						else if($_GetUserCredit['credit'] > 5)
	            						{
	            							$isResetPromotion = false;
	            							write_log("KBANK Topup ไม่รีโปร", $row_u['u_user']." ไม่รีโปรเนื่องจากเครดิตคงเหลือ = ".$_GetUserCredit['credit']);
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
													"kbank",
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

																	notify_message("✅ ".$row_u['u_fname']." ".$row_u['u_user']." เติมเงินผ่าน KBANK แบบไม่รีโปร ".number_format($t_amount,2,',','.')." บาท",$_CONFIG['token']);
																	echo $t_tx_id." - สำเร็จ status 1 (พบข้อมูล user)";
																	echo "<br>";
																}
												}
											}
											else
											{
												write_log("KBANK API-Deposit-ไม่รีโปร", json_encode($deposit->message));
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
													"kbank",
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

																	notify_message("✅ ".$row_u['u_fname']." ".$row_u['u_user']." เติมเงินผ่าน KBANK แบบรีโปร ".number_format($t_amount,2,'.',',')." บาท",$_CONFIG['token']);
																	echo $t_tx_id." - สำเร็จ status 1 (พบข้อมูล user)";
																	echo "<br>";
																}
												}
											}
											else
											{
												write_log("KBANK API-Deposit-รีโปร", json_encode($deposit->message));
												$insertFalse = true;
												echo "deposit".$deposit->message;
											}
										}
									}
									else
									{
										write_log("KBANK API-GetUserCredit", json_encode($_GetUserCredit));
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
											"kbank",
											date("Y-m-d"),
											date('H:i:s')
										]);
										notify_message("❌ ".$row_u['u_user']." เติมเงินผ่าน KBANK".number_format($t_amount,2,'.',',')." บาท แต่ API AMB Error กรุณายืนยันรายการฝากอีกครั้ง ตรวจสอบข้อผิดพลาดได้ที่ Log ❌",$_CONFIG['token']);
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
										"kbank",
										date("Y-m-d"),
										date('H:i:s')
									]);
									if($q_insert_t == true)
									{
										notify_message("❌ เติมเงินผ่าน KBANK".number_format($t_amount,2,'.',',')." บาท แต่ข้อมูล user ผิดพลาด กรุณาตรวจสอบรายการฝาก ❌",$_CONFIG['token']);
										echo $t_tx_id." - สำเร็จ status 2 (ไม่พบข้อมูล user หรือ มีมากกว่า 1 user)";
										echo "<br>";
									}
								}
							}
						}
					}
				}
            }
            else
			{
				echo "ไม่พบข้อมูล data";
				dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
					date('Y-m-d H:i:s'),
					'0',
					'kbank'
				]);
			}
	    }
	}
	else
	{
		echo "ไม่พบข้อมูล user/pass/acc_number";
		dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
			date('Y-m-d H:i:s'),
			'0',
			'kbank'
		]);
	}
}
else
{
	echo "ระบบ auto ปิดใช้งาน";
	dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
		date('Y-m-d H:i:s'),
		'0',
		'kbank'
	]);
}
?>