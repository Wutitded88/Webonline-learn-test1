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
	$oldpass = trim($_POST['oldpass']);
	$oldpass = str_replace("\n", "", $oldpass);
	$oldpass = str_replace("\r", "", $oldpass);

	$newpass = trim($_POST['newpass']);
	$newpass = str_replace("\n", "", $newpass);
	$newpass = str_replace("\r", "", $newpass);

	$newpass1 = trim($_POST['newpass1']);
	$newpass1 = str_replace("\n", "", $newpass1);
	$newpass1 = str_replace("\r", "", $newpass1);

	$username = trim($_POST['username']);
	$ip = trim($_POST['ip']);

	if($oldpass != "" AND $newpass != "" AND $newpass1 != "" AND $username != "" AND $ip != "")
	{
		$oldpass_uppercase = preg_match('@[A-Z]@', $oldpass);
		$oldpass_lowercase = preg_match('@[a-z]@', $oldpass);
		$oldpass_number = preg_match('@[0-9]@', $oldpass);
		$oldpass_thai = preg_match('@[ก-๙]@', $oldpass);

		$newpass_uppercase = preg_match('@[A-Z]@', $newpass);
		$newpass_lowercase = preg_match('@[a-z]@', $newpass);
		$newpass_number = preg_match('@[0-9]@', $newpass);
		$newpass_thai = preg_match('@[ก-๙]@', $newpass);

		$newpass1_uppercase = preg_match('@[A-Z]@', $newpass1);
		$newpass1_lowercase = preg_match('@[a-z]@', $newpass1);
		$newpass1_number = preg_match('@[0-9]@', $newpass1);
		$newpass1_thai = preg_match('@[ก-๙]@', $newpass1);

		if(!$oldpass_uppercase || !$oldpass_lowercase || !$oldpass_number || $oldpass_thai || strlen($oldpass) < 8)
		{
			dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
		}
		else if(!$newpass_uppercase || !$newpass_lowercase || !$newpass_number || $newpass_thai || strlen($newpass) < 8)
		{
			dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
		}
		else if(!$newpass1_uppercase || !$newpass1_lowercase || !$newpass1_number || $newpass1_thai || strlen($newpass1) < 8)
		{
			dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
		}
		else if($newpass1 != $newpass)
		{
			dd_return(false, "รหัสผ่านใหม่ และ ยืนยันรหัสผ่าน ไม่ตรงกัน");
		}
		else
		{
			if (preg_match('/^[a-zA-Z0-9]+$/', $oldpass) AND preg_match('/^[a-zA-Z0-9]+$/', $newpass) AND preg_match('/^[a-zA-Z0-9]+$/', $newpass1))
			{
				$q_u = dd_q('SELECT * FROM user_tb WHERE (u_user = ?)', [$username]);
				if ($q_u->rowCount() > 0)
				{
					$row_u = $q_u->fetch(PDO::FETCH_ASSOC);
					if(!empty($row_u['u_agent_id']))
					{
						$setpassword = $api->setPasswordUser($row_u['u_agent_id'], $newpass);
						if ($setpassword->success == true)
						{
							$password_hash = password_encode($newpass);
							$q_1 = dd_q('UPDATE user_tb SET u_password=?, u_agent_pass=? WHERE u_user=?', [$password_hash, $newpass, $username]);
							if($q_1 == true)
							{
								dd_return(true, "เปลี่ยนรหัสผ่านสำเร็จ");
							}
							else
							{
								write_log("changepassword", $username." - ไม่สามารถอัพเดทข้อมูล Password ได้", $ip);
								dd_return(false, "เกิดข้อผิดพลาด");
							}
						}
						else
						{
							write_log("SetPasswordUser", $username." - ".$setpassword->message, $ip);
							dd_return(false, "เปลี่ยนรหัสผ่านไม่สำเร็จ กรุณาติดต่อเจ้าหน้าที่");
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
				dd_return(false, "กรุณากรอก ภาษาอังกฤษ / ตัวเลข เท่านั้น");
			}
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
