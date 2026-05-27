<?php
if (!isset($teamListFilter)) {
	$teamListFilter = '';
	if (isset($_POST['teamListFilter'])) {
		$teamListFilter = trim((string) $_POST['teamListFilter']);
	} elseif (isset($_GET['teamListFilter'])) {
		$teamListFilter = trim((string) $_GET['teamListFilter']);
	}
}
if (!isset($teamListFilterSql)) {
	$teamListFilterSql = '';
	if ($teamListFilter !== '') {
		$tmConn = $Config->connect();
		if ($tmConn) {
			$tmEsc = $tmConn->real_escape_string($teamListFilter);
			$tmLike = "'%" . $tmEsc . "%'";
			$teamListFilterSql = " AND (
				a.Equipo_DESC LIKE $tmLike
				OR a.Equipo_FULLDESC LIKE $tmLike
			)";
		}
	}
}
