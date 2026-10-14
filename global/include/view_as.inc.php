<?php
/*
 * "Ver como": an admin temporarily takes another user's identity in the session to test what that user
 * sees. The admin's own session values are kept under "viewAsOriginal" and restored by az_view_as_end().
 * The session is per browser, so every window of this site acts as the viewed user until it ends.
 */

if (!function_exists('az_view_as_keys')) {
	/** Session values (alias-prefixed) that make up a logged-in identity. */
	function az_view_as_keys()
	{
		return array('userID', 'username', 'nombre', 'apellidop', 'apellidom', 'telefono', 'institucion', 'equipoBase',
			'equipoInstSeason', 'equipoInst', 'equipo', 'email', 'active', 'credit', 'problemBillingUser',
			'messageForUser', 'message', 'adminMessageForUser', 'adminMessage');
	}

	/** Username of the real (admin) user, also while viewing as someone else. */
	function az_view_as_real_username($Config)
	{
		$alias = $Config->getAlias();
		if (isset($_SESSION[$alias . 'viewAsOriginal']) && is_array($_SESSION[$alias . 'viewAsOriginal'])) {
			return (string) ($_SESSION[$alias . 'viewAsOriginal']['username'] ?? '');
		}
		return (string) ($_SESSION[$alias . 'username'] ?? '');
	}

	function az_view_as_active($Config)
	{
		return isset($_SESSION[$Config->getAlias() . 'viewAsOriginal']);
	}

	/** Loads $username into the session like a login would (no password, no message reset). */
	function az_view_as_start($Config, $fgmembersite, $username)
	{
		$alias = $Config->getAlias();
		$schema = $Config->getSchema();
		$connection = $fgmembersite->DBLogin();
		if (!$connection) {
			return false;
		}
		$stmt = $connection->prepare("SELECT a.username, phone_number, name, ApellidoP, ApellidoM, email, GROUP_CONCAT(b.Equipo_ID) Equipo_ID, id_user, message, adminMessage, paymentProblem, credit, active, a.Institucion_ID
				FROM $schema.usuarios a
					LEFT JOIN $schema.usuarios_equipo b ON a.username = b.username
				WHERE a.username = ?
				GROUP BY a.username");
		if (!$stmt) {
			return false;
		}
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($user, $phone, $name, $apellidoP, $apellidoM, $email, $equipo, $idUser, $message, $adminMessage, $paymentProblem, $credit, $active, $institucionId);
		$found = $stmt->fetch();
		$stmt->close();
		if (!$found) {
			return false;
		}

		if (!isset($_SESSION[$alias . 'viewAsOriginal'])) {
			$original = array();
			foreach (az_view_as_keys() as $key) {
				$original[$key] = array_key_exists($alias . $key, $_SESSION) ? $_SESSION[$alias . $key] : null;
			}
			$_SESSION[$alias . 'viewAsOriginal'] = $original;
		}
		foreach (az_view_as_keys() as $key) {
			unset($_SESSION[$alias . $key]);
		}

		$institucionIds = $fgmembersite->UserInstitutionIds($connection, $user, $institucionId);
		list($instSeason, $instTeams) = $fgmembersite->InstitutionTeams($connection, $institucionIds);
		$_SESSION[$alias . 'userID'] = SanitizeInteger($idUser);
		$_SESSION[$alias . 'username'] = SanitizeUsername($user);
		$_SESSION[$alias . 'nombre'] = SanitizeUsername($name);
		$_SESSION[$alias . 'apellidop'] = SanitizeUsername($apellidoP);
		$_SESSION[$alias . 'apellidom'] = SanitizeUsername($apellidoM);
		$_SESSION[$alias . 'telefono'] = SanitizeInteger($phone);
		$_SESSION[$alias . 'institucion'] = implode(',', $institucionIds);
		$_SESSION[$alias . 'equipoBase'] = SanitizeTextComa((string) $equipo);
		$_SESSION[$alias . 'equipoInstSeason'] = $instSeason;
		$_SESSION[$alias . 'equipoInst'] = SanitizeTextComa($instTeams);
		$_SESSION[$alias . 'equipo'] = SanitizeTextComa((string) $equipo);
		$fgmembersite->SyncCoachTeamsForSeason();
		$_SESSION[$alias . 'email'] = SanitizeEmail($email);
		$_SESSION[$alias . 'active'] = SanitizeEmail($active);
		$_SESSION[$alias . 'credit'] = SanitizeFloat($credit);
		$_SESSION[$alias . 'problemBillingUser'] = ($paymentProblem == '1');
		$_SESSION[$alias . 'messageForUser'] = !empty($message);
		if (!empty($message)) {
			$_SESSION[$alias . 'message'] = SanitizeBrowserName($message);
		}
		$_SESSION[$alias . 'adminMessageForUser'] = !empty($adminMessage);
		if (!empty($adminMessage)) {
			$_SESSION[$alias . 'adminMessage'] = SanitizeBrowserName($adminMessage);
		}
		mysqli_close($connection);
		return true;
	}

	/** Restores the admin's own session values. */
	function az_view_as_end($Config)
	{
		$alias = $Config->getAlias();
		if (!isset($_SESSION[$alias . 'viewAsOriginal']) || !is_array($_SESSION[$alias . 'viewAsOriginal'])) {
			return false;
		}
		foreach ($_SESSION[$alias . 'viewAsOriginal'] as $key => $value) {
			if ($value === null) {
				unset($_SESSION[$alias . $key]);
			} else {
				$_SESSION[$alias . $key] = $value;
			}
		}
		unset($_SESSION[$alias . 'viewAsOriginal']);
		return true;
	}
}
