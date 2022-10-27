<?php 
	require 'config.php';
	require 'db.php';
	require 'scb/class.php';
	require_once __DIR__.'/vendor/autoload.php';



	$login = new SCB($config['deviceid'], $config['pin'], $config['acc_num']);
	$login->login();

	use Zxing\QrReader;

	$path = "img/";
	$type = explode("/", $_FILES['image']['type']);
	if ($type[0] == "image") {

		$qrcode = new QrReader($_FILES["image"]["tmp_name"]);
		$text = $qrcode->text();
		$newname = $text.'.'.$type[1];
		$data = $login->qr_scan($text);

		$date = date_create($data['data']['pullSlip']['dateTime']);
		$date = date_format($date,"Y/m/d H:i:s");

		$check = $data['data']['pullSlip']['receiver']['name'];
		if (strpos($check, 'สมยศ เขียวตาด') !== false) {
			$checkUser = $db->prepare("SELECT * FROM image WHERE transRef = ?");
			$checkUser->execute(array($data['data']['pullSlip']['transRef']));
			if($checkUser->rowCount() > 0){
				echo "ใช้ไปแล้ว";
			}else{
				$stmt = $db->prepare("INSERT INTO image (amount, transRef, date) VALUES (:amount, :transRef, :date)");
				try {
					$stmt->execute([':amount' => $data['data']['amount'], ':transRef' => $data['data']['pullSlip']['transRef'], ':date' =>  $date]);
					echo "สำเร็จ";
				} catch (Exception $e) {
					echo "ผิดพลาด คิวรี่ไม่ผ่าน";
					
				}
			}
		}else{
			echo "ผู้รับเงินไม่ตรงกับที่ระบบกำหนดไว้";
		}
		
	}else{
		echo "อัพโหลดได้เฉพาะรูปภาพเท่านั้น";
	}
?>