<?php
date_default_timezone_set("Asia/Bangkok");
ini_set('max_execution_time', 60);
require_once 'config.php';
require_once 'scb-common.php';
require_once 'Service/amb.php';
$api = new AMBAPI();

$q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['scb', 1]);
if ($q_1->rowCount() > 0)
{
	$row = $q_1->fetch(PDO::FETCH_ASSOC);

	if($row['a_bank_username'] != "" && $row['a_bank_password'] != "" && $row['a_bank_acc_number'] != "")
	{
		echo "เวลาที่ระบบออโต้ทำงาน: " . date('Y-m-d H:i:s');
		echo "<br>";
		$device_id = $row['a_bank_username'];
		$pin = $row['a_bank_password'];
		$acc_number = str_replace("-", "", $row['a_bank_acc_number']);

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $_SCBCONFIG['scb'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"Content-Type: application/json"
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$data = json_decode($response, true);

		if(array_key_exists('data', $data) && array_key_exists('status', $data))
		{
			if($data["data"] != "error" && array_key_exists('status', $data["data"]))
			{
				if(array_key_exists('code', $data["data"]["status"]) && array_key_exists('description', $data["data"]["status"]))
				{
					if($data["data"]["status"]["code"] == 1000)
					{
						if(array_key_exists('data', $data["data"]))
						{
							if(array_key_exists('txnList', $data["data"]["data"]))
							{
								foreach ($data["data"]["data"]["txnList"] as $val)
								{
									if(array_key_exists('txnCode', $val) && array_key_exists('txnDebitCreditFlag', $val))
									{
										if(array_key_exists('description', $val["txnCode"]) && array_key_exists('code', $val["txnCode"]))
										{
											if($val["txnCode"]["code"] == "X1" && $val["txnDebitCreditFlag"] == "C" && strpos($val["txnCode"]["description"], "ฝากเงิน") !== false)
											{
												$t_topup_bank = "";
												if (strpos($val["txnRemark"], "รับโอนจาก SCB") !== false)
												{
													$str_number = str_replace("รับโอนจาก SCB ", "", $val["txnRemark"]);
													$arr_str_number = explode(" ", $str_number);
													if(count($arr_str_number) > 0)
													{
														$t_topup_bank = strtolower($arr_str_number[0]);
													}
												}
												else if (strpos($val["txnRemark"], "Transfer from SCB") !== false)
												{
													$str_number = str_replace("Transfer from SCB ", "", $val["txnRemark"]);
													$arr_str_number = explode(" ", $str_number);
													if(count($arr_str_number) > 0)
													{
														$t_topup_bank = strtolower($arr_str_number[0]);
													}
												}
												else
												{
													$arr_str_number = explode("/", $val["txnRemark"]);
													if(count($arr_str_number) > 1)
													{
														$t_topup_bank = strtolower($arr_str_number[1]);
													}
												}
												
												$txnDateTime = new DateTime($val["txnDateTime"]);
												$t_date_create = $txnDateTime->format('Y-m-d');
												$t_time_create = $txnDateTime->format('H:i:s');
												$t_create_date = $txnDateTime->format('Y-m-d H:i:s');

												$t_tx_id = $val["txnRemark"]." (".$t_create_date.")";

												$txnAmount = str_replace(",", "", $val["txnAmount"]);
												$t_amount = number_format($txnAmount, 2, '.', '');

												$q_temp = dd_q('SELECT * FROM topup_temp_db WHERE t_topup_bank = ? AND t_amount = ? AND t_time_create = ? AND t_type_system = ?', [
													$t_topup_bank, 
													$t_amount, 
													$t_time_create, 
													"scb"
												]);
												if ($q_temp->rowCount() == 0)
												{
													$current_time = date('H:i:s');
													if($current_time >= $t_time_create && $t_date_create == date("Y-m-d"))
													{
														$q_t = dd_q('SELECT * FROM topup_db WHERE t_tx_id = ?', [$t_tx_id]);
														if ($q_t->rowCount() == 0)
														{
															if (strpos($val["txnRemark"], "รับโอนจาก SCB") !== false)
															{
																$q_u = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($t_topup_bank, -4)]);
															}
															else if (strpos($val["txnRemark"], "Transfer from SCB") !== false)
															{
																$q_u = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($t_topup_bank, -4)]);
															}
															else
															{
																$q_u = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($t_topup_bank, -6)]);
															}
															if ($q_u->rowCount() == 1)
															{
																$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

																$insertFalse = false;
																$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
																if ($_GetUserCredit->success == true)
																{
																	$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
																	if($_GetUserCredit['credit'] > 5) //ไม่รีโปร
																	{
																		$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",","",number_format($t_amount, 2, '.', '')));
																		if ($deposit->success == true)
																		{
																			$deposit = json_decode(json_encode($deposit->data), true);
																			$ExternalTransactionId = $deposit['ref'];
																			$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time, t_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u['u_id'], 
																				$row_u['u_user'], 
																				$row_u['u_agent_id'], 
																				$row_u['u_fname']." ".$row_u['u_lname'], 
																				$t_topup_bank, 
																				$t_tx_id, 
																				$t_amount, 
																				$row_u['u_bank_code'], 
																				$row_u['u_bank_number'], 
																				$row_u['u_bank_name'], 
																				$row['a_bank_code'], 
																				$row['a_bank_name'], 
																				$row['a_bank_acc_number'], 
																				'System', 
																				$t_create_date,
																				$t_date_create, 
																				$t_time_create, 
																				'1', 
																				'1', 
																				$deposit['before'], 
																				$deposit['after'], 
																				"scb",
																				date("Y-m-d"),
																				date('H:i:s'),
																				$ExternalTransactionId
																			]);
																			if($q_insert_t == true)
																			{
																				$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
																					'Vip', 
																					$row_u['u_id']
																				]);
																				if($q_update_u == true)
																				{
																					if(!empty($row_u['u_aff']))
																					{
																						$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
																						if ($q_t_aff->rowCount() == 1)
																						{
																							//% แนะนำเพื่อน
																							$aff_percent = $t_amount * $_CONFIG['aff_percent'];
																							dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status) VALUES (?, ?, ?, ?, ?)', [
																								$row_u['u_aff'], 
																								$row_u['u_user'], 
																								$t_amount, 
																								$aff_percent, 
																								'0'
																							]);
																						}
																					}
																					notify_message($row_u['u_user']." เติมเงินผ่าน SCB แบบไม่รีโปร ".number_format($t_amount,2,',','.')." บาท",$_CONFIG['token']);
																					echo $t_tx_id." - สำเร็จ status 1 (พบข้อมูล user)";
																					echo "<br>";
																				}
																			}
																		}
																		else
																		{
																			write_log("SCB API-Deposit-ไม่รีโปร", json_encode($deposit->message));
																			$insertFalse = true;
																			echo "deposit".$deposit->message;
																		}
																	}
																	else //รีโปร
																	{
																		$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",","",number_format($t_amount, 2, '.', '')));
																		if ($deposit->success == true)
																		{
																			$deposit = json_decode(json_encode($deposit->data), true);
																			$ExternalTransactionId = $deposit['ref'];
																			dd_q('UPDATE transfergame_tb SET t_active=? WHERE t_user=?', [
																				"N", 
																				$row_u['u_user']
																			]);

																			$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time, t_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u['u_id'], 
																				$row_u['u_user'], 
																				$row_u['u_agent_id'], 
																				$row_u['u_fname']." ".$row_u['u_lname'], 
																				$t_topup_bank, 
																				$t_tx_id, 
																				$t_amount, 
																				$row_u['u_bank_code'], 
																				$row_u['u_bank_number'], 
																				$row_u['u_bank_name'], 
																				$row['a_bank_code'], 
																				$row['a_bank_name'], 
																				$row['a_bank_acc_number'], 
																				'System', 
																				$t_create_date,
																				$t_date_create, 
																				$t_time_create, 
																				'1', 
																				'1', 
																				$deposit['before'], 
																				$deposit['after'], 
																				"scb",
																				date("Y-m-d"),
																				date('H:i:s'),
																				$ExternalTransactionId
																			]);
																			if($q_insert_t == true)
																			{
																				$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
																					'Vip', 
																					$row_u['u_id']
																				]);
																				if($q_update_u == true)
																				{
																					if(!empty($row_u['u_aff']))
																					{
																						$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
																						if ($q_t_aff->rowCount() == 1)
																						{
																							//% แนะนำเพื่อน
																							$aff_percent = $t_amount * $_CONFIG['aff_percent'];
																							dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status) VALUES (?, ?, ?, ?, ?)', [
																								$row_u['u_aff'], 
																								$row_u['u_user'], 
																								$t_amount, 
																								$aff_percent, 
																								'0'
																							]);
																						}
																					}
																					notify_message($row_u['u_user']." เติมเงินผ่าน SCB แบบรีโปร ".number_format($t_amount,2,'.',',')." บาท",$_CONFIG['token']);
																					echo $t_tx_id." - สำเร็จ status 1 (พบข้อมูล user)";
																					echo "<br>";
																				}
																			}
																		}
																		else
																		{
																			write_log("SCB API-Deposit-รีโปร", json_encode($deposit->message));
																			$insertFalse = true;
																			echo "deposit".$deposit->message;
																		}
																	}
																}
																else
																{
																	write_log("SCB API-GetUserCredit", json_encode($_GetUserCredit));
																	$insertFalse = true;
																	echo "_GetUserCredit - ".json_encode($_GetUserCredit);
																}

																if($insertFalse == true) //api error
																{
																	$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																		$row_u['u_id'], 
																		$row_u['u_user'], 
																		$row_u['u_agent_id'], 
																		$row_u['u_fname']." ".$row_u['u_lname'], 
																		$t_topup_bank, 
																		$t_tx_id, 
																		$t_amount, 
																		$row_u['u_bank_code'], 
																		$row_u['u_bank_number'], 
																		$row_u['u_bank_name'], 
																		$row['a_bank_code'], 
																		$row['a_bank_name'], 
																		$row['a_bank_acc_number'], 
																		'System', 
																		$t_create_date,
																		$t_date_create, 
																		$t_time_create, 
																		'2', 
																		'1', 
																		$t_amount, 
																		$t_amount,
																		"scb",
																		date("Y-m-d"),
																		date('H:i:s')
																	]);
																	notify_message($row_u['u_user']." เติมเงินผ่าน SCB".number_format($t_amount,2,'.',',')." บาท แต่ API AMB Error กรุณายืนยันรายการฝากอีกครั้ง ตรวจสอบข้อผิดพลาดได้ที่ Log",$_CONFIG['token']);
																}
															}
															else
															{
																$q_insert_t = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_type_system, t_system_create_date, t_system_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
																	'', 
																	'', 
																	'', 
																	'', 
																	$t_topup_bank, 
																	$t_tx_id, 
																	$t_amount, 
																	'', 
																	'', 
																	'', 
																	$row['a_bank_code'], 
																	$row['a_bank_name'], 
																	$row['a_bank_acc_number'], 
																	'System', 
																	$t_create_date,
																	$t_date_create, 
																	$t_time_create, 
																	'2', 
																	'1', 
																	0, 
																	0,
																	"scb",
																	date("Y-m-d"),
																	date('H:i:s')
																]);
																if($q_insert_t == true)
																{
																	notify_message("เติมเงินผ่าน SCB".number_format($t_amount,2,'.',',')." บาท แต่ข้อมูล user ผิดพลาด กรุณาตรวจสอบรายการฝาก",$_CONFIG['token']);
																	echo $t_tx_id." - สำเร็จ status 2 (ไม่พบข้อมูล user หรือ มีมากกว่า 1 user)";
																	echo "<br>";
																}
															}
														}
													}
													else
													{
														dd_q('INSERT INTO topup_temp_db (t_topup_bank, t_tx_id, t_amount, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_create_date, t_date_create, t_time_create, t_status, t_add_datetime, t_update_datetime, t_type_system) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
															$t_topup_bank, 
															$t_tx_id, 
															$t_amount, 
															$row['a_bank_code'], 
															$row['a_bank_name'], 
															$row['a_bank_acc_number'], 
															$t_create_date, 
															$t_date_create, 
															$t_time_create, 
															'2', 
															date('Y-m-d H:i:s'), 
															date('Y-m-d H:i:s'), 
															"scb"
														]);
													}
												}
											}
										}
									}
								}
								dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
									date('Y-m-d H:i:s'),
									'1',
									'scb'
								]);
							}
							else
							{
								echo "ไม่พบข้อมูลรายการฝากของวันนี้ (txnList)";
								dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
									date('Y-m-d H:i:s'),
									'0',
									'scb'
								]);
							}
						}
						else
						{
							echo "ไม่พบข้อมูลรายการฝากของวันนี้ (result)";
							dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
								date('Y-m-d H:i:s'),
								'0',
								'scb'
							]);
						}
					}
					else
					{
						echo $data["data"]["status"]["description"];
						dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
							date('Y-m-d H:i:s'),
							'0',
							'scb'
						]);
					}
				}
				else
				{
					echo "ไม่พบข้อมูล code หรือ description";
					dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
						date('Y-m-d H:i:s'),
						'0',
						'scb'
					]);
				}
			}
			else
			{
				echo "ไม่พบข้อมูล status";
				dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
					date('Y-m-d H:i:s'),
					'0',
					'scb'
				]);
			}
		}
		else
		{
			echo "ไม่พบข้อมูล data";
			dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
				date('Y-m-d H:i:s'),
				'0',
				'scb'
			]);
		}
	}
	else
	{
		echo "ไม่พบข้อมูล device_id หรือ pin หรือ เลขบัญชีธนาคาร";
		dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
			date('Y-m-d H:i:s'),
			'0',
			'scb'
		]);
	}
}
else
{
	echo "ระบบ auto ปิดใช้งาน";
	dd_q('UPDATE autobank_tb SET a_bank_update=?, a_bank_run=? WHERE a_bank_code=?', [
		date('Y-m-d H:i:s'),
		'0',
		'scb'
	]);
}
?>