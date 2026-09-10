-- Physical seed ranking table for Generate Schedule (session-scoped).
-- Replaces MySQL TEMPORARY TABLE so ranks can be re-read later for the same PHP session.

CREATE TABLE IF NOT EXISTS `aztechn1_demomina`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_elite`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_huskies`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_lidep`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_nuestrodeporte`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_vollidep`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_voleibolmetepec`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_voleymvp`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `aztechn1_aztflag`.`GenerateScheduleSeeds` (
  `Session_ID` varchar(128) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `rank` int(11) NOT NULL,
  `Institucion_ID` bigint(20) NOT NULL,
  `Institucion_DESC` varchar(255) DEFAULT NULL,
  `TeamCount` int(11) NOT NULL DEFAULT 0,
  `RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
  KEY `idx_gs_seeds_session` (`Session_ID`),
  KEY `idx_gs_seeds_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- App write user needs DML on the new table (cPanel often does not auto-grant on tables
-- created by the account owner). Adjust user names if a site uses a different *adm user.
-- Run as cPanel / account owner in phpMyAdmin:

GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_demomina`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_elite`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_huskies`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_lidep`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_nuestrodeporte`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_vollidep`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_voleibolmetepec`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_voleymvp`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_aztflag`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'%';

-- Prefer granting to each site's own *adm user if different from lidepadm, e.g.:
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `aztechn1_lidep`.`GenerateScheduleSeeds` TO 'aztechn1_lidepadm'@'localhost';
-- FLUSH PRIVILEGES;
