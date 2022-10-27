<?php
class thsms
{
  public function send($username, $password, $phone, $message='')
  {
    if($username != '' && $password != '')
    {
      $params = array();
      $params['method']   = 'send';
      $params['username'] = $username;
      $params['password'] = $password;
   
      $params['from']     = 'SMSOTP';
      $params['to']       = $phone;
      $params['message']  = $message;
   
      $result = $this->curl( $params);
      $xml = @simplexml_load_string( $result);
      if (!is_object($xml))
      {
        return 'Respond error';
      }
      else
      {
        if ($xml->send->status == 'success')
        {
          return $xml->send->status;
        }
        else
        {
          return $xml->send->message;
        }
      }
    }
    else
    {
      return 'Please setting username and password api thsms.';
    }
  }
     
  private function curl( $params=array())
  {
    $api_url = 'http://www.thsms.com/api/rest';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
    $response  = curl_exec($ch);
    $lastError = curl_error($ch);
    $lastReq = curl_getinfo($ch);
    curl_close($ch);
 
    return $response;
  }
}
?>