<?php
class tmn_gift
{
  function verify($url)
  {
    $fields = [
      'type' => "verify",
      'link' => $url,
      'resiveNumber'  => "000000000"
    ];
    $postData = json_encode($fields);

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api-aqua.com/tmn_gift-class/api.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));
    $response = curl_exec($curl);
    $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
    curl_close($curl);
    return $response;
  }
  
  function hashcode($code)
  {
    return explode("?v=",$code)["1"];    
  }

  function redeem($url, $phone)
  {
    $fields = [
      'type' => "redeem",
      'link' => $url,
      'resiveNumber'  => $phone
    ];
    $postData = json_encode($fields);

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api-aqua.com/tmn_gift-class/api.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));
    $response = curl_exec($curl);
    $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
    return $response;
  }
}
?>