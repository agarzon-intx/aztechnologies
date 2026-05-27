<?php
/**
 * View / add / edit / delete PHP session variables for the current site session.
 * Restricted to league admin accounts (equipo 0 or -1). Requires login.
 */
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!defined('APP_SITE_ROOT')) {
	$___d = __DIR__;
	while ($___d !== dirname($___d)) {
		$___p = $___d . DIRECTORY_SEPARATOR . 'site_paths.php';
		if (is_readable($___p)) {
			require_once $___p;
			break;
		}
		$___d = dirname($___d);
	}
}

require 'membersite_config.php';

$pageName = 'ManageSessionVariables.php';
if ($fgmembersite->CheckLogin($pageName) === false) {
	header('HTTP/1.1 401 Unauthorized');
	header('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Session</title></head><body><p>Login required.</p></body></html>';
	exit;
}

$alias = $Config->getAlias();
$equipoKey = $alias . 'equipo';
$equipo = isset($_SESSION[$equipoKey]) ? $_SESSION[$equipoKey] : null;
if ($equipo != 0 && $equipo != -1 && $equipo !== '0' && $equipo !== '-1') {
	header('HTTP/1.1 403 Forbidden');
	header('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Session</title></head><body><p>Access denied. League admin only (equipo 0 or -1).</p></body></html>';
	exit;
}

$flash = '';
$flashError = '';

/**
 * @param mixed $value
 */
function az_session_var_format_display($value) {
	if (is_array($value) || is_object($value)) {
		return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
	if (is_bool($value)) {
		return $value ? 'true' : 'false';
	}
	if ($value === null) {
		return '';
	}
	return (string) $value;
}

/**
 * @return string
 */
function az_session_var_detect_type($value) {
	if (is_bool($value)) {
		return 'bool';
	}
	if (is_int($value)) {
		return 'int';
	}
	if (is_float($value)) {
		return 'float';
	}
	if ($value === null) {
		return 'null';
	}
	if (is_array($value) || is_object($value)) {
		return 'json';
	}
	return 'string';
}

/**
 * @param string $raw
 * @param string $type
 * @return mixed
 */
function az_session_var_cast($raw, $type) {
	switch ($type) {
		case 'int':
			return (int) $raw;
		case 'float':
			return (float) $raw;
		case 'bool':
			$v = strtolower(trim($raw));
			return in_array($v, array('1', 'true', 'yes', 'on', 's'), true);
		case 'null':
			return null;
		case 'json':
			$decoded = json_decode($raw, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
			}
			return $decoded;
		default:
			return $raw;
	}
}

/**
 * @param string $key
 */
function az_session_var_validate_key($key) {
	$key = trim($key);
	if ($key === '') {
		throw new InvalidArgumentException('Key is required.');
	}
	if (strlen($key) > 128) {
		throw new InvalidArgumentException('Key is too long (max 128 characters).');
	}
	if (!preg_match('/^[A-Za-z0-9_.-]+$/', $key)) {
		throw new InvalidArgumentException('Key may only contain letters, numbers, underscore, dot, and hyphen.');
	}
	return $key;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = isset($_POST['action']) ? (string) $_POST['action'] : '';
	try {
		$key = az_session_var_validate_key(isset($_POST['key']) ? (string) $_POST['key'] : '');
		if ($action === 'delete') {
			if (!array_key_exists($key, $_SESSION)) {
				throw new InvalidArgumentException('Key not found in session.');
			}
			unset($_SESSION[$key]);
			$flash = 'Deleted: ' . $key;
		} elseif ($action === 'add' || $action === 'update') {
			if ($action === 'add' && array_key_exists($key, $_SESSION)) {
				throw new InvalidArgumentException('Key already exists. Use Edit instead.');
			}
			if ($action === 'update' && !array_key_exists($key, $_SESSION)) {
				throw new InvalidArgumentException('Key not found. Use Add instead.');
			}
			$type = isset($_POST['type']) ? (string) $_POST['type'] : 'string';
			$allowedTypes = array('string', 'int', 'float', 'bool', 'null', 'json');
			if (!in_array($type, $allowedTypes, true)) {
				$type = 'string';
			}
			$raw = isset($_POST['value']) ? (string) $_POST['value'] : '';
			$_SESSION[$key] = az_session_var_cast($raw, $type);
			$flash = ($action === 'add' ? 'Added: ' : 'Updated: ') . $key;
		} else {
			throw new InvalidArgumentException('Unknown action.');
		}
	} catch (Throwable $e) {
		$flashError = $e->getMessage();
	}
}

if (isset($_GET['saved']) && $flash === '') {
	$flash = 'Changes saved.';
}

$sessionRows = array();
if (is_array($_SESSION)) {
	foreach ($_SESSION as $key => $value) {
		$sessionRows[] = array(
			'key' => (string) $key,
			'type' => az_session_var_detect_type($value),
			'value' => az_session_var_format_display($value),
		);
	}
}
usort($sessionRows, function ($a, $b) {
	return strcasecmp($a['key'], $b['key']);
});

$editKey = isset($_GET['edit']) ? trim((string) $_GET['edit']) : '';
$editRow = null;
if ($editKey !== '') {
	foreach ($sessionRows as $row) {
		if ($row['key'] === $editKey) {
			$editRow = $row;
			break;
		}
	}
}

/**
 * @param string $text
 */
function az_sess_h($text) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Session variables — <?php echo az_sess_h($alias); ?></title>
	<style>
		* { box-sizing: border-box; }
		body { font-family: system-ui, Segoe UI, sans-serif; margin: 0; padding: 1.25rem; background: #f4f6f8; color: #1a1a1a; }
		h1 { font-size: 1.35rem; margin: 0 0 0.25rem; }
		.meta { color: #555; font-size: 0.9rem; margin-bottom: 1rem; }
		.alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
		.alert.ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
		.alert.err { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
		.alert.warn { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
		table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
		th, td { text-align: left; padding: 0.6rem 0.75rem; border-bottom: 1px solid #e8e8e8; vertical-align: top; }
		th { background: #263238; color: #fff; font-weight: 600; font-size: 0.85rem; }
		tr:last-child td { border-bottom: none; }
		.key { font-family: Consolas, monospace; font-size: 0.88rem; word-break: break-all; }
		.val { font-family: Consolas, monospace; font-size: 0.82rem; white-space: pre-wrap; max-width: 42rem; }
		.type { font-size: 0.75rem; color: #666; text-transform: uppercase; }
		.actions form { display: inline; margin: 0 0.25rem 0 0; }
		.actions button, .panel button, .panel input[type=submit] {
			font: inherit; cursor: pointer; border-radius: 4px; border: 1px solid #ccc;
			padding: 0.35rem 0.65rem; background: #fff;
		}
		.actions button.primary, .panel input[type=submit] { background: #1877f2; color: #fff; border-color: #1877f2; }
		.actions button.danger { color: #b71c1c; border-color: #e57373; }
		.panel { background: #fff; border-radius: 8px; padding: 1rem; margin-top: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
		.panel h2 { font-size: 1.05rem; margin: 0 0 0.75rem; }
		label { display: block; font-size: 0.85rem; margin-bottom: 0.25rem; color: #444; }
		input[type=text], select, textarea {
			width: 100%; max-width: 36rem; font: inherit; padding: 0.45rem 0.5rem;
			border: 1px solid #ccc; border-radius: 4px; margin-bottom: 0.75rem;
		}
		textarea { min-height: 6rem; font-family: Consolas, monospace; font-size: 0.88rem; }
		.row { margin-bottom: 0.5rem; }
	</style>
</head>
<body>
	<h1>Session variables</h1>
	<p class="meta">
		Site alias: <strong><?php echo az_sess_h($alias); ?></strong>
		· Session ID: <code><?php echo az_sess_h(session_id()); ?></code>
		· User: <code><?php echo az_sess_h(isset($_SESSION[$alias . 'username']) ? (string) $_SESSION[$alias . 'username'] : ''); ?></code>
	</p>

	<?php if ($flash !== '') { ?>
		<div class="alert ok"><?php echo az_sess_h($flash); ?></div>
	<?php } ?>
	<?php if ($flashError !== '') { ?>
		<div class="alert err"><?php echo az_sess_h($flashError); ?></div>
	<?php } ?>
	<div class="alert warn">
		Changes apply to <strong>your current session only</strong>. Wrong values for
		<code><?php echo az_sess_h($alias); ?>username</code>,
		<code>CSRFtoken</code>, or <code>LAST_ACTIVITY</code> can log you out or break forms.
	</div>

	<table>
		<thead>
			<tr>
				<th>Key</th>
				<th>Type</th>
				<th>Value</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($sessionRows) === 0) { ?>
			<tr><td colspan="4">No session variables.</td></tr>
		<?php } else { ?>
			<?php foreach ($sessionRows as $row) { ?>
			<tr>
				<td class="key"><?php echo az_sess_h($row['key']); ?></td>
				<td class="type"><?php echo az_sess_h($row['type']); ?></td>
				<td class="val"><?php echo az_sess_h($row['value']); ?></td>
				<td class="actions">
					<a href="?edit=<?php echo rawurlencode($row['key']); ?>">Edit</a>
					<form method="post" onsubmit="return confirm('Delete <?php echo az_sess_h($row['key']); ?>?');">
						<input type="hidden" name="action" value="delete">
						<input type="hidden" name="key" value="<?php echo az_sess_h($row['key']); ?>">
						<button type="submit" class="danger">Delete</button>
					</form>
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
	</table>

	<div class="panel">
		<h2><?php echo $editRow !== null ? 'Edit variable' : 'Add variable'; ?></h2>
		<form method="post">
			<input type="hidden" name="action" value="<?php echo $editRow !== null ? 'update' : 'add'; ?>">
			<div class="row">
				<label for="key">Key</label>
				<?php if ($editRow !== null) { ?>
					<input type="text" id="key" name="key" value="<?php echo az_sess_h($editRow['key']); ?>" readonly>
				<?php } else { ?>
					<input type="text" id="key" name="key" placeholder="<?php echo az_sess_h($alias); ?>myKey" autocomplete="off">
				<?php } ?>
			</div>
			<div class="row">
				<label for="type">Type</label>
				<select id="type" name="type">
					<?php
					$types = array('string' => 'String', 'int' => 'Integer', 'float' => 'Float', 'bool' => 'Boolean', 'null' => 'Null', 'json' => 'JSON (array/object)');
					$selType = $editRow !== null ? $editRow['type'] : 'string';
					foreach ($types as $t => $label) {
						$sel = ($selType === $t) ? ' selected' : '';
						echo '<option value="' . az_sess_h($t) . '"' . $sel . '>' . az_sess_h($label) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="row">
				<label for="value">Value</label>
				<textarea id="value" name="value"><?php
					echo az_sess_h($editRow !== null ? $editRow['value'] : '');
				?></textarea>
			</div>
			<input type="submit" class="primary" value="<?php echo $editRow !== null ? 'Save changes' : 'Add variable'; ?>">
			<?php if ($editRow !== null) { ?>
				<a href="ManageSessionVariables.php">Cancel edit</a>
			<?php } ?>
		</form>
	</div>
</body>
</html>
