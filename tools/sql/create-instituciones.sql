CREATE TABLE IF NOT EXISTS `Instituciones` (
  `Institucion_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Institucion_DESC` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Activo` int(11) NOT NULL,
  `Logo` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Institucion_FULLDESC` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Institucion_DESC5` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`Institucion_ID`,`Torneo_ID`),
  UNIQUE KEY `Torneo_ID_UNIQUE` (`Torneo_ID`,`Institucion_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
