<?php

ob_start();

session_start();

date_default_timezone_set("Asia/Bangkok");

header ("Last-Modified: " . gmdate ("D, d M Y H:i:s") . " GMT");



function base_url()

{

  // return "https" . "://$_SERVER[HTTP_HOST]"."";

  return "http" . "://$_SERVER[HTTP_HOST]"."/t-DeDslot88.com"; // ใช้สำหรับ test localhost

  // return "https" . "://$_SERVER[HTTP_HOST]".""; // ใช้ตอนขึ้นเครื่องจริง

}



function get_client_ip()

{

  $ipaddress = '';

  if (getenv('HTTP_CLIENT_IP'))

    $ipaddress = getenv('HTTP_CLIENT_IP');

  else if(getenv('HTTP_X_FORWARDED_FOR'))

    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');

  else if(getenv('HTTP_X_FORWARDED'))

    $ipaddress = getenv('HTTP_X_FORWARDED');

  else if(getenv('HTTP_FORWARDED_FOR'))

    $ipaddress = getenv('HTTP_FORWARDED_FOR');

  else if(getenv('HTTP_FORWARDED'))

    $ipaddress = getenv('HTTP_FORWARDED');

  else if(getenv('REMOTE_ADDR'))

    $ipaddress = getenv('REMOTE_ADDR');

  else

    $ipaddress = 'UNKNOWN';

  return $ipaddress;

}



function password_encode($string)

{

  $ciphering = "AES-128-CTR";

  $encryption_key = "BS539TDGZF3ND71";

  $options = 0;

  $encryption_iv = '1234567891011121';

  $encrypted = openssl_encrypt($string, $ciphering, $encryption_key, $options, $encryption_iv); 

  return $encrypted;

}



function password_decode($string)

{

  $ciphering = "AES-128-CTR";

  $decryption_key = "BS539TDGZF3ND71";

  $options = 0;

  $decryption_iv = '1234567891011121';

  $decrypted = openssl_decrypt ($string, $ciphering, $decryption_key, $options, $decryption_iv); 

  return $decrypted;

}



function generateRandomString($length = 10)

{

  $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

  $charactersLength = strlen($characters);

  $randomString = '';

  for ($i = 0; $i < $length; $i++)

  {

    $randomString .= $characters[rand(0, $charactersLength - 1)];

  }

  return $randomString;

}



function generateStringAllCase($length = 5)

{

  $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

  $charactersLength = strlen($characters);

  $randomString = '';

  for ($i = 0; $i < $length; $i++)

  {

    $randomString .= $characters[rand(0, $charactersLength - 1)];

  }

  return $randomString;

}



function generateRandomInt($length = 6)

{

  $characters = '0123456789';

  $charactersLength = strlen($characters);

  $randomString = '';

  for ($i = 0; $i < $length; $i++)

  {

    $randomString .= $characters[rand(0, $charactersLength - 1)];

  }

  return $randomString;

}





class DB

{

  protected static $str_hosting = 'localhost'; // แก้ไขได้

  protected static $str_database = 'paylega_aqua783'; // แก้ไขได้

  protected static $str_username = 'root'; // แก้ไขได้

  protected static $str_password = ''; // แก้ไขได้



  protected static $pdo = null;

  public static function getConnection()

  {

    // initialize $pdo on first call

    if (self::$pdo == null)

    {

      self::init();

    }



    // now we should have a $pdo, whether it was initialized on this call or a previous one

    // but it could have experienced a disconnection

    try

    {

      // echo "Testing connection...\n";

      $old_errlevel = error_reporting(0);

      self::$pdo->query("SELECT 1");

    }

    catch (PDOException $e)

    {

      self::init();

    }

    error_reporting($old_errlevel);

    return self::$pdo;

  }



  protected static function init()

