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
            curl_setopt_array($curl, 
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
                        'user-agent:        Android/10;FastEasy/3.53.0/5618',
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
                    CURLOPT_URL => 'https://fasteasy.scbeasy.com/v1/fasteasy-login',
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

        public function transac_merchants($walletID){
            $startDate = date("Y-m-d", strtotime("first day of january this year"));
            $endDate = date('Y-m-d', strtotime("+1 day"));
            $pageNumber = 1;
            $pageSize = 50;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v1/merchants/transactions');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"walletList\":[{\"endDate\":\"".$endDate."\",\"pageNumber\":\"".$pageNumber."\",\"pageSize\":\"".$pageSize."\",\"startDate\":\"".$startDate."\",\"walletId\":\"".$walletID."\"}]}\"");
            curl_setopt($curl, CURLOPT_POST, 1);
            $headers = array();
            $headers[] = 'Api-Auth: ' . $this->api_auth;
            $headers[] = 'Accept-Language: th';
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                return array();
            }
            return json_decode($result, true);
        }


        public function summary(){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fasteasy.scbeasy.com/v2/deposits/summary",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\"depositList\":[{\"accountNo\":\"".$this->acc_num."\"}],\"numberRecentTxn\":2,\"tilesVersion\":\"26\"}",
            CURLOPT_HTTPHEADER => array(
            'Api-Auth: ' .$this->api_auth,
            'Accept-Language: th',
            'Content-Type: application/json; charset=UTF-8',
            ),
            ));
            $response = curl_exec($curl);
            $data = json_decode($response,true);
            return $data;
        }


        public function getTransaction(string $startDate = null, string $endDate = null, int $pageSize = null, int $pageNumber = null){
            if ($startDate === null) {
                $startDate = date('Y-m-d');
                // $startDate = '2021-07-29';
            }
            if ($endDate === null) {
                $endDate = date('Y-m-d', strtotime("+1 day"));
                // $endDate = '2021-07-29';
            }
            if ($pageNumber === null) {
                $pageNumber = 1;
            }
            if ($pageSize === null) {
                $pageSize = 50;
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v2/deposits/casa/transactions');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"accountNo\":\"".$this->acc_num."\",\"endDate\":\"$endDate\",\"pageNumber\":\"$pageNumber\",\"pageSize\":$pageSize,\"productType\":\"2\",\"startDate\":\"$startDate\"}");
            curl_setopt($curl, CURLOPT_POST, 1);
    
            $headers = array();
            $headers[] = 'Api-Auth: ' . $this->api_auth;
            $headers[] = 'Accept-Language: th';
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                return array();
            }
            // print_r($result);
            $json = json_decode($result, true);
            // return $json;
            if (isset($json['status'])) {
                if ($json['status']['description'] === 'สำเร็จ') {
                    return $json;
                }
            }
            return array();
        }

        //ค้นหาสลิปจาก RefID
        public function reciveQr($barcode){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v1/slip/generate');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('function'=> 'TRANSFER', 'transactionId' => $barcode)));
            curl_setopt($curl, CURLOPT_POST, 1);
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

        //แสดงรายการบช.ปลายทางทั้งหมด
        public function eligiblebanks(){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fasteasy.scbeasy.com/v1/transfer/eligiblebanks",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array( "accept-encoding: gzip", "accept-language: th", "api-auth: ".$this->api_auth, "cache-control: no-cache", "connection: Keep-Alive", "content-type: application/json", "host: fasteasy.scbeasy.com:8443", "scb-channel: APP", "user-agent: Android/7.0;FastEasy/3.35.0/3906" ),
            ));
            $response = curl_exec($curl);
            $data = json_decode($response,true);
            // print_r($data);
            return $data;
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

        //ตรวจสอบบช.ก่อนโอนเงิน บช. ไป บช.
        public function transfer_verify($accountTo, $bankCode, $amount){
            $transferType = "ORFT";
            if ($bankCode === "014") {
                $transferType = "3RD";
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v2/transfer/verification');
            curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"accountFrom\":\"$this->acc_num\",\"accountFromType\":\"2\",\"accountTo\":\"$accountTo\",\"accountToBankCode\":\"$bankCode\",\"amount\":\"$amount\",\"annotation\":null,\"transferType\":\"$transferType\"}");
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
                return array();
            }
            return json_decode($result, true);
        }

        // ยืนยันโอนเงิน บช. ไป บช.
        public function transfer_confrim($accountTo, $bankCode, $amount){
            $verify = $this->transfer_verify($accountTo, $bankCode, $amount);
            $totalFee=$verify['data']['totalFee']; $scbFee=$verify['data']['scbFee']; $botFee=$verify['data']['botFee']; $channelFee= $verify['data']['channelFee']; $accountFromName= $verify['data']['accountFromName']; $accountTo= $verify['data']['accountTo']; $accountToName= $verify['data']['accountToName']; $accountToType= $verify['data']['accountToType']; $accountToDisplayName= $verify['data']['accountToDisplayName']; $accountToBankCode= $verify['data']['accountToBankCode']; $pccTraceNo= $verify['data']['pccTraceNo']; $transferType= $verify['data']['transferType']; $feeType= $verify['data']['feeType']; $terminalNo= $verify['data']['terminalNo']; $sequence= $verify['data']['sequence']; $transactionToken= $verify['data']['transactionToken']; $bankRouting= $verify['data']['bankRouting']; $fastpayFlag= $verify['data']['fastpayFlag']; $ctReference= $verify['data']['ctReference'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fasteasy.scbeasy.com/v3/transfer/confirmation",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\"accountFrom\":\"$accountTo\",\"accountFromName\":\"" .$accountFromName. "\",\"accountFromType\":\"2\",\"accountTo\":\"" .$accountTo. "\",\"accountToBankCode\":\"" .$accountToBankCode. "\",\"accountToName\":\"" . $accountToName . "\",\"amount\":\"" . $amount . "\",\"botFee\":0.0,\"channelFee\":0.0,\"fee\":0.0,\"feeType\":\"\",\"pccTraceNo\":\"" . $pccTraceNo . "\",\"scbFee\":0.0,\"sequence\":\"" . $sequence. "\",\"terminalNo\":\"" . $terminalNo . "\",\"transactionToken\":\"" . $transactionToken. "\",\"transferType\":\"" . $transferType. "\"}",
            CURLOPT_HTTPHEADER => array(
            'Api-Auth: '.$this->api_auth,
            'Accept-Language: th',
            'Content-Type: application/json; charset=UTF-8'
            ),
            ));
            $response = curl_exec($curl);
            $data = json_decode($response,true);
            // print_r($data);
            return $data;
        }

        // เรียกรายการประวัติ
        public function getHistory(){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v1/transfer/history?pagingOffset=0');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            $headers = array();
            $headers[] = 'Api-Auth: ' . $this->api_auth;
            $headers[] = 'Accept-Language: th';
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                return array();
            }
            return json_decode($result, true);
        }


        /* สร้าง QR code ชำระเงินของแม่มณี */
        //tax_note = (string) หมายเลขอ้างอิงคำสั่งซื้อ ไม่เกิน 20ตัวอักษร 
        public function qr_generateMerchantQr($amount, $tax_note, $walletId){ 
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v1/merchants/request/qr');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('amount' => number_format($amount, 2, '.', ''), 'shopNote' => $tax_note, 'walletId' => $walletId)));  //014000003089802
            curl_setopt($curl, CURLOPT_POST, 1);
            $headers = array();
            $headers[] = 'Api-Auth: ' . $this->api_auth;
            $headers[] = 'Accept-Language: th';
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            if(curl_errno($curl)) {
                return ['status' => false, 'msg' => 'ผิดพลาด curl'];
            }   
            return json_decode($result, true);
        }



        /*
            $phone_number คือ เบอร์โทรศัพท์ ที่ลงทะเบียนไว้กับ SCB
            $amount คือยอดเงินที่ต้องการเซ็ทใน Qr
            สร้าง QR code รับเงินของบช.เรา 
            ใช้สำหรับสร้าง QRcode รับเงินเพื่อให้ผู้ใช้งานทรูวอลเลทโอนมา
        */
        public function qr_generateAccountRecive($amount, $phone_number){ 
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v1/transfer/request/qr');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('proxyType' => 'MOB', 'amount' => number_format($amount, 2, '.', ''), 'proxyId' => $phone_number)));
            curl_setopt($curl, CURLOPT_POST, 1);
            $headers = array();
            $headers[] = 'Api-Auth: ' . $this->api_auth;
            $headers[] = 'Accept-Language: th';
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            if(curl_errno($curl)) {
                return ['status' => false, 'msg' => 'ผิดพลาด curl'];
            }   
            return json_decode($result, true);
        }



        /* 
            จ่ายเงินวอลเลทให้ลูกค้า 
            $amount คือ จำนวนเงินที่ต้องการเติม วงเงินไม่เกิน 500,000/วัน
            $serviceNumber คือ หมายเลขพร้อมเพย์บัญชีทรูวอลเลท
            14000 และต่อด้วยเบอร์ 0840369966 serviceNumber จะเป็น 140000840369866
        */
        //ตรวจสอบก่อนโอนเงิน
        public function transferPromtpay_verify($amount, $serviceNumber){
            if(strlen(trim($serviceNumber)) < 15){
                return [ 'status' => false, 'msg' => 'ต้องใช้หมายเลข E-wallet 15 ตัว' ];
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v2/topup/billers/14/additionalinfo');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('note'=>'TOPUP','annotation'=> 'null', 'pmtAmt' => number_format($amount, 2, '.', ''), 'serviceNumber' => $serviceNumber, 'billerId' => '14', 'depAcctIdFrom' => $this->acc_num)));
            curl_setopt($curl, CURLOPT_POST, 1);
            $headers = array();
            $headers[] = 'Api-Auth: ' . $this->api_auth;
            $headers[] = 'Accept-Language: th';
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            if(curl_errno($curl)) {
                return ['status' => false, 'msg' => 'ผิดพลาด curl'];
            }   
            return json_decode($result, true);
        }

        //ดำเนินการโอนเงิน
        public function transferPromtpay_confrim($amount, $serviceNumber){
            $data = $this->transferPromtpay_verify(number_format($amount, 2, '.', ''), $serviceNumber);
            $refNo1 = $data['data']['refNo1']; $additionalInformation = $data['data']['additionalInformation']; $transactionToken = $data['data']['transactionToken'];
            // print_r($data); exit;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fasteasy.scbeasy.com/v2/topup');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('depAcctIdFrom' => $this->acc_num, 'billRef2' => '', 'billerId' => '14', 'mobileNumber' => $refNo1, 'feeAmt' => 0.0, 'transactionToken' => $transactionToken,
            'misc2' => '', 'note' => $additionalInformation, 'serviceNumber' => $refNo1, 'pmtAmt' => $amount, 'misc1' => '', 'billRef3' => '', 'billRef1' => $refNo1)));
            curl_setopt($curl, CURLOPT_POST, 1);
            $headers = array();
            $headers[] = 'Api-Auth: ' . $this->api_auth;
            $headers[] = 'Accept-Language: th';
            $headers[] = 'Content-Type: application/json; charset=UTF-8';
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            if(curl_errno($curl)) {
                return ['status' => false, 'msg' => 'ผิดพลาด curl'];
            }   
            return json_decode($result, true);
        }
    }

