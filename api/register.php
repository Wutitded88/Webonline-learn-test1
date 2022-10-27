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
					dd_return(false, "กรุณาตั้ง เบอร์โทรศัพท์ ด้วยตัวเลข เท่านั้น");
				}
				else if(strlen($s1_phone) != 10)
				{
					dd_return(false, "กรุณาตั้ง เบอร์โทรศัพท์ ด้วยตัวเลข 10 ตัวเท่านั้น");
				}
				else
				{
					$q_user_tb = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
						$s1_phone
					]);
					if ($q_user_tb->rowCount() == 0)
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
						dd_return(false, "มีเบอร์โทรศัพท์นี้ในระบบแล้ว");
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
			$s2_bank_code = trim($_POST['s2_bank_code']) == "undefined" ? "" : trim($_POST['s2_bank_code']);
			$s2_account_number = trim($_POST['s2_account_number']);
			$s2_account_name = trim($_POST['s2_account_name']);
			$s2_line = trim($_POST['s2_line']);
			$s2_register_channel_id = trim($_POST['s2_register_channel_id']);
			$s2_otp = trim($_POST['s2_otp']);
			$s2_phone = trim($_POST['s2_phone']);
			$s2_ref = trim($_POST['s2_ref']);
			$s2_account_aff = trim($_POST['s2_account_aff']);
			if($s2_bank_code != "" && $s2_account_number != "" && $s2_account_name != "" && $s2_line != "" && $s2_register_channel_id != "" && $s2_phone != "")
			{
				if($row_website_tb['sms_active'] == "1" && $s2_otp == "" && $s2_ref == "")
				{
					dd_return(false, "กรุณากรอกข้อมูลให้ครบทุกถ้วน");
				}
				else
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
							if ($q_user_tb_phone->rowCount() == 0)
							{
								if(!preg_match('/^[0-9]+$/', $s2_account_number))
								{
									dd_return(false, "กรุณาตั้ง หมายเลขบัญชี ด้วยตัวเลข เท่านั้น");
								}
								else
								{
									$q_user_tb_bank_number = dd_q('SELECT * FROM user_tb WHERE u_bank_number = ?', [
										$s2_account_number
									]);
									if ($q_user_tb_bank_number->rowCount() == 0)
									{
										$q_user_tb_line = dd_q('SELECT * FROM user_tb WHERE u_line = ?', [
											$s2_line
										]);
										if ($q_user_tb_line->rowCount() == 0)
										{
											$data = new stdClass();
											$data->phone = $s2_phone;
											$data->ref = $s2_ref;
											$data->otp = $s2_otp;
											$data->u_bank_code = $s2_bank_code;
											$data->u_bank_number = $s2_account_number;
											$data->u_fname = $s2_account_name;
											$data->u_line = $s2_line;
											$data->u_refer_id = $s2_register_channel_id;
											$data->u_aff = $s2_account_aff;
											dd_return(true, $data);
										}
										else
										{
											dd_return(false, "มีไอดีไลน์นี้ในระบบแล้ว");
										}
									}
									else
									{
										dd_return(false, "มีเลขบัญชีนี้ในระบบแล้ว");
									}
								}
							}
							else
							{
								dd_return(false, "มีเบอร์โทรศัพท์นี้ในระบบแล้ว");
							}
						}
						else
						{
							dd_return(false, "OTP ไม่ถูกต้อง");
						}
					}
					else
					{
						$q_user_tb_phone = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
							$s2_phone
						]);
						if ($q_user_tb_phone->rowCount() == 0)
						{
							if(!preg_match('/^[0-9]+$/', $s2_account_number))
							{
								dd_return(false, "กรุณาตั้ง หมายเลขบัญชี ด้วยตัวเลข เท่านั้น");
							}
							else
							{
								$q_user_tb_bank_number = dd_q('SELECT * FROM user_tb WHERE u_bank_number = ?', [
									$s2_account_number
								]);
								if ($q_user_tb_bank_number->rowCount() == 0)
								{
									$q_user_tb_line = dd_q('SELECT * FROM user_tb WHERE u_line = ?', [
										$s2_line
									]);
									if ($q_user_tb_line->rowCount() == 0)
									{
										$data = new stdClass();
										$data->phone = $s2_phone;
										$data->ref = $s2_ref;
										$data->otp = $s2_otp;
										$data->u_bank_code = $s2_bank_code;
										$data->u_bank_number = $s2_account_number;
										$data->u_fname = $s2_account_name;
										$data->u_line = $s2_line;
										$data->u_refer_id = $s2_register_channel_id;
										$data->u_aff = $s2_account_aff;
										dd_return(true, $data);
									}
									else
									{
										dd_return(false, "มีไอดีไลน์นี้ในระบบแล้ว");
									}
								}
								else
								{
									dd_return(false, "มีเลขบัญชีนี้ในระบบแล้ว");
								}
							}
						}
						else
						{
							dd_return(false, "มีเบอร์โทรศัพท์นี้ในระบบแล้ว");
						}
					}
				}
			}
			else
			{
				dd_return(false, "กรุณากรอกข้อมูลให้ครบทุกถ้วน");
			}
		}
		else if($step == "s3")
		{
			$s3_phone = trim($_POST['s3_phone']);
			$s3_password = trim($_POST['s3_password']);
			$s3_c_password = trim($_POST['s3_c_password']);
			$s3_ref = trim($_POST['s3_ref']);
			$s3_otp = trim($_POST['s3_otp']);
			$s3_u_bank_code = trim($_POST['s3_u_bank_code']);
			$s3_u_bank_number = trim($_POST['s3_u_bank_number']);
			$s3_u_fname = trim($_POST['s3_u_fname']);
			$s3_u_refer_id = trim($_POST['s3_u_refer_id']);

			$u_refer_name = "";
			if($s3_u_refer_id == "1")
			{
				$u_refer_name = "FaceBook";
			}
			if($s3_u_refer_id == "2")
			{
				$u_refer_name = "Tiktok";
			}
			else if($s3_u_refer_id == "3")
			{
				$u_refer_name = "Line";
			}
			else if($s3_u_refer_id == "4")
			{
				$u_refer_name = "Google";
			}
			else if($s3_u_refer_id == "5")
			{
				$u_refer_name = "Instargram";
			}
			else if($s3_u_refer_id == "6")
			{
				$u_refer_name = "Youtube";
			}
			else if($s3_u_refer_id == "7")
			{
				$u_refer_name = "SMS";
			}
			else if($s3_u_refer_id == "8")
			{
				$u_refer_name = "เพื่อนแนะนำ";
			}

			$s3_u_aff = trim($_POST['s3_u_aff']);
			$s3_u_line = trim($_POST['s3_u_line']);
			$ip = trim($_POST['ip']);
			if($s3_phone != "" && $s3_password != "" && $s3_c_password != "" && $s3_u_bank_code != "" && $s3_u_bank_number != "" && $s3_u_fname != "" && $s3_u_refer_id != "" && $s3_u_line != "" && $ip != "")
			{
				if($row_website_tb['sms_active'] == "1" && $s3_ref == "" && $s3_otp == "")
				{
					dd_return(false, "กรุณากรอกข้อมูลให้ครบทุกถ้วน");
				}
				else
				{
					if(!preg_match('/^[0-9]+$/', $s3_phone))
					{
						dd_return(false, "กรุณาตั้ง เบอร์โทรศัพท์ ด้วยตัวเลข เท่านั้น");
					}
					else if(strlen($s3_phone) != 10)
					{
						dd_return(false, "กรุณาตั้ง เบอร์โทรศัพท์ ด้วยตัวเลข 10 ตัวเท่านั้น");
					}
					else
					{
						$uppercase = preg_match('@[A-Z]@', $s3_password);
						$lowercase = preg_match('@[a-z]@', $s3_password);
						$number = preg_match('@[0-9]@', $s3_password);
						$thai = preg_match('@[ก-๙]@', $s3_password);
						if(!$uppercase || !$lowercase || !$number || $thai || strlen($s3_password) < 8)
						{
							dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
						}
						else
						{
							if($s3_password == $s3_c_password)
							{
								if($row_website_tb['sms_active'] == "1")
								{
									$q_user_otp_tb = dd_q('SELECT * FROM user_otp_tb WHERE u_otp_phone = ? AND u_otp_ref = ? AND u_otp_otp = ?', [
										$s3_phone,
										$s3_ref,
										$s3_otp
									]);
									if ($q_user_otp_tb->rowCount() > 0)
									{
										$row_user_otp_tb = $q_user_otp_tb->fetch(PDO::FETCH_ASSOC);
										$q_user_tb_phone = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
											$row_user_otp_tb['u_otp_phone']
										]);
										if ($q_user_tb_phone->rowCount() == 0)
										{
											if(!preg_match('/^[0-9]+$/', $s3_u_bank_number))
											{
												dd_return(false, "กรุณาตั้ง หมายเลขบัญชี ด้วยตัวเลข เท่านั้น");
											}
											else
											{
												$q_user_tb_bank_number = dd_q('SELECT * FROM user_tb WHERE u_bank_number = ?', [
													$s3_u_bank_number
												]);
												if ($q_user_tb_bank_number->rowCount() == 0)
												{
													$q_ip = dd_q('SELECT * FROM user_tb WHERE u_ip = ?', [$ip]);
													if ($q_ip->rowCount() == 0)
													{
														$username = "";
														$loopuser_stop = false;
														while (!$loopuser_stop)
														{
															$username = "9".generateRandomString(7);
															$q_loopuser = dd_q('SELECT * FROM user_tb WHERE u_agent_id  = ?', [$username]);
															if ($q_loopuser->rowCount() == 0)
															{
																$loopuser_stop = true;
															}
														}

														if($username != "")
														{
															$data = $api->createUser($username, $s3_password, $s3_u_fname, $s3_phone);
															if ($data->success == true)
															{
																$u_bank_code = "";
																$u_bank_name = "";
																$q_bank_tb = dd_q('SELECT * FROM bank_tb WHERE b_code  = ?', [$s3_u_bank_code]);
																if ($q_bank_tb->rowCount() > 0)
																{
																	$row_bank_tb = $q_bank_tb->fetch(PDO::FETCH_ASSOC);
																	$u_bank_code = $row_bank_tb['b_short_name'];
																	$u_bank_name = $row_bank_tb['b_official_name_th'];
																}

																$password_hash = password_encode($s3_password);
																$q_3 = dd_q('INSERT INTO user_tb (u_user, u_password, u_agent_id, u_agent_pass, u_wallet, u_fname, u_bank_code, u_bank_name, u_bank_number, u_phone, u_line, u_refer_id, u_refer_name, u_aff, u_block_login, u_block_agent, u_create_on, u_date_create, u_time_create, u_ip, u_vip, u_token, u_last_login, u_limitcredit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																	$s3_phone,
																	$password_hash,
																	$data->username,
																	$s3_password,
																	0,
																	$s3_u_fname,
																	$u_bank_code,
																	$u_bank_name,
																	$s3_u_bank_number,
																	$s3_phone,
																	$s3_u_line,
																	$s3_u_refer_id,
																	$u_refer_name,
																	$s3_u_aff,
																	0,
																	0,
																	date("Y-m-d H:i:s"),
																	date("Y-m-d"),
																	date("H:i:s"),
																	$ip,
																	'Normal',
																	session_id(),
																	date("Y-m-d H:i:s"),
																	$row_website_tb['all_limitcredit']
																]);
																if ($q_3 == true)
																{
																	$_SESSION['u_user'] = $s3_phone;
																	$loop_stop = false;
																	while (!$loop_stop)
																	{
																		$code = generateRandomString(8);
																		$q_4 = dd_q('SELECT * FROM gencode_tb WHERE g_code = ?', [$code]);
																		if ($q_4->rowCount() == 0)
																		{
																			$loop_stop = true;
																			$q_5 = dd_q('INSERT INTO gencode_tb (g_code, g_phone, g_use, g_create_by, g_create_date, g_date_create, g_time_create) VALUES (?, ?, ?, ?, ?, ?, ?)', [
																				$code, 
																				$s3_phone, 
																				'0', 
																				'system', 
																				date("Y-m-d H:i:s"),
																				date("Y-m-d"),
																				date("H:i:s")
																			]);
																			if($q_5 = true)
																			{
																				$q_6 = dd_q('UPDATE user_tb SET u_creditfree=? WHERE u_user=?', [
																					$code,
																					$s3_phone
																				]);
																				if($q_6 = true)
																				{
																					//ส่ง line แจ้งเตือนสมัคร
																					notify_message("เบอร์ : ".$s3_phone." สมัครสำเร็จ", $row_website_tb['lineregister']);
																					dd_return(true, "สมัครสมาชิกสำเร็จ");
																				}
																				else
																				{
																					write_log("register", $s3_phone." - ไม่สามารถอัพเดทรายการ Code free ได้", $ip);
																					dd_return(false, "เกิดข้อผิดพลาด");
																				}
																			}
																			else
																			{
																				write_log("register", $s3_phone." - ไม่สามารถสร้างรายการ Code free ได้", $ip);
																				dd_return(false, "เกิดข้อผิดพลาด");
																			}
																		}
																	}
																}
																else
																{
																	write_log("register", "การสมัครสมาชิกล้มเหลว", $ip);
																	dd_return(false, "เกิดข้อผิดพลาดทางระบบ");
																}
															}
															else
															{
																write_log("register", $data->message, $ip);
																dd_return(false, "ไม่สามารถสร้างข้อมูล User Game ได้ กรุณาติดต่อแอดมิน");
															}
														}
														else
														{
															dd_return(false, "ไม่สามารถสร้าง username ได้ กรุณาติดต่อแอดมิน");
														}
													}
													else
													{
														write_log("register", $s3_phone." IP นี้มียูสอยู่แล้ว ไม่สามารถสมัครได้", $ip);
														dd_return(false, "ระบบมีข้อมูลของคุณอยู่แล้ว กรุณาใช้ยูสเดิมในการเล่น");
													}
												}
												else
												{
													dd_return(false, "มีเลขบัญชีนี้ในระบบแล้ว");
												}
											}
										}
										else
										{
											dd_return(false, "มีเบอร์โทรศัพท์นี้ในระบบแล้ว");
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
											$s3_phone
										]);
										if ($q_user_tb_phone->rowCount() == 0)
										{
											if(!preg_match('/^[0-9]+$/', $s3_u_bank_number))
											{
												dd_return(false, "กรุณาตั้ง หมายเลขบัญชี ด้วยตัวเลข เท่านั้น");
											}
											else
											{
												$q_user_tb_bank_number = dd_q('SELECT * FROM user_tb WHERE u_bank_number = ?', [
													$s3_u_bank_number
												]);
												if ($q_user_tb_bank_number->rowCount() == 0)
												{
													$q_ip = dd_q('SELECT * FROM user_tb WHERE u_ip = ?', [$ip]);
													if ($q_ip->rowCount() == 0)
													{
														$username = "";
														$loopuser_stop = false;
														while (!$loopuser_stop)
														{
															$username = "9".generateRandomString(7);
															$q_loopuser = dd_q('SELECT * FROM user_tb WHERE u_agent_id  = ?', [$username]);
															if ($q_loopuser->rowCount() == 0)
															{
																$loopuser_stop = true;
															}
														}

														if($username != "")
														{
															$data = $api->createUser($username, $s3_password, $s3_u_fname, $s3_phone);
															if ($data->success == true)
															{
																$u_bank_code = "";
																$u_bank_name = "";
																$q_bank_tb = dd_q('SELECT * FROM bank_tb WHERE b_code  = ?', [$s3_u_bank_code]);
																if ($q_bank_tb->rowCount() > 0)
																{
																	$row_bank_tb = $q_bank_tb->fetch(PDO::FETCH_ASSOC);
																	$u_bank_code = $row_bank_tb['b_short_name'];
																	$u_bank_name = $row_bank_tb['b_official_name_th'];
																}

																$password_hash = password_encode($s3_password);
																$q_3 = dd_q('INSERT INTO user_tb (u_user, u_password, u_agent_id, u_agent_pass, u_wallet, u_fname, u_bank_code, u_bank_name, u_bank_number, u_phone, u_line, u_refer_id, u_refer_name, u_aff, u_block_login, u_block_agent, u_create_on, u_date_create, u_time_create, u_ip, u_vip, u_token, u_last_login, u_limitcredit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																	$s3_phone,
																	$password_hash,
																	$data->username,
																	$s3_password,
																	0,
																	$s3_u_fname,
																	$u_bank_code,
																	$u_bank_name,
																	$s3_u_bank_number,
																	$s3_phone,
																	$s3_u_line,
																	$s3_u_refer_id,
																	$u_refer_name,
																	$s3_u_aff,
																	0,
																	0,
																	date("Y-m-d H:i:s"),
																	date("Y-m-d"),
																	date("H:i:s"),
																	$ip,
																	'Normal',
																	session_id(),
																	date("Y-m-d H:i:s"),
																	$row_website_tb['all_limitcredit']
																]);
																if ($q_3 == true)
																{
																	$_SESSION['u_user'] = $s3_phone;
																	$loop_stop = false;
																	while (!$loop_stop)
																	{
																		$code = generateRandomString(8);
																		$q_4 = dd_q('SELECT * FROM gencode_tb WHERE g_code = ?', [$code]);
																		if ($q_4->rowCount() == 0)
																		{
																			$loop_stop = true;
																			$q_5 = dd_q('INSERT INTO gencode_tb (g_code, g_phone, g_use, g_create_by, g_create_date, g_date_create, g_time_create) VALUES (?, ?, ?, ?, ?, ?, ?)', [
																				$code, 
																				$s3_phone, 
																				'0', 
																				'system', 
																				date("Y-m-d H:i:s"),
																				date("Y-m-d"),
																				date("H:i:s")
																			]);
																			if($q_5 = true)
																			{
																				$q_6 = dd_q('UPDATE user_tb SET u_creditfree=? WHERE u_user=?', [
																					$code,
																					$s3_phone
																				]);
																				if($q_6 = true)
																				{
																					//ส่ง line แจ้งเตือนสมัคร
																					notify_message("เบอร์ : ".$s3_phone." สมัครสำเร็จ", $row_website_tb['lineregister']);
																					dd_return(true, "สมัครสมาชิกสำเร็จ");
																				}
																				else
																				{
																					write_log("register", $s3_phone." - ไม่สามารถอัพเดทรายการ Code free ได้", $ip);
																					dd_return(false, "เกิดข้อผิดพลาด");
																				}
																			}
																			else
																			{
																				write_log("register", $s3_phone." - ไม่สามารถสร้างรายการ Code free ได้", $ip);
																				dd_return(false, "เกิดข้อผิดพลาด");
																			}
																		}
																	}
																}
																else
																{
																	write_log("register", "การสมัครสมาชิกล้มเหลว", $ip);
																	dd_return(false, "เกิดข้อผิดพลาดทางระบบ");
																}
															}
															else
															{
																write_log("register", $data->message, $ip);
																dd_return(false, "ไม่สามารถสร้างข้อมูล User Game ได้ กรุณาติดต่อแอดมิน");
															}
														}
														else
														{
															dd_return(false, "ไม่สามารถสร้าง username ได้ กรุณาติดต่อแอดมิน");
														}
													}
													else
													{
														write_log("register", $s3_phone." IP นี้มียูสอยู่แล้ว ไม่สามารถสมัครได้", $ip);
														dd_return(false, "ระบบมีข้อมูลของคุณอยู่แล้ว กรุณาใช้ยูสเดิมในการเล่น");
													}
												}
												else
												{
													dd_return(false, "มีเลขบัญชีนี้ในระบบแล้ว");
												}
											}
										}
										else
										{
											dd_return(false, "มีเบอร์โทรศัพท์นี้ในระบบแล้ว");
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
		$firstname = trim($_POST['firstname']);
		$bank_id = trim($_POST['bank_id']);
		$bank_name = trim($_POST['bank_name']);

		$chars = array("\r\n", '\\n', '\\r', "\n", "\r", "\t", "\0", "\x0B");
		$acc_no = str_replace($chars,"", $_POST['acc_no']);
		$acc_no = trim($acc_no);

		$tel = str_replace("-","",trim($_POST['tel']));
		$tel = str_replace(".","",$tel);
		$tel = str_replace(",","",$tel);

		$lineid = trim($_POST['lineid']);
		$password = trim($_POST['password']);
		$password = str_replace("\n", "", $password);
		$password = str_replace("\r", "", $password);
		$refer_id = trim($_POST['refer_id']);
		$refer_name = trim($_POST['refer_name']);
		$ip = trim($_POST['ip']);
		$aff = trim($_POST['aff']);
		$url = trim($_POST['url']);

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
			if($firstname != "" AND $bank_id != "" AND $bank_name != "" AND $acc_no != "" AND $tel != "" AND $lineid != "" AND $password != "" AND $refer_id != "" AND $refer_name != "" AND $ip != "")
			{
				$uppercase = preg_match('@[A-Z]@', $password);
				$lowercase = preg_match('@[a-z]@', $password);
				$number = preg_match('@[0-9]@', $password);
				$thai = preg_match('@[ก-๙]@', $password);
				if(!$uppercase || !$lowercase || !$number || $thai || strlen($password) < 8)
				{
					dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
				}
				else
				{
					if (preg_match('/^[a-zA-Z0-9]+$/', $password) AND preg_match('/^[0-9]+$/', $tel))
					{
						$q_1 = dd_q('SELECT * FROM user_tb WHERE (u_phone = ?)', [$tel]);
						if ($q_1->rowCount() == 0)
						{
							$q_2 = dd_q('SELECT * FROM user_tb WHERE (u_bank_number = ?)', [$acc_no]);
							if ($q_2->rowCount() == 0)
							{
								if (strlen($tel) == 10)
								{
									$username = "";
									$loopuser_stop = false;
									while (!$loopuser_stop)
									{
										$username = "9".generateRandomString(7);
										$q_loopuser = dd_q('SELECT * FROM user_tb WHERE u_agent_id  = ?', [$username]);
										if ($q_loopuser->rowCount() == 0)
										{
											$loopuser_stop = true;
										}
									}

									if($username != "")
									{
										$data = $api->createUser($username, $password, $firstname, $tel);
										if ($data->success == true)
										{
											$password_hash = password_encode($password);
											$q_3 = dd_q('INSERT INTO user_tb (u_user, u_password, u_agent_id, u_agent_pass, u_wallet, u_fname, u_bank_code, u_bank_name, u_bank_number, u_phone, u_line, u_refer_id, u_refer_name, u_aff, u_block_login, u_block_agent, u_create_on, u_date_create, u_time_create, u_ip, u_vip, u_token, u_last_login, u_limitcredit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
												$tel,
												$password_hash,
												$data->username,
												$password,
												0,
												$firstname,
												$bank_id,
												$bank_name,
												$acc_no,
												$tel,
												$lineid,
												$refer_id,
												$refer_name,
												$aff,
												0,
												0,
												date("Y-m-d H:i:s"),
												date("Y-m-d"),
												date("H:i:s"),
												$ip,
												'Normal',
												session_id(),
												date("Y-m-d H:i:s"),
												$row_website_tb['all_limitcredit']
											]);
											if ($q_3 == true)
											{
												$_SESSION['u_user'] = $tel;
												$loop_stop = false;
												while (!$loop_stop)
												{
													$code = generateRandomString(8);
													$q_4 = dd_q('SELECT * FROM gencode_tb WHERE g_code = ?', [$code]);
													if ($q_4->rowCount() == 0)
													{
														$loop_stop = true;
														$q_5 = dd_q('INSERT INTO gencode_tb (g_code, g_phone, g_use, g_create_by, g_create_date, g_date_create, g_time_create) VALUES (?, ?, ?, ?, ?, ?, ?)', [
															$code, 
															$tel, 
															'0', 
															'system', 
															date("Y-m-d H:i:s"),
															date("Y-m-d"),
															date("H:i:s")
														]);
														if($q_5 = true)
														{
															$q_6 = dd_q('UPDATE user_tb SET u_creditfree=? WHERE u_user=?', [
																$code,
																$tel
															]);
															if($q_6 = true)
															{
																//ส่ง line แจ้งเตือนสมัคร
																//notify_message("เบอร์ : ", $tel." สมัครสำเร็จ แก้ไขข้อความที่ ", $row_website_tb['lineregister']);
																notify_message("เบอร์ : ".$tel." สมัครสำเร็จ", $row_website_tb['lineregister']);

																write_log("register", $tel." - สมัครสมาชิกสำเร็จ ด้วย URL: ".urldecode($url), $ip);
																dd_return(true, "สมัครสมาชิกสำเร็จ");
															}
															else
															{
																write_log("register", $tel." - ไม่สามารถอัพเดทรายการ Code free ได้", $ip);
																dd_return(false, "เกิดข้อผิดพลาด");
															}
														}
														else
														{
															write_log("register", $tel." - ไม่สามารถสร้างรายการ Code free ได้", $ip);
															dd_return(false, "เกิดข้อผิดพลาด");
														}
													}
												}
											}
											else
											{
												write_log("register", "การสมัครสมาชิกล้มเหลว", $ip);
												dd_return(false, "เกิดข้อผิดพลาดทางระบบ");
											}
										}
										else
										{
											write_log("register", $data->message, $ip);
											dd_return(false, "ไม่สามารถสร้างข้อมูล User Game ได้ กรุณาติดต่อแอดมิน");
										}
									}
									else
									{
										dd_return(false, "ไม่สามารถสร้าง username ได้ กรุณาติดต่อแอดมิน");
									}
								}
								else
								{
									dd_return(false, "เบอร์โทรศัพท์ กรอกเลข 10 ตัวเท่านั้น");
								}
							}
							else
							{
								dd_return(false, "มีเลขบัญชีนี้ในระบบแล้ว");
							}
						}
						else
						{
							dd_return(false, "มีเบอร์โทรศัพท์นี้ในระบบแล้ว");
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
			dd_return(false, "ไม่สามารถใช้งานได้");
		}
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
