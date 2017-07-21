<?php
require_once('simpletest/browser.php');

class Way2SMSapi{
	function sendmsg($msg,$mobile,$username,$password){
		//preparation for message 
		$msgLen = 140 - strlen($msg);

		//validation
		if(!is_numeric($mobile)){
			echo "Invalid mobile number";
			return;
		}
		else if(!is_numeric($username)){
			echo "Bad username";
			return;
		}
		else if($msgLen<0){
			echo "Message Length exceed 140";
			return;
		}

		//Connect to site
		$browser = new SimpleBrowser();
		$browser->useCookies();
		$browser->get('http://site21.way2sms.com/Login1.action?username='.$username.'&password='.$password);
		$url = $browser->getUrl();
		//echo $url;

		//Check for succes full login
		//if successfully logged in then redirected to main.action page
		if(stripos($url, "main.action")===False){
			echo "Authentication Failed";
			return;
		}
	
		//Get token
		$token = explode("jsessionid=",$url);
		$token = explode("?",$token[1]);
		$token = $token[0];
		//echo $token."</br>";

		//site24.way2sms.com/smstoss.action?ssaction=ss&Token=<token>&mobile=<mobile>&message=<message>&msgLen=<length>
		//Prepare for message
		$msg_url = 'http://site21.way2sms.com/smstoss.action?ssaction=ss&Token='.urlencode($token).'&mobile='.urlencode($mobile).'&message='.urlencode($msg).'&msgLen='.urlencode($msgLen);
		//echo $msg_url;
		$browser->get($msg_url);

		return;
	}
}

function sendMessage($msg,$mobile,$username,$password){
	$obj = new Way2SMSapi();
	$obj->sendmsg($msg,$mobile,$username,$password);
}
?>
