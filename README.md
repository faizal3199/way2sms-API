API For way2sms [deprecated]
=====

**This project is not maintained anymore**


An simple unofficial API written in PHP to send messages using service provided by way2sms.</br></br>

**PHP-How to use?**
----
```php
<?php
	require_once('php/way2smsapi.php');
	//sendMessage($msg,$mobile,$username,$password)
	//sendMessage("<your_message>","<mobile_numbers_to_send>","<username>","<password>");
	sendMessage("Test Message","0123456789, 7894561230","8521470369","your_password");//Send message to multiple numbers
	sendMessage("Test Message","0123456789","8521470369","your_password");//Send message to single number
?>
```
>Put the following code on your server and add your own username and password</br>
>Also can use data from \$_POST and \$_GET



**PYTHON-How to use?**
----
```python
pip install -r python/requirements.txt
python pyhton/way2sms.py -h
```
</br>
