<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'Service/amb.php';
$api = new AMBAPI();

$check_outstading = 20; // ตั้งค่า outstading

function dd_return($status, $message)
{
	$json = ['message' => $message];
    if($status)
    {
        http_response_code(200);
        die(json_encode($json));
    }
    else
    {
        http_response_code(400);
        die(json_encode($json));
    }
}

//////////////////////////////////////////////////////////////////////////

header('Content-Type: application/json; charset=utf-8;');

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else
	{
		$q_website = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
		$row_website = $q_website->fetch(PDO::FETCH_ASSOC);

		$ip = trim($_POST['ip']);
		$q_u = dd_q('SELECT * FROM user_tb WHERE (u_user = ? AND u_block_agent = 0)', [get_session()]);
		if ($q_u->rowCount() > 0)
		{
			$row_u = $q_u->fetch(PDO::FETCH_ASSOC);
			if($row_u['u_token'] == session_id())
			{
				$link = trim($_POST['link']);
				if($link != "")
				{
					require_once "../library/tmn_gift-class/tmn_gift.api.php";
					$tw = new tmn_gift();

					$code = $tw->hashcode($link);
					$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_tx_id = ?', [$code]);
					if ($q_topup_tb->rowCount() == 0)
					{
						$verify = json_decode($tw->verify($link), true);
						if ($verify['status']['code'] == "SUCCESS")
						{
							if ($verify['data']['voucher']['member'] == 1)
							{
								$q_website_tb = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
								if ($q_website_tb->rowCount() > 0)
								{
									$row_website_tb = $q_website_tb->fetch(PDO::FETCH_ASSOC);
									$redeem = json_decode($tw->redeem($link, $row_website_tb['truewallet']), true);
									if ($redeem['status']['code'] == "SUCCESS")
									{
											$amount = number_format(str_replace(",", "", $redeem['data']['voucher']['amount_baht']), 2, '.', '');

											$insertSuccess = false;
											$msg_alert = "";
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
			                                    	write_log("True Gift Topup ไม่รีโปร", get_wallet("u_user")." ไม่รีโปรเนื่องจาก outstanding = ".$sum_outstanding, $ip);
			                                    }
			                                    else if($_GetUserCredit['credit'] > 5)
			                                    {
			                                    	$isResetPromotion = false;
			                                    	write_log("True Gift ไม่รีโปร", get_wallet("u_user")." ไม่รีโปรเนื่องจากเครดิตคงเหลือ = ".$_GetUserCredit['credit'], $ip);
			                                    }

												if(!$isResetPromotion) //ไม่รีโปร
												{
													$deposit = $api->transferCreditTo($row_u['u_agent_id'], $amount);
													if ($deposit->success == true)
													{
														$deposit = json_decode(json_encode($deposit->data), true);
														$ExternalTransactionId = $deposit['ref'];
														$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time, t_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
															$row_u['u_id'], 
															$row_u['u_user'], 
															$row_u['u_agent_id'],
															$row_u['u_fname']." ".$row_u['u_lname'], 
															$code, 
															$code, 
															$amount, 
															$row_u['u_bank_code'], 
															$row_u['u_bank_number'], 
															$row_u['u_bank_name'], 
															"tmw", 
															"Truewallet Gift", 
															$row_website_tb['truewallet'], 
															'System', 
															date("Y-m-d H:i:s"),
															date("Y-m-d"),
															date('H:i:s'),
															'1', 
															'1', 
															$deposit['before'], 
															$deposit['after'], 
															"tmw",
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
																				$aff_percent = $amount * $row_website['affpersen'];
																				dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																					$row_u['u_aff'], 
																					$row_u['u_user'], 
																					$amount, 
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
																						$aff_percent2 = $amount * $row_website['affpersen2'];
																						dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																							$row_u2['u_aff'],
																							$row_u['u_user'], 
																							$amount, 
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
																								$aff_percent3 = $amount * $row_website['affpersen3'];
																								dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																									$row_u3['u_aff'], 
																									$row_u['u_user'], 
																									$amount, 
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
																			$aff_percent = $amount * $row_website['affpersen'];
																			dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u['u_aff'], 
																				$row_u['u_user'], 
																				$amount, 
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
																					$aff_percent2 = $amount * $row_website['affpersen2'];
																					dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																						$row_u2['u_aff'], 
																						$row_u['u_user'], 
																						$amount, 
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
																							$aff_percent3 = $amount * $row_website['affpersen3'];
																							dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																								$row_u3['u_aff'], 
																								$row_u['u_user'], 
																								$amount, 
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
																$insertSuccess = true;
																$msg_alert = "เติมเงินสำเร็จ คุณได้รับเครดิต จำนวน: ".number_format($amount, 2, '.', ',')." เครดิต";

																//ส่ง line แจ้งเตือน เติม wallet
																notify_message("✅ เบอร์ ".$row_u['u_user']." เติมเงินแบบ ไม่รีโปรโมชั่น จำนวน : ".number_format($amount, 2, '.', ',')." เครดิต True Wallet", $row_website_tb['linewallet']);
															}
														}
													}
													else
													{
														$insertSuccess = false;
														$msg_alert = "deposit ".$deposit->message;
													}
												}
												else //รีโปร
												{
													$deposit = $api->transferCreditTo($row_u['u_agent_id'], $amount);
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
															$code, 
															$code, 
															$amount, 
															$row_u['u_bank_code'], 
															$row_u['u_bank_number'], 
															$row_u['u_bank_name'], 
															"tmw", 
															"Truewallet Gift", 
															$row_website_tb['truewallet'], 
															'System', 
															date("Y-m-d H:i:s"),
															date("Y-m-d"),
															date('H:i:s'),
															'1', 
															'1', 
															$deposit['before'], 
															$deposit['after'], 
															"tmw",
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
																				$aff_percent = $amount * $row_website['affpersen'];
																				dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																					$row_u['u_aff'], 
																					$row_u['u_user'], 
																					$amount, 
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
																						$aff_percent2 = $amount * $row_website['affpersen2'];
																						dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																							$row_u2['u_aff'],
																							$row_u['u_user'], 
																							$amount, 
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
																								$aff_percent3 = $amount * $row_website['affpersen3'];
																								dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																									$row_u3['u_aff'], 
																									$row_u['u_user'], 
																									$amount, 
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
																			$aff_percent = $amount * $row_website['affpersen'];
																			dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u['u_aff'], 
																				$row_u['u_user'], 
																				$amount, 
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
																					$aff_percent2 = $amount * $row_website['affpersen2'];
																					dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																						$row_u2['u_aff'], 
																						$row_u['u_user'], 
																						$amount, 
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
																							$aff_percent3 = $amount * $row_website['affpersen3'];
																							dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																								$row_u3['u_aff'], 
																								$row_u['u_user'], 
																								$amount, 
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
																$insertSuccess = true;
																$msg_alert = "เติมเงินสำเร็จ คุณได้รับเครดิต จำนวน: ".number_format($amount, 2, '.', ',')." เครดิต";

																//ส่ง line แจ้งเตือน เติม wallet
																notify_message("✅ เบอร์ ".$row_u['u_user']."เติมเงินแบบ รีโปรโมชั่น  จำนวน : ".number_format($amount, 2, '.', ',')." เครดิต True Wallet", $row_website_tb['linewallet']);
															}
														}
													}
													else
													{
														$insertFalse = true;
														echo "deposit".$deposit->message;
													}
												}
											}
											else
											{
												$insertSuccess = false;
												$msg_alert = "ไม่สามารถดึงข้อมูลเครดิตได้ กรุณาลองใหม่อีกครั้ง";
											}

											if($insertSuccess)
											{
												dd_return(true, $msg_alert);
											}
											else
											{
												$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
													$row_u['u_id'], 
													$row_u['u_user'], 
													$row_u['u_agent_id'], 
													$row_u['u_fname']." ".$row_u['u_lname'], 
													$code, 
													$code, 
													$amount, 
													$row_u['u_bank_code'], 
													$row_u['u_bank_number'], 
													$row_u['u_bank_name'], 
													"tmw", 
													"Truewallet Gift", 
													$row_website_tb['truewallet'], 
													'System', 
													date("Y-m-d H:i:s"),
													date("Y-m-d"),
													date('H:i:s'),
													'2', 
													'1', 
													0, 
													0, 
													"tmw",
													date("Y-m-d"),
													date('H:i:s')
												]);
												dd_return(false, $msg_alert);
											}
									}
									else
									{
										dd_return(false, "ลิงก์ไม่ถูกต้องหรือเคยใช้งานไปแล้ว");
									}
								}
								else
								{
									dd_return(false, "ไม่พบข้อมูลเบอร์ Wallet");
								}
							}
							else
							{
								dd_return(false, "พบข้อมูลซองของขวัญแต่จำนวนผู้รับซองไม่ใช่จำนวน 1 คน, กรุณาสร้างซองของขวัญใหม่");
							}
						}
						else
						{
							if ($verify['status']['code'] == "VOUCHER_OUT_OF_STOCK")
							{
								dd_return(false, "ซองของขวัญนี้ถูกใช้งานไปแล้ว");
							}
							else
							{
								dd_return(false, "ไม่พบข้อมูลซองของขวัญ, กรุณาตรวจสอบใหม่อีกครั้ง");
							}
						}
					}
					else
					{
						dd_return(false, "ลิงก์นี้ถูกใช้งานแล้ว");
					}	
				}
				else
				{
					dd_return(false, "กรุณากรอกข้อมูลให้ครบทุกถ้วน");
				}
			}
			else
			{
				write_log("topup", $row_u['u_username']. " - Login ซ้อน", $ip);
				dd_return(false, "พบการกระทำต้องสงสัย กรุณาเข้าสู่ระบบใหม่อีกครั้ง");
			}
		}
		else
		{
			dd_return(false, "ไม่พบข้อมูลผู้ใช้งานในระบบ");
		}
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
