API For way2sms
=====
</br>
An simple but unofficial API written in PHP to send messages using service provided by way2sms.</br>
Written with help of SimpleTest PHP unit tester</br></br>

**How to use?**
----
```php
<?php
	require_once('way2smsapi.php');
	//sendMessage($msg,$mobile,$username,$password)
	sendMessage("<your_message>","<mobile_number_to_send>","<username>","<password>");
?>
```
>Put the following code on your server and add your username and password</br>
>Also can use data from \$_POST and \$_GET
</br>

Link to [SimpleTest PHP unit tester](http://www.simpletest.org)
