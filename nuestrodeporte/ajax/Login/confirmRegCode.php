<?PHP
	session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(1);

$__APP_SITE_PATHS_START__ = __DIR__;
$__app_here = __DIR__;
for ($__i = 0, $__prev = null; $__i < 24; $__i++) {
	$__base = ($__i === 0) ? $__app_here : dirname($__app_here, $__i);
	if ($__base === $__prev) {
		break;
	}
	$__prev = $__base;
	$__inc = $__base . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'app_site_paths.inc.php';
	if (is_readable($__inc)) {
		require_once $__inc;
		break;
	}
}
unset($__i, $__prev, $__base, $__inc, $__app_here);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('confirmRegCode.php');
	
	$retunData = array('status' => '0', 'dataError' => 'Something went wrong,please try again.');
	$error = 1;
	if(isset($_POST['code']))
	{
	    if(!$fgmembersite->twoFactorAuthMode){
			if($fgmembersite->ConfirmUser()){
				$retunData = array('status' => '1', 'msg' => '0');
				$error = 0;
			}
		} else {  
			$returning = $fgmembersite->ConfirmBrowser();
			if(!(false === $returning)){
				if (isset($_COOKIE[$Config->getAlias() . "BrowserValidation".$returning['username']])) {
						unset($_COOKIE[$Config->getAlias() . "BrowserValidation".$returning['username']]);
				}
				$cookieName = "BrowserValidation".$returning['username'];
				setcookie($Config->getAlias() . $cookieName, $returning['code'], time() + (60 * 60 * 24 * 30), '/');
				$retunData = array('status' => '1', 'msg' => '1');
				$error = 0;
		   }
		}
	}
	if($error == 1){
	    $retunData = array('status' => '0', 'msg' => '3', 'dataError' => $fgmembersite->GetErrorMessage());
	}
	echo json_encode($retunData);
?>