<?php
if (!isset($institutionListFilter)) {
	$institutionListFilter = '';
	if (isset($_POST['institutionListFilter'])) {
		$institutionListFilter = trim((string) $_POST['institutionListFilter']);
	} elseif (isset($_GET['institutionListFilter'])) {
		$institutionListFilter = trim((string) $_GET['institutionListFilter']);
	}
}
if (!isset($institutionListFilterSql)) {
	$institutionListFilterSql = '';
	if ($institutionListFilter !== '') {
		$imConn = $Config->connect();
		if ($imConn) {
			$imEsc = $imConn->real_escape_string($institutionListFilter);
			$imLike = "'%" . $imEsc . "%'";
			$institutionListFilterSql = " AND (
				a.Institucion_DESC LIKE $imLike
				OR a.Institucion_FULLDESC LIKE $imLike
				OR a.Institucion_DESC5 LIKE $imLike
			)";
		}
	}
}
