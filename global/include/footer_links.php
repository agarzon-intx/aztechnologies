<?php
/**
 * Footer logos and social links administered in Configuration → General.
 * Tables: {schema}.FooterLogos and {schema}.RedesSociales (see tools/sql/create-footer-tables.sql).
 */

if (!function_exists('az_footer_social_types')) {

	/** type => [label, Font Awesome class, brand color] */
	function az_footer_social_types()
	{
		return array(
			'facebook' => array('Facebook', 'fab fa-facebook', '#1877F2'),
			'instagram' => array('Instagram', 'fab fa-instagram', '#E4405F'),
		);
	}

	function az_footer_conn($Config)
	{
		$conn = $Config->connect();
		if ($conn) {
			$conn->set_charset('utf8');
		}
		return $conn;
	}

	function az_footer_tables_exist($Config)
	{
		static $cache = array();
		$schema = (string) $Config->getSchema();
		if (isset($cache[$schema])) {
			return $cache[$schema];
		}
		$conn = az_footer_conn($Config);
		if (!$conn || $schema === '') {
			return $cache[$schema] = false;
		}
		$schemaEsc = $conn->real_escape_string($schema);
		$r = @$conn->query("SELECT COUNT(*) AS n FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$schemaEsc}' AND TABLE_NAME IN ('FooterLogos', 'RedesSociales')");
		$row = $r instanceof mysqli_result ? $r->fetch_assoc() : null;
		return $cache[$schema] = ($row !== null && (int) $row['n'] === 2);
	}

	/** Logos in display order, without the image bytes. */
	function az_footer_logos($Config)
	{
		if (!az_footer_tables_exist($Config)) {
			return array();
		}
		$conn = az_footer_conn($Config);
		$r = $conn->query("SELECT Logo_ID, Alias, Url, Ancho, UNIX_TIMESTAMP(Actualizado) AS v FROM `" . $conn->real_escape_string($Config->getSchema()) . "`.FooterLogos ORDER BY Orden, Logo_ID");
		$out = array();
		while ($r && ($row = $r->fetch_assoc())) {
			$out[] = array(
				'id' => (int) $row['Logo_ID'],
				'alias' => (string) $row['Alias'],
				'url' => (string) $row['Url'],
				'width' => (int) $row['Ancho'],
				'v' => (int) $row['v'],
			);
		}
		return $out;
	}

	function az_footer_social($Config)
	{
		if (!az_footer_tables_exist($Config)) {
			return array();
		}
		$conn = az_footer_conn($Config);
		$r = $conn->query("SELECT Red_ID, Tipo, Alias, Url FROM `" . $conn->real_escape_string($Config->getSchema()) . "`.RedesSociales ORDER BY Orden, Red_ID");
		$types = az_footer_social_types();
		$out = array();
		while ($r && ($row = $r->fetch_assoc())) {
			if (!isset($types[$row['Tipo']])) {
				continue;
			}
			$out[] = array(
				'id' => (int) $row['Red_ID'],
				'type' => (string) $row['Tipo'],
				'alias' => (string) $row['Alias'],
				'url' => (string) $row['Url'],
			);
		}
		return $out;
	}

	/** http(s) URL or '' when invalid; a missing scheme is treated as https. */
	function az_footer_clean_url($url)
	{
		$url = trim((string) $url);
		if ($url === '') {
			return '';
		}
		if (!preg_match('~^[a-z][a-z0-9+.-]*://~i', $url)) {
			$url = 'https://' . $url;
		}
		if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $url)) {
			return '';
		}
		return $url;
	}

	/**
	 * Footer logos followed by the social icons, or null when the tables are not there.
	 * $margin is the CSS margin around each item (the page footer spaces them to the left, the side menu all around).
	 */
	function az_footer_html($Config, $margin = '0 0 0 20px')
	{
		if (!az_footer_tables_exist($Config)) {
			return null;
		}
		$h = function ($s) {
			return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
		};
		$html = '';
		foreach (az_footer_logos($Config) as $logo) {
			$img = '<img src="Form/fetch_footer_logo.php?id=' . $logo['id'] . '&amp;v=' . $logo['v'] . '" alt="' . $h($logo['alias']) . '" title="' . $h($logo['alias']) . '"'
				. ' style="margin: ' . $margin . '; vertical-align: middle; object-fit: contain; max-height: 80px; '
				. ($logo['width'] > 0 ? 'width: ' . $logo['width'] . 'px; height: auto;' : 'height: 40px; width: auto;') . '">';
			$html .= $logo['url'] !== '' ? '<a href="' . $h($logo['url']) . '" target="_blank" rel="noopener">' . $img . '</a>' : $img;
		}
		$types = az_footer_social_types();
		foreach (az_footer_social($Config) as $net) {
			list(, $icon, $color) = $types[$net['type']];
			$html .= '<a href="' . $h($net['url']) . '" target="_blank" rel="noopener" title="' . $h($net['alias']) . '" aria-label="' . $h($net['alias']) . '" style="margin: ' . $margin . '; vertical-align: middle; text-decoration: none; display: inline-block;">'
				. '<i class="' . $icon . '" style="font-size: 26px; color: ' . $color . '; vertical-align: middle;"></i></a>';
		}
		return $html;
	}

	/**
	 * Echo the page footer items. Returns false (and prints nothing) when the tables are not there,
	 * so callers can keep their legacy markup.
	 */
	function az_footer_render($Config)
	{
		$html = az_footer_html($Config);
		if ($html === null) {
			return false;
		}
		echo $html;
		return true;
	}
}
