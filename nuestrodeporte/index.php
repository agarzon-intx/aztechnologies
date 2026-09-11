<?php
/**
 * Temporary maintenance homepage for Liga Nuestro Deporte.
 * Previous app homepage: index.php.bak-20260910-pre-maintenance
 */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
http_response_code(503);
header('Retry-After: 3600');

$brand = 'Liga Nuestro Deporte';
$logoCandidates = array('LogoLiga', 'LeagueLogo', 'LogoLiga1', 'LogoLiga2');
$logoFile = 'imagenes/LogoLiga.png';
foreach ($logoCandidates as $base) {
	$path = __DIR__ . '/imagenes/' . $base . '.png';
	if (is_readable($path)) {
		$logoFile = 'imagenes/' . $base . '.png';
		break;
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<title><?php echo htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?> — Mantenimiento</title>
	<style>
		:root {
			--bg1: #0b1f17;
			--bg2: #143d2c;
			--accent: #c8a24a;
			--text: #f4f7f5;
			--muted: #b7c7be;
		}
		* { box-sizing: border-box; }
		html, body {
			margin: 0;
			min-height: 100%;
			font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
			color: var(--text);
			background:
				radial-gradient(1200px 700px at 20% 10%, rgba(200,162,74,0.18), transparent 55%),
				radial-gradient(900px 600px at 90% 80%, rgba(46,125,90,0.35), transparent 50%),
				linear-gradient(160deg, var(--bg1), var(--bg2));
		}
		.wrap {
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 2rem 1.25rem;
			text-align: center;
		}
		.card {
			max-width: 36rem;
			width: 100%;
		}
		.logo {
			width: min(280px, 70vw);
			height: auto;
			display: block;
			margin: 0 auto 1.75rem;
			filter: drop-shadow(0 12px 28px rgba(0,0,0,0.35));
		}
		.brand {
			margin: 0 0 0.75rem;
			font-size: clamp(1.6rem, 4vw, 2.25rem);
			font-weight: 700;
			letter-spacing: 0.02em;
			line-height: 1.15;
		}
		.msg {
			margin: 0 auto 0.5rem;
			max-width: 28rem;
			font-size: 1.05rem;
			line-height: 1.5;
			color: var(--muted);
		}
		.accent {
			display: inline-block;
			margin-top: 1.5rem;
			padding: 0.45rem 1rem;
			border: 1px solid rgba(200,162,74,0.55);
			border-radius: 999px;
			color: var(--accent);
			font-size: 0.85rem;
			letter-spacing: 0.08em;
			text-transform: uppercase;
		}
	</style>
</head>
<body>
	<main class="wrap">
		<div class="card">
			<img class="logo" src="<?php echo htmlspecialchars($logoFile, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?>">
			<h1 class="brand"><?php echo htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="msg">Estamos en mantenimiento. Volvemos pronto.</p>
			<p class="msg">We're performing maintenance. We'll be back shortly.</p>
			<span class="accent">En mantenimiento</span>
		</div>
	</main>
</body>
</html>
