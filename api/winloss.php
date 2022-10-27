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
	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else
	{
		$ip = trim($_POST['ip']);
		$type = trim($_POST['type']);
		if($type != "")
		{
			if($type == "receivewinloss")
			{
				$sum_winloass = 0;
				$q_winloss_receive_tb = dd_q('SELECT * FROM winloss_receive_tb WHERE wl_user = ? AND wl_date = ?', [
					get_session(),
					date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'))
				]);
				if ($q_winloss_receive_tb->rowCount() == 0)
				{
					$q_user_tb = dd_q('SELECT * FROM user_tb WHERE u_user = ? AND u_block_agent = 0', [get_session()]);
					if ($q_user_tb->rowCount() > 0)
				    {
				    	$row_user_tb = $q_user_tb->fetch(PDO::FETCH_ASSOC);
				    	if($row_user_tb['u_token'] == session_id())
				    	{
				    		if(!empty($row_user_tb['u_agent_id']))
				    		{
				    			$q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_system_create_date = ? AND t_transaction_id != ? AND t_status = ?', [
				    				$row_user_tb['u_user'],
				    				date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
				    				"",
				    				"1"
						        ]);
						        if ($q_topup_tb->rowCount() > 0)
						        {
						        	$amount_winloss = 0;
						        	while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
						        	{
							            if($row_topup_tb['t_transaction_id'] != "")
							            {
							            	$winloss = $api->GetWinLose($row_user_tb['u_agent_id'], $row_topup_tb['t_transaction_id']);
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
								    $q_withdraw_tb = dd_q('SELECT SUM(w_amount) AS w_amount FROM withdraw_tb WHERE w_user = ? AND w_date_create = ? AND w_status = ?', [
								    	$row_user_tb['u_user'],
								    	date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
								    	'1'
								    ]);
								    if ($q_withdraw_tb->rowCount() > 0)
								    {
								    	$row_withdraw_tb = $q_withdraw_tb->fetch(PDO::FETCH_ASSOC);
								    	$sum_winloass = $row_withdraw_tb['w_amount'] - $amount_winloss;
								    }
								    else
								    {
								    	$sum_winloass = $amount_winloss;
								    }

							        if (strpos($sum_winloass, '-') !== false)
							        {
							        	$q_website_tb = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
							        	$row_website_tb = $q_website_tb->fetch(PDO::FETCH_ASSOC);

							        	$receive_winloss = str_replace(",", "", str_replace("-", "", $sum_winloass)) * $row_website_tb['affwinloss'];

							        	$check_sum_winloass = str_replace(",", "", str_replace("-", "", $sum_winloass));
							        	if($check_sum_winloass >= $row_website_tb['minwinloss'])
							        	{
							        		$deposit = $api->transferCreditTo($row_user_tb['u_agent_id'], number_format($receive_winloss, 2, '.', ''));
								        	if ($deposit->success == true)
								        	{
								        		$deposit = json_decode(json_encode($deposit->data), true);
								        		$ExternalTransactionId = $deposit['ref'];

								        		$q_insert_t = dd_q('INSERT INTO winloss_receive_tb (wl_date, wl_amount, wl_cashback, wl_transaction_id, wl_user, wl_u_id, wl_fname, wl_action_date, wl_action_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
								        			date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')), 
								        			str_replace(",", "", str_replace("-", "", $sum_winloass)), 
								        			$receive_winloss, 
								        			$ExternalTransactionId, 
								        			$row_user_tb['u_user'], 
								        			$row_user_tb['u_id'], 
								        			$row_user_tb['u_fname'], 
								        			date("Y-m-d"),
								        			date('H:i:s')
								        		]);
								        		if($q_insert_t == true)
								        		{
								        			dd_return(true, "รับเครดิตคืนยอดเสียสำเร็จ");
								        		}
								        		else
								        		{
								        			dd_return(false, "เพิ่มข้อมูล winloss ไม่สำเร็จ");
								        		}
								        	}
								        	else
								        	{
								        		write_log("winloss", $row_user_tb['u_user']. " - ".json_encode($deposit->message)." - ".$deposit->request, $ip);
								        		dd_return(false, $deposit->message);
								        	}
							        	}
							        	else
							        	{
							        		write_log("winloss", $row_user_tb['u_user']. " - จำนวนคืนยอดเสียน้อยกว่ายอดขั้นต่ำ แต่กดรับยอดเสีย ด้วยการแก้ไขข้อมูล ควรแบน", $ip);
							        		dd_return(false, "จำนวนคืนยอดเสียน้อยกว่ายอดขั้นต่ำ ไม่สามารถกดรับได้");
							        	}
							        }
							        else
							        {
							        	write_log("winloss", $row_user_tb['u_user']. " - ได้กำไร แต่กดรับยอดเสีย ด้วยการแก้ไขข้อมูล ควรแบน", $ip);
							        	dd_return(false, "คุณได้กำไร คุณไม่มีสิทธิ์รับยอดเสีย");
							        }
						        }
						        else
						        {
						        	dd_return(false, "ไม่พบข้อมูลการเติมเงินของวันที่ ".date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')));
						        }
				    		}
				    		else
				    		{
				    			dd_return(false, "รหัสเข้าเกมยังไม่ถูกสร้าง กรุณาติดต่อแอดมิน");
				    		}
					    }
					    else
					    {
					    	write_log("winloss", $row_user_tb['u_user']. " - Login ซ้อน", $ip);
					    	dd_return(false, "พบการกระทำต้องสงสัย กรุณาเข้าสู่ระบบใหม่อีกครั้ง");
					    }
				    }
				    else
				    {
				    	dd_return(false, "ไม่พบข้อมูลผู้ใช้งานในระบบ");
				    }
				}
				else
				{
					write_log("winloss", get_session()." กดรับยอดเสียซ้ำ", $ip);
					dd_return(false, "คุณรับยอดเสียของวันที่ ".date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'))." แล้ว");
				}
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบถ้วน");
		}
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
