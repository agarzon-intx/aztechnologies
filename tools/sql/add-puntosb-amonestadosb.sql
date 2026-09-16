-- Adds basketball scoring/foul tables on all site schemas.
--   PuntosB      : 1/2/3-point totals per player per game
--   AmonestadosB : basketball fouls / sanctions per player per game
-- Safe to re-run (IF NOT EXISTS).
-- Run with a database user that has CREATE privileges (cPanel account owner).

CREATE TABLE IF NOT EXISTS `aztechn1_demomina`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_demomina`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_elite`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_elite`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_huskies`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_huskies`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_lidep`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_lidep`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_nuestrodeporte`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_nuestrodeporte`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_vollidep`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_vollidep`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_voleibolmetepec`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_voleibolmetepec`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_voleymvp`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_voleymvp`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_aztflag`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_aztflag`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_binde`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_binde`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_mapaches`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_mapaches`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_deportivolasminas`.`PuntosB` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Puntos1` int(11) NOT NULL,
  `Puntos2` int(11) NOT NULL,
  `Puntos3` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_deportivolasminas`.`AmonestadosB` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `CantidadP` int(11) NOT NULL DEFAULT '1',
  `CantidadT` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
