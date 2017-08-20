<?php
require_once('simpletest/browser.php');

class Way2SMSapi{
	private $ch;
	private $token = "null";
	private $silent;
	private $cookie;

	public function setSession($username,$password,$verbose)
	{
		if(!is_numeric($username)){
			echo "Bad username.";
			return;
		}
		$this->silent = !$verbose;

		//Initialize cURL
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_URL, 'http://site21.way2sms.com/Login1.action?username='.$username.'&password='.$password);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($this->ch, CURLOPT_HEADER, true);

		$data = curl_exec($this->ch);

		if($data===False){
			echo "Some error occured while making request";
			return;
		}

		//Check for succes full login
		//if successfully logged in then redirected to main.action page
		if(stripos($data, "way2sms.com/main.action")===False){
			echo "Authentication Failed";
			return;
		}

		//Get cookies
		$cookie = explode("Set-Cookie: ", $data);
		$cookie = explode("; Path=/;", $cookie[1]);
		$this->cookie = array("Cookie: ".$cookie[0]);
		
		//Get token
		$token = explode(";jsessionid=",$data);
		$token = explode("?",$token[1]);
		$this->token = $token[0];
		
		if($verbose)
			echo "Token: ".$this->token;

		return;
	}

	public function sendmsg($msg,$numbers){
		//Check for token
		if($this->token==="null"){
			echo "<br>"."Authorize first";
			return;
		}

		//preparation for message 
		$msgLen = 140 - strlen($msg);

		//Message Validation
		if($msgLen<0){
			echo "Message Length exceed 140.";
			return;
		}
		else if($msgLen==140){
			echo "Message is empty.";
			return;
		}

		//Get each mobile number
		$mobile = explode(",", $numbers);

		//site24.way2sms.com/smstoss.action?ssaction=ss&Token=<token>&mobile=<mobile>&message=<message>&msgLen=<length>
		foreach ($mobile as $num) {
			//validate mobile number
			$num = trim($num);
			if(!is_numeric($num)){
				echo "Invalid mobile number : `".$num."`";
				return;
			}

			//Prepare for message
			$msg_url = 'http://site21.way2sms.com/smstoss.action?ssaction=ss&Token='.urlencode($this->token).'&mobile='.urlencode($num).'&message='.urlencode($msg).'&msgLen='.urlencode($msgLen);

			if(!$this->silent)
				echo "<br>".$msg_url;
			
			curl_setopt($this->ch, CURLOPT_URL, $msg_url);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->cookie);
			$dump = curl_exec($this->ch);

			if($dump===False){
				echo "<br>Some error occured while sending message";
				return;
			}
			if(!$this->silent)
				echo "<br>Message sent to : ".$num;
		}

		return;
	}
}

function sendMessage($msg,$numbers,$username,$password,$verbose){
	//Check if curl is enabled
	if (!function_exists('curl_version')){
		echo "cURL is disabled.";
		return;
	}

	$obj = new Way2SMSapi();
	$obj->setSession($username,$password,$verbose);
	$obj->sendmsg($msg,$numbers);
	return;
}
?>