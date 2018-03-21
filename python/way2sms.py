import requests,getpass,sys,argparse

if sys.version_info < (3,0):
	input = raw_input

class way2smsUser:
	login_url = "http://site21.way2sms.com/Login1.action"
	msg_url = "http://site21.way2sms.com/smstoss.action"

	def __init__(self,username,password):
		self.username = username
		self.password = password

		self.validate_creds()
		self.auth_user()

	def validate_creds(self):
		self.validate_pass = False

		if type(self.username).__name__ == 'int' or type(self.username).__name__ == 'long':
			self.username = str(self.username)

		if not len(self.username)==10:
			print('Length of username should be 10')
			return False

		if not type(self.password).__name__ == 'str':
			print('Password should be a string. %s passed' % (type(self.password).__name__))
			return False

		if not self.username.isdigit():
			print('Username should be only digits')
			return False

		self.validate_pass = True

		return True

	def auth_user(self):
		self.auth_pass = False
		
		if not self.validate_pass:
			return False

		try:
			req = requests.get(self.login_url,params={'username':self.username,'password':self.password}, allow_redirects = False)
		except:
			print('Can\'t establish connection to servers')
			exit(1)

		if not req.status_code == 302:
			print('Some error occured on server.')
			return False

		if req.headers.get('Location').find('wpwd.action')>=0:
			print('Wrong password.')
			return False

		if req.headers.get('Location').find('nruser.action')>=0:
			print('User not registered.')
			return False

		if not req.headers.get('Location').find('main.action')>=0:
			print('Some error occured while authenticating')
			return False

		self.cookies = dict(req.cookies)

		self.token = self.cookies['JSESSIONID']
		self.token = self.token.split('~')[1]

		self.auth_pass = True
		return True

	def parse_recpt(self,recpt):
		try:
			if not type(recpt).__name__ == 'str':
				print('%s: Expected a string. %d passed' %(recpt,type(recpt).__name__))
				return False

			if not len(recpt)==10:
				print('%s: Length of recipient\'s address should be 10' %(recpt))
				return False

			if not recpt.isdigit():
				print('%s: Recipient\'s address should be only digits'%(recpt))
				return False
		except Exception as e:
			print('%s: Unexpected error in recipient\'s address' % (recpt))
			print(e)
			return False

		return True

	def send_single_message(self,recpt,msgTxt,msgLenRem):
		if not self.auth_pass:
			print('Authenticate first.')
			return False

		try:
			req = requests.get(self.msg_url,params={'ssaction':'ss','Token':self.token,'mobile':recpt,'message':msgTxt,'msgLen':msgLenRem},cookies=self.cookies)
		except:
			print('Can\'t establish connection to servers')
			exit(1)

		if req.status_code == 200:
			return 'Message sent to: %s' %(recpt)
		else:
			return 'Message sending failed to: %s' %(recpt)

	def send_message(self,recipient,msgTxt):
		if not self.auth_pass:
			print('Authenticate first.')
			return False

		if not type(recipient).__name__ == 'list':
			print('Pass recipient\'s address as list')
			return False

		if len(recipient) < 1:
			print('No recipient mentioned.')
			return False

		msgLenRem = 140 - len(msgTxt)

		if msgLenRem < 0:
			print('Message length exceeds 140.')
			return False
		elif msgLenRem == 0 :
			print('Message is empty.')
			return False
		else:
			msgTxt = msgTxt + ' '*msgLenRem

		for recpt in recipient:
			if self.parse_recpt(recpt):
				print(self.send_single_message(recpt,msgTxt,msgLenRem))

		return True

def arg_parser(args):
	parser = argparse.ArgumentParser(description='Send messages using way2sms.')

	parser.add_argument('-u','--username',help="Username for login",required=True)
	parser.add_argument('-p','--password',help="Password for login",required=True)

	parser.add_argument('-r','--recipient',help="Recipient's address. For multiple recipients use -r/--recipient 'first' 'second'",required=True,action='append',nargs='+')
	parser.add_argument('-t','--text',help="Message text to send",required=True)

	return parser.parse_args(args)

if __name__ == '__main__':
	if len(sys.argv) <= 1:
		username = input('Enter username: ')
		password = getpass.getpass('Enter password: ')

		way2smsObj = way2smsUser(username,password)

		if not way2smsObj.auth_pass:
			exit(1)

		recipient = input('Enter recipient\'s address: ')
		msgTxt = input('Enter message text:\n')

		way2smsObj.send_message([recipient],msgTxt)

	else:
		results = arg_parser(sys.argv[1:])

		recipient = []
		for x in results.recipient:
			recipient += x

		username = results.username
		password = results.password

		way2smsObj = way2smsUser(username,password)

		if not way2smsObj.auth_pass:
			exit(1)

		msgTxt = results.text

		way2smsObj.send_message(recipient,msgTxt)