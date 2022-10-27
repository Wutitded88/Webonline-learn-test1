<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'config_sms.php';
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
	$q_website_tb = dd_q('SELECT * FROM website_tb WHERE id = ?', [1]);
	$row_website_tb = $q_website_tb->fetch(PDO::FETCH_ASSOC);

	if(isset($_POST['s']))
	{
		$step = trim($_POST['s']);
		if($step == "s1")
		{
			$s1_phone = trim($_POST['s1_phone']);
			if($s1_phone != "")
			{
				if(!preg_match('/^[0-9]+$/', $s1_phone))
				{
					dd_return(false, "เบอร์โทรศัพท์ ต้องเป็นตัวเลข เท่านั้น");
				}
				else if(strlen($s1_phone) != 10)
				{
					dd_return(false, "เบอร์โทรศัพท์ ต้องเป็นตัวเลข 10 ตัวเท่านั้น");
				}
				else
				{
					$q_user_tb = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
						$s1_phone
					]);
					if ($q_user_tb->rowCount() > 0)
					{
						$ref = "";
						$otp = "";
						if($row_website_tb['sms_active'] == "1")
			            {
			            	$loop_ref_stop = false;
							while (!$loop_ref_stop)
							{
								$code_ref = generateRandomString(5);
								$q_ref = dd_q('SELECT * FROM user_otp_tb WHERE u_otp_ref = ?', [
									$code_ref
								]);
								if ($q_ref->rowCount() == 0)
								{
									$ref = $code_ref;
									$loop_ref_stop = true;
								}
							}
							$loop_otp_stop = false;
							while (!$loop_otp_stop)
							{
								$code_otp = generateRandomInt(6);
								$q_otp = dd_q('SELECT * FROM user_otp_tb WHERE u_otp_otp = ?', [
									$code_otp
								]);
								if ($q_otp->rowCount() == 0)
								{
									$otp = $code_otp;
									$loop_otp_stop = true;
								}
							}

							if($ref != "" && $otp != "")
							{
								$sms = new thsms();
								$result = $sms->send($row_website_tb['sms_user'], $row_website_tb['sms_pass'], $s1_phone, $otp." is your OTP for verify user with ref code ".$ref.".");
								if ($result == "success")
								{
									$q_insert_user_otp_tb = dd_q('INSERT INTO user_otp_tb (u_otp_phone, u_otp_ref, u_otp_otp, u_otp_action) VALUES (?, ?, ?, ?)', [
										$s1_phone,
										$ref,
										$otp,
										date("Y-m-d H:i:s")
									]);
									if ($q_insert_user_otp_tb == true)
									{
										$data = new stdClass();
										$data->phone = $s1_phone;
										$data->ref = $ref;
										dd_return(true, $data);
									}
									else
									{
										dd_return(false, "เกิดข้อผิดพลาดทางระบบ");
									}
								}
								else
								{
									dd_return(false, $result);
								}
							}
							else
							{
								dd_return(false, "ข้อมูลไม่ครบถ้วน กรุณาลองใหม่อีกครั้ง");
							}
			            }
			            else
			            {
			            	$data = new stdClass();
							$data->phone = $s1_phone;
							$data->ref = $ref;
							dd_return(true, $data);
			            }
					}
					else
					{
						dd_return(false, "ไม่มีเบอร์โทรศัพท์นี้ในระบบ");
					}
				}
			}
			else
			{
				dd_return(false, "กรุณากรอกข้อมูลให้ครบทุกถ้วน");
			}
		}
		else if($step == "s2")
		{
			$s2_phone = trim($_POST['s2_phone']);
			$s2_password = trim($_POST['s2_password']);
			$s2_c_password = trim($_POST['s2_c_password']);
			$s2_ref = trim($_POST['s2_ref']);
			$s2_otp = trim($_POST['s2_otp']);
			$ip = trim($_POST['ip']);
			if($s2_phone != "" && $s2_password != "" && $s2_c_password != "" && $ip != "")
			{
				if($row_website_tb['sms_active'] == "1" && $s2_ref == "" && $s2_otp == "")
				{
					dd_return(false, "กรุณากรอกข้อมูลให้ครบทุกถ้วน");
				}
				else
				{
					if(!preg_match('/^[0-9]+$/', $s2_phone))
					{
						dd_return(false, "เบอร์โทรศัพท์ ต้องเป็นตัวเลข เท่านั้น");
					}
					else if(strlen($s2_phone) != 10)
					{
						dd_return(false, "เบอร์โทรศัพท์ ต้องเป็นตัวเลข 10 ตัวเท่านั้น");
					}
					else
					{
						$uppercase = preg_match('@[A-Z]@', $s2_password);
						$lowercase = preg_match('@[a-z]@', $s2_password);
						$number = preg_match('@[0-9]@', $s2_password);
						$thai = preg_match('@[ก-๙]@', $s2_password);
						if(!$uppercase || !$lowercase || !$number || $thai || strlen($s2_password) < 8)
						{
							dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
						}
						else
						{
							if($s2_password == $s2_c_password)
							{
								if($row_website_tb['sms_active'] == "1")
								{
									$q_user_otp_tb = dd_q('SELECT * FROM user_otp_tb WHERE u_otp_phone = ? AND u_otp_ref = ? AND u_otp_otp = ?', [
										$s2_phone,
										$s2_ref,
										$s2_otp
									]);
									if ($q_user_otp_tb->rowCount() > 0)
									{
										$row_user_otp_tb = $q_user_otp_tb->fetch(PDO::FETCH_ASSOC);
										$q_user_tb_phone = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
											$row_user_otp_tb['u_otp_phone']
										]);
										if ($q_user_tb_phone->rowCount() > 0)
										{
											$row_user_tb_phone = $q_user_tb_phone->fetch(PDO::FETCH_ASSOC);
											$setpassword = $api->setPasswordUser($row_user_tb_phone['u_agent_id'], $s2_password);
											if ($setpassword->success == true)
											{
												$password_hash = password_encode($s2_password);
												$q_update = dd_q('UPDATE user_tb SET u_password=?, u_agent_pass=? WHERE u_user=?', [
													$password_hash,
													$s2_password,
													$s2_phone
												]);
												if ($q_update == true)
												{
													dd_return(true, "เปลี่ยนรหัสผ่านสำเร็จ");
												}
												else
												{
													dd_return(false, "เกิดข้อผิดพลาดทางระบบ");
												}
											}
											else
											{
												dd_return(false, $setpassword->message);
											}
										}
										else
										{
											dd_return(false, "ไม่มีเบอร์โทรศัพท์นี้ในระบบ");
										}
									}
									else
									{
										dd_return(false, "OTP ไม่ถูกต้อง");
									}
								}
								else
								{
									$secret = $_CONFIG['secretkey'];
									$response = $_POST["captcha"];
									$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
									$captcha_success = json_decode($verify);
									if($captcha_success->success==false)
									{
										dd_return(false, "กรุณายืนยันตัวตน");
									}
									else if($captcha_success->success==true)
									{
										$q_user_tb_phone = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
											$s2_phone
										]);
										if ($q_user_tb_phone->rowCount() > 0)
										{
											$row_user_tb_phone = $q_user_tb_phone->fetch(PDO::FETCH_ASSOC);
											$setpassword = $api->setPasswordUser($row_user_tb_phone['u_agent_id'], $s2_password);
											if ($setpassword->success == true)
											{
												$password_hash = password_encode($s2_password);
												$q_update = dd_q('UPDATE user_tb SET u_password=?, u_agent_pass=? WHERE u_user=?', [
													$password_hash,
													$s2_password,
													$s2_phone
												]);
												if ($q_update == true)
												{
													dd_return(true, "เปลี่ยนรหัสผ่านสำเร็จ");
												}
												else
												{
													dd_return(false, "เกิดข้อผิดพลาดทางระบบ");
												}
											}
											else
											{
												dd_return(false, $setpassword->message);
											}
										}
										else
										{
											dd_return(false, "ไม่มีเบอร์โทรศัพท์นี้ในระบบ");
										}
									}
									else
									{
										dd_return(false, "ไม่สามารถใช้งานได้");
									}
								}
							}
							else
							{
								dd_return(false, "รหัสผ่าน ไม่เหมือนกัน");
							}
						}
					}
				}
			}
			else
			{
				dd_return(false, "กรุณากรอกข้อมูลให้ครบทุกถ้วน");
			}
		}
	}
	else
	{
		dd_return(false, "ระบบทำงานไม่ถูกต้อง");
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
