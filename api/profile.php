<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

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
	$username = get_session();
    $truewallet_id = trim($_POST['truewallet_id']);
	$ip = trim($_POST['ip']);

	if($username != "" AND $truewallet_id != "" AND $ip != "")
	{
		$q_1 = dd_q('SELECT * FROM user_tb WHERE (u_user = ?)', [$username]);
		if ($q_1->rowCount() > 0)
		{
			$row = $q_1->fetch(PDO::FETCH_ASSOC);
			if ($row['u_truewallet_id'] == "")
			{
				$q_2 = dd_q('SELECT * FROM user_tb WHERE (u_truewallet_id = ?)', [$truewallet_id]);
				if ($q_2->rowCount() == 0)
				{
					dd_q('UPDATE user_tb SET u_truewallet_id=? WHERE u_user=?', [
						$truewallet_id,
						$username
					]);
					dd_return(true, "อัพเดทข้อมูลสำเร็จ");
				}
				else
				{
					write_log("update wallet id", $username." : อัพเดทซ้ำ", $ip);
					dd_return(false, "Wallet ID นี้ถูกใช้งานแล้ว");
				}
			}
			else
			{
				write_log("update wallet id", $username." : อัพเดทซ้ำ", $ip);
				dd_return(false, "Wallet ID ถูกอัพเดทแล้ว");
			}
		}
		else
		{
			write_log("update wallet id", $username." : ไม่พบข้อมูล", $ip);
			dd_return(false, "ไม่พบข้อมูล");
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
