<?PHP /*
	Registration/Login script from HTML Form Guide
	V2.0
	This program is free software published under the
	terms of the GNU Lesser General Public License.
	http://www.gnu.org/copyleft/lesser.html
	
This program is distributed in the hope that it will be useful - WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. For updates, please visit: 
http://www.html-form-guide.com/php-form/php-registration-form.html http://www.html-form-guide.com/php-form/php-login-form.html */ 

/*

	0 Initialization
		0.1 Defining Global Variables
		0.2 Setting Global Variables
			0.2.1 FGMembersite()
			0.2.2 SetRandomKey($randkey)
			0.2.3 InitDB($host,$uname,$pwd,$database)
			0.2.4 SetAdminEmail($email)
			0.2.5 SetWebsiteName($sitename)
			0.2.6 EnableTwoFactorAuthenticationMode($do)
			0.2.7 EnableClientSidePasswordHashing($do)
			0.2.8 EnableTransactions($do)
	1 Main Operations
		1.1 Registering a User
			1.1.1 RegisterUser()
			1.1.2 ConfirmUser()
			1.1.3 ValidateRegistrationSubmission()
			1.1.4 CollectRegistrationSubmission(&$formvars)
			1.1.5 SaveToDatabase(&$formvars)
			1.1.6 SendUserConfirmationEmail(&$formvars)
			1.1.7 SendAdminIntimationEmail(&$formvars)
		1.2 Registering a Browser
			1.2.1 RegisterBrowser()
			1.2.2 SendBrowserConfirmationEmail($email, $username, $confirmcode)
			1.2.3 ConfirmBrowser()
			1.2.4 SendBrowserRegisteredConfirmation($email, $browserDescription)
			1.2.5 SendAdminIntimationOnRegComplete($email)
		1.3 Logging on a User
			1.3.1 Login()
			1.3.2 CheckLogin()
			1.3.3 ConfirmCSRFToken()
			1.3.4 AuthenticateChangeRequest()
		1.4 Logging out a user
			1.4.1 LogOut()
		1.5 Getting Session Variables // Likely unused, they're in $_SESSION
			1.5.1 UserFullName()
			1.5.2 UserName()
			1.5.3 UserEmail()
	2 Updating User Profiles
		2.1 Resetting a Password
			2.1.1 EmailResetPasswordLink()
			2.1.2 SendResetPasswordLink($email, $username)
			2.1.3 GetResetPasswordCode($email)
			2.1.4 ResetPassword($authcode, $newpassword, $confirmpassword, $csalt)
			2.1.5 NotifyOfNewPassword($email, $username)
		2.2 Changing a Password
			2.2.1 ChangePassword()
		2.3 Changing an Email Address
			2.3.1 ChangeEmailAddress()
		2.4 Changing a 'Real Name'
			2.4.1 ChangeName()
		2.5 Disabling a Browser
			2.5.1 DisableBrowser()
	3 Infrastructure
		3.1 PHP Helpers
			3.1.1 GetSelfScript()
			3.1.2 SafeDisplay($value_name)
			3.1.3 RedirectToURL($url)
			3.1.4 GetSpamTrapInputName()
			3.1.5 GetErrorMessage()
			3.1.6 HandleError($err)
			3.1.7 HandleDBError($err)
			3.1.8 GetFromAddress()
			3.1.9 GetAbsoluteURLFolder()
		3.2 MySQL Helpers
			3.2.1 DBLogin()
			3.2.2 EnsureTable()
			3.2.3 EnsureBrowserTable()
			3.2.4 EnsureTransactionTable()
			3.2.5 CreateTable()
			3.2.6 CreateBrowserTable()
			3.2.7 CreateTransactionTable()
			3.2.8 IsFieldUnique($column,$content)
		3.3 MySQL Actors
			3.3.1 MySQL Inserts
				3.3.1.1 InsertIntoDB(&$formvars)
				3.3.1.2 UpdateDBforBrowserVerification($email, $IP, $description)
			3.3.2 MySQL Updates
				3.3.2.1 DisableBrowserInDB($browser_id)
				3.3.2.2 UpdateDBRecForConfirmation($confirmcode)
				3.3.2.3 ResetUserPasswordInDB($username)
				3.3.2.4 ChangePasswordInDB($username, $newpwd)
				3.3.2.5 ChangeConfirmCodeInDB($email)
				3.3.2.6 ChangeEmailInDB($username, $email)
				3.3.2.7 ChangeNameInDB($username, $name)
				3.3.2.8 MarkUserAsHavingBillingInfoProblem($username)
				3.3.2.9 SetUserMessage($username, $message)
			3.3.3 MySQL Selects
				3.3.3.1 CheckLoginInDB($username,$password,$browserverification)
				3.3.3.2 GetUsernameFromEmail($email)
				3.3.3.4 GetEmailFromUsername($username)
				3.3.3.5 GetSaltFromUsername($username)
				3.3.3.6 GetSaltFromUsernamePublic($username, $browserverification)
				3.3.3.7 GetRegisteredBrowsersForCurrentUser()
				3.3.3.8 WhatWillNextUserIdBe()
		3.4 Billing Functions
			3.4.1 ValidateBillingInfo()
			3.4.2 billUser($username, $reason, $price)
		3.5 Transaction Loggers
			3.5.1 createTransaction($reason, $price)
			3.5.2 markTransactionPaid($transactionId, $transactionResult)
			
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require("formvalidator.php"); 
require("Sanitizers.php");
require("phpMailer/Exception.php");
require("phpMailer/PHPMailer.php");
require("phpMailer/SMTP.php");

class FGMembersite {

	// 0 Initialization
	// 0.1 Defining Global Variables
	var $admin_email;
	var $from_address;
	var $sitename;
	
	var $username;
	var $pwd;
	var $database;
	var $connection;
	var $rand_key;
	var $count=0;
	
	var $newIterations;
	
	var $sessionLifeTime;
	
	var $error_message;
	
	var $passwordRequiredForAdministration;
	
	var $CSRFTokenRequired;
	
	var $clientSidePasswordHashing;
	
	var $twoFactorAuthMode;
	
	var $acceptedCreditCardTypes;
	
	var $Config;
	
	var $lang;
	
	// End Defining Global Variables
	
	// 0.2 Setting Global Variables
	function __construct($Configuration)
	{
		$this->Config = $Configuration;
		$this->newIterations = 100000;
		$this->sessionLifeTime = 1800;
	}
	
	function SetRandomKey($randkey)
	{
		$this->rand_key = $randkey;
	}
	
	function InitDB()
	{
	}
	
	function SetAdminEmail($email)
	{
		$this->admin_email = SanitizeEmail($email);
	}
	
	function SetWebsiteName($sitename)
	{
		$this->sitename = $sitename;
	}
	
	function SetSessionLifetimeInMinutes($minutes) {
		$this->sessionLifeTime = $minutes * 60;
	}
	
	function EnablePasswordRequiredForAdministration($do)
	{
		$this->passwordRequiredForAdministration = $do;
	}
	
	function EnableTwoFactorAuthenticationMode($do) {
		$this->twoFactorAuthMode = $do;
		if ($this->clientSidePasswordHashing && $this->twoFactorAuthMode === false) {
			error_log('You cannot do this, this software would provide unnecessary confirmation of usernames if you used client-side password hashing in the manner utilized by this application without two-factor authentication');
			exit;
		}
		//if ($do) {
		//    $this->EnsureBrowserTable();
		//}
	}
	
	function EnableCSRFTokenRequired($do)
	{
		$this->CSRFTokenRequired = $do;
	}
	
	function EnableClientSidePasswordHashing($do) {
		$this->clientSidePasswordHashing = $do;
		if ($do) {
			$this->EnableTwoFactorAuthenticationMode(true);
		}
	}
	
	function getSitename(){
		return $this->sitename;
	}
	
	function EnableTransactions($do) {
		//if ($do) {
		//    $this->EnsureTransactionTable();
		//}
	}
	
	function SetAcceptedCreditCards($array) {
		$this->acceptedCreditCardTypes = $array;
	}
	// End Setting Global Variables
	
	// 1 Main Operations
	
	// 1.1 Registering a User
	//-------Main Operations ----------------------
	function RegisterUser(){
		
		$formvars = array();
		
		if(!$this->ValidateRegistrationSubmission(0)){
			return false;
		}
		
		$this->CollectRegistrationSubmission($formvars, 0);
		
		if(!$this->SaveToDatabase($formvars, 0)){
			return false;
		}
		
		if(!$this->SendUserConfirmationEmail($formvars, 0)){
			return false;
		}
		$this->SendAdminIntimationEmail($formvars, 0);
		
		return true;
	}
	
	function UpdateUser(){
		$formvars = array();
		
		if(!$this->ValidateRegistrationSubmission(1)){
			return false;
		}
		$this->CollectRegistrationSubmission($formvars, 1);
		
		if(!$this->SaveToDatabase($formvars, 1)){
			return false;
		}
		
		if(!$this->SendUserConfirmationEmail($formvars, 1)){
			return false;
		}
		$this->SendAdminIntimationEmail($formvars, 1);
		
		return true;
	}
	
	function ConfirmUser(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if(empty($_GET['code'])){
			$this->HandleError($lang['10001']);
			return false;
		}
		
		if(!$this->UpdateDBRecForConfirmation(SanitizeHex($_GET['code']))){
			return false;
		}
		
		return true;
	} 
	
	function ValidateRegistrationSubmission($type){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		//This is a hidden input field. Humans won't fill this field.
		if(!empty($_POST[$this->GetSpamTrapInputName()]) ){
			//The proper error is not given intentionally
			$this->HandleError($lang['10002']);
			return false;
		}
		
		$validator = new FormValidator();
		$validator->addValidation("nombreCompleto","maxlen=64",$lang['10009']);
		$validator->addValidation("nombre","req",$lang['10011']);
		$validator->addValidation("nombre","alpha_s",$lang['10012']);
		$validator->addValidation("nombre","maxlen=20",$lang['10013']);
		$validator->addValidation("apellidop","req",$lang['10014']);
		$validator->addValidation("apellidop","alpha_s",$lang['10015']);
		$validator->addValidation("apellidop","maxlen=20",$lang['10016']);
		$validator->addValidation("apellidom","req",$lang['10017']);
		$validator->addValidation("apellidom","alpha_s",$lang['10018']);
		$validator->addValidation("apellidom","maxlen=20",$lang['10019']);
		$validator->addValidation("telefono","req",$lang['10020']);
		$validator->addValidation("telefono","num",$lang['10021']);
		$validator->addValidation("email","email",$lang['10003']);
		$validator->addValidation("email","req",$lang['10004']);
		$validator->addValidation("email","maxlen=64",$lang['10005']);
		if($type == 0){
			$validator->addValidation("username","req",$lang['10006']);
			$validator->addValidation("username","alnum",$lang['10007']);
			$validator->addValidation("username","maxlen=12",$lang['10008']);
			$validator->addValidation("password","req",$lang['10010']);
		}
		
		if(!$validator->ValidateForm()){
			$error='';
			$error_hash = $validator->GetErrors();
			foreach($error_hash as $inpname => $inp_err){
				$error .= $inpname.':'.$inp_err."\n";
			}
			$this->HandleError($error);
			return false;
		}        
		
		return true;
	}
	
	function CollectRegistrationSubmission(&$formvars, $type){
		$formvars['nombre'] = SanitizeRealName($_POST['nombre']);
		$formvars['username'] = SanitizeUsername($_POST['username']);
		$formvars['apellidop'] = SanitizeRealName($_POST['apellidop']);
		$formvars['apellidom'] = SanitizeRealName($_POST['apellidom']);
		$formvars['telefono'] = SanitizeInteger($_POST['telefono']);
		$formvars['equipo'] = SanitizeTextComa($_POST['equipo']);
		$formvars['email'] = SanitizeEmail($_POST['email']);
		if($type == 0){
			$formvars['password'] = $_POST['password'];
			if ($this->clientSidePasswordHashing) {
				$formvars['salt'] = SanitizeHex($_POST['salt']);
			} else {
				$formvars['salt'] = '';
			}
			$formvars['username'] = SanitizeUsername($_POST['username']);
		}
		if($type == 1){
			$formvars['active'] = 1;
			if($_POST['active'] == 'false'){
				$formvars['active'] = 0;
			}
			$formvars['userid'] = $_POST['userid'];
		}
	}
	
	function SaveToDatabase(&$formvars, $type){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if(!$this->DBLogin()){
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($type == 0){
			if(!$this->EnsureTable()){
				return false;
			}
		}
		
		if($type == 0){
			if(!$this->IsFieldUnique('email', $formvars['email'])){
				$this->HandleError($lang['10120']);
				return false;
			}
		}
		
		if($type == 0){
			if(!$this->IsFieldUnique('username', $formvars['username'])){
				$this->HandleError($lang['10121']);
				return false;
			}   
		}
		if($type == 0){
			if(!$this->InsertIntoDB($formvars)){
				$this->HandleError($lang['10122']);
				return false;
			}
		}else{
			if(!$this->InsertIntoDBUpdate($formvars)){
				$this->HandleError($lang['10122']);
				return false;
			}
		}
		return true;
	}
	
	function SendUserConfirmationEmail(&$formvars, $type){
		
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$mail = new PHPMailer(true); 
		try{
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$this->Config->getEmailConnectionInfo();
			
			//Server settings
			$mail->SMTPDebug = $this->Config->MAILSMTPDebug;			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = $this->Config->MAILHost;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = $this->Config->MAILSMTPAuth;				// Enable SMTP authentication
			$mail->Username = $this->Config->MAILUsername;				// SMTP username
			$mail->Password = $this->Config->MAILPassword;				// SMTP password
			$mail->Port = $this->Config->MAILPort;


			//Content
			$mail->isHTML(true); // Set email format to HTML
			
			//Recipients
			$mail->setFrom($this->Config->MAILsetFrom, 'Admin ' . $this->sitename);
			//$this->GetFromAddress()
			$mail->addAddress(SanitizeEmail($formvars['email']),SanitizeUsername($formvars['username'])); 
			$mail->addReplyTo($this->Config->MAILaddReplyTo, 'Information');
			
			$confirmcode = $formvars['confirmcode'];
			$confirm_url = $this->sitename . '/ajax/Login/confirmregAdmin.php?code='.$confirmcode;
			
			
			$mail->CharSet = 'UTF-8';
		
			if($type == 0){
				$mail->Subject = 	$lang['10022'] . $this->sitename;
				$mail->Body    = 	$lang['10023'] . SanitizeUsername($formvars['username']) . $lang['10024'] . $this->sitename . $lang['10025'] . $confirm_url . $lang['10026'] . $this->sitename;
			}else{
				$mail->Subject = 	$lang['10125'] . $this->sitename;
				$mail->Body    = 	$lang['10023'] . SanitizeUsername($formvars['username']) . $lang['10126'] . $lang['10127'] . $confirm_url . $lang['10128'] . $this->sitename;
			}
			if(!$mail->send()) {
			  $this->HandleError($lang['10033'] . $mail->ErrorInfo);
			}
		} catch (exception $e) {
			$this->HandleError($lang['10027'] . $e->errorMessage());
			return false;
		}
		return true;
	}
	
	function SendAdminIntimationEmail(&$formvars, $type){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if(empty($this->admin_email))
		{
			return false;
		}
		$mail = new PHPMailer(true); 
		try{
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$this->Config->getEmailConnectionInfo();
			
			//Server settings
			$mail->SMTPDebug = $this->Config->MAILSMTPDebug;			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = $this->Config->MAILHost;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = $this->Config->MAILSMTPAuth;				// Enable SMTP authentication
			$mail->Username = $this->Config->MAILUsername;				// SMTP username
			$mail->Password = $this->Config->MAILPassword;				// SMTP password
			$mail->Port = $this->Config->MAILPort; 


			//Content
			$mail->isHTML(true); // Set email format to HTML
			
			//Recipients
			$mail->setFrom($this->Config->MAILsetFrom, 'Admin ' . $this->sitename);
			//$this->GetFromAddress()
			$mail->addAddress($this->Config->MAILsetFrom, 'Admin ' . $this->sitename); 
			$mail->addReplyTo($this->Config->MAILaddReplyTo, 'Information');
			
			$mail->CharSet = 	'UTF-8';
			if($type == 0){
				$mail->Subject = 	$lang['10028'] . SanitizeUsername($formvars['username']);
				$mail->Body    = 	$lang['10029'] . "".$this->sitename . $lang['10030'] . SanitizeRealName($formvars['nombre']) . ' ' . SanitizeRealName($formvars['apellidop']) . ' ' . SanitizeRealName($formvars['apellidom']) . ' ' . $lang['10031'] . SanitizeEmail($formvars['email']) . $lang['10032'] . SanitizeUsername($formvars['username']);
			}else{
				$mail->Subject = 	$lang['10028-1'] . $this->sitename;
				$mail->Body    = 	$lang['10029-1'] . "".$this->sitename . $lang['10030'] . SanitizeRealName($formvars['nombre']) . ' ' . SanitizeRealName($formvars['apellidop']) . ' ' . SanitizeRealName($formvars['apellidom']) . $lang['10031'] . SanitizeEmail($formvars['email']) . $lang['10032'] . SanitizeUsername($formvars['username']);
			}
			if(!$mail->send()) {
				$this->HandleError($lang['10033'] . $mail->ErrorInfo);
			}
		} catch (phpmailerException $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
			$this->HandleError($lang['10034'] . $e->errorMessage());
			return false;
		}
		
		return true;
	}
	
	// End Registering a User
	
	// 1.2 Registering a Browser
	
	function RegisterBrowser(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$email = SanitizeEmail($_POST['email']);

		$confirmcode = $this->ChangeConfirmCodeInDB($email);
		
		if(false == $confirmcode){
		   $this->HandleError($lang['10035']);
		   return false;
		}
		
		$username = SanitizeUsername($this->GetUsernameFromEmail($email));
		
		return $this->SendBrowserConfirmationEmail($email, $username, $confirmcode);
	}
	
	function SendBrowserConfirmationEmail($email, $username, $confirmcode){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$mail = new PHPMailer(true); 
		//echo '577';
		try{
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$this->Config->getEmailConnectionInfo();
			
			//Server settings
			$mail->SMTPDebug = $this->Config->MAILSMTPDebug;			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = $this->Config->MAILHost;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = $this->Config->MAILSMTPAuth;				// Enable SMTP authentication
			$mail->Username = $this->Config->MAILUsername;				// SMTP username
			$mail->Password = $this->Config->MAILPassword;				// SMTP password
			$mail->Port = $this->Config->MAILPort;


			//Content
			$mail->isHTML(true); // Set email format to HTML
			
			//Recipients
			$mail->setFrom($this->Config->MAILsetFrom, 'Admin ' . $this->sitename);
			//$this->GetFromAddress()
			$mail->addAddress(SanitizeEmail($email),SanitizeUsername($username)); 
			$mail->addReplyTo($this->Config->MAILaddReplyTo, 'Information');
		//echo '611';
			
			$confirmcode = SanitizeHex($confirmcode);
			$confirm_url = $this->sitename . '/ajax/Login/confirmreg.php?code='.$confirmcode;
			$browser = get_browser(null,true);
			$browserDescription = SanitizeBrowserName($browser["browser"] . " " . $lang['10047'] . " " . $browser["platform"]);
			$browserDescription = '';
		//echo '618';
			
			$mail->CharSet = 	'UTF-8';
			
			$mail->Subject = 	$lang['10036'] . $this->sitename;
			$mail->Body = 		$lang['10037'] . SanitizeUsername($username) . $lang['10038'] . $this->sitename . $lang['10039'] . $browserDescription . $lang['10040'] . SanitizeFloat($_SERVER['REMOTE_ADDR']) . $lang['10069'] . $confirmcode . $lang['10045'] . $lang['10046'] . $this->sitename;
			if(!$mail->send()) {
		//echo '625';
				$this->HandleError($lang['10033'] . $mail->ErrorInfo);
				return false;
			}
		//echo '629';
		} catch (phpmailerException $e) {
			//echo $e->getMessage(); //Boring error messages from anything else!
			$this->HandleError($lang['10034'] . $e->errorMessage());
			return false;
		}catch (Exception $e) {
			//echo $e->getMessage(); //Boring error messages from anything else!
			$this->HandleError($lang['10034'] . $e->errorMessage());
			error_log($e);
			return false;
		}
		//echo '635';
		return true;
	}

	function ConfirmBrowser(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if(empty($_POST['code'])||strlen($_POST['code'])!=8){
			$this->HandleError($lang['10048']);
			return false;
		}
		
		$email = $this->UpdateDBRecForConfirmation(SanitizeHex(strtolower($_POST['code'])));
		
		if(false === $email){
			// the previous method provides its own errors
			return false;
		}
		$email = SanitizeEmail($email);

		$browser = get_browser(null,true);
		$browserDescription = SanitizeBrowserName($browser["browser"] . " " . $lang['10047'] . " " . $browser["platform"]);

		$verificationCode = $this->UpdateDBforBrowserVerification($email, SanitizeFloat($_SERVER['REMOTE_ADDR']), $browserDescription);
			
		$this->SendBrowserRegisteredConfirmation($email, $browserDescription);
		
		$this->SendAdminIntimationOnRegComplete($email);
			
		$username = $this->GetUsernameFromEmail($email);

		// this two element array is what is needed to set the browser verification cookie in the user's browser
		$returning['code'] = $verificationCode;
		$returning['username'] = $username;

		return $returning;
	}
	
	function SendBrowserRegisteredConfirmation($email, $browserDescription){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$email = SanitizeEmail($email);
		$username = $this->GetUsernameFromEmail($email);
		if(false === $username)
		{
			$this->HandleError($lang['10116']);
			return false;
		}
	
		$mail = new PHPMailer(true); 
		try{
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$this->Config->getEmailConnectionInfo();
			
			//Server settings
			$mail->SMTPDebug = $this->Config->MAILSMTPDebug;			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = $this->Config->MAILHost;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = $this->Config->MAILSMTPAuth;				// Enable SMTP authentication
			$mail->Username = $this->Config->MAILUsername;				// SMTP username
			$mail->Password = $this->Config->MAILPassword;				// SMTP password
			$mail->Port = $this->Config->MAILPort;


			//Content
			$mail->isHTML(true); // Set email format to HTML
			
			//Recipients
			$mail->setFrom($this->Config->MAILsetFrom, 'Admin ' . $this->sitename);
			//$this->GetFromAddress()
			$mail->addAddress(SanitizeEmail($email),SanitizeUsername($username)); 
			$mail->addReplyTo($this->Config->MAILaddReplyTo, 'Information');
			
			$browser = get_browser(null,true);
			$browserDescription = SanitizeBrowserName($browser["browser"] . " " . $lang['10047'] . " " . $browser["platform"]);
			$browserDescription = '';
			
			$mail->CharSet = 	'UTF-8';
			
			$mail->Subject = 	$lang['10049'] . $this->sitename;
			$mail->Body = 		$lang['10050'] . $username . $lang['10051'] . $this->sitename . $lang['10052'] . $browserDescription . $lang['10053'] . SanitizeFloat($_SERVER['REMOTE_ADDR']) . $lang['10054'] . $this->admin_email . $lang['10055'] . $this->sitename;
			if(!$mail->send()) {
			  $this->HandleError("Mailer error: " . $mail->ErrorInfo);
			}
		} catch (phpmailerException $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
			$this->HandleError("Failed sending registration confirmation email. " . $e->errorMessage());
			return false;
		}
		return true;
	}
	
	function SendAdminIntimationOnRegComplete($email){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		
		$email = SanitizeEmail($email);
		$username = $this->GetUsernameFromEmail($email);
		
		if(false === $username)
		{
			// not critical
			return false;
		}
	
		if(empty($this->admin_email))
		{
			error_log("There is no admin_email value set");
			return false;
		}
		
		$mail = new PHPMailer(true); 
		try{
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$this->Config->getEmailConnectionInfo();
			
			//Server settings
			$mail->SMTPDebug = $this->Config->MAILSMTPDebug;			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = $this->Config->MAILHost;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = $this->Config->MAILSMTPAuth;				// Enable SMTP authentication
			$mail->Username = $this->Config->MAILUsername;				// SMTP username
			$mail->Password = $this->Config->MAILPassword;				// SMTP password
			$mail->Port = $this->Config->MAILPort; 


			//Content
			$mail->isHTML(true); // Set email format to HTML
			
			//Recipients
			$mail->setFrom($this->Config->MAILsetFrom, 'Admin ' . $this->sitename);
			//$this->GetFromAddress()
			$mail->addAddress(SanitizeEmail($email),SanitizeUsername($username)); 
			$mail->addReplyTo($this->Config->MAILaddReplyTo, 'Information');
			
			$mail->CharSet = 	'UTF-8';
			
			$mail->Subject = 	$lang['10056'] . $this->sitename;
			$mail->Body = 		$lang['10056'] . $this->sitename . $lang['10057'] . $username . $lang['10058'] . $email . $lang['10059'];
			if(!$mail->send()) {
			  $this->HandleError("Mailer error: " . $mail->ErrorInfo);
			}
		} catch (phpmailerException $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
			$this->HandleError("Failed sending registration confirmation email. " . $e->errorMessage());
			return false;
		}
		
		return true;
	}
	
	// End Registering a Browser
	
	// 1.3 Logging on a User
	
	function Login($username, $password){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		
		//if(empty($_POST['username']))
		if(empty($username))
		{
			$this->HandleError($lang['10060']);
			return false;
		}
		
		//if(empty($_POST['password']))
		if(empty($password))
		{
			$this->HandleError($lang['10061']);
			return false;
		}
		
		//$username = trim($_POST['username']);
		//$password = trim($_POST['password']);
		
		if(!isset($_SESSION)) {
			
			session_set_cookie_params(3600,'/','',true,true); // make it expire after 1 hour
			session_start();
			if ($this->CSRFTokenRequired) {
				$token = hash("sha512",mt_rand(0,mt_getrandmax()));
				$_POST['CSRFtoken'] = $_SESSION[$this->Config->getAlias() . 'CSRFtoken'] = $token;
			}
		}
		
		// If we are in two-factor mode we must identify a cookie named BrowserValidationUsername
		// If the user is loggin in with their email address we must first figure out what their username is
		if ($this->twoFactorAuthMode) {
			if ($_POST['username'] == SanitizeUsername($_POST['username'])) {
				$BVname = 'BrowserValidation'.SanitizeUsername($_POST['username']);
			} else if ($_POST['username'] == SanitizeEmail($_POST['username'])) {
				$BVname = 'BrowserValidation'.SanitizeUsername($this->GetUsernameFromEmail(SanitizeEmail($_POST['username'])));
			}
			if (isset($_COOKIE[$this->Config->getAlias() . $BVname])) {
				$BVvalue = $_COOKIE[$this->Config->getAlias() . $BVname];
			} else {
				$BVvalue = '';
			}
		} else {
			$BVvalue = '';
		}

		// Note that we have not sanitized their username, this is to allow them to login with their email address
		if(!$this->CheckLoginInDB($username, $password, SanitizeHex($BVvalue))){
			return false;
		}
		
		// Set session variables
		$_SESSION[$this->Config->getAlias() . 'LAST_ACTIVITY'] = time();
		$_SESSION[$this->Config->getAlias() . 'CREATED'] = time();
		$_SESSION[$this->Config->getAlias() . 'hasPurchasedThisSession'] = false;
		$_SESSION[$this->Config->getAlias() . 'username'] = SanitizeEmail($_POST['username']);
		
		return true;
	}
	
	function CheckLogin($arg1, $arg2 = null){
		// Support both CheckLogin('page.php') and legacy CheckLogin($Config, 'page.php').
		$source = ($arg2 !== null) ? $arg2 : $arg1;
		
		// Check that they at least have a session, and if not, create it
		if(!isset($_SESSION)){ 
			session_set_cookie_params(3600,'/','',true,true); // make it expire after 1 hour
			session_start(); 
		}
		
		/*
		$datetime = new DateTime();
		$txt = "Before " . $source . ", " . $datetime->format('Y-m-d H:i:s');
		$txt .= "-------------------------------------\n";
		$txt .= "this->CSRFTokenRequired => " . $this->CSRFTokenRequired . "\n";
		$txt .= $this->Config->getAlias() . 'CSRFtoken' . "\n";
		$txt .= "-------------------------------------\n"; 
		$myfile = file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/Desarrollo/hectorbarraza/log/log.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);	
		*/
		
		// If they do not have a CSRF token, set that too; if we are requiring them.
		if ($this->CSRFTokenRequired) {
			if (!isset($_SESSION[$this->Config->getAlias() . 'CSRFtoken'])) {
				$token = hash("sha512",mt_rand(0,mt_getrandmax()));
				$_SESSION[$this->Config->getAlias() . 'CSRFtoken'] = $token;
			}
		}
		
		// This would mean that they are not logged in, as we set this when a user logs in
		if(empty($_SESSION[$this->Config->getAlias() . 'username'])){
			//http_response_code(401);
			return false;
		}
		
		// They were properly logged in, but that was too long ago (sessionLifeTime) so they need to login again
		if (isset($_SESSION[$this->Config->getAlias() . 'LAST_ACTIVITY']) && (time() - $_SESSION[$this->Config->getAlias() . 'LAST_ACTIVITY'] > $this->sessionLifeTime)) {
			/* last request was more than sessionLifeTime ago*/
			session_destroy(); // destroy session data in storage
			//http_response_code(401);
			return false;
		}
		
		// They are properly logged in, so let's update their session timers as appropriate.
		$_SESSION[$this->Config->getAlias() . 'LAST_ACTIVITY'] = time(); // update last activity time stamp
		if (!isset($_SESSION[$this->Config->getAlias() . 'CREATED'])) {
			$_SESSION[$this->Config->getAlias() . 'CREATED'] = time();
		} else if (time() - $_SESSION[$this->Config->getAlias() . 'CREATED'] > $this->sessionLifeTime) {
			/* session started more than sessionLifeTime ago*/
			session_regenerate_id(true); // change session ID for the current session and invalidate old session ID
			$_SESSION[$this->Config->getAlias() . 'CREATED'] = time(); // update creation time
		}
		if ($source === 'cedulas.php') {
			$Config = $this->Config;
			$schema = $Config->getSchema();
			$Season = 0;
			$Category = 'null';
			$Language = 'null';
			if(!isset($_COOKIE[$Config->getAlias() . "season"]) || $_COOKIE[$Config->getAlias() . "season"] === ''){
				$sql = "select max(Torneo_ID) Torneo_ID
						from $schema.Torneos
						where Actual = 'S'";
				$result = $Config->query($sql);
				if ($result && $result->num_rows > 0) {
						while($row2 = $result->fetch_assoc()) {
								setcookie($Config->getAlias() . "season",$row2["Torneo_ID"],0,'/');
								$Season = $row2["Torneo_ID"];
						}
				}
			}else{
					$Season = $_COOKIE[$Config->getAlias() . "season"];
			}
			if(!isset($_COOKIE[$Config->getAlias() . "category"]) || $_COOKIE[$Config->getAlias() . "category"] === ''){
					$sql = "select Categoria_ID
							from $schema.Categorias
							where Categoria_ID in ( select Fuerza
													from $schema.Equipos
													where Torneo_ID = $Season)
							order by Categoria_Orden asc
							limit 1;";
					$result = $Config->query($sql);
					if ($result && $result->num_rows > 0) {
							while($row2 = $result->fetch_assoc()) {
									setcookie($Config->getAlias() . "category",$row2["Categoria_ID"],0,'/');
									$Category = $row2["Categoria_ID"];
							}
					}
			}else{
					$Category = $_COOKIE[$Config->getAlias() . "category"];
			}
			if(!isset($_COOKIE[$Config->getAlias() . "language"]) || $_COOKIE[$Config->getAlias() . "language"] === ''){
					$Config->LoadLanguage();
					setcookie($Config->getAlias() . "language",$Config->lan,0,'/');
					$Language = $Config->lan;
			}else{
					$Language = $_COOKIE[$Config->getAlias() . "language"];
			}
		}
		return true;
	}
	
	function ConfirmCSRFToken() {
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		
		if ($this->CSRFTokenRequired) {
			if (($_SESSION[$this->Config->getAlias() . 'CSRFtoken'] == SanitizeHex($_POST['CSRFtoken']) || $_SESSION[$this->Config->getAlias() . 'CSRFtoken'] == SanitizeHex($_GET['CSRFtoken']))) {
				return true;
			} else {
				$this->HandleError($lang['10062']);
				return false;
			}
		}
	}
	
	function AuthenticateChangeRequest() {
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if ($this->CheckLogin('AuthenticateChangeRequest') === false) {
			$this->HandleError($lang['10063']);
			return false;
		}
		
		if ($this->twoFactorAuthMode) {
			$BVname = 'BrowserValidation'.SanitizeUsername($_SESSION[$this->Config->getAlias() . 'username']);
			$BVvalue = $_COOKIE[$this->Config->getAlias() . $BVname];
		} else {
			$BVvalue = '';
		}
		
		if ($this->passwordRequiredForAdministration) {
			if (!$this->CheckLoginInDB(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'username']), $_POST['pwd'], SanitizeHex($BVvalue))) {
				$this->HandleError($lang['10064']);
				return false;
			}
		} else {
			if ($this->ConfirmCSRFToken() === false) {
				// previous method provides its own error
				return false;
			}
		}
		return true;
	}
	
	// End Logging on a User
	
	// 1.4 Logging out a user
	
	function LogOut(){
		session_destroy(); // destroy session data in storage
	}
	
	// End Logging out a User
	
	// 1.5 Getting Session Variables (Likely Unused)
	
	function UserID(){
		return isset($_SESSION[$this->Config->getAlias() . 'userid'])?SanitizeUsername($_SESSION[$this->Config->getAlias() . 'userid']):'not set (error)';
	}
	
	function UserFullName()
	{
		return isset($_SESSION[$this->Config->getAlias() . 'nombre'])?SanitizeRealName($_SESSION[$this->Config->getAlias() . 'nombre']):'';
	}
	
	function UserApellidoP()
	{
		return isset($_SESSION[$this->Config->getAlias() . 'apellidop'])?SanitizeRealName($_SESSION[$this->Config->getAlias() . 'apellidop']):'';
	}
	
	function UserApellidoM()
	{
		return isset($_SESSION[$this->Config->getAlias() . 'apellidom'])?SanitizeRealName($_SESSION[$this->Config->getAlias() . 'apellidom']):'';
	}
	
	function UserTelefono()
	{
		return isset($_SESSION[$this->Config->getAlias() . 'telefono'])?SanitizeRealName($_SESSION[$this->Config->getAlias() . 'telefono']):'';
	}
	
	function UserEquipo()
	{
		return isset($_SESSION[$this->Config->getAlias() . 'equipo'])?SanitizeRealName($_SESSION[$this->Config->getAlias() . 'equipo']):'';
	}
	
	function UserActive()
	{
		return isset($_SESSION[$this->Config->getAlias() . 'active'])?SanitizeRealName($_SESSION[$this->Config->getAlias() . 'active']):'';
	}
	
	function UserEmail()
	{
		return isset($_SESSION[$this->Config->getAlias() . 'email'])?SanitizeRealName($_SESSION[$this->Config->getAlias() . 'email']):'';
	}
	
	// End Getting Session Variables
	
	// 2 Updating User Profiles
	
	// 2.1 Resetting a Password 
	
	function EmailResetPasswordLink(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if(empty($_POST['email'])){
			$this->HandleError($lang['10065']);
			return false;
		}
		
		$email = SanitizeEmail($_POST['email']);
		$username = $this->GetUsernameFromEmail($email);
		
		// If there is no user with this email address act like the reset was successful. This way we do not reveal any information unncessarily.
		if(false === $username){
			return false;
		}
		
		$confirmcode = $this->ChangeConfirmCodeInDB($email);
		
		if(false === $confirmcode){
			$this->HandleError("Sorry, something went wrong on our end.");
			return false;
		}
		
		if(!$this->SendResetPasswordLink($email, $username, $confirmcode)){
			$this->HandleError("Sorry, something went wrong on our end.");
			return false;
		}
		
		return true;
	}
	
	function SendResetPasswordLink($email, $username, $confirmcode){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$username = SanitizeUsername($username);
		$email = SanitizeEmail($email);
		
		$mail = new PHPMailer(true); 
		try{
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$this->Config->getEmailConnectionInfo();
			
			//Server settings
			$mail->SMTPDebug = $this->Config->MAILSMTPDebug;			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = $this->Config->MAILHost;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = $this->Config->MAILSMTPAuth;				// Enable SMTP authentication
			$mail->Username = $this->Config->MAILUsername;				// SMTP username
			$mail->Password = $this->Config->MAILPassword;				// SMTP password
			$mail->Port = $this->Config->MAILPort;


			//Content
			$mail->isHTML(true); // Set email format to HTML
			
			//Recipients
			$mail->setFrom($this->Config->MAILsetFrom, 'Admin ' . $this->sitename);
			//$this->GetFromAddress()
			$mail->addAddress(SanitizeEmail($email),SanitizeUsername($username)); 
			$mail->addReplyTo($this->Config->MAILaddReplyTo, 'Information');
			
			$link = $this->sitename . '/ajax/Login/resetpwd.php?email=' . urlencode($email) . '&code=' . urlencode($confirmcode);
			
			$mail->CharSet = 'UTF-8';
			
			$mail->Subject = 	$lang['10066'] . $this->sitename . $lang['10069'] . $confirmcode;
			$mail->Body =		$lang['10067'] . $username . $lang['10068'] . $this->sitename . $lang['10069'] . $confirmcode . $lang['10070'] . $this->sitename;
			if(!$mail->send()) {
			  $this->HandleError("Mailer error: " . $mail->ErrorInfo);
			}
		} catch (phpmailerException $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
			$this->HandleError("Failed sending registration confirmation email. " . $e->errorMessage());
			return false;
		}
		
		return true;
	}
	
	function ResetPassword($code, $newpassword, $csalt){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if(empty($code)){
			$this->HandleError($lang['10071']);
			return false;
		}
		
		$code = SanitizeHex($code);

		$confirmedemail = SanitizeEmail($this->UpdateDBRecForConfirmation(SanitizeHex(strtolower($code))));
		if(false === $confirmedemail){
			$this->HandleError($lang['10072']);
			return false;
		}        
		
		// BUG: I'm not sure this would work; if the boolean value false was returned, would it still be the boolean value false after being Sanitized?
		$username = SanitizeUsername($this->GetUsernameFromEmail($confirmedemail));
		
		if(false === $username){
			error_log($lang['10073'] . $confirmedemail);
			// This is very strange, so do not let the visitor know anything is wrong; it is unlikely they are one of our users
			return true;
		}
		
		
		if(false === $this->ResetUserPasswordInDB($username, $csalt, $newpassword)){
			$this->HandleError($lang['10074']);
			return false;
		}
		
		$this->NotifyOfNewPassword($confirmedemail, $username);

		return true;
	}
	
	function NotifyOfNewPassword($email, $username){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$username = SanitizeUsername($username);
		$email = SanitizeEmail($email);
		
		$mail = new PHPMailer(true); 
		try{
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$this->Config->getEmailConnectionInfo();
			
			//Server settings
			$mail->SMTPDebug = $this->Config->MAILSMTPDebug;			// Enable verbose debug output
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = $this->Config->MAILHost;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = $this->Config->MAILSMTPAuth;				// Enable SMTP authentication
			$mail->Username = $this->Config->MAILUsername;				// SMTP username
			$mail->Password = $this->Config->MAILPassword;				// SMTP password
			$mail->Port = $this->Config->MAILPort;


			//Content
			$mail->isHTML(true); // Set email format to HTML
			
			//Recipients
			$mail->setFrom($this->Config->MAILsetFrom, 'Admin ' . $this->sitename);
			//$this->GetFromAddress()
			$mail->addAddress(SanitizeEmail($email),SanitizeUsername($username)); 
			$mail->addReplyTo($this->Config->MAILaddReplyTo, 'Information');
			
			$mail->CharSet = 'UTF-8';
			
			$mail->Subject = 	$lang['10075'] . $this->sitename . $lang['10076'];
			$mail->Body =		$lang['10077'] . $username . $lang['10078'] . $this->admin_email . $lang['10079'] . $this->sitename;
			if(!$mail->send()) {
			  $this->HandleError("Mailer error: " . $mail->ErrorInfo);
			}
		} catch (exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
			echo $e->getTraceAsString();	
			$this->HandleError("Failed sending registration confirmation email. " . $e->errorMessage());
			return false;
		}
		
		return true;
	}
	
	// End Resetting a Password 
	
	// 2.2 Changing a Password
	
	function ChangePassword(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if($this->CheckLogin('ChangePassword') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if(empty($_POST['newpwd'])){
			$this->HandleError($lang['10081']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newpwd = $_POST['newpwd'];
		
		if(!$this->ChangePasswordInDB($_SESSION[$this->Config->getAlias() . 'username'], $newpwd)){
			return false;
		}
		
		$this->NotifyOfNewPassword($this->GetEmailFromUsername($_SESSION[$this->Config->getAlias() . 'username']), $_SESSION[$this->Config->getAlias() . 'username']);
		
		return true;
	}
	
	// End Changing a Password
	
	// 2.3 Changing an Email Address
	
	function ChangeEmailAddress(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if($this->CheckLogin('ChangeEmailAddress') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if(empty($_POST['newemail'])){
			$this->HandleError($lang['10082']);
			return false;
		}
		
		if(md5($_POST['newemailrepeat']) != md5($_POST['newemail'])){
			$this->HandleError($lang['10083']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newemail = SanitizeEmail(trim($_POST['newemail']));
		if($newemail != trim($_POST['newemail'])){
			$this->HandleError($lang['10084']);
			return false;
		}
		
		if(!$this->ChangeEmailInDB($_SESSION[$this->Config->getAlias() . 'username'], $newemail)){
			return false;
		}
		
		$_SESSION[$this->Config->getAlias() . "email"] = $newemail;
		
		return true;
	}
	
	// End Changing an Email Address
	
	// 2.4 Changing a 'Real Name'

	function ChangeName(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		if($this->CheckLogin('fg->ChangeName') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newname = SanitizeNonNumericText(trim($_POST['newname']));
		// Removing the "Real Name" value is a valid request
		if(empty($_POST['newname'])){
			$newname = "";
		}
		if(!$this->ChangeNameInDBName(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'nombre']), $newname)){
			// previous method provides its own error
			return false;
		}
	
		$_SESSION[$this->Config->getAlias() . "nombre"] = $newname;
		return true;
	}
	
	function ChangeLastName(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		if($this->CheckLogin('fg->ChangeLastName') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newlastName = SanitizeNonNumericText(trim($_POST['newlastName']));
		// Removing the "Real Name" value is a valid request
		if(empty($_POST['newlastName'])){
			$newlastName = "";
		}
		if(!$this->ChangeNameInDBLastName(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'apellidop']), $newlastName)){
			// previous method provides its own error
			return false;
		}
	
		$_SESSION[$this->Config->getAlias() . "apellidop"] = $newlastName;
		return true;
	}
	
	function ChangeLastName2(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		if($this->CheckLogin('fg->ChangeLastName') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newlastName2 = SanitizeNonNumericText(trim($_POST['newlastName2']));
		// Removing the "Real Name" value is a valid request
		if(empty($_POST['newlastName2'])){
			$newlastName2 = "";
		}
		if(!$this->ChangeNameInDBLastName2(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'apellidom']), $newlastName2)){
			// previous method provides its own error
			return false;
		}
	
		$_SESSION[$this->Config->getAlias() . "apellidom"] = $newlastName2;
		return true;
	}
	
	function ChangePhone(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		if($this->CheckLogin('fg->ChangeLastName') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newphone = SanitizeNonNumericText(trim($_POST['newphone']));
		// Removing the "Real Name" value is a valid request
		if(empty($_POST['newphone'])){
			$newphone = "";
		}
		if(!$this->ChangeNameInDBPhone(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'telefono']), $newphone)){
			// previous method provides its own error
			return false;
		}
	
		$_SESSION[$this->Config->getAlias() . "telefono"] = $newphone;
		return true;
	}
	
	function ChangeEmail(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		if($this->CheckLogin('fg->ChangeLastName') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newemail = SanitizeNonNumericText(trim($_POST['newemail']));
		// Removing the "Real Name" value is a valid request
		if(empty($_POST['newemail'])){
			$newemail = "";
		}
		if(!$this->ChangeNameInDBEmail(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'email']), $newemail)){
			// previous method provides its own error
			return false;
		}
	
		$_SESSION[$this->Config->getAlias() . "email"] = $newemail;
		return true;
	}
	
	function ChangeTeam(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		if($this->CheckLogin('fg->ChangeLastName') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newteam = SanitizeNonNumericText(trim($_POST['newequipo']));
		// Removing the "Real Name" value is a valid request
		if(empty($_POST['newequipo'])){
			$newteam = "";
		}
		if(!$this->ChangeNameInDBEmail(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'equipo']), $newteam)){
			// previous method provides its own error
			return false;
		}
	
		$_SESSION[$this->Config->getAlias() . "equipo"] = $newteam;
		return true;
	}
	
	function ChangeActive(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		if($this->CheckLogin('fg->ChangeLastName') === false){
			$this->HandleError($lang['10080']);
			return false;
		}
		
		if ($this->AuthenticateChangeRequest() === false){
			// previous method provides its own error
			return false;
		}
		
		// Request has been authenticated, proceed.
		$newactive = SanitizeNonNumericText(trim($_POST['newactive']));
		// Removing the "Real Name" value is a valid request
		if(empty($_POST['newactive'])){
			$newactive = "";
		}
		if(!$this->ChangeNameInDBEmail(SanitizeUsername($_SESSION[$this->Config->getAlias() . 'active']), $newactive)){
			// previous method provides its own error
			return false;
		}
	
		$_SESSION[$this->Config->getAlias() . "active"] = $newactive;
		return true;
	}
	
	// End Changing a 'Real Name
	
	// 2.5 Disabling a Browser

	function DisableBrowser() {
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if($this->CheckLogin('DisableBrowser') === false)
		{
			$this->HandleError($lang['10080']);
			return false;
		}
		
		$browser_id = SanitizeInteger($_POST['browserID']);

		return $this->DisableBrowserInDB($browser_id);
	}
	
	// End Disabling a Browser
	
	// End Updating User Profiles
	
	// 3 Infrastructure
	
	// 3.1 PHP Helpers
	function GetSelfScript()
	{
		return htmlentities($_SERVER['PHP_SELF']);
	}    
	
	function SafeDisplay($value_name)
	{
		if(empty($_POST[$value_name]))
		{
			return'';
		}
		return htmlentities($_POST[$value_name]);
	}
	
	function RedirectToURL($url)
	{
		header("Location: $url");
		exit;
	}
	
	function GetSpamTrapInputName()
	{
		return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
	}
	
	function GetErrorMessage()
	{
		if(empty($this->error_message))
		{
			return '';
		}
		$errormsg = nl2br($this->error_message);
		return $errormsg;
	}    
	//-------Private Helper functions-----------
	
	function HandleError($err)
	{
		$this->error_message .= $err;
	}
	
	function HandleDBError($err)
	{
		$this->HandleError($err."\r\n mysqlerror:".mysql_error());
	}
	
	function GetFromAddress()
	{
		if(!empty($this->from_address))
		{
			return $this->from_address;
		}

		$host = $_SERVER['SERVER_NAME'];

		$from ="nobody@$host";
		return $from;
	} 
	
	function GetAbsoluteURLFolder()
	{
		$scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
		$scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
		return $scriptFolder;
	}
	
	// End PHP Helpers
	
	// 3.2 MySQL Helpers
	function EnsureTable(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError(['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Show tables like 'usuarios'")){
			$stmt->execute();
			$stmt->bind_result($tables);
			$stmt->fetch();
			$stmt->close();
		}

		
		mysqli_close($connection);
 
		if(!isset($tables))
		{
			return $this->CreateTable();
		}
	
		return true;
	}
	
	function EnsureBrowserTable(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if (!$this->EnsureTable()) {
			$this->HandleError($lang['10085']);
			return false;
		}
		
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Show tables like 'registeredBrowsers'")){
			$stmt->execute();
			$stmt->bind_result($tables);
			$stmt->fetch();
			$stmt->close();
		}

		mysqli_close($connection);
 
		if(!isset($tables))
		{
			return $this->CreateBrowserTable();
		}
	
		return true;
	}
	
	function EnsureTransactionTable(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if (!$this->EnsureTable()) {
			$this->HandleError(['10087']);
			return false;
		}
		
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Show tables like 'transactions'")){
			$stmt->execute();
			$stmt->bind_result($tables);
			$stmt->fetch();
			$stmt->close();
		}

		mysqli_close($connection);
 
		if(!isset($tables))
		{
			return $this->CreateTransactionTable();
		}
	
		return true;
	}
	
	function CreateTable(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
	
		if($stmt = $connection->prepare("Create Table " . $this->Config->getSchema() . ".usuarios (".
				"id_user INT NOT NULL AUTO_INCREMENT ,".
				"name VARCHAR( 64 ) NULL ,".
				"email VARCHAR( 64 ) NOT NULL ,".
				"lastEmail VARCHAR( 64 ) NOT NULL ,".
				"username VARCHAR( 64 ) NOT NULL ,".
				"password CHAR( 80 ) NOT NULL ,".
				"csalt CHAR( 32 ) NULL ,".
				"salt CHAR( 32 ) NOT NULL ,".
				"iterations INT UNSIGNED NOT NULL ,".
				"confirmcode VARCHAR(8) NULL ,".
				"confirmtime datetime NULL ,".
				"paymentProblem TINYINT ( 1 ) UNSIGNED NOT NULL DEFAULT 0,".
				"totalSpending DECIMAL( 11,2 ) UNSIGNED NOT NULL DEFAULT 0,".
				"credit DECIMAL( 11,2 ) UNSIGNED NOT NULL DEFAULT 0,".
				"accountOrigin TINYINT UNSIGNED NOT NULL DEFAULT 0,".
				"message VARCHAR(300) NULL ,".
				"adminMessage VARCHAR(300) NULL ,".
				"PRIMARY KEY ( id_user )".
				")"))
		{
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10117'] . $stmt->errno . ") " . $stmt->error);
				return false;
			}
			$stmt->close();
		} else
		{
			$this->HandleDBError($lang['10091']);
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}
	
	function CreateBrowserTable(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
	
		if($stmt = $connection->prepare("Create Table " . $this->Config->getSchema() . ".registeredBrowsers (".
				"id_browser INT NOT NULL AUTO_INCREMENT ,".
				"active TINYINT( 1 ) NOT NULL DEFAULT 0,".
				"secret CHAR( 80 ) NOT NULL ,".
				"id_user INT NOT NULL ,".
				"ip_address CHAR( 15 ) NOT NULL ,".
				"platform VARCHAR( 32) NOT NULL ,".
				"email VARCHAR( 64 ) NOT NULL ,".
				"time_registered timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,".
				"disabled_by  VARCHAR( 80 ) NULL ,".
				"CONSTRAINT FOREIGN KEY (id_user) REFERENCES usuarios (id_user),".
				"PRIMARY KEY ( id_browser )".
				")"))
		{
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10117'] . $stmt->errno . ") " . $stmt->error);
				return false;
			}
			$stmt->close();
		} else
		{
			$this->HandleDBError($lang['10091']);
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}
	
	function CreateTransactionTable(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
	
		if($stmt = $connection->prepare("Create Table " . $this->Config->getSchema() . ".transactions (".
				"skey INT NOT NULL AUTO_INCREMENT ,".
				"id_user INT NOT NULL ,".
				"reason VARCHAR(255) ,".
				"price DECIMAL(8,2) ,".
				"date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,".
				"paid TINYINT( 1 ) NOT NULL DEFAULT 0 ,".
				"paymentId VARCHAR(40) NULL,".
				"CONSTRAINT FOREIGN KEY (id_user) REFERENCES usuarios (id_user) ,".
				"PRIMARY KEY (skey) ".
				")"))
		{
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10117'] . $stmt->errno . ") " . $stmt->error);
				return false;
			}
			$stmt->close();
		} else
		{
			$this->HandleDBError($lang['10091']);
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}
	
	// This function only works with preconfigured fields in the database.
	// This limitation is due to the inability to set column names in prepared statements with PHP's mysqli
	function IsFieldUnique($column,$content){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		} 
	
		switch ($column) {
			case 'username':
				$content = SanitizeText($content);
				if($stmt = $connection->prepare("Select username from " . $this->Config->getSchema() . ".usuarios where username=?")){
					$stmt->bind_param("s", $content);
					$stmt->execute();
					$stmt->bind_result($username);
					$stmt->fetch();
					$stmt->close();
				}
				break;
				
			case 'email':
				$content = SanitizeEmail($content);
				if($stmt = $connection->prepare("Select username from " . $this->Config->getSchema() . ".usuarios where email=?")){
					$stmt->bind_param("s", $content);
					$stmt->execute();
					$stmt->bind_result($username);
					$stmt->fetch();
					$stmt->close();
				}
				break;
				
			case 'salt':
				$content = SanitizeHex($content);
				if($stmt = $connection->prepare("Select username from " . $this->Config->getSchema() . ".usuarios where salt=?")){
					$stmt->bind_param("s", $content);
					$stmt->execute();
					$stmt->bind_result($username);
					$stmt->fetch();
					$stmt->close();
				}
				break;
				
			case 'confirmcode':
				$content = SanitizeHex($content);
				if($stmt = $connection->prepare("Select username from " . $this->Config->getSchema() . ".usuarios where confirmcode=?")){
					$stmt->bind_param("s", $content);
					$stmt->execute();
					$stmt->bind_result($username);
					$stmt->fetch();
					$stmt->close();
				}
				break;
			
			default:
				error_log($lang['10088'] . $column);
				return false;
		}

		mysqli_close($connection);
 
		if($username){
			return false;
		}
		
		return true;
	}
	
	// End MySQL Helpers
	
	// End PHP Helpers
	
	// 3.2 MySQL Helpers
	function DBLogin()
	{
		return $this->Config->connectFG();
	}    
	
	// 3.3 MySQL Actors
	
	// 3.3.1 MySQL Inserts
	function InsertIntoDB(&$formvars){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		$connection = $this->DBLogin();
		if(!$connection){
			$this->HandleError($lang['10086']);
			return false;
		}
	
		// do not allow duplicate confirmcodes
		while (!isset($confirmcode) || !$this->IsFieldUnique('confirmcode', $confirmcode)) {
			$confirmcode = SanitizeHex(substr(bin2hex(random_bytes(8)), -8));
			//SanitizeHex(substr(bin2hex(random_bytes(8)), -8));
		}
		
		$formvars['confirmcode'] = $confirmcode;
		
		// do not allow duplicate salts
		while (!isset($salt) || !$this->IsFieldUnique('salt', $salt)) {
		   $salt = bin2hex(random_bytes(16));
		}

		$protected_password = hash_pbkdf2("sha512", $formvars['password'], $salt, $this->newIterations, 80);
		
		if($stmt = $connection->prepare("insert into " . $this->Config->getSchema() . ".usuarios(
				name,
				ApellidoP,
				ApellidoM,
				phone_number,
				email,
				username,
				Equipo_ID,
				password,
				csalt,
				salt,
				iterations,
				confirmcode,
				confirmtime,
				totalSpending,
				accountOrigin
				)
				values
				(?,?,?,?,?,?,1000,?,?,?,?,?,?,?,?)"))
			{
				$a = 0;
				$b = 0;
				$stmt->bind_param("sssisssssissii",
					SanitizeRealName($formvars['nombre']),
					SanitizeRealName($formvars['apellidop']),
					SanitizeRealName($formvars['apellidom']),
					SanitizeInteger($formvars['telefono']),
					SanitizeEmail($formvars['email']),
					SanitizeUsername($formvars['username']),
					SanitizeHex($protected_password),
					SanitizeHex($formvars['salt']),
					SanitizeHex($salt),
					SanitizeInteger($this->newIterations),
					SanitizeHex($confirmcode),
					date("Y:m:d H:i:s", strtotime("+3 day")),
					$a,
					$b
					);
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10090'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else{
			$this->HandleDBError($lang['10091']);
			mysqli_close($connection);
			return false;
		}
		if(SanitizeTextComa($formvars['equipo']) == '0' || SanitizeTextComa($formvars['equipo']) == '-1'){
		    if($stmt = $connection->prepare("insert into " . $this->Config->getSchema() . ".usuarios_equipo
                                			select '" . $formvars['username'] ."', ".$formvars['equipo'])){
				if (!$stmt->execute()){
				    $error = $stmt->error;
					$this->HandleDBError($lang['10090'] . $stmt->errno . ") " . $stmt->error);
					mysqli_close($connection);
					return false;
				}
				$stmt->close();
			}else{
				    $error = $stmt->error;
						echo "33 2 - > " . $error;
				$this->HandleDBError($lang['10091']);
				mysqli_close($connection);
				return false;
			}
		    
		}else{
		    if($stmt = $connection->prepare("insert into " . $this->Config->getSchema() . ".usuarios_equipo
                                			select '" . $formvars['username'] ."', Equipo_ID from " . $this->Config->getSchema() . ".Equipos
                                            where FIND_IN_SET(Equipo_ID, '" . SanitizeTextComa($formvars['equipo']) . "')")){
				if (!$stmt->execute()){
				    $error = $stmt->error;
					$this->HandleDBError($lang['10090'] . $stmt->errno . ") " . $stmt->error);
					mysqli_close($connection);
					return false;
				}
				$stmt->close();
			}else{
				    $error = $stmt->error;
						echo "33 2 - > " . $error;
				$this->HandleDBError($lang['10091']);
				mysqli_close($connection);
				return false;
			}
		}
		mysqli_close($connection);
		return true;
	}
	
	function InsertIntoDBUpdate(&$formvars){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
	
		$connection = $this->DBLogin();
		if(!$connection){
			$this->HandleError($lang['10086']);
			return false;
		}
		
		// do not allow duplicate confirmcodes
		while (!isset($confirmcode) || !$this->IsFieldUnique('confirmcode', $confirmcode)) {
			$confirmcode = SanitizeHex(substr(bin2hex(random_bytes(8)), -8));
			//SanitizeHex(substr(bin2hex(random_bytes(8)), -8));
		}
		$formvars['confirmcode'] = $confirmcode;
		$error = '';
		try{
			if($stmt = $connection->prepare("update " . $this->Config->getSchema() . ".usuarios
					set name = ?,
						ApellidoP = ?,
						ApellidoM = ?,
						phone_number = ?,
						Equipo_ID = 1000,
						email = ?,
						confirmcode = ?,
						confirmtime = ?,
						active = ?
					where id_user = ?")){
					$stmt->bind_param("sssisssii",
						SanitizeRealName($formvars['nombre']),
						SanitizeRealName($formvars['apellidop']),
						SanitizeRealName($formvars['apellidom']),
						SanitizeInteger($formvars['telefono']),
						SanitizeEmail($formvars['email']),
						SanitizeHex($confirmcode),
						date("Y:m:d H:i:s", strtotime("+3 day")),
						SanitizeInteger($formvars['active']),
						SanitizeInteger($formvars['userid'])
						);
				if (!$stmt->execute()){
				    $error = $stmt->error;
					$this->HandleDBError($lang['10090'] . $stmt->errno . ") " . $stmt->error);
					mysqli_close($connection);
					return false;
				}
				$stmt->close();
			}else{
				    $error = $stmt->error;
						echo "95 2 - > " . $error;
				$this->HandleDBError($lang['10091']);
				mysqli_close($connection);
				return false;
			}
			if($stmt = $connection->prepare("delete from " . $this->Config->getSchema() . ".usuarios_equipo where username = ?")){
					$stmt->bind_param("s",
						SanitizeUsername($formvars['username'])
						);
				if (!$stmt->execute()){
				    $error = $stmt->error;
					$this->HandleDBError($lang['10090'] . $stmt->errno . ") " . $stmt->error);
					mysqli_close($connection);
					return false;
				}
				$stmt->close();
			}else{
				    $error = $stmt->error;
						echo "13 2 - > " . $error;
				$this->HandleDBError($lang['10091']);
				mysqli_close($connection);
				return false;
			}
			if(SanitizeTextComa($formvars['equipo']) == '0' || SanitizeTextComa($formvars['equipo']) == '-1'){
			    if($stmt = $connection->prepare("insert into " . $this->Config->getSchema() . ".usuarios_equipo
                                    			select '" . $formvars['username'] ."', ".$formvars['equipo'])){
    				if (!$stmt->execute()){
    				    $error = $stmt->error;
    					$this->HandleDBError($lang['10090'] . $stmt->errno . ") " . $stmt->error);
    					mysqli_close($connection);
    					return false;
    				}
    				$stmt->close();
    			}else{
    				    $error = $stmt->error;
    						echo "33 2 - > " . $error;
    				$this->HandleDBError($lang['10091']);
    				mysqli_close($connection);
    				return false;
    			}
			    
			}else{
			    if($stmt = $connection->prepare("insert into " . $this->Config->getSchema() . ".usuarios_equipo
                                    			select '" . $formvars['username'] ."', Equipo_ID from " . $this->Config->getSchema() . ".Equipos
                                                where FIND_IN_SET(Equipo_ID, '" . SanitizeTextComa($formvars['equipo']) . "')")){
    				if (!$stmt->execute()){
    				    $error = $stmt->error;
    					$this->HandleDBError($lang['10090'] . $stmt->errno . ") " . $stmt->error);
    					mysqli_close($connection);
    					return false;
    				}
    				$stmt->close();
    			}else{
    				    $error = $stmt->error;
    						echo "33 2 - > " . $error;
    				$this->HandleDBError($lang['10091']);
    				mysqli_close($connection);
    				return false;
    			}
			}
			
		}catch (Exception $e) {
			echo $e->errorMessage();
		}
		mysqli_close($connection);
		return true;
	}
	
	function UpdateDBforBrowserVerification($email, $IP, $description){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}

		if($stmt = $connection->prepare("Select id_user from " . $this->Config->getSchema() . ".usuarios where email=?")){
			$stmt->bind_param("s", SanitizeEmail($email));
			$stmt->execute();
			$stmt->bind_result($id_user);
			$stmt->fetch();
			$stmt->close();
		}

		if(!$id_user)
		{
			$this->HandleError($lang['10089']);
			mysqli_close($connection);
			return false;
		}

		$secret = bin2hex(random_bytes(40));

		if($stmt = $connection->prepare("Insert into " . $this->Config->getSchema() . ".registeredBrowsers (active, secret, id_user, ip_address, platform, email) values (1,?,?,?,?,?)"))
		{
			$stmt->bind_param("sisss", SanitizeHex($secret), SanitizeInteger($id_user), SanitizeFloat($IP), SanitizeBrowserName($description), SanitizeEmail($email));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10092'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		}
		mysqli_close($connection);
		return $secret;
	}
	
	// End MySQL Inserts
	
	// 3.3.2 MySQL Updates
	function DisableBrowserInDB($browser_id){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		$BVname = 'BrowserValidation'.SanitizeUsername($_SESSION[$this->Config->getAlias() . 'username']);

		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".registeredBrowsers set active='0', disabled_by=? where id_browser=?")){
			$stmt->bind_param("si", SanitizeHex($_COOKIE[$this->Config->getAlias() . $BVname]), SanitizeInteger($browser_id));
				if (!$stmt->execute()) {
					$this->HandleDBError($lang['10093'] . $stmt->errno . ") " . $stmt->error);
					mysqli_close($connection);
					return false;
				}
			$stmt->close();
		}
		
		mysqli_close($connection);
		return true;
	}
	
	// This function veririfes that the confirmcode is good, resets it to 'y', and returns results with a boolean false or the email address of the user
	function validateConfirmation($confirmcode){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Select count(*) num from " . $this->Config->getSchema() . ".usuarios where confirmcode=?")){
			$stmt->bind_param("s", SanitizeHex($confirmcode));
			$stmt->execute();
			$stmt->bind_result($num);
			$stmt->fetch();
			$stmt->close();
		}
		
		if($num == 0){
			$this->HandleError($lang['10132']);
			mysqli_close($connection);
			return false;
		}
		
		mysqli_close($connection);   
		
		return true;
	}
	
	// This function veririfes that the confirmcode is good, resets it to 'y', and returns results with a boolean false or the email address of the user
	function UpdateDBRecForConfirmation($confirmcode){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Select email, confirmtime from " . $this->Config->getSchema() . ".usuarios where confirmcode=?")){
			$stmt->bind_param("s", SanitizeHex($confirmcode));
			$stmt->execute();
			$stmt->bind_result($email, $confirmtime);
			$stmt->fetch();
			$stmt->close();
		}
		
		if(gettype($email) != "string")
		{
			$this->HandleError($lang['10094'] . $email);
			mysqli_close($connection);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set confirmcode='y' where email=?"))
		{
			$stmt->bind_param("s", $email);
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10095'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		}

		if (strtotime($confirmtime)<strtotime(date("Y:m:d H:i:s"))) {
			$this->HandleError($lang['10096']);
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);   
		
		return $email;
	}
	
	function ResetUserPasswordInDB($username, $csalt, $newpassword){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}

		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set csalt=? where username=?"))
		{
			$stmt->bind_param("ss", $csalt, $username);
			if (!$stmt->execute())
			{
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		}
		
		return $this->ChangePasswordInDB($username,$newpassword);
	}
	
	function ChangePasswordInDB($username, $newpwd){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}   
		
		$haveGoodSalt = false;
		
		while (!$haveGoodSalt) {
			$salt = bin2hex(random_bytes(16));  
			$haveGoodSalt = $this->IsFieldUnique("salt", $salt);
		}
		
		$protected_password = hash_pbkdf2("sha512", $newpwd, $salt, $this->newIterations, 80);

		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set password=?, salt=?, iterations=? where username=?"))
		{
			$stmt->bind_param("ssis", SanitizeHex($protected_password), SanitizeHex($salt), SanitizeInteger($this->newIterations), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10097'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}
	
	// function returns confirmcode or boolean false if failure; this function only allows a 2 hour window for confirmation by user
	function ChangeConfirmCodeInDB($email){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection){
			$this->HandleError($lang['10086']);
			return false;
		}
		
		
		// do not duplicate a confirmcode
		while (!isset($confirmcode) || !$this->IsFieldUnique('confirmcode', $confirmcode)) {
			$confirmcode = SanitizeHex(substr(bin2hex(random_bytes(8)), -8));
		}
		//$confirmcode = "01234567";

		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set confirmcode=?, confirmtime=? where email=?")){
			$stmt->bind_param("sss", SanitizeHex($confirmcode), date("Y:m:d H:i:s", strtotime("+2 hour")), SanitizeEmail($email));
			if (!$stmt->execute()){
				$this->HandleDBError($lang['10098'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		}else{
			$this->HandleError($lang['10099'] . $connection->errno . ": " . $connection->erro);
			mysqli_close($connection);
			return false;
		}
		
		/*
		$datetime = new DateTime();
		$txt = "" . $datetime->format('Y-m-d H:i:s') . "\n";
		$txt .= "-------------------------------------\n";
		$txt .= "ok \n";
		$txt .= "-------------------------------------\n"; 
		$myfile = file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/Desarrollo/hectorbarraza/log/log.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);	
		*/

		mysqli_close($connection);
		return $confirmcode;
	}
	
	function ChangeEmailInDB($username, $email){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if($this->IsFieldUnique("email", $email)) {
			$connection = $this->DBLogin();
			if(!$connection)
			{
				$this->HandleError($lang['10086']);
				mysqli_close($connection);
				return false;
			}
			if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set lastEmail=email where username=?"))
			{
				$stmt->bind_param("s", SanitizeUsername($username));
				if (!$stmt->execute())
				{
					$this->HandleDBError($lang['10100'] . $stmt->errno . ") " . $stmt->error);
					mysqli_close($connection);
					return false;
				}
				$stmt->close();
			} else
			{
				mysqli_close($connection);
				return false;
			}
			if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set email=? where username=?"))
			{
				$stmt->bind_param("ss", SanitizeEmail($email), SanitizeUsername($username));
				if (!$stmt->execute())
				{
					$this->HandleDBError($lang['10101'] . $stmt->errno . ") " . $stmt->error);
					mysqli_close($connection);
					return false;
				}
				$stmt->close();
			} else
			{
				mysqli_close($connection);
				return false;
			}
		} else {
			$this->HandleError($lang['10102']);
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}
	
	function ChangeNameInDB($username, $name){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set name=? where username=?"))
		{
			$stmt->bind_param("ss", SanitizeRealName($name), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function ChangeNameInDBName($username, $name){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set name=? where username=?"))
		{
			$stmt->bind_param("ss", SanitizeRealName($name), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function ChangeNameInDBLastName($username, $lastname){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set ApellidoP = ? where username = ?"))
		{
			$stmt->bind_param("ss", SanitizeRealName($lastname), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function ChangeNameInDBLastName2($username, $lastname2){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set ApellidoM = ? where username = ?"))
		{
			$stmt->bind_param("ss", SanitizeRealName($lastname2), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function ChangeNameInDBPhone($username, $phone){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set phone_number = ? where username = ?"))
		{
			$stmt->bind_param("is", SanitizeRealName($pnone), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function ChangeNameInDBEmail($username, $email){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set email = ? where username = ?"))
		{
			$stmt->bind_param("ss", SanitizeRealName($email), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function ChangeNameInDBActive($username, $active){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set active = ? where username = ?"))
		{
			$stmt->bind_param("is", SanitizeRealName($active), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function ChangeNameInDBTeam($username, $team){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set Equipo_ID = ? where username = ?"))
		{
			$stmt->bind_param("is", SanitizeRealName($team), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10103'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function MarkUserAsHavingBillingInfoProblem($username) {
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set paymentProblem='1' where username=?"))
		{
			$stmt->bind_param("s", SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10104'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		mysqli_close($connection);
		return true;
	}

	function SetUserMessage($username, $message){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}

		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set message=? where username=?"))
		{
			$stmt->bind_param("ss", SanitizeBrowserName($message), SanitizeUsername($username));
			if (!$stmt->execute())
			{
				$this->HandleDBError($lang['10105'] . $stmt->errno . ") " . $stmt->error);
				mysqli_close($connection);
				return false;
			}
			$stmt->close();
		} else
		{
			mysqli_close($connection);
			return false;
		}
		
		mysqli_close($connection);
		return true;
	}
	
	// End MySQL Updates
	
	// 3.3.3 MySQL Selects
	
	function CheckLoginInDB($username,$password, $browserverification){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		if ($this->CSRFTokenRequired) {
			if (!($_SESSION[$this->Config->getAlias() . 'CSRFtoken'] == SanitizeHex($_POST['CSRFtoken']) || $_SESSION[$this->Config->getAlias() . 'CSRFtoken'] == SanitizeHex($_GET['CSRFtoken']))) {
				$this->HandleError($lang['10106']);
				return false;
			}
		}
		
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}           
		
		$usernameS = SanitizeUsername($username);
		// Check if User is active
		if($stmt = $connection->prepare("Select count(username) from " . $this->Config->getSchema() . ".usuarios where username=? and active = 1")){
			$stmt->bind_param("s", $usernameS);
			$stmt->execute();
			$stmt->bind_result($exist);
			$stmt->fetch();
			$stmt->close();
		}
		if($exist == 0)
		{
			$this->HandleError($lang['10133']);
			mysqli_close($connection);
			return false;
		}
		
		// These two steps attempt to identify which user is logging in, first by username and then by email address
		if($stmt = $connection->prepare("Select salt, iterations from " . $this->Config->getSchema() . ".usuarios where username=?")){
			$stmt->bind_param("s", $usernameS);
			$stmt->execute();
			$stmt->bind_result($salt, $iterations);
			$stmt->fetch();
			$stmt->close();
		}
		
		$usernameE = SanitizeEmail($username);
		if(!$salt || !$iterations)
		{
			if($stmt = $connection->prepare("Select salt, iterations, username from " . $this->Config->getSchema() . ".usuarios where email=?")){
				$stmt->bind_param("s", $usernameE);
				$stmt->execute();
				$stmt->bind_result($salt, $iterations, $username);
				$stmt->fetch();
				$stmt->close();
			}
		}
		
		// neither worked, so this is not a registered user
		if(!$salt || !$iterations)
		{
			$this->HandleError($lang['10107']);
			mysqli_close($connection);
			return false;
		}
		
		// Ok, we know the username now and should sanitize it for further use
		$username = SanitizeUsername($username);
		$pwdhash = SanitizeHex(hash_pbkdf2("sha512", $password, $salt, $iterations, 80));
		//echo $pwdhash;
		if($stmt = $connection->prepare("Select a.username, phone_number, name, ApellidoP, ApellidoM, email, GROUP_CONCAT(b.Equipo_ID) Equipo_ID, id_user, message, adminMessage, paymentProblem, credit, active 
                                            from " . $this->Config->getSchema() . ".usuarios a
                                            	join " . $this->Config->getSchema() . ".usuarios_equipo b on a.username = b.username
                                            where a.username=? and password=?
                                            group by a.username")) {
			$stmt->bind_param("ss", $username, $pwdhash);
			$stmt->execute();
			$stmt->bind_result($username, $phone_number, $name, $ApellidoP, $ApellidoM, $email, $Equipo_ID, $id_user, $message, $adminMessage, $paymentProblem, $credit, $active);
			$stmt->fetch();
			$stmt->close();
		}

		if (!isset($email)) {
			//$this->HandleError($lang['10108'] . ' - "' . $pwdhash . '", password = "' . $password . '", salt = "' . $salt . '", iteration = "' . $iterations . '"');
			$this->HandleError($lang['10108']);
			mysqli_close($connection);
			return false;
		}

		// twoFactorAuthMode requires the user to login with a registeredBrowser. Without twoFactorAuthMode we are just checking that the user has confirmed their email address.
		if ($this->twoFactorAuthMode) {
			if($stmt = $connection->prepare("Select id_user, id_browser from " . $this->Config->getSchema() . ".registeredBrowsers where secret=? AND id_user=? AND active='1'")){
				$stmt->bind_param("si", SanitizeHex($browserverification), SanitizeInteger($id_user));
				$stmt->execute();
				$stmt->bind_result($found_id, $browser_id);
				$stmt->fetch();
				$stmt->close();
			}
			
			if (isset($found_id) && strcmp(SanitizeInteger($found_id),SanitizeInteger($id_user)) == 0) {
				$browserKnown = true;
			} else {
				$this->HandleError($lang['10109']);
				mysqli_close($connection);
				return false;
			} 
		} else {
			if($stmt = $connection->prepare("Select confirmcode from " . $this->Config->getSchema() . ".usuarios where username=?")){
				$stmt->bind_param("s", $usernameS);
				$stmt->execute();
				$stmt->bind_result($confirmcode);
				$stmt->fetch();
				$stmt->close();
			}
			// BUG: If a user has requested a password reset and not used it; they will be denied access here
			if ($confirmcode !== "y") {
				 $this->HandleError($lang['10110']);
				mysqli_close($connection);
				return false;
			}
		}
		
		if(empty($username) || empty($email)) {
			$this->HandleError($lang['10111']);
			mysqli_close($connection);
			return false;
		}

		// let's reset messages for this user; they have been collected for serving
		if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set message=NULL, adminMessage=NULL where username=? and password=?")){
			$stmt->bind_param("ss", $usernameS, SanitizeHex($pwdhash));
			if (!$stmt->execute()) {
				// not sure what to do here; not exactly good but not critical either
			}
			$stmt->close();
		}
		
		$_SESSION[$this->Config->getAlias() . 'userID'] = SanitizeInteger($id_user);
		$_SESSION[$this->Config->getAlias() . 'username'] = SanitizeUsername($username);
		$_SESSION[$this->Config->getAlias() . 'nombre']  = SanitizeUsername($name);
		$_SESSION[$this->Config->getAlias() . 'apellidop'] = SanitizeUsername($ApellidoP);
		$_SESSION[$this->Config->getAlias() . 'apellidom']  = SanitizeUsername($ApellidoM);
		$_SESSION[$this->Config->getAlias() . 'telefono'] = SanitizeInteger($phone_number);
		$_SESSION[$this->Config->getAlias() . 'equipo']  = SanitizeTextComa($Equipo_ID);
		$_SESSION[$this->Config->getAlias() . 'email'] = SanitizeEmail($email);
		$_SESSION[$this->Config->getAlias() . 'active'] = SanitizeEmail($active);
		
		if ($this->twoFactorAuthMode) {
			$_SESSION[$this->Config->getAlias() . 'browserID'] = SanitizeInteger($browser_id);
		}
		$_SESSION[$this->Config->getAlias() . 'credit'] = SanitizeFloat($credit);

		// if we recorded a billing problem for the user that remains unresolved, let them know to fix it
		$_SESSION[$this->Config->getAlias() . 'problemBillingUser'] = ($paymentProblem == '1');
		if ($_SESSION[$this->Config->getAlias() . 'problemBillingUser'] === true) {
			$_SESSION[$this->Config->getAlias() . "messageForUser"] = true;
			$_SESSION[$this->Config->getAlias() . 'message'] = SanitizeBrowserName("Your billing information is invalid, please update it.");
		}

		// if there are messages for the user, set them in the session
		if (!empty($message)) {
			$_SESSION[$this->Config->getAlias() . "messageForUser"] = true;
			$_SESSION[$this->Config->getAlias() . 'message'] = SanitizeBrowserName($message);
		} else  if (!isset($_SESSION[$this->Config->getAlias() . "messageForUser"])) {
			$_SESSION[$this->Config->getAlias() . "messageForUser"] = false;
		}

		if (!empty($adminMessage)) {
			$_SESSION[$this->Config->getAlias() . "adminMessageForUser"] = true;
			$_SESSION[$this->Config->getAlias() . 'adminMessage'] = SanitizeBrowserName($adminMessage);
		} else  if (!isset($_SESSION[$this->Config->getAlias() . "adminMessageForUser"])) {
			$_SESSION[$this->Config->getAlias() . "adminMessageForUser"] = false;
		}
		
		/*
		$txt =  "\n";
		$txt .= "\n";
		$txt .= "-------------------------------------\n";
		$txt .= "Select username, phone_number, name, ApellidoP, ApellidoM, email, Equipo_ID, id_user, message, adminMessage, paymentProblem, credit from " . 		$this->Config->getSchema() . ".usuarios where username=? and password=?" . "\n";
		$txt .= $username . "\n";
		$txt .= $pwdhash . "\n";
		$txt .= "-------------------------------------"; 
		$myfile = file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/Desarrollo/hectorbarraza/log/log.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);		 
		*/
		
		
		mysqli_close($connection);
		return true;
	}
	
	function GetUsernameFromEmail($email){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}   
		
		if($stmt = $connection->prepare("Select username from " . $this->Config->getSchema() . ".usuarios where email=?")){
			$stmt->bind_param("s", SanitizeEmail($email));
			$stmt->execute();
			$stmt->bind_result($username);
			$stmt->fetch();
			$stmt->close();
		}
 
		if(!$username)
		{
			$this->HandleError($lang['10112'] . $email);
			mysqli_close($connection);
			return false;
		}
		
		mysqli_close($connection);
		return SanitizeUsername($username);
	}
	
	function GetEmailFromUsername($username){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}   
		
		if($stmt = $connection->prepare("Select email from " . $this->Config->getSchema() . ".usuarios where username=?")){
			$stmt->bind_param("s", SanitizeUsername($username));
			$stmt->execute();
			$stmt->bind_result($email);
			$stmt->fetch();
			$stmt->close();
		}
 
		if(!$email)
		{
			$this->HandleError($lang['10113'] . $username);
			mysqli_close($connection);
			return false;
		}
		
		mysqli_close($connection);
		return SanitizeEmail($email);
	}
	
	function GetSaltFromUsername($username){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}
		
		if($stmt = $connection->prepare("Select csalt from " . $this->Config->getSchema() . ".usuarios where username=?")){
			$stmt->bind_param("s", SanitizeUsername($username));
			$stmt->execute();
			$stmt->bind_result($csalt);
			$stmt->fetch();
			$stmt->close();
		}
		
		if(strlen($csalt) == 0)
		{
			$this->HandleError($lang['10113'] . $username);
			mysqli_close($connection);
			return false;
		}
		
		mysqli_close($connection);
		return SanitizeHex($csalt);
	}

	// This method finds the client-side hashing salt for a user (by username) without unnecessarily sharing it with browsers not registered/trusted by that user.
	function GetSaltFromUsernamePublic($username, $browserverification){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection){
			$this->HandleError($lang['10086']);
			return false;
		}

		if($stmt = $connection->prepare("Select csalt, id_user from " . $this->Config->getSchema() . ".usuarios where username=?")){
				$stmt->bind_param("s", SanitizeUsername($username));
				$stmt->execute();
				$stmt->bind_result($salt, $id_user);
				$stmt->fetch();
				$stmt->close();
		}

		if(isset($salt) && isset($id_user))
		{
			if($stmt = $connection->prepare("Select id_browser from " . $this->Config->getSchema() . ".registeredBrowsers where secret=? AND id_user=? AND active='1'")){
				$stmt->bind_param("si", SanitizeHex($browserverification), SanitizeInteger($id_user));
				$stmt->execute();
				$stmt->bind_result($browser_id);
				$stmt->fetch();
				$stmt->close();
			}
		}
	
		if (isset($browser_id)) {
			return SanitizeHex($salt);
		} else {
			return "BVrequired";
		}
		  
	}

	// List all registered/trusted browsers for a user. Useful for providing an interface to selectively disable, or simply view, all registered browsers.
	function GetRegisteredBrowsersForCurrentUser(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
			$this->HandleError($lang['10086']);
			return false;
		}

		if($stmt = $connection->prepare("Select ip_address, id_browser, platform, secret from " . $this->Config->getSchema() . ".registeredBrowsers where id_user=? AND active='1'")){
			$stmt->bind_param("s", SanitizeInteger($_SESSION[$this->Config->getAlias() . 'userID']));
			$stmt->execute();
			$stmt->bind_result($temp1, $temp2, $temp3, $temp4);
			$known_browsers = array();
			while ($stmt->fetch()) {
				if (SanitizeHex($_COOKIE[$this->Config->getAlias() . 'BrowserValidation'.SanitizeUserName($_SESSION[$this->Config->getAlias() . 'username'])]) == $temp4) {
					$known_browsers[$temp2] = $temp3." @ ".$temp1." (current)";
				} else {
					$known_browsers[$temp2] = $temp3." @ ".$temp1;
				}
			}
			$stmt->close();
		}
	
		if(!isset($known_browsers))
		{
			$this->HandleError($lang['10114']);
			mysqli_close($connection);
			return false;
		}

		mysqli_close($connection);
		return $known_browsers;
	}

	
	function WhatWillNextUserIdBe(){
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		$connection = $this->DBLogin();
		if(!$connection)
		{
				$this->HandleError($lang['10086']);
				return 0;
		} else {
			if($stmt = $connection->prepare("select max(id_user) from " . $this->Config->getSchema() . ".usuarios;")) {
				if (!$stmt->execute()) {
						$this->HandleDBError($lang['10115']);
				}
				$stmt->bind_result($num);
				$stmt->fetch();
				$stmt->close();
			}
			mysqli_close($connection);
			return $num+1;
		}
	}

	// End MySQL Selects
	
	// End MySQL Actors
	
	// 3.4 Billing Functions
	function ValidateBillingInfo() {
		$validator = new FormValidator();
		//$validator->addValidation("firstname","alpha_s","Your billing first name should only include letters and spaces");
		//$validator->addValidation("lastname","alpha_s","Your billing last name should only include letters and spaces");
		$validator->addValidation("credit-card","req","Please fill in a credit card number");
		$validator->addValidation("credit-card","num","Your credit card number should only include numbers");
		$validator->addValidation("credit-card","maxlen=16","Your credit card number should only be 16 digits long");
		$validator->addValidation("credit-card","minlen=15","Your credit card number should be 15 or 16 digits long");
		$validator->addValidation("credit-card-type","minlen=4","You did not specify youre credit card type");
		 $validator->addValidation("monthExpires","req","Please fill in an expiration month for your credit card");
		$validator->addValidation("monthExpires","num","Please fill in the valid and future expiration month of your credit card");
		$validator->addValidation("yearExpires","req","Please fill in an expiration year for your credit card");

		if(!$validator->ValidateForm())
		{
			$error='';
			$error_hash = $validator->GetErrors();
			foreach($error_hash as $inpname => $inp_err)
			{
				$error .= $inpname.':'.$inp_err."\n";
			}
			$this->HandleError($error);
			return false;
		}

		if (!inarray(strtolower($_POST['credit-card-type']), $this->acceptedCreditCardTypes)) {
			$this->HandleError("Sorry, we do not accept that kind of credit card.");
			return false;
		}
		
		$input_time = mktime(0,0,0,SanitizeInteger($_POST['monthExpires'])+1,0,SanitizeInteger($_POST['yearExpires'])); 

		if ($input_time < time()){
			$this->HandleError("Provided expiration date has already elapsed.");
			return false;    
		}
		
		return true;
	}
	
	function billUser($username, $reason, $price) {
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		error_log("Billing ".$username." $".$price." for ".$reason);        
		$_SESSION[$this->Config->getAlias() . 'hasPurchasedThisSession'] = true;
		
		// A fourth parameter indicates we are paying the balance on an existing, unpaid transaction
		if (func_num_args() > 3) {
			$transactionId = func_get_arg(3);
		} else {
			$transactionId = $this->createTransaction($reason, $price);
			if ($transactionId === false) {
				return [false, ""];
			}
		}
		// You might have some reason to allow unpaid transactions, you would fill that in here
		if (true) {
			$credit = SanitizeFloat($_SESSION[$this->Config->getAlias() . 'credit']);
			$price = SanitizeFloat($price);
			$billUserAmount = $price;
			// You may have given this user some credit; if so, consume that credit first
			if ($credit >= 0) {
				if ($credit - $price > 0) {
					$billUserAmount = 0;
					$credit = $credit - $price;
					$_SESSION[$this->Config->getAlias() . 'credit'] = $credit;
					$transactionResult = true;
				} else {
					$billUserAmount = -($credit - $price);
					$credit = $credit - ($price - $billUserAmount);
					$_SESSION[$this->Config->getAlias() . 'credit'] = $credit;
					// This is where you implement your actual billing function
					// This will often return an unique paymentId from your payment processor
					//$transactionResult = someAction();
				}
			} else {
				// This is where you implement your actual billing function
				// This will often return an unique paymentId from your payment processor
				//$transactionResult = someAction();
			}
			
			if ($transactionResult !== false) { // success
				$this->markTransactionPaid($transactionId, $transactionResult);
				$connection = $this->DBLogin();
				if(!$connection)
				{
					$this->HandleError($lang['10086']);
				} else {
					// Let's update the user's totalSpending, so we can easily see who our best customers are
					if($stmt = $connection->prepare("Select totalSpending from " . $this->Config->getSchema() . ".usuarios where username=?")) {
						$stmt->bind_param("s", SanitizeUsername($username));
						$stmt->execute();
						$stmt->bind_result($previousSpending);
						$stmt->fetch();
						$stmt->close();
					}
					
					$totalSpending = $price + $previousSpending;
					
					if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".usuarios set totalSpending=?, credit=? where username=?")) {
						$stmt->bind_param("dds", SanitizeFloat($totalSpending), SanitizeFloat($credit), SanitizeUsername($username));
						if (!$stmt->execute()) {
								// not sure what to do here; not exactly good but not critical either
						}
						$stmt->close();
					}
					mysqli_close($connection);
				}
				return [true, $transactionId];
			} else { // failure
				$_SESSION[$this->Config->getAlias() . 'problemBillingUser'] = true;
				$this->MarkUserAsHavingBillingInfoProblem(SanitizeUsername($username));
				return [false, $transactionId];
			}
		} else {
			return [true, $transactionId];
		}
	}
	
	// End Billing Functions

	// 3.5 Transaction Loggers

	// Create an unpaid transaction
	function createTransaction($reason, $price) {
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}

		$connection = $this->DBLogin();
		if(!$connection)
		{
		   $this->HandleError($lang['10086']);
			return false;
		} else {
			if($stmt = $connection->prepare("INSERT INTO " . $this->Config->getSchema() . ".transactions (id_user, reason, price) values (?,?,?)")) {
				$stmt->bind_param("isd", SanitizeInteger($_SESSION[$this->Config->getAlias() . 'userID']), SanitizeNonNumericText($reason), SanitizeFloat($price));
				if (!$stmt->execute()) {
					return false;
				}
				$transactionId = $stmt->insert_id;
				$stmt->fetch();
				$stmt->close();
				return $transactionId;
			}
			mysqli_close($connection);
		}
	}
	
	// Mark an existing transaction as having been paid
	function markTransactionPaid($transactionId, $transactionResult) {
		if(isset($_COOKIE[$this->Config->getAlias() . 'language'])){
			include('lang.' . $_COOKIE[$this->Config->getAlias() . 'language'] . '.php');
		}else{
			include('lang.' . $this->Config->LoadLanguage() . '.php');
		}
		// remember that $transactionResult is often a unique ID from your payment processor
		$connection = $this->DBLogin();
		if(!$connection){
			$this->HandleError($lang['10086']);
		}else {
			if($stmt = $connection->prepare("Update " . $this->Config->getSchema() . ".transactions set paid=1, paymentId=? where skey=?")){
				$stmt->bind_param("si", SanitizeNonNumericText($transactionResult), $transactionId);
				if (!$stmt->execute()) {
				   // not sure what to do here; not exactly good but not critical either
				}
				$stmt->close();
			}
			mysqli_close($connection);
		}
	}
	// End Transaction Loggers
}
?>
