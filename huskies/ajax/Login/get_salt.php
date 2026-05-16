<?PHP
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
	error_reporting(0);

	$retunData = array('status' => '0', 'response' => 'Something went wrong,please try again.');

	if ($_POST['username'] == SanitizeUsername($_POST['username'])) {
		$username = SanitizeUsername($_POST['username']);
		$BVname = 'BrowserValidation'.$username;
	} else if ($_POST['username'] == SanitizeEmail($_POST['username'])) {
		$username = SanitizeUsername($fgmembersite->GetUsernameFromEmail(SanitizeEmail($_POST['username'])));
		$BVname = 'BrowserValidation'.$username;
	}

/*
    echo 'GetSaltFromUsernamePublic => $fgmembersite->GetSaltFromUsernamePublic(' . $username . ', ' . $_COOKIE[$Config->getAlias() . '' . $BVname] . ')';
    echo $fgmembersite->GetSaltFromUsernamePublic($username, $_COOKIE[$Config->getAlias() . '' . $BVname]) . '<br>';
	echo $Config->getAlias() . $BVname . '<br>';
	print_r($_COOKIE);
	
	echo $_COOKIE[$Config->getAlias() . $BVname];
*/
	if(isset($_COOKIE[$Config->getAlias() . $BVname])) {
	    //echo '123--- ' . $_COOKIE[$Config->getAlias() . '' . $BVname];
		$retunData = array('status' => '1', 'salt' => $fgmembersite->GetSaltFromUsernamePublic($username, $_COOKIE[$Config->getAlias() . '' . $BVname]));
	} else {
		$retunData = array('status' => '0', 'salt' => 'BVrequired');
	}
    echo json_encode($retunData);
?>