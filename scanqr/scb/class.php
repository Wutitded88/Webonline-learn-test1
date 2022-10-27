<?php
class SCB
{
    private $deviceid;
    private $pin;
    private $acc_num;
    private $token_id;

    public function __construct($deviceid, $pin, $acc_num)
    {
        $res = new stdClass();
        if(preg_match('/^[0-9]{6,6}$/', $pin))
        {
            $this->deviceid = $deviceid;
            $this->pin = $pin;
            $this->acc_num = $acc_num;
        }
        else
        {
            $res->success=false;
            $res->message='CHECK DATA!!';
            return $res;
        }
    }

    public function login()
    {
        $res = new stdClass();

        $curl = curl_init();
        curl_setopt_array(
            $curl, 
            array(
                CURLOPT_URL => 'https://fasteasy.scbeasy.com:8443/v3/login/preloadandresumecheck',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_HEADER=> 1,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"deviceId":"'.$this->deviceid.'","jailbreak":"0","tilesVersion":"41","userMode":"INDIVIDUAL"}',
                CURLOPT_HTTPHEADER => array(
                    'Accept-Language:      th',
                    'user-agent:        Android/10;FastEasy/3.44.0/4739',
                    'Content-Type:  application/json; charset=UTF-8',
                ),
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        preg_match_all('/(?<=Api-Auth: ).+/', $response, $Auth);
        if ($Auth == "")
        {
            $res->success=false;
            $res->message='api Auth error!';
            return $res;
        }
        else
        {
            $this->auth0 = $Auth[0][0];

            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://fasteasy.scbeasy.com/isprint/soap/preAuth',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{"loginModuleId":"PseudoFE"}',
                    CURLOPT_HTTPHEADER => array(
                        'Api-Auth: '.$this->auth0,
                        'Content-Type: application/json',
                    ),
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response, true);

            if(!is_null($data) && $data != "")
            {
                if(array_key_exists('e2ee', $data))
                {
                    if(array_key_exists('pseudoOaepHashAlgo', $data['e2ee']) && array_key_exists('pseudoSid', $data['e2ee']) && array_key_exists('pseudoRandom', $data['e2ee']) && array_key_exists('pseudoPubKey', $data['e2ee']))
                    {
                        $this->dataPre = [
                            'hashType' => $data['e2ee']['pseudoOaepHashAlgo'],
                            'Sid' => $data['e2ee']['pseudoSid'],
                            'ServerRandom' => $data['e2ee']['pseudoRandom'],
                            'pubKey' => $data['e2ee']['pseudoPubKey'],
                        ];

                        $curl = curl_init();
                        curl_setopt_array(
                            $curl, 
                            array(
                                CURLOPT_URL => "http://174.138.25.250:3000/pin/encrypt",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS => "Sid=".$this->dataPre['Sid']."&ServerRandom=".$this->dataPre['ServerRandom']."&pubKey=".$this->dataPre['pubKey']."&pin=".$this->pin."&hashType=".$this->dataPre['hashType'],
                                CURLOPT_HTTPHEADER => array(
                                    "Content-Type: application/x-www-form-urlencoded"
                                ),
                            )
                        );
                        $response = curl_exec($curl);
                        curl_close($curl);
                        if(!is_null($response) && $response != "")
                        {
                            $this->pseudoPin = $response;

                            $curl = curl_init();
                            curl_setopt_array(
                                $curl, 
                                array(
                                    CURLOPT_URL => 'https://fasteasy.scbeasy.com/v3/login',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_HEADER=> 1,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS =>'{"deviceId":"'.$this->deviceid.'","pseudoPin":"'.$this->pseudoPin.'","pseudoSid":"'.$this->dataPre['Sid'].'"}',
                                    CURLOPT_HTTPHEADER => 
                                    array(
                                        'Api-Auth: '.$this->auth0,
                                        'Content-Type: application/json'
                                    ),  
                                )
                            );
                            $response = curl_exec($curl);
                            curl_close($curl);
                            preg_match_all('/(?<=Api-Auth:).+/', $response, $Auth_result);

                            if(!empty($Auth_result) && !is_null($Auth_result))
                            {
                                if(!empty($Auth_result[0]) && !is_null($Auth_result[0]))
                                {
                                    $Auth1 = $Auth_result[0][0];
                                    if($Auth1 == "")
                                    {
                                        $res->success=false;
                                        $res->message='error Auth!!';
                                        return $res;
                                    }
                                    else
                                    {
                                        $this->api_auth = $Auth1;

                                        $res->success=true;
                                        $res->message='success Auth!!';
                                        return $res;
                                    }
                                }
                                else
                                {
                                    $res->success=false;
                                    $res->message='Auth_result[0] null!!';
                                    return $res;
                                }
                            }
                            else
                            {
                                $res->success=false;
                                $res->message='Auth_result null!!';
                                return $res;
                            }
                        }
                        else
                        {
                            $res->success=false;
                            $res->message='api url /pin/encrypt get data null!';
                            return $res;
                        }
                    }
                    else
                    {
                        $res->success=false;
                        $res->message='api url /isprint/soap/preAuth get data->e2ee object name pseudoOaepHashAlgo, pseudoSid, pseudoRandom, pseudoPubKey null!';
                        return $res;
                    }
                }
                else
                {
                    $res->success=false;
                    $res->message='api url /isprint/soap/preAuth get data object name e2ee null!';
                    return $res;
                }
            }
            else
            {
                $res->success=false;
                $res->message='api url /isprint/soap/preAuth get data null!';
                return $res;
            }
        }
    }

    //ตรวจสอบสลิปด้วย QRcode (ต้องหาทาง Decypt Qr ให้ได้ก่อน)
    public function qr_scan($barcode)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://fasteasy.scbeasy.com/v7/payments/bill/scan");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array("barcode" => $barcode, "tilesVersion" => "41")));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Api-Auth: ' . $this->api_auth;
        $headers[] = 'Accept-Language: th';
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);
        if (curl_errno($curl))
        {
            return ['status' => false, 'msg' => 'ผิดพลาด curl'];
        }
        return json_decode($result, true);
    }
}
