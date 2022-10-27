<?php

ob_start();

session_start();

date_default_timezone_set("Asia/Bangkok");

header ("Last-Modified: " . gmdate ("D, d M Y H:i:s") . " GMT");



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



class DB

{

  protected static $str_hosting = 'localhost'; // แก้ไขได้

  protected static $str_database = 'paylega_aqua783'; // แก้ไขได้

  protected static $str_username = 'paylega_aqua783'; // แก้ไขได้

  protected static $str_password = 'RNeH5W2832lV'; // แก้ไขได้



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

    $exec->execute($arr);

  }

  catch (PDOException $e)

  {

    return false;

  }

  return $exec;

}

$q_u = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);

$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

$_CONFIG = array();

// เปอร์เซ็นชวนเพื่อน

$_CONFIG['aff_percent'] = $row_u['affpersen']; // แก้ไขได้

$_CONFIG['token'] = $row_u['linewallet']; // แก้ไขได้



function write_log($page_name, $detail)

{

  $q_log = dd_q('INSERT INTO log_tb (l_page, l_detail, l_create_date, l_create_by) VALUES (?, ?, ?, ?)', [

              $page_name,

              $detail,

              date("Y-m-d H:i:s"),

              "SCB API"

            ]);

  return $q_log;

}



function notify_message($message, $token = "")

{

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

?>