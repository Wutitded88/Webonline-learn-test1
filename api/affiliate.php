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
	$credit = str_replace("-", "", $_POST['credit']);
	$credit = number_format(str_replace(",", "", $credit), 2, '.', '');
	$ip = trim($_POST['ip']);
	$username = get_session();
	if($username != "" AND $ip != "" AND $credit != "")
	{
		$q_u = dd_q('SELECT * FROM user_tb WHERE (u_user = ? AND u_block_agent = 0)', [$username]);
		if ($q_u->rowCount() > 0)
		{
			$row_u = $q_u->fetch(PDO::FETCH_ASSOC);
			if($row_u['u_token'] == session_id())
			{
				$countAmount = 0;
				$q_percent_tb = dd_q('SELECT SUM(aff_amount) AS oAmount FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_status = ?', [
					$row_u['u_user'], 
					"0"
				]);
				if ($q_percent_tb->rowCount() > 0)
				{
					$row_percent_tb = $q_percent_tb->fetch(PDO::FETCH_ASSOC);
					$countAmount = $row_percent_tb["oAmount"];
				}

    			if($countAmount == $credit)
    			{
    				if(!empty($row_u['u_agent_id']))
    				{
    					$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
    					if ($_GetUserCredit->success == true)
    					{
    						$q_website_tb = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
    						$row_website = $q_website_tb->fetch(PDO::FETCH_ASSOC);

    						if($row_website['aff_promotion'] == "0")
    						{
    							$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
								if($_GetUserCredit['credit'] < 5)
	    						{
	    							dd_q('UPDATE transfergame_tb SET t_active = ? WHERE t_user = ?', [
	    								"N", 
	    								$row_u['u_user']
	    							]);
	    						}

	    						// จำกัดยอดสูงสุดต่อวัน
	    						$countAmount_max = 0;
	    						$q_percent_max = dd_q('SELECT SUM(aff_amount) AS oAmount FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_action_date = ? AND aff_status = ?', [
									$row_u['u_user'], 
									date('Y-m-d'),
									"1"
								]);
								if ($q_percent_max->rowCount() > 0)
								{
									$row_percent_max = $q_percent_max->fetch(PDO::FETCH_ASSOC);
									$countAmount_max = $row_percent_max["oAmount"];
								}

								if($row_website['aff_maxofday'] > $countAmount_max)
								{
									$total_credit = 0;
									$_countTotal = $row_website['aff_maxofday'] - $countAmount_max;
									if($_countTotal >= $countAmount)
									{
										$total_credit = $countAmount;
									}
									else
									{
										$total_credit = $_countTotal;
									}

									$ExternalTransactionId = uniqid();
		    						$deposit = $api->transferCreditTo($row_u['u_agent_id'], $total_credit);
		    						if ($deposit->success == true)
		    						{
		    							$aff_u_user = $username;
		    							$aff_amount = $total_credit;
		    							$t_create_date = date('Y-m-d H:i:s');
		    								
		    							$q_in_aff_receive = dd_q('INSERT INTO aff_receive (aff_u_user, aff_amount, aff_action) VALUES (?, ?, ?)', [
		    								$aff_u_user,
		    								$total_credit,
		    								date('Y-m-d H:i:s')
		    							]);
		    							if ($q_in_aff_receive == true)
		    							{
		    								write_log("aff", $aff_u_user." - รับเงินแนะนำเพื่อน จำนวน ".$total_credit." (".$ExternalTransactionId.")", $ip);
		    									
		    								$q_up_aff_percent_tb = dd_q('UPDATE aff_percent_tb SET aff_status = ?, aff_action = ?, aff_action_date = ? WHERE aff_u_user_ref = ?', [
		    									"1", 
		    									date('Y-m-d H:i:s'), 
		    									date('Y-m-d'), 
		    									$row_u['u_user']
		    								]);
		    								if ($q_up_aff_percent_tb == true)
		    								{
		    									dd_return(true, "ทำรายการสำเร็จ คุณได้โบนัสจำนวน ".$total_credit." วันนี้รับได้อีก ".($_countTotal - $total_credit));
		    								}
		    								else
		    								{
		    									dd_return(false, "ไม่สามารถอัพเดทข้อมูลได้ กรุณาติดต่อเจ้าหน้าที่");
		    								}
		    							}
		    							else
		    							{
		    								dd_return(false, "ไม่สามารถเพิ่มข้อมูลได้ กรุณาติดต่อเจ้าหน้าที่");
		    							}
		    						}
		    						else
		    						{
		    							write_log("aff", $username." - ".$deposit->message, $ip);
		    							dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
		    						}
								}
								else
								{
									dd_return(false, "วันนี้คุณรับโบนัสแนะนำเพื่อนครบ ".$row_website['aff_maxofday']." แล้ว");
								}
    						}
    						else
    						{
    							$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
								if($_GetUserCredit['credit'] < 5)
	    						{
	    							$q_b = dd_q('SELECT * FROM promotion_tb WHERE (p_transfer_type = ?)', ['5']);
	    							if ($q_b->rowCount() > 0)
	    							{
	    								$row_b = $q_b->fetch(PDO::FETCH_ASSOC);

										// จำกัดยอดสูงสุดต่อวัน
			    						$countAmount_max = 0;
			    						$q_percent_max = dd_q('SELECT SUM(aff_amount) AS oAmount FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_action_date = ? AND aff_status = ?', [
											$row_u['u_user'], 
											date('Y-m-d'),
											"1"
										]);
										if ($q_percent_max->rowCount() > 0)
										{
											$row_percent_max = $q_percent_max->fetch(PDO::FETCH_ASSOC);
											$countAmount_max = $row_percent_max["oAmount"];
										}

										if($row_website['aff_maxofday'] > $countAmount_max)
										{
											$total_credit = 0;
											$_countTotal = $row_website['aff_maxofday'] - $countAmount_max;
											if($_countTotal >= $countAmount)
											{
												$total_credit = $countAmount;
											}
											else
											{
												$total_credit = $_countTotal;
											}

											$ExternalTransactionId = uniqid();
				    						$deposit = $api->transferCreditTo($row_u['u_agent_id'], $total_credit);
				    						if ($deposit->success == true)
				    						{
				    							$aff_u_user = $username;
				    							$aff_amount = $total_credit;
				    							$q_in_aff_receive = dd_q('INSERT INTO aff_receive (aff_u_user, aff_amount, aff_action) VALUES (?, ?, ?)', [
				    								$aff_u_user,
				    								$total_credit,
				    								date('Y-m-d H:i:s')
				    							]);
				    							if ($q_in_aff_receive == true)
				    							{
				    								write_log("aff", $aff_u_user." - รับเงินแนะนำเพื่อน จำนวน ".$total_credit." (".$ExternalTransactionId.")", $ip);
				    									
				    								$q_up_aff_percent_tb = dd_q('UPDATE aff_percent_tb SET aff_status = ?, aff_action = ?, aff_action_date = ? WHERE aff_u_user_ref = ?', [
				    									"1", 
				    									date('Y-m-d H:i:s'), 
				    									date('Y-m-d'), 
				    									$row_u['u_user']
				    								]);
				    								if ($q_up_aff_percent_tb == true)
				    								{
				    									dd_q('UPDATE transfergame_tb SET t_active = ? WHERE t_user = ?', [
						    								"N", 
						    								$row_u['u_user']
						    							]);

						    							$credit = $total_credit;
														$bonus = 0;
														$total = $credit + $bonus; //คำนวนยอดที่จะได้รับ
														$_turnover = (int)$row_b["p_turnover"]; //ยอดเทิร์นที่ตั้งค่าไว้
														$summary = $total * $_turnover; //คำนวนยอดเทิร์น

														$t_u_id = $row_u['u_id'];
														$t_user = $username;
														$t_agent_id = $row_u['u_agent_id'];
														$t_fname = $row_u['u_fname']." ".$row_u['u_lname'];
														$t_amount = $_GetUserCredit['credit'];
														$t_promotion_id = $row_b['p_id'];
														$t_promotion_title = $row_b['p_title'];
														$t_promotion_turntype = $row_b['p_turnover_type'];
														$t_bonus = $bonus;
														$t_total = $total;
														$t_turnover = $summary;
														$t_withdraw_max = 0;
														$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
														if ($_GetUserCredit->success == true)
														{
															$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
															$t_after_credit = $_GetUserCredit['credit'];
															$t_after_wallet = $_GetUserCredit['credit'];
														}
														else
														{
															$t_after_credit = 0;
															$t_after_wallet = 0;
														}
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
															dd_return(true, "ทำรายการสำเร็จ คุณได้โบนัสจำนวน ".$total_credit." วันนี้รับได้อีก ".($_countTotal - $total_credit));
														}
														else
														{
															dd_return(false, "ไม่สามารถอัพเดทข้อมูลได้ กรุณาติดต่อเจ้าหน้าที่");
														}
				    								}
				    								else
				    								{
				    									dd_return(false, "ไม่สามารถอัพเดทข้อมูลได้ กรุณาติดต่อเจ้าหน้าที่");
				    								}
				    							}
				    							else
				    							{
				    								dd_return(false, "ไม่สามารถเพิ่มข้อมูลได้ กรุณาติดต่อเจ้าหน้าที่");
				    							}
				    						}
				    						else
				    						{
				    							write_log("aff", $username." - ".$deposit->message, $ip);
				    							dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
				    						}
										}
										else
										{
											dd_return(false, "วันนี้คุณรับโบนัสแนะนำเพื่อนครบ ".$row_website['aff_maxofday']." แล้ว");
										}
	    							}
	    							else
	    							{
	    								write_log("aff", $username." - ไม่พบข้อมูลโบนัส กรุณาลองอีกครั้ง", $ip);
	    								dd_return(false, "ไม่พบข้อมูลโบนัส กรุณาลองอีกครั้ง");
	    							}
	    						}
	    						else
	    						{
	    							write_log("aff", $username." - เครดิตคงเหลือมากกว่า 5 ไม่สามารถทำรายการได้", $ip);
									dd_return(false, "เครดิตคงเหลือมากกว่า 5 ไม่สามารถทำรายการได้");
	    						}
    						}
    					}
    					else
    					{
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
    				dd_return(false, "ยอดเงินไม่ตรงกับในระบบกรุณาลองใหม่อีกครั้ง");
    			}
			}
			else
			{
				 dd_return(false, "พบการกระทำต้องสงสัย กรุณาเข้าสู่ระบบใหม่อีกครั้ง ระบบได้บันทึกพฤติกรรมของคุณไว้แล้ว");
			}
		}
		else
		{
			dd_return(false, "ไม่พบข้อมูลผู้ใช้งานในระบบ");
		}
	}
	else
	{
		if($credit == "")
		{
			dd_return(false, "กรุณาใส่จำนวนเครดิต");
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
