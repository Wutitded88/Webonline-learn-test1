<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'Service/amb.php';
$api = new AMBAPI();

function dd_return($status, $message, $total = "", $bonus = "")
{
    $json = ['message' => $message];
    if ($status)
    {
        http_response_code(200);
        if (isset($total) && $total != "") $json['total'] = $total;
        if (isset($bonus) && $bonus != "") $json['bonus'] = $bonus;
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
		$username = get_session();
		$ip = trim($_POST['ip']);
		if($username != "" && $ip != "")
		{
			$q_w = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? AND (w_status = ? OR w_status = ?)', [$username, "0", "99"]);
			if ($q_w->rowCount() == 0)
			{
				$q_b = dd_q('SELECT * FROM checkin_tb WHERE c_day = ? AND c_status = ? AND c_reward > ?', [date('j'), '1', 0]);
				if ($q_b->rowCount() > 0)
				{
					$q_checkin_reward_tb = dd_q('SELECT * FROM checkin_reward_tb WHERE c_user = ? AND c_reward_day = ? AND c_reward_month = ? AND c_reward_year = ?',[
                      get_session(),
                      date('j'),
                      date('m'),
                      date('Y')
                    ]);
                    if ($q_checkin_reward_tb->rowCount() == 0)
                    {
						$q_u = dd_q('SELECT * FROM user_tb WHERE u_user = ? AND u_block_agent = 0', [$username]);
						if ($q_u->rowCount() > 0)
						{
							$sum_topupdeposit = 0;
							$q_topupdeposit = dd_q('SELECT SUM(t_amount) AS s_topup FROM topup_db WHERE t_user = ? AND t_date_create = ? AND t_status = ?', [
								$username, 
								date('Y-m-d'),
								'1'
							]);
							if($q_topupdeposit->rowCount() > 0)
							{	
								$row_topupdeposit = $q_topupdeposit->fetch(PDO::FETCH_ASSOC);
								if(!is_null($row_topupdeposit['s_topup']))
								{
									$sum_topupdeposit = $row_topupdeposit['s_topup'];
								}
							}

							$row_b = $q_b->fetch(PDO::FETCH_ASSOC);
							$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

							$sum_topup = 0;
							$q_topup = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_date_create = ? AND t_status = ?', [
								$username, 
								date('Y-m-d'),
								'1'
							]);
							if($q_topup->rowCount() > 0)
							{	
								while($row_topup_tb = $q_topup->fetch(PDO::FETCH_ASSOC))
								{
									if($row_topup_tb['t_transaction_id'] != "")
									{
										$winloss = $api->GetWinLose($row_u['u_agent_id'], $row_topup_tb['t_transaction_id']);
										if ($winloss->success == true)
										{
											$winloss = json_decode(json_encode($winloss->data), true);
											foreach ($winloss["data"] as $val)
											{
												$sum_topup = $sum_topup + ($val['wlTurnAmount']);
											}
										}
									}
								}
							}
							$tt_turn = $row_b['c_target_topup'] * $row_b['c_turnover1'];
							if($sum_topupdeposit >= $row_b['c_target_topup'])
							{
								if($sum_topup >= $tt_turn)
								{
									if(!empty($row_u['u_agent_id']))
									{
										$row_tr = null;
										$q_tr = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? ORDER BY t_id DESC LIMIT 1', [$username]);
										if ($q_tr->rowCount() > 0)
										{
											$row_tr = $q_tr->fetch(PDO::FETCH_ASSOC);
										}
										$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
										if ($_GetUserCredit->success == true)
										{
											$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);

											if($_GetUserCredit['credit'] <= 5)
											{
												$isInsert = true;
												if($row_tr != null)
												{
													if($row_tr['t_active'] == "Y")
													{
														$isInsert = false;
													}
												}

												if($isInsert)
												{
													$_reward = $row_b["c_reward"]; //จำนวนโบนัส
													$bonus = $_reward; //คำนวนโบนัส เครดิต
													$total = $bonus; //คำนวนยอดที่จะได้รับ
													$_turnover = $row_b["c_turnover"]; //ยอดเทิร์นที่ตั้งค่าไว้
													$summary = $_reward * $_turnover; //คำนวนยอดเทิร์น

													$ExternalTransactionId = uniqid();
													$deposit = $api->transferCreditTo($row_u['u_agent_id'], $bonus);
													if ($deposit->success == true)
													{
														$t_u_id = $row_u['u_id'];
														$t_user = $username;
														$t_agent_id = $row_u['u_agent_id'];
														$t_fname = $row_u['u_fname']." ".$row_u['u_lname'];
														$t_amount = $_GetUserCredit['credit'];
														$t_promotion_id = $row_b['c_id'];
														$t_promotion_title = "เช็คอินวันที่ ".date('j');
														$t_promotion_turntype = "c";
														$t_bonus = $bonus;
														$t_total = $total;
														$t_turnover = $summary;
														if($row_b['c_withdraw_max'] > 0)
														{
															$t_withdraw_max = $row_b['c_withdraw_max'];
														}
														else
														{
															$t_withdraw_max = 0;
														}
														$t_after_credit = $_GetUserCredit['credit'];
														$t_after_wallet = $_GetUserCredit['credit'];
														$t_create_date = date('Y-m-d H:i:s');
														$t_ip = $ip;

														$q_in_transfer = dd_q('INSERT INTO transfergame_tb (t_u_id, t_user, t_agent_id, t_fname, t_amount, t_promotion_id, t_promotion_title, t_promotion_turntype, t_bonus, t_total, t_turnover, t_withdraw_max, t_after_credit, t_after_wallet, t_create_date, t_date_create, t_time_create, t_ip, t_transaction_id, t_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
															$t_u_id,
															$t_user, 
															$t_agent_id, 
															$t_fname, 
															$t_amount, 
															$t_promotion_id, 
															$t_promotion_title, 
															$t_promotion_turntype, 
															$t_bonus, 
															$t_total, 
															$t_turnover, 
															$t_withdraw_max, 
															$t_after_credit, 
															$t_after_wallet, 
															$t_create_date,
															date('Y-m-d'),
															date('H:i:s'),
															$t_ip,
															$ExternalTransactionId,
															"Y"
														]);
														if ($q_in_transfer == true)
														{
															dd_q('INSERT INTO checkin_reward_tb (c_user, c_reward_day, c_reward_month, c_reward_year, c_action_datetime, c_action_date, c_action_time) VALUES (?, ?, ?, ?, ?, ?, ?)', [
																$row_u['u_user'],
																date('j'),
																date('m'),
																date('Y'),
																date('Y-m-d H:i:s'),
																date('Y-m-d'),
																date('H:i:s')
															]);
															write_log("checkin", $username." - ทำรายการสำเร็จ (".$ExternalTransactionId.")", $ip);
															dd_return(true, "ทำรายการสำเร็จ");
														}
													}
													else
													{
														write_log("checkin", $username." - ".$deposit->message, $ip);
														dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
													}
												}
												else
												{
													dd_return(false, "ขณะนี้คุณใช้สิทธิ์โบนัส '".$row_tr['t_promotion_title']."' กรุณาเคลียยอด เพื่อรับโบนัสใหม่");
												}
											}
											else
											{
												write_log("freecredit", $username." - เครดิตคงเหลือมากกว่า 5 ไม่สามารถทำรายการได้", $ip);
												dd_return(false, "เครดิตคงเหลือมากกว่า 5 ไม่สามารถทำรายการได้");
											}
										}
										else
										{
											write_log("checkin", $username." - ".$_GetUserCredit->success, $ip);
											dd_return(false, "ไม่สามารถดึงข้อมูลเครดิตได้ กรุณาลองใหม่อีกครั้ง");
										}
									}
									else
									{
										dd_return(false, "รหัสเข้าเกมยังไม่ถูกสร้าง กรุณาติดต่อแอดมิน");
									}
								}
								else
								{
									dd_return(false, "ต้องมียอดเล่นจริงมากกว่า ".$tt_turn." บาท ถึงสามารถรับรางวัลได้");
								}
							}
							else
							{
								dd_return(false, "คุณต้องมียอดเติมเงินวันนี้ให้ครบ ".$row_b['c_target_topup']." บาท ถึงสามารถรับรางวัลได้");
							}
						}
						else
						{
							dd_return(false, "ไม่พบข้อมูลผู้ใช้งานในระบบ");
						}
					}
                    else
                    {
                    	dd_return(false, "คุณรับรางวัลของวันนี้ไปแล้ว");
                    }
				}
				else
				{
					dd_return(false, "ไม่พบข้อมูลรางวัลเช็คอิน กรุณาลองอีกครั้ง");
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
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
