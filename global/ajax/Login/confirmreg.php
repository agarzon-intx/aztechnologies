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
	$sessionstat = $fgmembersite->CheckLogin('confirmreg.php');
	if(isset($_GET['code']))
	{
		if(!$fgmembersite->twoFactorAuthMode){
			if($fgmembersite->ConfirmUser()){
				$fgmembersite->RedirectToURL("thank-you-regd.php");
			}
		} else {  
			$returning = $fgmembersite->ConfirmBrowser();
			if(!(false === $returning)){
				if (isset($_COOKIE[$Config->getAlias() . "BrowserValidation".$returning['username']])) {
						unset($_COOKIE[$Config->getAlias() . "BrowserValidation".$returning['username']]);
				}
				$cookieName = "BrowserValidation".$returning['username'];
				setcookie($Config->getAlias() . $cookieName, $returning['code'], time() + (60 * 60 * 24 * 30), '/');
				$fgmembersite->RedirectToURL("thank-you-regd.php");
		   }
		}
	}
	echo $fgmembersite->GetErrorMessage();
	echo '<a href="' . $fgmembersite->sitename . '" rel=\'noreferrer\'>' . $fgmembersite->sitename . '</a><p><p>';
?>