<?php
include 'common_amb.php';
class AMBAPI
{
	function createUser($username, $password, $name, $tel)
	{
		try
		{
			$fields = [
				'memberLoginName' => $username,
				'memberLoginPass' => $password,
				'phoneNo' => $tel,
				'contact' => $name,
				'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_agent'])
			];
			$postData = json_encode($fields);

			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/create/".$GLOBALS['amb_key'];
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
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
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
					$response->username=$GLOBALS['amb_agent'].$username;
					$response->data=$result['result'];
				}
				else
				{
					$response->success=false;
					$response->message=$result['message'];
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode;
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}

	function setPasswordUser($username, $new_password)
	{
		try
		{
			$fields = [
				'password' => $new_password,
				'signature' => GetSignatureAMB($new_password.":".$GLOBALS['amb_agent'])
			];
			$postData = json_encode($fields);

			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/reset-password/".$GLOBALS['amb_key']."/".$username;
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'PUT',
			  CURLOPT_POSTFIELDS => $postData,
			  CURLOPT_HTTPHEADER => array(
			  	'Content-Type: application/json'
			  ),
			));
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
				}
				else
				{
					$response->success=false;
					$response->message=$result['message'];
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode;
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}

	function getUserCredit($username)
	{
		try
		{
			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/credit/".$GLOBALS['amb_key']."/".$username;
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			));
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
					$response->data=$result['result'];
				}
				else
				{
					$response->success=false;
					$response->message=$result['message'];
					$response->real=$result;
					$response->url=$url;
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode;
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}

	function transferCreditTo($username, $amount)
	{
		if ($amount < 0)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message='amount less than 0';
			return $response;
		}
		if ($amount == 0)
		{
			$response = new stdClass();
			$response->success=true;
			$response->message='amount equals 0';
			return $response;
		}

		try
		{
			$fields = [
				'amount' => $amount,
				'signature' => GetSignatureAMB($amount.":".$username.":".$GLOBALS['amb_agent'])
			];
			$postData = json_encode($fields);

			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/deposit/".$GLOBALS['amb_key']."/".$username;
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
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
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
					$response->data=$result['result'];
				}
				else
				{
					$response->success=false;
					$response->message=$result['message'];
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode;
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}

	function TransferCreditOut($username, $amount)
	{
		if ($amount < 0)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message='amount less than 0';
			return $response;
		}
		if ($amount == 0)
		{
			$response = new stdClass();
			$response->success=true;
			$response->message='amount equals 0';
			return $response;
		}

		try
		{
			$fields = [
				'amount' => $amount,
				'signature' => GetSignatureAMB($amount.":".$username.":".$GLOBALS['amb_agent'])
			];
			$postData = json_encode($fields);

			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/withdraw/".$GLOBALS['amb_key']."/".$username;
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
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
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
					$response->data=$result['result'];
				}
				else
				{
					$response->success=false;
					$response->message=$result['message'];
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode;
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}

	function AMBGameList($company)
	{
		try
		{
			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/gameList/".$company."/".$GLOBALS['amb_key'];

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET'
			));
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
					$response->data=$result['data'];
				}
				else
				{
					$response->success=false;
					$response->message=$result;
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode." - ".$result;
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}

	function AMBGameLogin($username, $password, $company, $gamemode, $isMobile, $website, $osMobile, $gameId)
	{
		try
		{
			// gamemode คือเลขหัวข้อใน api document ของ AMB
			// gamemode 2 คือ หัวข้อ 2.xx (Baccarat)
			// gamemode 3 คือ หัวข้อ 3.xx (Slot Game)
			// gamemode 4 คือ หัวข้อ 4.xx (Sport)
			// gamemode 5 คือ หัวข้อ 5.xx (Lotto)
			// gamemode 6 คือ หัวข้อ 6.xx (Poker)
			if($gamemode == "2")
			{
				if($company == "ag")
				{
					$fields = [
						'username' => $username,
						'password' => $password,
						'isMobile' => $isMobile,
						'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_clientname']),
						'website' => $website
					];
				}
				else
				{
					$fields = [
						'username' => $username,
						'password' => $password,
						'isMobile' => $isMobile,
						'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_clientname'])
					];
				}
			}
			else if($gamemode == "3")
			{
				if($company == "live22")
				{
					$fields = [
						'username' => $username,
						'password' => $password,
						'isMobile' => $isMobile,
						'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_clientname']),
						'osMobile' => $osMobile,
						'gameId' => $gameId
					];
				}
				else if($company == "slotxo")
				{
					$fields = [
						'username' => $username,
						'password' => $password,
						'isMobile' => $isMobile,
						'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_clientname']),
						'gameId' => $gameId,
						'language' => 'th_th'
					];
				}
				else
				{
					$fields = [
						'username' => $username,
						'password' => $password,
						'isMobile' => $isMobile,
						'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_clientname']),
						'gameId' => $gameId
					];
				}
			}
			else if($gamemode == "4")
			{
				if($company == "sportsbo")
				{
					$response = new stdClass();
					$response->success=true;
					$response->url=$GLOBALS['amb_api_endpoint']."/#!/redirect?username=".$username."&password=".$password."&url=".$website."&hash=".$GLOBALS['amb_hash']."&state=sport&lang=th";
					return $response;
				}
				else if($company == "sportsboambbet")
				{
					$response = new stdClass();
					$response->success=true;
					$response->url="https://aquabet.co/login/auto/?username=".$username."&password=".$password."&hash=".$GLOBALS['amb_hash']."&url=".$website."&hash=".$GLOBALS['amb_hash']."&state=sport&lang=th";
					return $response;
				}
			}
			else if($gamemode == "5")
			{
				$fields = [
					'username' => $username,
					'password' => $password,
					'isMobile' => $isMobile,
					'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_clientname'])
				];
			}
			else if($gamemode == "6")
			{
				$fields = [
					'username' => $username,
					'password' => $password,
					'isMobile' => $isMobile,
					'signature' => GetSignatureAMB($username.":".$password.":".$GLOBALS['amb_clientname']),
					'gameId' => $gameId
				];
			}
			else
			{
				$response = new stdClass();
				$response->success=false;
				$response->message="parameter gamemode not match";
				return $response;
			}
			
			$postData = json_encode($fields);
			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/login/".$company."/".$GLOBALS['amb_key'];
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
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
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
					$response->url=$result['url'];
					$response->data=$result;
				}
				else
				{
					$response->success=false;
					$response->message=$result;
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode." - ".$result;
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}

	function GetWinLose($username, $ref)
	{
		try
		{
			$url = $GLOBALS['amb_api_endpoint']."/v0.1/partner/member/winLose/".$GLOBALS['amb_key']."/".$username."/".$ref;

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET'
			));
			$data = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$result = json_decode($data, true);
			$response = new stdClass();

			if ($httpCode == 200)
			{
				if ($result['code'] == 0)
				{
					$response->success=true;
					$response->data=$result['result'];
				}
				else
				{
					$response->success=false;
					$response->message=$result['message'];
				}
			}
			else
			{
				$response->success=false;
				$response->message='code '.$httpCode." ".json_encode($result);
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response = new stdClass();
			$response->success=false;
			$response->message=$e->getMessage();
			return $response;
		}
	}
}

?>