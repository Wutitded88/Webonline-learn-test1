<?php
require_once 'config.php';
require_once 'scbClass.php';
$q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['scb', 1]);

$row = $q_1->fetch(PDO::FETCH_ASSOC);
$_deviceid = $row['a_bank_username'];
$_pin = $row['a_bank_password'];
$_acc_num = str_replace("-", "", $row['a_bank_acc_number']);
$apiSCB = new SCB($_deviceid, $_pin, $_acc_num);
$apiSCB->login();

//$stmt = $apiSCB->getTransaction();
//print_r($stmt);
/*for ($x = 0; $x <= 30; $x++) {
        echo "<pre>";
		print_r($data['data']['txnList'][$x]['txnRemark']);
        print_r($data['data']['txnList'][$x]['txnCode']['description']);
}*/
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <script src="https://kit.fontawesome.com/adb36dfc51.js" crossorigin="anonymous"></script>


<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300&display=swap" rel="stylesheet">


  </head>
  <style>
  body {
	font-family: 'Prompt', sans-serif;
    background: #000;
	}
	.h1,h2,h3,h4,h5,h6 {
	font-family: 'Prompt', sans-serif;
	}
    .box {
        width:500px;
        padding: 15px;
        color:#fff;
        background: #404040;
        border-radius: 10px;
        margin: auto;
}
.box1 {
        width:500px;
        padding: 15px;
        color:#fff;
        background: #3c2a00;
        border-radius: 10px;
        margin: auto;
}
.boxmoney-success {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 95%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    border-radius: 0.25rem;
    color: #fff;
    background-color: #28a745;

}
.boxmoney-danger {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 95%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    border-radius: 0.25rem;
    color: #fff;
    background-color: #9d000e;

}
  </style>
  <body>
<?php   
$datab = $apiSCB->summary();
echo "<div class='box1'>";

echo("จำนวนเงินในบัญชี : ".$datab['totalAvailableBalance']." <br>");
echo "</div>";
?>

<?php 
//ฟังชั่นแปลง วันที่และ เวลาเป็นไทย 
$dayTH = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
$monthTH = [null,'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
$monthTH_brev = [null,'ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
function thai_date_and_time($time){   // 19 ธันวาคม 2556 เวลา 10:10:43
    global $dayTH,$monthTH;   
    $thai_date_return = date("j",$time);   
    $thai_date_return.=" ".$monthTH[date("n",$time)];   
    $thai_date_return.= " ".(date("Y",$time)+543);   
    //$thai_date_return.= " เวลา ".date("H:i:s",$time);
    return $thai_date_return;   
} 
function thai_date_and_time_short($time){   // 19  ธ.ค. 2556 10:10:4
    global $dayTH,$monthTH_brev;   
    $thai_date_return = date("j",$time);   
    $thai_date_return.=" ".$monthTH_brev[date("n",$time)];   
    $thai_date_return.= " ".(date("Y",$time)+543);   
    $thai_date_return.= " ".date("H:i:s",$time);
    return $thai_date_return;   
} 
function thai_date_short($time){   // 19  ธ.ค. 2556a
    global $dayTH,$monthTH_brev;   
    $thai_date_return = date("j",$time);   
    $thai_date_return.=" ".$monthTH_brev[date("n",$time)];   
    $thai_date_return.= " ".(date("Y",$time)+543);   
    return $thai_date_return;   
} 
function thai_date_fullmonth($time){   // 19 ธันวาคม 2556
    global $dayTH,$monthTH;   
    $thai_date_return = date("j",$time);   
    $thai_date_return.=" ".$monthTH[date("n",$time)];   
    $thai_date_return.= " ".(date("Y",$time)+543);   
    return $thai_date_return;   
} 
function thai_date_short_number($time){   // 19-12-56
    global $dayTH,$monthTH;   
    $thai_date_return = date("d",$time);   
    $thai_date_return.="-".date("m",$time);   
    $thai_date_return.= "-".substr((date("Y",$time)+543),-2);   
    return $thai_date_return;   
} 

$data = $apiSCB->getTransaction();
 
for ($x = 0; $x <= 49; $x++) {

$txnDateTime = new DateTime($data['data']['txnList'][$x]["txnDateTime"]);
$t_date_create = $txnDateTime->format('Y-m-d');
$t_time_create = $txnDateTime->format('H:i:s');
$t_create_date = $txnDateTime->format('Y-m-d H:i:s');
$t_tx_idday = "".$t_date_create."";
$t_tx_id = "".$t_time_create."";


?>
<div style="padding-top:10px">
		<div class="box" style="<?php if($data['data']['txnList'][$x]['txnCode']['description'] == "ฝากเงิน") { echo "background:#055400;"; } else { echo "background:#540000;"; } ?>">
            <p>รายการที่ : <span><?php echo $x; ?></span></p>
            <p>จำนวน : <span class="boxmoney-<?php if($data['data']['txnList'][$x]['txnCode']['description'] == "ฝากเงิน") { echo "success"; } else { echo "danger"; } ?>"><?php echo($data['data']['txnList'][$x]['txnAmount']); ?> บาท</span></p>
            <p>จาก : <span><?php echo($data['data']['txnList'][$x]['txnRemark']); ?></span></p>
			<p>วันที่ : <span><?=thai_date_and_time(strtotime($t_tx_idday)); ?></span></p>
            <p>เวลา : <span><?php echo $t_tx_id; ?></span></p>
            <p>รายการ : <span style="<?php if($data['data']['txnList'][$x]['txnCode']['description'] == "ฝากเงิน") { echo "color:#00a715;"; } else { echo "color:#ff0000;"; } ?>"><?php echo($data['data']['txnList'][$x]['txnCode']['description']); ?></span></p>
        </div>
        </div>
<?php } ?>
 
 </body>