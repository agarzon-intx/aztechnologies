-- Physical seed ranking table for Generate Schedule (session-scoped).
-- Replaces MySQL TEMPORARY TABLE so ranks can be re-read later for the same PHP session.
--
-- Run as cPanel account owner in phpMyAdmin.
-- After CREATE, assign each site's *adm user to that database (cPanel → MySQL Databases)
-- or run the GRANTs below. App writes use usernamea from each site's ini/config.ini.

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

-- ---------------------------------------------------------------------------
-- Privileges (required for seed persistence)
-- Creating the table as account owner does NOT grant app *adm users access.
-- Production error seen: Access denied for user 'aztechn1_lidepadm'@'162.241.219.113'
-- to database 'aztechn1_lidep'
--
-- Preferred: cPanel → MySQL® Databases → Add User To Database
--   aztechn1_lidepadm → aztechn1_lidep (ALL PRIVILEGES)
-- Same pattern for each site's adm user / schema below.
--
-- Or run as owner (host may be '%', 'localhost', or '162.241.219.113'):

GRANT ALL PRIVILEGES ON `aztechn1_lidep`.* TO 'aztechn1_lidepadm'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_elite`.* TO 'aztechn1_lidepadm'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_huskies`.* TO 'aztechn1_lidepadm'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_vollidep`.* TO 'aztechn1_lidepadm'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_demomina`.* TO 'aztechn1_nuedepadm'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_nuestrodeporte`.* TO 'aztechn1_nuedepadm'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_voleibolmetepec`.* TO 'aztechn1_lmvmadm'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_voleymvp`.* TO 'aztechn1_voleymvpadmin'@'%';
GRANT ALL PRIVILEGES ON `aztechn1_aztflag`.* TO 'aztechn1_aztflagadm'@'%';
FLUSH PRIVILEGES;