  {

    try

    {

      self::$pdo = new PDO("mysql:host=".self::$str_hosting.";dbname=".self::$str_database, self::$str_username, self::$str_password);

      self::$pdo->exec("set names utf8");

      self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    catch (PDOException $e)

    {

      die($e->getMessage());

    }

  }

}



function dd_q($str, $arr = [])

{

  $pdo = DB::getConnection();

  try

  {

    $exec = $pdo->prepare($str);

    if (!$exec->execute($arr))

    {

      write_log("dd_q->exec", $exec->errorInfo(), "");

    }

  }

  catch (PDOException $e)

  {

    write_log("dd_q->PDOException", $e->getMessage(), "");

    return false;

  }

  return $exec;

}



function check_login_disabled()

{

  if(isset($_SESSION['u_user']))

  {

    $show = "";

  }

  else

  {

    $show = "disabled";

  }

  return $show;

}



function check_login_text($text)

{

  if(isset($_SESSION['u_user']))

  {

    $show = $text;

  }

  else

  {

    $show = "เข้าสู่ระบบ";

  }

  return $show;

}



function get_session()

{

  if(isset($_SESSION['u_user']))

  {

    $user = $_SESSION['u_user'];

  }

  else

  {

    $user = "";

  }

  return $user;

}



function get_wallet($col_name)

{

  $q_credit = dd_q('SELECT * FROM user_tb WHERE u_user = ? LIMIT 1', [$_SESSION['u_user']]);

  $row_credit = $q_credit->fetch(PDO::FETCH_ASSOC);

  return $row_credit["$col_name"];

}



function write_log($page_name, $detail, $ip)

{

  $q_log = dd_q('INSERT INTO log_tb (l_page, l_detail, l_create_date, l_ip, l_create_by) VALUES (?, ?, ?, ?, ?)', [

              $page_name,

              $detail,

              date("Y-m-d H:i:s"),

              $ip,

              get_session()

            ]);

  return $q_log;

}

$_CONFIG = array();

function notify_message($message, $wallettoken = "")

{

  if($wallettoken == "")

  {

    $q_u = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);

    $row_u = $q_u->fetch(PDO::FETCH_ASSOC);

    $token = $row_u['tokenline']; //ใส่ Token line n zAsIWCNDeZ2jYsuirykx97NIoPUH6vPp9RTqqTwjcL2

  }

  else

  {

    $token = $wallettoken;

  }

  

  $chOne = curl_init(); 

  curl_setopt($chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 

  curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0); 

  curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0); 

  curl_setopt($chOne, CURLOPT_POST, 1); 

  curl_setopt($chOne, CURLOPT_POSTFIELDS, "message=".$message); 

  $headers = array('Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$token.'',);

  curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 

  curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1); 

  $result = curl_exec($chOne); 



  //Result error

  $resMsg = "";

  if(curl_error($chOne)) 

  { 

    $resMsg = 'error: '.curl_error($chOne); 

  } 

  else { 

    $result_ = json_decode($result, true); 

    $resMsg = "status: ".$result_['status']." message: ".$result_['message'];

  } 

  curl_close($chOne);

  return $resMsg;

}



$q_u = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);

$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

//ชื่อเว็บ

$_CONFIG['domain_name'] = $row_u['namesite']; // แก้ไขได้



//title หัวเว็บ

$_CONFIG['title'] = $row_u['title']; // แก้ไขได้



//title หัวเว็บ

$_CONFIG['lineid'] = $row_u['line']; // แก้ไขได้



//title หัวเว็บ

$_CONFIG['urlline'] = $row_u['lineurl']; // แก้ไขได้



//copyright หน้า login

$_CONFIG['copyright'] = $row_u['copyright'];//"©copyright 2019-2020 powered by <a href='https://betmix357.com/' target='_blank'>BETMIX357.COM</a>"; // แก้ไขได้



// ตั้งค่าคำนำหน้า user

$_CONFIG['start_prefix'] = ""; // แก้ไขได้



// แก้ไขประกาศในหน้า member

$_CONFIG['member_post'] = $row_u['post']; // แก้ไขได้



// ถอนขั้นต่ำ

$_CONFIG['min_withdraw'] = $row_u['min_withdraw']; // แก้ไขได้



//Google reCAPTCHA Site V2

$_CONFIG['sitekey'] = $row_u['recaptchakey']; // แก้ไขได้



//Google reCAPTCHA Secret V2

$_CONFIG['secretkey'] = $row_u['recapchasecret']; // แก้ไขได้



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

?>