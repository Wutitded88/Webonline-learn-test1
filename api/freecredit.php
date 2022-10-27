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
	$username = get_session();
	$code = trim($_POST['code']);
	$ip = trim($_POST['ip']);

	if($username != "" AND $code != "" AND $ip != "")
	{
		$q_free = dd_q('SELECT * FROM freecredit_tb WHERE (f_user = ?)', [$username]);
		if ($q_free->rowCount() == 0)
		{
			$q_u = dd_q('SELECT * FROM user_tb WHERE (u_user = ? AND u_block_agent = 0)', [$username]);
			if ($q_u->rowCount() >= 1)
			{
				$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

				$q_gen = dd_q('SELECT * FROM gencode_tb WHERE (g_phone = ? AND g_code = ? AND g_use = ?)', [
					$row_u['u_phone'],
					$code,
					"0"
				]);
				if ($q_gen->rowCount() > 0)
				{
					if(!empty($row_u['u_agent_id']))
					{
						$q_b = dd_q('SELECT * FROM promotion_tb WHERE (p_transfer_type = ?)', ['4']);
						if ($q_b->rowCount() > 0)
						{
							$row_b = $q_b->fetch(PDO::FETCH_ASSOC);

							$credit = 0;
							$_reward = (int)$row_b["p_reward"]; //จำนวนโบนัส
							if($row_b["p_type"] == "p") //เปอร์เซ็น
							{
								$bonus = ($credit * $_reward) / 100; //คำนวนโบนัส %
							}
							else //เครดิต
							{
								$bonus = $_reward; //คำนวนโบนัส เครดิต
							}
							if((int)$row_b["p_reward_max"] > 0)
							{
								if($bonus > $row_b["p_reward_max"])
								{
									$bonus = (int)$row_b["p_reward_max"];
								}
							}
							$total = $credit + $bonus; //คำนวนยอดที่จะได้รับ
							$_turnover = (int)$row_b["p_turnover"]; //ยอดเทิร์นที่ตั้งค่าไว้
							$summary = $total * $_turnover; //คำนวนยอดเทิร์น

							$data = $api->getUserCredit($row_u['u_agent_id']);
							if ($data->success == true)
							{
								$data = json_decode(json_encode($data->data), true);
								if($data['credit'] < 5)
								{
									$ExternalTransactionId = uniqid();
									$deposit = $api->transferCreditTo($row_u['u_agent_id'], $row_b['p_reward']);
									if ($deposit->success == true)
									{
										$t_u_id = $row_u['u_id'];
										$t_user = $username;
										$t_agent_id = $row_u['u_agent_id'];
										$t_fname = $row_u['u_fname']." ".$row_u['u_lname'];
										$t_amount = 0;
										$t_promotion_id = $row_b['p_id'];
										$t_promotion_title = $row_b['p_title'];
										$t_bonus = $bonus;
										$t_total = $total;
										$t_turnover = $summary;
										$t_withdraw_max = $row_b['p_withdraw_max'];

										$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
										if ($_GetUserCredit->success == true)
										{
											$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
											$t_after_credit = $_GetUserCredit['credit'];
										}
										else
										{
											$t_after_credit = 0;
										}

										$t_after_wallet = $row_u['u_wallet'];
										$t_create_date = date("Y-m-d H:i:s");
										$t_ip = $ip;
										$q_in_transfer = dd_q('INSERT INTO transfergame_tb (t_u_id, t_user, t_agent_id, t_fname, t_amount, t_promotion_id, t_promotion_title, t_bonus, t_total, t_turnover, t_withdraw_max, t_after_credit, t_after_wallet, t_create_date, t_date_create, t_time_create, t_ip, t_transaction_id, t_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
											$t_u_id,
											$t_user, 
											$t_agent_id, 
											$t_fname, 
											$t_amount, 
											$t_promotion_id, 
											$t_promotion_title, 
											$t_bonus, 
											$t_total, 
											$t_turnover, 
											$t_withdraw_max, 
											$t_after_credit, 
											$t_after_wallet, 
											$t_create_date,
											date("Y-m-d"),
											date("H:i:s"),
											$t_ip,
											$ExternalTransactionId,
											"Y"
										]);
										if ($q_in_transfer == true)
										{
											$q_up_gen = dd_q('UPDATE gencode_tb SET g_use=? WHERE g_code=?', ["1", $code]);
											if($q_up_gen == true)
											{
												$q_in_freecredit = dd_q('INSERT INTO freecredit_tb (f_u_id, f_user, f_fname, f_code, f_create_date, f_date_create, f_time_create, f_ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
													$row_u['u_id'], 
													$username, 
													$t_fname, 
													$code, 
													date("Y-m-d H:i:s"), 
													date("Y-m-d"),
													date("H:i:s"),
													$ip
												]);
												if($q_in_freecredit == true)
												{
													if($row_u['u_vip'] != "Vip")
													{
														dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', ["Free", $row_u['u_id']]);
													}
													write_log("freecredit", $username." - ทำรายการสำเร็จ", $ip);
													dd_return(true, "ทำรายการสำเร็จ");
												}
												else
												{
													write_log("freecredit", $username." - เพิ่มข้อมูลรายการ freecredit ไม่สำเร็จ", $ip);
													dd_return(false, "เพิ่มข้อมูลรายการ freecredit ไม่สำเร็จ");
												}
											}
											else
											{
												write_log("freecredit", $username." - อัพเดทข้อมูลรายการ gencode ไม่สำเร็จ", $ip);
												dd_return(false, "อัพเดทข้อมูลรายการ gencode ไม่สำเร็จ");
											}
										}
										else
										{
											write_log("freecredit", $username." - เพิ่มข้อมูลรายการ transfer ไม่สำเร็จ", $ip);
											dd_return(false, "เพิ่มข้อมูลรายการ transfer ไม่สำเร็จ");
										}
									}
									else
									{
										write_log("freecredit", $username." - ".$deposit->message, $ip);
										dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
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
								write_log("freecredit", $username." - ".$data->message, $ip);
								dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
							}
						}
						else
						{
							write_log("freecredit", $username." - ไม่พบข้อมูลโบนัส กรุณาลองอีกครั้ง", $ip);
							dd_return(false, "ไม่พบข้อมูลโบนัส กรุณาลองอีกครั้ง");
						}
					}
					else
					{
						write_log("freecredit", $username." - รหัสเข้าเกมยังไม่ถูกสร้าง กรุณาติดต่อแอดมิน", $ip);
						dd_return(false, "รหัสเข้าเกมยังไม่ถูกสร้าง กรุณาติดต่อแอดมิน");
					}
				}
				else
				{
					write_log("freecredit", $username." - ไม่พบข้อมูล Credit Code ในระบบ", $ip);
					dd_return(false, "ไม่พบข้อมูล Credit Code ในระบบ");
				}
			}
			else
			{
				write_log("freecredit", $username." - ไม่พบข้อมูลผู้ใช้งานในระบบ", $ip);
				dd_return(false, "ไม่พบข้อมูลผู้ใช้งานในระบบ");
			}
		}
		else
		{
			write_log("freecredit", $username." รับสิทธิ์ซ้ำ (".$code.")", $ip);
			dd_return(false, "คุณรับสิทธิ์ไปแล้ว");
		}
	}
	else
	{
		if($code == "")
		{
			dd_return(false, "กรุณาใส่ โค้ดเครดิตฟรี");
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
