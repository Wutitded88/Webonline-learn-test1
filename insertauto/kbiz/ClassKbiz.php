<?php

class KbankBiz
{
	public $baseurl = "https://ib.gateway.kasikornbank.com";
	public $response = [];

	function __construct($response = null)
	{
		$this->response = $response;
		$this->chkPoint();
	}

	function Ccurl($path, $headers, $hstatus, $body)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->baseurl.$path,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_HEADER => $hstatus,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $body,
		CURLOPT_HTTPHEADER => $headers,
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}

	function login()
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://online.kasikornbankgroup.com/kbiz/login.do",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => "userName=".$this->response["username"]."&password=".$this->response["password"]."&tokenId=1".rand(1000000000, 9999999999)."&cmd=authenticate&locale=th&custType=&captcha=&app=0",
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/x-www-form-urlencoded',
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$dataRsso = explode("dataRsso=", htmlspecialchars($response))[1];
		$dataRsso = explode('&quot;;', $dataRsso)[0];
		return $dataRsso;
	}

	function chkPoint()
	{
		$dataID = "";
		if (file_exists(dirname(__FILE__)."/tmp/id.txt"))
		{
			$id = fopen(dirname(__FILE__)."/tmp/id.txt", "r");
			$dataID = fread($id,filesize(dirname(__FILE__)."/tmp/id.txt"));
			fclose($id);
		}
		$datatoken = "";
		if (file_exists(dirname(__FILE__)."/tmp/token.txt"))
		{
			$token = fopen(dirname(__FILE__)."/tmp/token.txt", "r");
			$datatoken = fread($token, filesize(dirname(__FILE__)."/tmp/token.txt"));
			fclose($token);
		}
		$this->response["ibId"] = $dataID;
		$this->response["token"] = $datatoken;
	}

	function validateSession($dataRsso)
	{
		$info["dataRsso"] = $dataRsso;
		$response = $this->Ccurl("/api/authentication/validateSession", array('Content-Type: application/json'), 1, json_encode($info));
		$token = explode("X-SESSION-TOKEN", $response)[1];
		$token = explode("\n", $token);
		$token = trim(explode(":", $token[0])[1]);
		$data = array('ibId' => explode('"', explode('{"ibId":"', $response)[1])[0], "token" => $token);
		$file1 = fopen(dirname(__FILE__)."/tmp/id.txt","w");
		fwrite($file1, $data['ibId']);
		fclose($file1);
		$file2 = fopen(dirname(__FILE__)."/tmp/token.txt","w");
		fwrite($file2, $data['token']);
		fclose($file2);
	}

	function refreshSession()
	{
		$headers = array(
			"Connection: keep-alive",
			"X-IB-ID: ".$this->response["ibId"],
			"Authorization: ".$this->response["token"],
			"Content-Type: application/json" ,
		);
		$response = $this->Ccurl("/gateway/refreshSession", $headers, 0, '{}');
		return json_decode($response, true);
	}

	function getTransactionHistory($limit = null, $startDate = null, $endDate = null) {
		$limit = ($startDate == null) ? 5 : $startDate;
		$startDate = ($startDate == null) ? date("d/m/Y", strtotime('-1 day')) : $startDate;
		$endDate = ($endDate == null) ? date("d/m/Y", time()) : $endDate;
		$headers = array(
			"Connection: keep-alive",
			"X-IB-ID: ".$this->response["ibId"],
			"Authorization: ".$this->response["token"],
			"Content-Type: application/json" ,
		);
		$body = '{"acctNo":"'.$this->response["accountFrom"].'","acctType":"SA","custType":"I","ownerType":"Retail","ownerId":"'.$this->response["ibId"].'","pageNo":"1","rowPerPage":"'.$limit.'","refKey":"","startDate":"'.$startDate.'","endDate":"'.$endDate.'"}';
		$response = $this->Ccurl("/api/accountsummary/getRecentTransactionList", $headers, 0, $body);
		return json_decode($response, true);
	}

	function getAccountSummaryList() {
		$headers = array(
			"Connection: keep-alive",
			"X-IB-ID: ".$this->response["ibId"],
			"Authorization: ".$this->response["token"],
			"Content-Type: application/json" ,
		);
		$body = '{"custType":"I","ownerId":"'.$this->response["ibId"].'","ownerType":"Retail","nicknameType":"OWNAC","pageAmount":100,"lang":"th","isReload":"N"}';
		$response = $this->Ccurl("/api/accountsummary/getAccountSummaryList", $headers, 0, $body);
		return json_decode($response, true);
	}

	function getRecentTransactionDetail($transDate, $origRqUid, $originalSourceId, $debitCreditIndicator, $transCode, $transType) {
		$headers = array(
			"Connection: keep-alive",
			"X-IB-ID: ".$this->response["ibId"],
			"Authorization: ".$this->response["token"],
			"Content-Type: application/json" ,
		);
		$body = '{"transDate":"'.$transDate.'","acctNo":"'.$this->response["accountFrom"].'","origRqUid":"'.$origRqUid.'","custType":"I","originalSourceId":"'.$originalSourceId.'","transCode":"'.$transCode.'","debitCreditIndicator":"'.$debitCreditIndicator.'","transType":"'.$transType.'","ownerType":"Retail","ownerId":"'.$this->response["ibId"].'"}';
		$response = $this->Ccurl("/api/accountsummary/getRecentTransactionDetail", $headers, 0, $body);
		return json_decode($response, true);
	}

	function GetNumberOtherBank($res) {
		foreach ($res["data"]["recentTransactionList"] as $i => $value) {
			$res["data"]["recentTransactionList"][$i]["data"] = $this->getRecentTransactionDetail(explode(" ", $value["transDate"])[0], $value["origRqUid"], $value["originalSourceId"], $value["debitCreditIndicator"], $value["transCode"], $value["transType"]);
		}
		return $res;
	}

}
