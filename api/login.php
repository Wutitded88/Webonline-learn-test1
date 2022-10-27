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
    $password = trim($_POST['password']);
	$ip = trim($_POST['ip']);

	if($username != "" AND $password != "" AND $ip != "")
	{
		if (preg_match('/^[a-zA-Z0-9]+$/', $username) AND preg_match('/^[a-zA-Z0-9@.]+$/', $password))
		{
			$q_1 = dd_q('SELECT * FROM user_tb WHERE (u_user = ?)', [$username]);
			if ($q_1->rowCount() >= 1)
			{
				$row = $q_1->fetch(PDO::FETCH_ASSOC);
				if (password_decode($row['u_password']) == $password)
				{
					if($row['u_block_login'] == 0)
					{
						$_SESSION['u_user'] = $row['u_user'];
						write_log("login", $username." : เข้าสู่ระบบสำเร็จ", $ip);
						$q_2 = dd_q('UPDATE user_tb SET u_last_login=?, u_token=? WHERE u_user=?', [
							date("Y-m-d H:i:s"),
							session_id(),
							$username
						]);

						$q_website_tb = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
    					$row_website = $q_website_tb->fetch(PDO::FETCH_ASSOC);
    					
    					if($row_website['aff_type'] == "3" || $row_website['aff_type'] == "4") //ยอดเสียของเพื่อน - Winloss
    					{
    						if($row_website['aff_step'] >= 1)
			                {
			                	$sum_winloass1 = 0;
			                	$q_aff1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [
	    							$row['u_user']
	    						]);
	    						while($row_aff1 = $q_aff1->fetch(PDO::FETCH_ASSOC))
	    						{
	    							if($row_aff1['u_agent_id'] != "")
	    							{
		    							$q_percent_tb = dd_q('SELECT * FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_user = ? AND aff_create_date = ?', [
		    								$row['u_user'], 
		    								$row_aff1['u_user'], 
		    								date("Y-m-d")
		    							]);
		    							if ($q_percent_tb->rowCount() == 0)
		    							{
		    								$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_system_create_date = ? AND t_transaction_id != ? AND t_status = ?', [
		    									$row_aff1['u_user'],
		    									date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
		    									"",
		    									"1"
		    								]);
		    								$amount_winloss = 0;
		    								while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
		    								{
		    									if($row_topup_tb['t_transaction_id'] != "")
									            {
									            	$winloss = $api->GetWinLose($row_aff1['u_agent_id'], $row_topup_tb['t_transaction_id']);
									            	if ($winloss->success == true)
									            	{
									            		$winloss = json_decode(json_encode($winloss->data), true);
									            		$_check_sum = 0;
									            		foreach ($winloss["data"] as $val)
									            		{
									            			if(array_key_exists('game', $val) && $val['game'] == "FOOTBALL")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "STEP")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "PARLAY")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "GAME")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "CASINO")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "LOTTO")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "M2")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "MULTI_PLAYER")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "POKER")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "E_SPORT")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "KENO")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            			else if(array_key_exists('game', $val) && $val['game'] == "TRADING")
									            			{
									            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
									            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
									            			}
									            		}
									            		if($_check_sum > $row_topup_tb['t_amount'])
									            		{
									            			$amount_winloss = $amount_winloss + $row_topup_tb['t_amount'];
									            		}
									            	}
									            }
									        }
									        if($row_website['aff_type'] == "3")
									        {
									        	$q_withdraw_tb = dd_q('SELECT SUM(w_amount) AS w_amount FROM withdraw_tb WHERE w_user = ? AND w_date_create = ? AND w_status = ?', [
										        	$row_aff1['u_user'],
										        	date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
										        	'1'
										        ]);
										        if ($q_withdraw_tb->rowCount() > 0)
										        {
										        	$row_withdraw_tb = $q_withdraw_tb->fetch(PDO::FETCH_ASSOC);
										        	$sum_winloass1 = $row_withdraw_tb['w_amount'] - $amount_winloss;
										        }
										        else
										        {
										        	$sum_winloass1 = $amount_winloss;
										        }
									        }
									        else
									        {
									        	$sum_winloass1 = $amount_winloss;
									        }
									        
									        $t_amount = 0;
									        $aff_percent = 0;
									        $percent_tb_status = '1';
									        if (strpos($sum_winloass1, '-') !== false)
									        {
									        	$t_amount = str_replace(",", "", str_replace("-", "", $sum_winloass1));
									        	$aff_percent = $t_amount * $row_website['affpersen'];
									        	$percent_tb_status = '0';
									        }

									        dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
									        	$row['u_user'], 
									        	$row_aff1['u_user'], 
									        	$t_amount, 
									        	$aff_percent, 
									        	$percent_tb_status,
									        	1, 
									        	date("Y-m-d"),
									        	date('H:i:s')
									        ]);
		    							}
		    						}
	    						}
			                }
			                if($row_website['aff_step'] >= 2)
			                {
			                	$sum_winloass2 = 0;
			                	$q_aff1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [
	    							$row['u_user']
	    						]);
	    						while($row_aff1 = $q_aff1->fetch(PDO::FETCH_ASSOC))
	    						{
	    							$q_aff2 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [
		    							$row_aff1['u_user']
		    						]);
		    						while($row_aff2 = $q_aff2->fetch(PDO::FETCH_ASSOC))
		    						{
		    							if($row_aff2['u_agent_id'] != "")
		    							{
			    							$q_percent_tb = dd_q('SELECT * FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_user = ? AND aff_create_date = ?', [
			    								$row['u_user'], 
			    								$row_aff2['u_user'], 
			    								date("Y-m-d")
			    							]);
			    							if ($q_percent_tb->rowCount() == 0)
			    							{
			    								$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_system_create_date = ? AND t_transaction_id != ? AND t_status = ?', [
			    									$row_aff2['u_user'],
			    									date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
			    									"",
			    									"1"
			    								]);
			    								$amount_winloss = 0;
			    								while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
			    								{
			    									if($row_topup_tb['t_transaction_id'] != "")
										            {
										            	$winloss = $api->GetWinLose($row_aff2['u_agent_id'], $row_topup_tb['t_transaction_id']);
										            	if ($winloss->success == true)
										            	{
										            		$winloss = json_decode(json_encode($winloss->data), true);
										            		$_check_sum = 0;
										            		foreach ($winloss["data"] as $val)
										            		{
										            			if(array_key_exists('game', $val) && $val['game'] == "FOOTBALL")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "STEP")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "PARLAY")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "GAME")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "CASINO")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "LOTTO")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "M2")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "MULTI_PLAYER")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "POKER")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "E_SPORT")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "KENO")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            			else if(array_key_exists('game', $val) && $val['game'] == "TRADING")
										            			{
										            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
										            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
										            			}
										            		}
										            		if($_check_sum > $row_topup_tb['t_amount'])
										            		{
										            			$amount_winloss = $amount_winloss + $row_topup_tb['t_amount'];
										            		}
										            	}
										            }
										        }

										        if($row_website['aff_type'] == "3")
										        {
										        	$q_withdraw_tb = dd_q('SELECT SUM(w_amount) AS w_amount FROM withdraw_tb WHERE w_user = ? AND w_date_create = ? AND w_status = ?', [
											        	$row_aff2['u_user'],
											        	date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
											        	'1'
											        ]);
											        if ($q_withdraw_tb->rowCount() > 0)
											        {
											        	$row_withdraw_tb = $q_withdraw_tb->fetch(PDO::FETCH_ASSOC);
											        	$sum_winloass2 = $row_withdraw_tb['w_amount'] - $amount_winloss;
											        }
											        else
											        {
											        	$sum_winloass2 = $amount_winloss;
											        }
										        }
										        else
										        {
										        	$sum_winloass2 = $amount_winloss;
										        }
										        
										        $t_amount = 0;
										        $aff_percent = 0;
										        $percent_tb_status = '1';
										        if (strpos($sum_winloass2, '-') !== false)
										        {
										        	$t_amount = str_replace(",", "", str_replace("-", "", $sum_winloass2));
										        	$aff_percent = $t_amount * $row_website['affpersen2'];
										        	$percent_tb_status = '0';
										        }

										        dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
										        	$row['u_user'], 
										        	$row_aff2['u_user'], 
										        	$t_amount, 
										        	$aff_percent, 
										        	$percent_tb_status,
										        	2, 
										        	date("Y-m-d"),
										        	date('H:i:s')
										        ]);
			    							}
			    						}
		    						}
	    						}
			                }
			                if($row_website['aff_step'] == 3)
			                {
			                	$sum_winloass3 = 0;
			                	$q_aff1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [
	    							$row['u_user']
	    						]);
	    						while($row_aff1 = $q_aff1->fetch(PDO::FETCH_ASSOC))
	    						{
	    							$q_aff2 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [
		    							$row_aff1['u_user']
		    						]);
		    						while($row_aff2 = $q_aff2->fetch(PDO::FETCH_ASSOC))
		    						{
		    							$q_aff3 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [
			    							$row_aff2['u_user']
			    						]);
			    						while($row_aff3 = $q_aff3->fetch(PDO::FETCH_ASSOC))
			    						{
			    							if($row_aff3['u_agent_id'] != "")
			    							{
				    							$q_percent_tb = dd_q('SELECT * FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_user = ? AND aff_create_date = ?', [
				    								$row['u_user'], 
				    								$row_aff3['u_user'], 
				    								date("Y-m-d")
				    							]);
				    							if ($q_percent_tb->rowCount() == 0)
				    							{
				    								$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_system_create_date = ? AND t_transaction_id != ? AND t_status = ?', [
				    									$row_aff3['u_user'],
				    									date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
				    									"",
				    									"1"
				    								]);
				    								$amount_winloss = 0;
				    								while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
				    								{
				    									if($row_topup_tb['t_transaction_id'] != "")
											            {
											            	$winloss = $api->GetWinLose($row_aff3['u_agent_id'], $row_topup_tb['t_transaction_id']);
											            	if ($winloss->success == true)
											            	{
											            		$winloss = json_decode(json_encode($winloss->data), true);
											            		$_check_sum = 0;
											            		foreach ($winloss["data"] as $val)
											            		{
											            			if(array_key_exists('game', $val) && $val['game'] == "FOOTBALL")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "STEP")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "PARLAY")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "GAME")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "CASINO")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "LOTTO")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "M2")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "MULTI_PLAYER")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "POKER")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "E_SPORT")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "KENO")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            			else if(array_key_exists('game', $val) && $val['game'] == "TRADING")
											            			{
											            				// หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
											            				$_check_sum = $_check_sum + ($val['wlTurnAmount']);
											            			}
											            		}
											            		if($_check_sum > $row_topup_tb['t_amount'])
											            		{
											            			$amount_winloss = $amount_winloss + $row_topup_tb['t_amount'];
											            		}
											            	}
											            }
											        }

											        if($row_website['aff_type'] == "3")
											        {
											        	$q_withdraw_tb = dd_q('SELECT SUM(w_amount) AS w_amount FROM withdraw_tb WHERE w_user = ? AND w_date_create = ? AND w_status = ?', [
												        	$row_aff3['u_user'],
												        	date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
												        	'1'
												        ]);
												        if ($q_withdraw_tb->rowCount() > 0)
												        {
												        	$row_withdraw_tb = $q_withdraw_tb->fetch(PDO::FETCH_ASSOC);
												        	$sum_winloass3 = $row_withdraw_tb['w_amount'] - $amount_winloss;
												        }
												        else
												        {
												        	$sum_winloass3 = $amount_winloss;
												        }
											        }
											        else
											        {
											        	$sum_winloass3 = $amount_winloss;
											        }
											        
											        $t_amount = 0;
											        $aff_percent = 0;
											        $percent_tb_status = '1';
											        if (strpos($sum_winloass3, '-') !== false)
											        {
											        	$t_amount = str_replace(",", "", str_replace("-", "", $sum_winloass3));
											        	$aff_percent = $t_amount * $row_website['affpersen3'];
											        	$percent_tb_status = '0';
											        }

											        dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
											        	$row['u_user'], 
											        	$row_aff3['u_user'], 
											        	$t_amount, 
											        	$aff_percent, 
											        	$percent_tb_status,
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
						dd_return(true, "เข้าสู่ระบบสำเร็จ");
					}
					else
					{
						write_log("login", $username." : ผู้ใช้งานที่ถูกบล็อค พยายามเข้าสู่ระบบ", $ip);
						dd_return(false ," ".$username." ผู้ใช้งานถูกบล็อค เนื่องจาก มีการพยายามเข้าสู่ระบบ ผิดพลาดมากเกินไป และตรวจพบ พฤติกรรมที่ผิดปกติ");
					}
				}
				else
				{
					write_log("login", $username." : รหัสผ่านไม่ถูกต้อง", $ip);
					dd_return(false, "รหัสผ่านไม่ถูกต้อง");
				}
			}
			else
			{
				write_log("login", $username." : ไม่พบข้อมูล", $ip);
				dd_return(false, "ไม่พบข้อมูล");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอก ภาษาอังกฤษ / ตัวเลข เท่านั้น");
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
