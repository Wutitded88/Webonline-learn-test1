<?php
    class SCB {
        private $deviceid;
        private $pin;
        private $acc_num;
        private $token_id;

        public function __construct($deviceid, $pin, $acc_num){
            if(preg_match('/^[0-9]{6,6}$/', $pin)){
                $this->deviceid = $deviceid;
                $this->pin = $pin;
                $this->acc_num = $acc_num;
            }else{
                die('CHECK DATA!!');
            }
        }

        public function login(){
            $curl = curl_init();
            curl_setopt_array($curl, array(
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
            if ($Auth == "") {
                die('api Auth error!');
            }else{
                $this->auth0 = $Auth[0][0];
            }


            $curl = curl_init();
            curl_setopt_array($curl,
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
            $data = json_decode($response,true);
            
            $this->dataPre = [
                'hashType' => $data['e2ee']['pseudoOaepHashAlgo'],
                'Sid' => $data['e2ee']['pseudoSid'],
                'ServerRandom' => $data['e2ee']['pseudoRandom'],
                'pubKey' => $data['e2ee']['pseudoPubKey'],
            ];
            


            $curl = curl_init();
            curl_setopt_array($curl, 
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
            $this->pseudoPin = $response;
        


            $curl = curl_init();
            curl_setopt_array($curl, 
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
            $Auth1 = $Auth_result[0][0];
            if($Auth1 == "") {
                die('error Auth!!');
            }else{
                $this->api_auth = $Auth1;
            }
            
        }

        //ตรวจสอบสลิปด้วย QRcode (ต้องหาทาง Decypt Qr ให้ได้ก่อน)
        public function qr_scan($barcode){
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
            if (curl_errno($curl)) {
                return ['status' => false, 'msg' => 'ผิดพลาด curl'];
            }
            return json_decode($result, true);
        }

    }
