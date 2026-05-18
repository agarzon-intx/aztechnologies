<?php
/**
 * Single definition for Config → Images tab: allowed POST fields and UI rows.
 * All assets are stored as PNG (including report background).
 *
 * @return array with keys 'allowed' (field => rel path) and 'rows' (list of row defs)
 */
return function () {
	$allowed = array(
		'cfg_fondo_reporte' => 'imagenes/FondoReporte.png',
		'cfg_fondo_flyer_s' => 'pdf/FondoFlyerS.png',
		'cfg_fondo_flyer' => 'pdf/FondoFlyer.png',
		'cfg_credencial' => 'pdf/Credencial.png',
		'cfg_calendar' => 'pdf/calendar.png',
		'cfg_clock' => 'pdf/clock.png',
		'cfg_pointer' => 'pdf/pointer.png',
		'cfg_marcador' => 'imagenes/marcador.png',
	);

	$acceptPng = 'image/png,image/jpeg,image/jpg,image/webp,image/gif,.png,.jpg,.jpeg,.webp,.gif';

	$rows = array();
	$rows[] = array(
		'post' => 'cfg_fondo_reporte',
		'rel' => 'imagenes/FondoReporte.png',
		'accept' => $acceptPng,
		'lang' => '452-10',
	);
	$rows[] = array('post' => 'cfg_fondo_flyer_s', 'rel' => 'pdf/FondoFlyerS.png', 'accept' => $acceptPng, 'lang' => '452-11');
	$rows[] = array('post' => 'cfg_fondo_flyer', 'rel' => 'pdf/FondoFlyer.png', 'accept' => $acceptPng, 'lang' => '452-12');
	$rows[] = array('post' => 'cfg_credencial', 'rel' => 'pdf/Credencial.png', 'accept' => $acceptPng, 'lang' => '452-13');
	$rows[] = array('post' => 'cfg_calendar', 'rel' => 'pdf/calendar.png', 'accept' => $acceptPng, 'lang' => '452-14');
	$rows[] = array('post' => 'cfg_clock', 'rel' => 'pdf/clock.png', 'accept' => $acceptPng, 'lang' => '452-15');
	$rows[] = array('post' => 'cfg_pointer', 'rel' => 'pdf/pointer.png', 'accept' => $acceptPng, 'lang' => '452-16');
	$rows[] = array(
		'post' => 'cfg_marcador',
		'rel' => 'imagenes/marcador.png',
		'accept' => $acceptPng,
		'lang' => '452-17',
	);

	return array('allowed' => $allowed, 'rows' => $rows);
};
