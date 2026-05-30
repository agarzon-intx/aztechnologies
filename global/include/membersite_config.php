<?PHP
(function () {
	if (defined('APP_PHP_LOGGING_BOOTSTRAP')) {
		return;
	}
	define('APP_PHP_LOGGING_BOOTSTRAP', true);
	$root = dirname(__DIR__, 2);
	$logDir = $root . DIRECTORY_SEPARATOR . 'logs';
	if (!is_dir($logDir)) {
		@mkdir($logDir, 0755, true);
	}
	$logFile = $logDir . DIRECTORY_SEPARATOR . 'php_errors.log';
	ini_set('log_errors', '1');
	ini_set('error_log', $logFile);
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
})();
// Always load from this tree (do not rely on cwd or include_path for shared libs).
require_once __DIR__ . DIRECTORY_SEPARATOR . 'encoding_compat.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'fg_membersite.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Configuration.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'sport_helpers.inc.php';
$Config = new Configuration();
$fgmembersite = new FGMembersite($Config);

$googleMapsBrowserKey = '';
$__gmkFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.local' . DIRECTORY_SEPARATOR . 'google_maps_api_key';
if (is_readable($__gmkFile)) {
	$googleMapsBrowserKey = trim((string) file_get_contents($__gmkFile));
}
if ($googleMapsBrowserKey === '' && getenv('GOOGLE_MAPS_API_KEY')) {
	$googleMapsBrowserKey = trim((string) getenv('GOOGLE_MAPS_API_KEY'));
}

$googleMapsMapId = '';
$__gmmidFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.local' . DIRECTORY_SEPARATOR . 'google_maps_map_id';
if (is_readable($__gmmidFile)) {
	$googleMapsMapId = trim((string) file_get_contents($__gmmidFile));
}
if ($googleMapsMapId === '' && getenv('GOOGLE_MAPS_MAP_ID')) {
	$googleMapsMapId = trim((string) getenv('GOOGLE_MAPS_MAP_ID'));
}
if ($googleMapsMapId === '') {
	$googleMapsMapId = 'DEMO_MAP_ID';
}

//Provide your site name here
$fgmembersite->SetWebsiteName($Config->getWebSite());

//Provide the email address where you want to get notifications
$fgmembersite->SetAdminEmail($Config->getAdminEmail());

//Provide your database login details here:
//hostname, user name, password, database name and table name
//note that the script will create the table (for example, fgusers in this case)
//by itself on submitting register.php for the first time
$fgmembersite->InitDB();

//This is a compromise between entirely static values and randomly generated ones that the nature of PHP has a hard time with.
$today = getdate();                      
$fgmembersite->SetRandomKey(md5($today["year"].$today["yday"]));                      

//How long should sessions stay valid for (in minutes)?
$fgmembersite->SetSessionLifetimeInMinutes(30);
                      
//Do you want to require additional verification (password, and browser verification if using two-factor authentication) for account administration?
$fgmembersite->EnablePasswordRequiredForAdministration(true);   

//Do you want to prevent Cross-Site Request Forgery by requiring CSRF tokens to authenticate requests?
$fgmembersite->EnableCSRFTokenRequired(false);                 
                      
//Do you want to enable two-factor authentication mode?  
$fgmembersite->EnableTwoFactorAuthenticationMode(true);

//Do you want to enable client-side password hashing?  If you do this, you must also enable Two Factor Authentication
$fgmembersite->EnableClientSidePasswordHashing(true);

//Do you want to include support for recording billing operations?
$fgmembersite->EnableTransactions(false);

$fgmembersite->SetAcceptedCreditCards(array("visa", "discover", "amex", "mastercard"));

?>