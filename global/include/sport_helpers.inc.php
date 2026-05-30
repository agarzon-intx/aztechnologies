<?php
/**
 * Sport type helpers (config.ini [sport]).
 * 0 = Soccer, 1 = Voleibol, 2 = Basket, 3 = Flag football
 */
if (!defined('APP_SPORT_SOCCER')) {
	define('APP_SPORT_SOCCER', 0);
	define('APP_SPORT_VOLEY', 1);
	define('APP_SPORT_BASKET', 2);
	define('APP_SPORT_FLAG', 3);
}

if (!function_exists('app_sport_id')) {
	function app_sport_id($config = null): int
	{
		if ($config === null) {
			global $Config;
			$config = $Config ?? null;
		}
		if ($config && method_exists($config, 'getSport')) {
			return (int) $config->getSport();
		}
		return APP_SPORT_SOCCER;
	}
}

if (!function_exists('app_sport_uses_soccer')) {
	/** Soccer and flag football share soccer screens until flag-specific endpoints exist. */
	function app_sport_uses_soccer(?int $sport = null): bool
	{
		if ($sport === null) {
			$sport = app_sport_id();
		}
		return $sport === APP_SPORT_SOCCER || $sport === APP_SPORT_FLAG;
	}
}

if (!function_exists('app_sport_uses_voleibol')) {
	function app_sport_uses_voleibol(?int $sport = null): bool
	{
		if ($sport === null) {
			$sport = app_sport_id();
		}
		return $sport === APP_SPORT_VOLEY;
	}
}

if (!function_exists('app_sport_uses_basket')) {
	function app_sport_uses_basket(?int $sport = null): bool
	{
		if ($sport === null) {
			$sport = app_sport_id();
		}
		return $sport === APP_SPORT_BASKET;
	}
}

if (!function_exists('app_sport_uses_flag')) {
	function app_sport_uses_flag(?int $sport = null): bool
	{
		if ($sport === null) {
			$sport = app_sport_id();
		}
		return $sport === APP_SPORT_FLAG;
	}
}
