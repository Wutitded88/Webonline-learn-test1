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
	$id = trim($_POST['id']);
	$bonus = trim($_POST['id']);
	$ip = trim($_POST['ip']);
	$username = get_session();
	if($username != "" AND $ip != "" AND $id != "")
	{
		$q_w = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? AND (w_status = ? OR w_status = ?)', [$username, "0", "99"]);
		if ($q_w->rowCount() == 0)
		{
			$q_b = dd_q('SELECT * FROM promotion_fixed_deposit_tb WHERE p_id = ?', [$id]);
			if ($q_b->rowCount() > 0)
			{
				$q_u = dd_q('SELECT * FROM user_tb WHERE (u_user = ? AND u_block_agent = 0)', [$username]);
				if ($q_u->rowCount() > 0)
				{
					$row_b = $q_b->fetch(PDO::FETCH_ASSOC);
					$row_u = $q_u->fetch(PDO::FETCH_ASSOC);
					if(!empty($row_u['u_agent_id']))
					{
						$isAllow = false;
						$sum_topup = 0;
						for ($i=0; $i < $row_b['p_deposit_day']; $i++)
						{
							$q_f = dd_q('SELECT f_id FROM fixed_deposit_receive_tb WHERE f_action_date = ? AND f_u_user = ?', [
								date('Y-m-d', strtotime(date('Y-m-d'). ' - '.$i.' days')), 
								$username
							]);
							if ($q_f->rowCount() == 0)
							{
								$q_topup = dd_q('SELECT * FROM topup_db WHERE t_date_create = ? AND t_user = ? AND t_status = ?', [
									date('Y-m-d', strtotime(date('Y-m-d'). ' - '.$i.' days')), 
									$username,
									"1"
								]);
								if ($q_topup->rowCount() > 0)
								{
									$t_amount = 0;
									while($row_topup_tb = $q_topup->fetch(PDO::FETCH_ASSOC))
									{
										$t_amount = $t_amount + $row_topup_tb['t_amount'];

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

									if($t_amount >= $row_b["p_transfer_min"])
									{
										$isAllow = true;
									}
									else
									{
										$sum_topup = 0;
										$isAllow = false;
									}
								}
							}
							else
							{
								$sum_topup = 0;
								$isAllow = false;
							}
							if(!$isAllow)
							{
								break;
							}
						}
						$tt_turn = $row_b['p_transfer_min'] * $row_b['p_turnover1'];

						if($isAllow && $sum_topup >= $tt_turn)
						{
							$row_tr = null;
							$q_tr = dd_q('SELECT * FROM transfergame_tb WHERE (t_user = ?) ORDER BY t_id DESC LIMIT 1', [$username]);
							if ($q_tr->rowCount() > 0)
							{
								$row_tr = $q_tr->fetch(PDO::FETCH_ASSOC);
							}
							$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
							if ($_GetUserCredit->success == true)
							{
								$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
								if($_GetUserCredit['credit'] < 5)
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
										$bonus = $row_b["p_reward"]; //คำนวนโบนัส เครดิต
										$credit = 0;
										$total = $credit + $bonus; //คำนวนยอดที่จะได้รับ
										$_turnover = (int)$row_b["p_turnover"]; //ยอดเทิร์นที่ตั้งค่าไว้

										if($row_b["p_turnover_type"] == "c") //เท่า
										{
											$summary = $row_b["p_transfer_min"] * $_turnover; //คำนวนยอดเทิร์น
										}
										else if($row_b["p_turnover_type"] == "p") //เปอร์เซ็น
										{
											$summary = $row_b["p_transfer_min"] + $row_b["p_transfer_min"] * ($_turnover / 100); //คำนวนยอดเทิร์น
										}
										else if($row_b["p_turnover_type"] == "w") //winloss
										{
											$summary = $row_b["p_transfer_min"] * $_turnover; //คำนวนยอดเทิร์น
										}

										$ExternalTransactionId = uniqid();
										$deposit = $api->transferCreditTo($row_u['u_agent_id'], $bonus);
										if ($deposit->success == true)
										{
											$t_u_id = $row_u['u_id'];
											$t_user = $username;
											$t_agent_id = $row_u['u_agent_id'];
											$t_fname = $row_u['u_fname']." ".$row_u['u_lname'];
											$t_amount = $_GetUserCredit['credit'];
											$t_promotion_id = $row_b['p_id'];
											$t_promotion_title = $row_b['p_title'];
											$t_promotion_turntype = "w";
											$t_bonus = $bonus;
											$t_total = $total;
											$t_turnover = $summary;
											if($row_b['p_withdraw_max'] > 0)
											{
												$t_withdraw_max = $row_b['p_withdraw_max'];
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
												dd_q('INSERT INTO fixed_deposit_receive_tb (f_u_user, f_amount, p_id, p_name, f_action_date, f_action_time) VALUES (?, ?, ?, ?, ?, ?)', [
													$t_user, 
													$total, 
													$t_promotion_id, 
													$t_promotion_title, 
													date('Y-m-d'),
													date('H:i:s')
												]);

												write_log("fixed_deposit", $username." - ทำรายการสำเร็จ (".$ExternalTransactionId.")", $ip);
												dd_return(true, "ทำรายการสำเร็จ");
											}
										}
										else
										{
											write_log("fixed_deposit", $username." - ".$deposit->message, $ip);
											dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
										}
									}
									else
									{
										dd_return(false, "ขณะนี้คุณใช้สิทธิ์โบนัส '".$row_tr['t_promotion_title']."' กรุณาเคลียยอดเทิร์น เพื่อรับโบนัสใหม่");
									}
								}
								else
								{
									dd_return(false, "เครดิตคงเหลือมากกว่า 5 ไม่สามารถทำรายการได้");
								}
							}
							else
							{
								write_log("fixed_deposit", $username." - ".$_GetUserCredit->success, $ip);
								dd_return(false, "ไม่สามารถดึงข้อมูลเครดิตได้ กรุณาลองใหม่อีกครั้ง");
							}
						}
						else
						{
							if(!$isAllow)
							{
								dd_return(false, "ไม่สามารภรับเครดิตได้ คุณยังเติมเงินไม่ครบตามกำหนด");
							}
							else
							{
								dd_return(false, "ไม่สามารถรับเครดิตได้ ต้องมียอดเล่นจริงมากกว่า ".$tt_turn." บาท");
							}
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
				dd_return(false, "ไม่พบข้อมูลโบนัส กรุณาลองอีกครั้ง");
			}
		}
		else
		{
			dd_return(false, "คุณมีรายการถอนค้างอยู่ กรุณารอแอดมินดำเนินการ");
		}		
	}
	else
	{
		if($id == "")
		{
			dd_return(false, "กรุณา เลือกโปรโมชั่น");
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
