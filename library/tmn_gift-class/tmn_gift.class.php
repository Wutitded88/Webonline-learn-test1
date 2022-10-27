<?php
class tmn_gift
{
  public function verify($url)
  {
    $hash = $this->hashcode($url);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://gift.truemoney.com/campaign/vouchers/'.$hash.'/verify');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $result = curl_exec($ch);
    return $result;
    curl_close($ch);
  }
  
  public function hashcode($code)
  {
    return explode("?v=",$code)["1"];    
  }

  public function redeem($hash, $phone)
  {
    $hash = $this->hashcode($hash);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://gift.truemoney.com/campaign/vouchers/'.$hash.'/redeem');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"mobile":"'.$phone.'","voucher_hash":"'.$hash.'"}');

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}
?>