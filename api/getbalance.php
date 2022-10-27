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
	$ip = trim($_POST['ip']);

	if($username != "" AND $ip != "")
	{
		$q_1 = dd_q('SELECT * FROM user_tb WHERE (u_user = ? AND u_block_agent = 0)', [$username]);
		if ($q_1->rowCount() >= 1)
		{
			$row_u = $q_1->fetch(PDO::FETCH_ASSOC);
			if(!empty($row_u['u_agent_id']))
			{
				$data = $api->getUserCredit($row_u['u_agent_id']);
				if ($data->success == true)
				{
					$data = json_decode(json_encode($data->data), true);
					dd_return(true, $data['credit']);
				}
				else
				{
					write_log("getcredit", $username." - ".$data->success, $ip);
					dd_return(false, "ไม่สามารถดึงยอดเครดิตได้ กรุณาติดต่อแอดมิน - ".$data->success);
				}
			}
			else
			{
				dd_return(true, "0");
			}
		}
		else
		{
			dd_return(false, "ไม่พบข้อมูลผู้ใช้งานในระบบ");
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
