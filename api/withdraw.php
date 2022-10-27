<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'Service/amb.php';
$api = new AMBAPI();

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
	$username = trim($_POST['username']);
	$credit = (int)trim(str_replace(",","",$_POST['credit']));
	$ip = trim($_POST['ip']);

	if($username != "" AND $ip != "")
	{
		$q_w = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? AND (w_status = ? OR w_status = ?)', [$username, "0", "99"]);
		if ($q_w->rowCount() == 0)
		{
			$q_1 = dd_q('SELECT * FROM user_tb WHERE (u_user = ? AND u_block_agent = 0)', [$username]);
			if ($q_1->rowCount() >= 1)
			{
				$row = $q_1->fetch(PDO::FETCH_ASSOC);
				$row_t = null;
				if(!empty($row['u_agent_id']))
				{
					$last_promotion = "";
					$wd_max = false; //ถอนทั้งหมด
					$wd_tover = false; //เช็คยอดเทิร์น
					$q_t = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? ORDER BY t_id DESC LIMIT 1', [$username]);
					if ($q_t->rowCount() > 0)
					{
						$row_t = $q_t->fetch(PDO::FETCH_ASSOC);
						if($row_t["t_active"] == "Y")
						{
							$last_promotion = $row_t["t_id"];
							// if($row_t["t_promotion_id"] == "3" && strpos($row_t["t_promotion_title"], 'ฟรีเครดิต') !== false)
							// {
								$_GetUserCredit = $api->getUserCredit($row['u_agent_id']);
								if ($_GetUserCredit->success == true)
								{
									$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
									$credit = str_replace(",","",number_format(floor(str_replace(",", "", $_GetUserCredit['credit'])), 2));
								}
								else
								{
									dd_return(false, "ไม่สามารถดึงยอดเครดิตได้ กรุณาติดต่อแอดมิน");
								}
								$wd_max = true;
							// }
							$wd_tover = true;
						}
					}

					$q_topup_db = dd_q('SELECT SUM(w_amount) AS total FROM withdraw_tb WHERE w_date_create = ? AND w_user = ?', [date("Y-m-d"),$username])->fetchColumn();
					$q_4 = dd_q('SELECT * FROM user_tb WHERE (u_user = ?)', [$username]);
					$row_l = $q_4->fetch(PDO::FETCH_ASSOC);
					$limitcredit = $credit + $q_topup_db;
					$creditlimit = (int)trim(str_replace(",","",$row_l['u_limitcredit']));
					if($creditlimit >= $limitcredit)
					{
						if($credit >= $_CONFIG['min_withdraw'])
						{
							$data = $api->getUserCredit($row['u_agent_id']);
							if ($data->success == true)
							{
								$data = json_decode(json_encode($data->data), true);
								if(floor($data['credit']) >= $credit)
								{
									if($wd_tover == true) //ถอนแบบเช็คโปรที่รับ
									{
										$t_turnover = $row_t['t_turnover'];
										$_winloss = 0;
										$_outstanding = 0;
										if($row_t['t_promotion_turntype'] == "w")
										{
											// เช็ค t_transaction_id จากการรับโปร
											$winloss_pro = $api->GetWinLose($row['u_agent_id'], $row_t['t_transaction_id']);
											if ($winloss_pro->success == true)
											{
												$winloss_pro = json_decode(json_encode($winloss_pro->data), true);

												$q_turnover_tb = dd_q('SELECT * FROM turnover_tb');
												while($row_turnover_tb = $q_turnover_tb->fetch(PDO::FETCH_ASSOC))
												{
													foreach ($winloss_pro["data"] as $val)
													{
														if(array_key_exists('game', $val) && $val['game'] == $row_turnover_tb['t_code'])
														{
															if($row_turnover_tb['t_active'] == "1")
															{
																$_winloss = $_winloss + ($val['wlTurnAmount']);
															}
															$_outstanding = $_outstanding + ($val['outstanding']);
														}
													}
												}
											}
											// เช็ค t_transaction_id จากการรับโปร


											// เช็ค t_transaction_id จากการเติมเงินล่าสุด
											$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_status = ? ORDER BY t_id DESC LIMIT 1', [
												$username,
												'1'
											]);
											if ($q_topup_tb->rowCount() > 0)
											{
												while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
												{
													if($row_topup_tb['t_transaction_id'] != "")
													{
														$winloss = $api->GetWinLose($row['u_agent_id'], $row_topup_tb['t_transaction_id']);
														if ($winloss->success == true)
														{
															$winloss = json_decode(json_encode($winloss->data), true);

															$q_turnover_tb = dd_q('SELECT * FROM turnover_tb');
															while($row_turnover_tb = $q_turnover_tb->fetch(PDO::FETCH_ASSOC))
															{
																foreach ($winloss["data"] as $val)
																{
																	if(array_key_exists('game', $val) && $val['game'] == $row_turnover_tb['t_code'])
																	{
																		if($row_turnover_tb['t_active'] == "1")
																		{
																			$_winloss = $_winloss + ($val['wlTurnAmount']);
																		}
																		$_outstanding = $_outstanding + ($val['outstanding']);
																	}
																}
															}
														}
													}
												}
											}
											// เช็ค t_transaction_id จากการเติมเงินล่าสุด
										}
										else
										{
											// เช็ค t_transaction_id จากการรับโปร
											$winloss_pro = $api->GetWinLose($row['u_agent_id'], $row_t['t_transaction_id']);
											if ($winloss_pro->success == true)
											{
												$winloss_pro = json_decode(json_encode($winloss_pro->data), true);
												foreach ($winloss_pro["data"] as $val)
												{
													$_winloss = $_winloss + ($val['wlTurnAmount']);
													$_outstanding = $_outstanding + ($val['outstanding']);
												}
											}
											// เช็ค t_transaction_id จากการรับโปร


											// เช็ค t_transaction_id จากการเติมเงินล่าสุด
											$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_status = ? ORDER BY t_id DESC LIMIT 1', [
												$username,
												'1'
											]);
											if ($q_topup_tb->rowCount() > 0)
											{
												while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
												{
													if($row_topup_tb['t_transaction_id'] != "")
													{
														$winloss = $api->GetWinLose($row['u_agent_id'], $row_topup_tb['t_transaction_id']);
														if ($winloss->success == true)
														{
															$winloss = json_decode(json_encode($winloss->data), true);
															foreach ($winloss["data"] as $val)
															{
																$_winloss = $_winloss + ($val['wlTurnAmount']);
																$_outstanding = $_outstanding + ($val['outstanding']);
															}
														}
													}
												}
											}
											// เช็ค t_transaction_id จากการเติมเงินล่าสุด
										}

										$isComplete = false;
										if($row_t['t_promotion_turntype'] != "w" && floor($data['credit']) >= $t_turnover && $_outstanding <= 0)
										{
											$isComplete = true;
										}
										else if($row_t['t_promotion_turntype'] == "w" && $_winloss >= $t_turnover && $_outstanding <= 0)
										{
											$isComplete = true;
										}
										else
										{
											if($_outstanding > 20)
											{
												dd_return(false, "ไม่สามารถถอนเงินได้ มียอดค้างอยู่ในเกมไม่สามารถถอนได้ จำนวน ".$_outstanding." กรุณารอสักครู่ หรือติดต่อแอดมิน");
											}
											else
											{
												if($row_t['t_promotion_turntype'] != "w")
												{
													dd_return(false, "ไม่สามารถถอนเงินได้ ยอดเทิร์นเครดิตของคุณตอนนี้คือ ".number_format(floor($data['credit']), 2, '.', ',')." ต้องทำให้ถึง ".number_format($t_turnover, 2, '.', ','));
												}
												else
												{
													dd_return(false, "ไม่สามารถถอนเงินได้ ยอดเทิร์น winloss ของคุณตอนนี้คือ ".number_format($_winloss, 2, '.', ',')." ต้องทำให้ถึง ".number_format($t_turnover, 2, '.', ','));
												}
											}
										}

										if($isComplete)
										{
											// if($_winloss >= $credit)
											// {
												$withdraw = $api->TransferCreditOut($row['u_agent_id'], str_replace(",", "", $credit));
												if ($withdraw->success == true)
												{
													$withdraw = json_decode(json_encode($withdraw->data), true);
													$ExternalTransactionId = $withdraw['ref'];
													$w_amount = floor($credit);
													if($row_t['t_withdraw_max'] > 0)
													{
														if(floor($credit) >= $row_t['t_withdraw_max'])
														{
															$w_amount = $row_t['t_withdraw_max'];
														}
													}
													$q_2= dd_q('INSERT INTO withdraw_tb (w_u_id, w_user, w_agent_id, w_fname, w_amount, w_credit, w_bank_name, w_bank_number, w_action_by, w_create_date, w_date_create, w_time_create, w_status, w_type, w_msg, w_ip, w_transaction_id, w_transfer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
														$row['u_id'],
														$row['u_user'],
														$row['u_agent_id'],
														$row['u_fname']." ".$row['u_lname'],
														$w_amount,
														floor($credit),
														$row['u_bank_name'],
														$row['u_bank_number'],
														"",
														date("Y-m-d H:i:s"),
														date("Y-m-d"),
														date("H:i:s"),
														"0",
														"1",
														"",
														$ip,
														$ExternalTransactionId,
														$last_promotion
													]);
													if ($q_2 == true)
													{
														$q_3 = dd_q('UPDATE transfergame_tb SET t_active=? WHERE t_id=?', [
															"N",
															$row_t['t_id']
														]);
														write_log("withdraw", "ยื่นเรื่องถอนเงินสำเร็จ ทีมงานกำลังตรวจสอบ กรุณารอสักครู่...", $ip);
														$resLine = notify_message("User ".$row['u_user']." (".$_CONFIG['start_prefix'].$row['u_agent_id'].") แจ้งถอนเงิน จำนวน ".$w_amount." บาท (เครดิตที่หัก ".number_format(floor($credit))." เครดิต) เวลา ".date("d/m/Y H:i:s")." กรุณาตรวจสอบรายการ => รหัสสำหรับโอนเงินแบบ Auto: ".$ExternalTransactionId);
														dd_return(true, "ยื่นเรื่องถอนเงินสำเร็จ ทีมงานกำลังตรวจสอบ กรุณารอสักครู่...");
													}
													else
													{
														dd_return(false, "ไม่สามารถเพิ่มข้อมูลรายการถอนเงินได้ กรุณาติดต่อแอดมิน");
													}
												}
												else
												{
													write_log("withdraw", $username." - ".$withdraw->message, $ip);
													dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
												}
											// }
											// else
											// {
											// 	dd_return(false, "ไม่สามารถทำรายการได้ เนื่องจากยอดเล่นจริงโดยคิดจากรายการฝากล่าสุดของคุณคือ ".$_winloss." กรุณาทำยอดให้มากกว่าจำนวนเงินที่ต้องการถอน");
											// }
										}
									}
									else //ถอนแบบไม่เช็คโปร 
									{
										$_outstanding = 0;
										// เช็ค t_transaction_id จากการเติมเงิน
										$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_status = ? ORDER BY t_id DESC LIMIT 3', [
											$username,
											'1'
										]);
										if ($q_topup_tb->rowCount() > 0)
										{
											while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
											{
												if($row_topup_tb['t_transaction_id'] != "")
												{
													$winloss = $api->GetWinLose($row['u_agent_id'], $row_topup_tb['t_transaction_id']);
													if ($winloss->success == true)
													{
														$winloss = json_decode(json_encode($winloss->data), true);
														foreach ($winloss["data"] as $val)
														{
															$_outstanding = $_outstanding + ($val['outstanding']);
														}
													}
												}
											}
										}
										// เช็ค t_transaction_id จากการเติมเงิน

										if($_outstanding > 1000)
										{
											dd_return(false, "ไม่สามารถถอนเงินได้ มียอดค้างอยู่ในเกมไม่สามารถถอนได้ จำนวน ".$_outstanding." กรุณารอสักครู่ หรือติดต่อแอดมิน");
										}
										else
										{
											$withdraw = $api->TransferCreditOut($row['u_agent_id'], str_replace(",", "", $credit));
											if ($withdraw->success == true)
											{
												$withdraw = json_decode(json_encode($withdraw->data), true);
												$ExternalTransactionId = $withdraw['ref'];
												$w_amount = floor($credit);
												$q_2= dd_q('INSERT INTO withdraw_tb (w_u_id, w_user, w_agent_id, w_fname, w_amount, w_credit, w_bank_name, w_bank_number, w_action_by, w_create_date, w_date_create, w_time_create, w_status, w_type, w_msg, w_ip, w_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
													$row['u_id'],
													$row['u_user'],
													$row['u_agent_id'],
													$row['u_fname']." ".$row['u_lname'],
													$w_amount,
													floor($credit),
													$row['u_bank_name'],
													$row['u_bank_number'],
													"",
													date("Y-m-d H:i:s"),
													date("Y-m-d"),
													date("H:i:s"),
													"0",
													"1",
													"",
													$ip,
													$ExternalTransactionId
												]);
												if ($q_2 == true)
												{
													write_log("withdraw", "ยื่นเรื่องถอนเงินสำเร็จ ทีมงานกำลังตรวจสอบ กรุณารอสักครู่...", $ip);
													$resLine = notify_message("User ".$row['u_user']." (".$_CONFIG['start_prefix'].$row['u_agent_id'].") แจ้งถอนเงิน จำนวน ".$w_amount." บาท (เครดิตที่หัก ".number_format(floor($credit))." เครดิต) เวลา ".date("d/m/Y H:i:s")." กรุณาตรวจสอบรายการ => รหัสสำหรับโอนเงินแบบ Auto: ".$ExternalTransactionId);
													dd_return(true, "ยื่นเรื่องถอนเงินสำเร็จ ทีมงานกำลังตรวจสอบ กรุณารอสักครู่...");
												}
												else
												{
													dd_return(false, "ไม่สามารถเพิ่มข้อมูลรายการถอนเงินได้ กรุณาติดต่อแอดมิน");
												}
											}
											else
											{
												write_log("withdraw", $username." - ".$withdraw->message, $ip);
												dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
											}
										}
									}
								}
								else
								{
									dd_return(false, "ยอดเงินคงเหลือไม่พอ ไม่สามารถทำรายการถอนได้");
								}
							}
							else
							{
								write_log("withdraw", $username." - ".$data->message, $ip);
								dd_return(false, "ไม่สามารถดึงยอดเครดิตได้ กรุณาติดต่อแอดมิน");
							}
						}
						else
						{
							dd_return(false, "ถอนเครดิตขั้นต่ำ ".$_CONFIG['min_withdraw']);
						}
					}
					else
					{
						dd_return(false, "ถอนได้สูงสุด ".$row_l['u_limitcredit']." บาท ถอนไปแล้ว ".$q_topup_db." บาท");
					}
				}
				else
				{
					dd_return(false, "รหัสเข้าเกมยังไม่ถูกสร้าง กรุณาติดต่อแอดมิน");
				}
			}
			else
			{
				dd_return(false, "ไม่พบข้อมูลผู้ใช้งานในระบบ");
			}
		}
		else
		{
			dd_return(false, "คุณมีรายการถอนค้างอยู่ กรุณารอแอดมินดำเนินการ");
		}
	}
	else
	{
		dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
