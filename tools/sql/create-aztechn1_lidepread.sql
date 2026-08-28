-- =============================================================================
-- Create schema aztechn1_lidepread (same structure as all league sites)
-- Source template: aztechn1_lidep on www.aztechnologies.tech
-- Generated: 2026-06-02
--
-- Includes: 39 tables, 3 functions, 41 stored procedures (structure only, no rows)
-- All AUTO_INCREMENT counters start at 1 (CREATE + ALTER TABLE at end of script)
--
-- cPanel / Bluehost notes:
--   1. Create database "aztechn1_lidepread" in cPanel if CREATE DATABASE fails.
--   2. User aztechn1_admin already exists on this host — assign it to the new DB
--      in cPanel (All Privileges), or run the GRANT block below if you have rights.
--   3. Import this file in phpMyAdmin or MySQL Workbench (may take ~1 minute).
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- Allow creating functions without SUPER (shared hosting; ignore error if denied)
SET GLOBAL log_bin_trust_function_creators = 1;

CREATE DATABASE IF NOT EXISTS `aztechn1_lidepread`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- User already exists on production; (re)grant access to the new schema.
-- If CREATE USER fails, create/link the user in cPanel → MySQL Databases instead.
CREATE USER IF NOT EXISTS 'aztechn1_admin'@'localhost' IDENTIFIED BY 'RTd*jaey)Y@5';
GRANT ALL PRIVILEGES ON `aztechn1_lidepread`.* TO 'aztechn1_admin'@'localhost';
FLUSH PRIVILEGES;

USE `aztechn1_lidepread`;
-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: www.aztechnologies.tech    Database: aztechn1_lidepread
-- ------------------------------------------------------
-- Server version	5.7.44-48

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Amonestados`
--

DROP TABLE IF EXISTS `Amonestados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Amonestados` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Arbitro`
--

DROP TABLE IF EXISTS `Arbitro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Arbitro` (
  `Arbitro_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `Apellido_P` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Apellido_M` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `CURP` varchar(18) COLLATE utf8_unicode_ci NOT NULL,
  `Telefono` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Correo` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Apodo` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Identificacion` longblob NOT NULL,
  `Foto` longblob NOT NULL,
  `Estatus` int(11) NOT NULL,
  `Sexo` int(11) NOT NULL,
  `Validado` int(11) NOT NULL,
  `Historial` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Cursos` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Comentarios` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FechaAlta` datetime DEFAULT NULL,
  `FechaCambio` datetime DEFAULT NULL,
  PRIMARY KEY (`Arbitro_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Asistencia`
--

DROP TABLE IF EXISTS `Asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asistencia` (
  `Torneo_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Fecha` date NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Asistencia` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Torneo_ID`,`Jornada_ID`,`Equipo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Avisos`
--

DROP TABLE IF EXISTS `Avisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Avisos` (
  `Aviso_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Aviso_Fecha_Inicio` datetime NOT NULL,
  `Aviso_Fecha_Fin` datetime NOT NULL,
  `Aviso_Contenido` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `Aviso_Titulo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Aviso_Tipo` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Aviso_Estatus` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `Aviso_Mostrar` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Aviso_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Calendario`
--

DROP TABLE IF EXISTS `Calendario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Calendario` (
  `Calendario_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Calendario_DESC` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Calendario_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Campos`
--

DROP TABLE IF EXISTS `Campos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Campos` (
  `Campo_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Campo_DESC` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Google` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Lat` decimal(11,8) DEFAULT NULL,
  `Lon` decimal(11,8) DEFAULT NULL,
  `Zoom` int(11) DEFAULT NULL,
  PRIMARY KEY (`Campo_ID`),
  UNIQUE KEY `Campo_ID_UNIQUE` (`Campo_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Categorias`
--

DROP TABLE IF EXISTS `Categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Categorias` (
  `Categoria_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Categoria_Desc` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Categoria_Orden` int(11) NOT NULL,
  `Edad_Inicial` int(11) DEFAULT NULL,
  `Edad_Final` int(11) DEFAULT NULL,
  `Color` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Torneo_ID` int(11) NOT NULL,
  `Calendario_ID` int(11) DEFAULT NULL,
  `Rondas` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Categoria_ID`,`Torneo_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Colores`
--

DROP TABLE IF EXISTS `Colores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Colores` (
  `Color_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Color_HEX` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Color_DESC` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Color_ID`),
  UNIQUE KEY `Color_ID_UNIQUE` (`Color_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Configuration`
--

DROP TABLE IF EXISTS `Configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Configuration` (
  `Logo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `LogoX` int(11) NOT NULL,
  `LogoY` int(11) NOT NULL,
  `ColorHeader` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `ColorBody` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `ColorFooter` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `TorneoCopaLiga` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `Categorias` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `EmpatesPenales` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `JugadorJugado` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `JuegoCedulas` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `MarcadorArbitro` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `MarcadorFecha` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `MarcadorDiaDefault` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `JornadaCedulas` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `AvisosTemplete` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `Idioma` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL,
  `LeagueName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ShowIDColumn` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `Latitude` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Longitude` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `ByeWeekPoints` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `ByeWeekPointsGoals` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `ExtraPoints` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `RedFee` varchar(1) COLLATE utf8_unicode_ci DEFAULT '0',
  `RosterBirthDate` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `UnJuegoSemana` int(11) NOT NULL DEFAULT '1',
  `TresSets` int(11) NOT NULL DEFAULT '1',
  `PerfilJugadores` int(1) NOT NULL DEFAULT '1',
  `JugadoresApellidos1` int(11) NOT NULL DEFAULT '0',
  `JuegosxNombre` int(11) NOT NULL DEFAULT '0',
  `CoachJuegos` int(11) NOT NULL DEFAULT '0',
  `CoachJuegosDiaInicial` int(11) NOT NULL DEFAULT '1',
  `CoachJuegosDiaFinal` int(11) NOT NULL DEFAULT '7',
  `MarcadorHoraDefault` time NOT NULL DEFAULT '14:00:00',
  `CoachJuegosHoraFinal` time NOT NULL DEFAULT '16:00:00',
  `TarjetaCambios` int(1) DEFAULT '0',
  `VollByeWeekSets` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `VollByeWeekPoints` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `VollByeWeekSetPoints` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `Apodo` int(1) NOT NULL DEFAULT '1',
  `BuscaCurp` int(1) NOT NULL DEFAULT '0',
  `MultiJugador` int(1) NOT NULL DEFAULT '0',
  `playerIDPDF` int(11) NOT NULL DEFAULT '0',
  `playerSignature` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ControlCurpRequest`
--

DROP TABLE IF EXISTS `ControlCurpRequest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ControlCurpRequest` (
  `CURP` varchar(18) COLLATE utf8_unicode_ci NOT NULL,
  `Equipo_ID` bigint(10) NOT NULL,
  `Fecha` datetime NOT NULL,
  `Respuesta` varchar(45) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Control_Table`
--

DROP TABLE IF EXISTS `Control_Table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Control_Table` (
  `Control_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Action` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Module` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `SQLString` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Status` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `FechayHora` datetime DEFAULT '2017-01-01 00:00:00',
  PRIMARY KEY (`Control_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Convocatoria`
--

DROP TABLE IF EXISTS `Convocatoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Convocatoria` (
  `Convocatoria_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Convocatoria_Fecha_Inicio` datetime NOT NULL,
  `Convocatoria_Fecha_Fin` datetime NOT NULL,
  `Convocatoria_Contenido` varchar(4000) COLLATE utf8_unicode_ci NOT NULL,
  `Convocatoria_Titulo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Convocatoria_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Counter`
--

DROP TABLE IF EXISTS `Counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Counter` (
  `ID` int(11) NOT NULL,
  `Count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Curp`
--

DROP TABLE IF EXISTS `Curp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Curp` (
  `CURP` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `CodigoValidacion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Estatus` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `Nombre` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `ApellidoPaterno` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ApellidoMaterno` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Sexo` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `EstadoNacimiento` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `PaisNacimiento` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `FechaNacimiento` date NOT NULL,
  `Documento` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`CURP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Equipo_Stats`
--

DROP TABLE IF EXISTS `Equipo_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Equipo_Stats` (
  `Torneo_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Last5` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Pts` int(11) NOT NULL,
  `Reales` int(11) NOT NULL,
  `PosGrupo` int(11) NOT NULL,
  `PosGeneral` int(11) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Equipos`
--

DROP TABLE IF EXISTS `Equipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Equipos` (
  `Equipo_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Equipo_DESC` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Activo` int(11) NOT NULL,
  `Fuerza` int(11) NOT NULL,
  `Logo` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Equipo_FULLDESC` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Campo_ID` bigint(20) NOT NULL,
  `Short` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `Playera` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `Calcetas` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `Equipo_DESC3` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`Equipo_ID`,`Torneo_ID`),
  UNIQUE KEY `Equipo_ID_UNIQUE` (`Equipo_ID`,`Torneo_ID`),
  UNIQUE KEY `Torneo_ID_UNIQUE` (`Torneo_ID`,`Equipo_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Expulsados`
--

DROP TABLE IF EXISTS `Expulsados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Expulsados` (
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Doble` int(11) NOT NULL,
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Dias_Castigo` int(11) NOT NULL,
  `Multa` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Pagado` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Goles`
--

DROP TABLE IF EXISTS `Goles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Goles` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Goles` int(11) NOT NULL,
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Instituciones`
--

DROP TABLE IF EXISTS `Instituciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Instituciones` (
  `Institucion_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Institucion_DESC` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Institucion_DESC_Corta` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Institucion_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Jornada`
--

DROP TABLE IF EXISTS `Jornada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Jornada` (
  `Jornada_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Fecha` date NOT NULL,
  `Fecha_Inicio` date NOT NULL,
  `Fecha_Fin` date NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Jornada_Desc` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Jornada_DescCorta` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Jornada_Orden` int(11) NOT NULL DEFAULT '0',
  `Calendario_ID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Jornada_Type` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jornada_ID`,`Torneo_ID`),
  UNIQUE KEY `Jornada_ID_UNIQUE` (`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Juego_Estatus`
--

DROP TABLE IF EXISTS `Juego_Estatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Juego_Estatus` (
  `Juego_Estatus_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Juego_Estatus_DESC_ID` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `Juego_Estatus_DESC2_ID` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `JGL` int(11) NOT NULL DEFAULT '0',
  `JEL` int(11) NOT NULL DEFAULT '0',
  `JPL` int(11) NOT NULL DEFAULT '0',
  `PTSL` int(11) NOT NULL DEFAULT '0',
  `JGV` int(11) NOT NULL DEFAULT '0',
  `JEV` int(11) NOT NULL DEFAULT '0',
  `JPV` int(11) NOT NULL DEFAULT '0',
  `PTSV` int(11) NOT NULL DEFAULT '0',
  `JJL` int(11) NOT NULL DEFAULT '0',
  `JJV` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Juego_Estatus_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Juegos`
--

DROP TABLE IF EXISTS `Juegos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Juegos` (
  `Juego_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Visitante_ID` bigint(20) DEFAULT NULL,
  `Gol_Local` int(11) NOT NULL DEFAULT '0',
  `Gol_Visitante` int(11) NOT NULL DEFAULT '0',
  `Arbitro` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Comentarios` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Local_ID` bigint(20) NOT NULL,
  `Jugado` int(11) NOT NULL DEFAULT '0',
  `Penal_Local` int(11) DEFAULT NULL,
  `Penal_Visitante` int(11) DEFAULT NULL,
  `Estatus` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Extra_Local` int(11) NOT NULL DEFAULT '0',
  `Extra_Visitante` int(11) NOT NULL DEFAULT '0',
  `Campo_ID` bigint(20) DEFAULT NULL,
  `Horario` time NOT NULL DEFAULT '08:00:00',
  `Fecha` date NOT NULL DEFAULT '2017-01-01',
  PRIMARY KEY (`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Juegos_Set`
--

DROP TABLE IF EXISTS `Juegos_Set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Juegos_Set` (
  `Juego_ID` bigint(20) NOT NULL COMMENT 'ID del juego',
  `Torneo_ID` bigint(20) NOT NULL COMMENT 'Id del Torneo',
  `Jornada_ID` bigint(20) NOT NULL COMMENT 'Id de la Jornada',
  `Set1_L` int(3) DEFAULT NULL COMMENT '1er. Set Local',
  `Set1_V` int(3) DEFAULT NULL COMMENT '1er. Set Visitante',
  `Set2_L` int(3) DEFAULT NULL COMMENT '2do. Set Local',
  `Set2_V` int(3) DEFAULT NULL COMMENT '2do. Set Visitante',
  `Set3_L` int(3) DEFAULT NULL COMMENT '3er. Set Local',
  `Set3_V` int(3) DEFAULT NULL COMMENT '3er. Set Visitante',
  `Set4_L` int(3) DEFAULT NULL COMMENT '4to. Set Local',
  `Set4_V` int(3) DEFAULT NULL COMMENT '4to. Set Visitante',
  `Set5_L` int(3) DEFAULT NULL COMMENT '5to. Set Local',
  `Set5_V` int(3) DEFAULT NULL COMMENT '5to. Set Visitante',
  `DefaultL` int(3) DEFAULT '0' COMMENT 'Se agrega este campo para poner si el local pierde por default',
  `DefaultV` int(3) DEFAULT '0' COMMENT 'Se agrega este campo para poner el visitante pierde por default',
  PRIMARY KEY (`Juego_ID`,`Torneo_ID`,`Jornada_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Juegostmp`
--

DROP TABLE IF EXISTS `Juegostmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Juegostmp` (
  `Juego_ID` bigint(20) NOT NULL DEFAULT '0',
  `Visitante_ID` bigint(20) DEFAULT NULL,
  `Gol_Local` int(11) NOT NULL DEFAULT '0',
  `Gol_Visitante` int(11) NOT NULL DEFAULT '0',
  `Arbitro` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Comentarios` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Local_ID` bigint(20) NOT NULL,
  `Jugado` int(11) NOT NULL DEFAULT '0',
  `Penal_Local` int(11) DEFAULT NULL,
  `Penal_Visitante` int(11) DEFAULT NULL,
  `Estatus` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Extra_Local` int(11) NOT NULL DEFAULT '0',
  `Extra_Visitante` int(11) NOT NULL DEFAULT '0',
  `Campo_ID` bigint(20) DEFAULT NULL,
  `Horario` time NOT NULL DEFAULT '08:00:00',
  `Fecha` date NOT NULL DEFAULT '2017-01-01'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `JugadorJugado`
--

DROP TABLE IF EXISTS `JugadorJugado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `JugadorJugado` (
  `Jugador_ID` bigint(20) NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Juego_ID` bigint(20) NOT NULL,
  `Jornada_ID` bigint(20) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Jugado` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Jugador_ID`,`Equipo_ID`,`Juego_ID`,`Jornada_ID`,`Torneo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Jugador_multi`
--

DROP TABLE IF EXISTS `Jugador_multi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Jugador_multi` (
  `Clave_jugador` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Numero` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Clave_jugador`),
  UNIQUE KEY `Jugador_ID_UNIQUE` (`Clave_jugador`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Jugadores`
--

DROP TABLE IF EXISTS `Jugadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Jugadores` (
  `Jugador_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Clave` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Nombre` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Apellido_P` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Apellido_M` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `Estatus` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `Equipo_ID` bigint(20) NOT NULL,
  `Validado` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Comentarios` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FechaAlta` datetime NOT NULL,
  `FechaCambio` datetime NOT NULL,
  `Curp` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Numero` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `correo` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Apodo` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Foto` longblob,
  `Identificacion` longblob,
  `Firma` longblob,
  `FechaValidacionCurp` datetime DEFAULT NULL,
  `IntentosValidacionCurp` bigint(20) NOT NULL DEFAULT '0',
  `Actualizado` bigint(20) NOT NULL DEFAULT '0',
  `ValidacionCurpComentario` varchar(500) COLLATE utf8_unicode_ci DEFAULT '',
  `Sexo` int(11) NOT NULL DEFAULT '0',
  `Jugador_tipo` int(11) NOT NULL DEFAULT '0',
  `Fecha_Validacion` datetime DEFAULT NULL,
  `Fecha_Alta` datetime DEFAULT NULL,
  `Fecha_Baja` datetime DEFAULT NULL,
  `IdentificacionPDF` longblob DEFAULT NULL,
  PRIMARY KEY (`Jugador_ID`),
  UNIQUE KEY `Jugador_ID_UNIQUE` (`Jugador_ID`),
  KEY `Equipo_ID_UNIQUE` (`Equipo_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Lenguaje`
--

DROP TABLE IF EXISTS `Lenguaje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Lenguaje` (
  `Lenguaje_ID` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `Lenguaje_DESC` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Lenguaje_FULLDESC` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `LenguajeDB` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `DateFormat1` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `DateFormat2` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `DateFormat3` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `DateFormat4` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `DateFormat5` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Lenguaje_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Minutas`
--

DROP TABLE IF EXISTS `Minutas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Minutas` (
  `Minuta_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Minuta_Fecha` datetime NOT NULL,
  `Minuta_Contenido` varchar(4000) COLLATE utf8_unicode_ci NOT NULL,
  `Minuta_Titulo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Minuta_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pagos`
--

DROP TABLE IF EXISTS `Pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pagos` (
  `Equipo_ID` bigint(20) NOT NULL,
  `Feccha` date NOT NULL,
  `Monto` int(11) NOT NULL,
  `Comentario` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Multa` int(11) NOT NULL,
  PRIMARY KEY (`Equipo_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Partidos_Jugados`
--

DROP TABLE IF EXISTS `Partidos_Jugados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Partidos_Jugados` (
  `Juego_ID` bigint(20) NOT NULL,
  `Local_ID` bigint(20) NOT NULL,
  `Equipo_Local` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Visitante_ID` bigint(20) DEFAULT NULL,
  `Equipo_Visitante` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Fuerza` int(11) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Jugado` int(11) NOT NULL DEFAULT '0',
  `Vueltas` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Permisos`
--

DROP TABLE IF EXISTS `Permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Permisos` (
  `Equipo_ID` bigint(20) DEFAULT NULL,
  `Fuerza` int(11) NOT NULL,
  `Torneo_ID` bigint(20) NOT NULL,
  `Jornada_ID` int(6) DEFAULT NULL,
  `Fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Range_Age`
--

DROP TABLE IF EXISTS `Range_Age`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Range_Age` (
  `Range_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Range_Name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Range_Type` int(11) NOT NULL,
  `Range_Active` int(11) NOT NULL,
  `Range_Start` int(11) NOT NULL,
  `Range_End` int(11) NOT NULL,
  `CreateDate` datetime NOT NULL,
  `CreateBy` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `UpdateDate` datetime NOT NULL,
  `UpdayeBy` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Range_Color_ID` int(11) NOT NULL,
  `Range_Sort` int(11) NOT NULL,
  PRIMARY KEY (`Range_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Torneos`
--

DROP TABLE IF EXISTS `Torneos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Torneos` (
  `Torneo_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Torneo_Desc` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Costo` int(11) NOT NULL DEFAULT '0',
  `Descuento` int(11) NOT NULL DEFAULT '0',
  `Intereses` int(11) NOT NULL DEFAULT '0',
  `Jornada_Pago` bigint(20) NOT NULL DEFAULT '0',
  `Actual` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `FechaAlta` date DEFAULT NULL,
  `FechaCambio` date DEFAULT NULL,
  `Inscripciones` int(11) NOT NULL DEFAULT '1',
  `TodosVsTodos` int(11) NOT NULL DEFAULT '0',
  `Jornadas` int(11) NOT NULL DEFAULT '0',
  `Rondas` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Torneo_ID`),
  UNIQUE KEY `idTorneos_UNIQUE` (`Torneo_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VBPoints`
--

DROP TABLE IF EXISTS `VBPoints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VBPoints` (
  `LV` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Symbol1` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Symbol2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Points` int(11) DEFAULT NULL,
  `Order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registeredBrowsers`
--

DROP TABLE IF EXISTS `registeredBrowsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registeredBrowsers` (
  `id_browser` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `secret` char(80) COLLATE utf8_unicode_ci NOT NULL,
  `id_user` int(11) NOT NULL,
  `ip_address` char(15) COLLATE utf8_unicode_ci NOT NULL,
  `platform` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `time_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disabled_by` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_browser`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `skey` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `paymentId` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`skey`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `confirmcode` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ApellidoP` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `ApellidoM` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `Equipo_ID` int(11) NOT NULL,
  `lastEmail` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `csalt` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salt` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iterations` int(10) unsigned NOT NULL,
  `confirmtime` datetime DEFAULT NULL,
  `paymentProblem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `totalSpending` decimal(11,2) unsigned NOT NULL DEFAULT '0.00',
  `credit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00',
  `accountOrigin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `message` varchar(305) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adminMessage` varchar(305) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_equipo`
--

DROP TABLE IF EXISTS `usuarios_equipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_equipo` (
  `username` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `Equipo_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'aztechn1_lidepread'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `CAP_FIRST` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER FUNCTION `CAP_FIRST`(input VARCHAR(255)) RETURNS varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci
    DETERMINISTIC
BEGIN
	DECLARE len INT;
	DECLARE i INT;

	SET len   = CHAR_LENGTH(input);
	SET input = LOWER(input);
	SET i = 0;

	WHILE (i < len) DO
		IF (MID(input,i,1) = ' ' OR i = 0) THEN
			IF (i < len) THEN
				SET input = CONCAT(
					LEFT(input,i),
					UPPER(MID(input,i + 1,1)),
					RIGHT(input,len - i - 1)
				);
			END IF;
		END IF;
		SET i = i + 1;
	END WHILE;

	RETURN input;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `CLEAN_JUGADOR_STR` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER FUNCTION `CLEAN_JUGADOR_STR`() RETURNS int(11)
BEGIN
update Jugadores
SET Nombre = ltrim(rtrim(Concat(CONCAT(UPPER(LEFT(SPLIT_STR(Nombre, ' ',1), 1)),
Lower(SUBSTRING(SPLIT_STR(Nombre, ' ',1), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Nombre, ' ',2), 1)),
Lower(SUBSTRING(SPLIT_STR(Nombre, ' ',2), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Nombre, ' ',3), 1)),
Lower(SUBSTRING(SPLIT_STR(Nombre, ' ',3), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Nombre, ' ',4), 1)),
Lower(SUBSTRING(SPLIT_STR(Nombre, ' ',4), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Nombre, ' ',5), 1)),
Lower(SUBSTRING(SPLIT_STR(Nombre, ' ',5), 2)))))),
Apellido_P = ltrim(rtrim(Concat(CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_P, ' ',1), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_P, ' ',1), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_P, ' ',2), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_P, ' ',2), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_P, ' ',3), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_P, ' ',3), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_P, ' ',4), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_P, ' ',4), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_P, ' ',5), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_P, ' ',5), 2)))))),
Apellido_M = ltrim(rtrim(Concat(CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_M, ' ',1), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_M, ' ',1), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_M, ' ',2), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_M, ' ',2), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_M, ' ',3), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_M, ' ',3), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_M, ' ',4), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_M, ' ',4), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apellido_M, ' ',5), 1)),
Lower(SUBSTRING(SPLIT_STR(Apellido_M, ' ',5), 2)))))),
Apodo = ltrim(rtrim(Concat(CONCAT(UPPER(LEFT(SPLIT_STR(Apodo, ' ',1), 1)),
Lower(SUBSTRING(SPLIT_STR(Apodo, ' ',1), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apodo, ' ',2), 1)),
Lower(SUBSTRING(SPLIT_STR(Apodo, ' ',2), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apodo, ' ',3), 1)),
Lower(SUBSTRING(SPLIT_STR(Apodo, ' ',3), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apodo, ' ',4), 1)),
Lower(SUBSTRING(SPLIT_STR(Apodo, ' ',4), 2))), ' ',
CONCAT(UPPER(LEFT(SPLIT_STR(Apodo, ' ',5), 1)),
Lower(SUBSTRING(SPLIT_STR(Apodo, ' ',5), 2))))));
RETURN 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `SPLIT_STR` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER FUNCTION `SPLIT_STR`(x VARCHAR(255),
  delim VARCHAR(12),
  pos INT
) RETURNS varchar(255) CHARSET latin1
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '') ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `AlertCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `AlertCreate`(
	IN inUserName varchar(45),
	IN inalerttitle varchar(45),
	IN inalertstart date,
	IN inalertend date,
	IN inalertstatus int(11),
    IN inalertmessage MEDIUMTEXT,
	IN inalertshow int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    IF (inalertshow > 0) then
		UPDATE Avisos
		set Aviso_Mostrar = 0;
	END IF;
    
	INSERT INTO Avisos
		(Aviso_Fecha_Inicio,
		Aviso_Fecha_Fin,
		Aviso_Contenido,
		Aviso_Titulo,
		Aviso_Tipo,
		Aviso_Estatus,
        Aviso_Mostrar)
	VALUES
		(inalertstart,
		inalertend,
		inalertmessage,
		inalerttitle,
		'',
		inalertstatus,
        inalertshow);
    
     SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'ALERT', CONCAT('INSERT INTO Avisos (Aviso_Fecha_Inicio, Aviso_Fecha_Fin, Aviso_Contenido, Aviso_Titulo, Aviso_Tipo, Aviso_Estatus, Aviso_Mostrar) VALUES (' , inalertstart , ', ' , inalertend , ', ''' , inalertmessage , ''', ''' , inalerttitle , ''', '', ' , inalertstatus , ',' , inalertshow , ');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `AlertUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `AlertUpdate`(
	IN inUserName varchar(45),
	IN inalertid int(11),
	IN inalerttitle varchar(45),
	IN inalertstart date,
	IN inalertend date,
	IN inalertstatus int(11),
    IN inalertmessage MEDIUMTEXT,
	IN inalertshow int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
        
    IF (inalertshow > 0) then
		UPDATE Avisos
		set Aviso_Mostrar = 0;
	END IF;
    
	UPDATE Avisos
	SET Aviso_Fecha_Inicio = inalertstart,
		Aviso_Fecha_Fin = inalertend,
		Aviso_Contenido = inalertmessage,
		Aviso_Titulo = inalerttitle,
		Aviso_Tipo = '',
		Aviso_Estatus = inalertstatus,
        Aviso_Mostrar = inalertshow
	WHERE Aviso_ID = inalertid;    
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'ALERT', CONCAT('Update Avisos SET Aviso_Fecha_Inicio = ' , inalertstart , ', Aviso_Fecha_Fin = ' , inalertend , ', Aviso_Contenido = ''' , inalertmessage , ''', Aviso_Titulo = ''' , inalerttitle , ''', Aviso_Tipo = '', Aviso_Estatus = ' , inalertstatus , ', Aviso_Mostrar = ' , inalertshow , ' where Aviso_ID = ' , inalertid , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CalendarCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `CalendarCreate`(
	IN inUserName varchar(45),
	IN incalendarname varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Calendario
		(Calendario_DESC)
	VALUES
		(incalendarname);

            
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'CALENDAR', CONCAT('INSERT INTO Calendario (Calendario_DESC) VALUES (''' , incalendarname , ''');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CalendarUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `CalendarUpdate`(
	IN inUserName varchar(45),
	IN incalendarid int(11),
    IN incalendarname varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    Update Calendario
	SET Calendario_DESC = incalendarname
	where Calendario_ID = incalendarid;

            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'CALENDAR', CONCAT('Update Calendario SET Calendario_DESC = ''' , incalendarname , ''' where Calendario_ID = ' , incalendarid , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Cal_Sem` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `Cal_Sem`(
	IN torneo bigint(20),
    IN jornada bigint(20)
)
BEGIN
DROP TEMPORARY TABLE IF EXISTS PrimSem;
SET @rank:=0;
CREATE TEMPORARY TABLE IF NOT EXISTS PrimSem AS (
SELECT @rank:=@rank+1 AS rank, Logo, Equipo_ID, Equipo_DESC, JJ, JG, JE, JP, GF, GC, DIFF, Puntos, Reales, last5 
from (Select Logo, j.Equipo_ID, Equipo_DESC, fuerza, sum(Juegos) as JJ, sum(JG) as JG, sum(JE) as JE, sum(JP) as JP, sum(Puntos) as Puntos, sum(Reales) as Reales, Sum(GF) as GF, 
	Sum(GC) as GC, Sum(GF) - Sum(GC) as DIFF, last5
	from (
	select distinct Logo, l.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante then 2
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante then 1
				else 0
			end
		as Puntos,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante then 2
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante then 1
				else 0
			end + Extra_Local as Reales, Gol_Local as GF, Gol_Visitante as GC,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 
					case when l.Estatus like '5' then 1 else 0 end
			end
		as Juegos,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JE,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos l on e.Equipo_ID = l.Local_ID  
													and e.Torneo_ID = torneo 
													and l.Torneo_ID = torneo
													and Equipo_ID <> 28 
	where e.Fuerza = 1 and e.Torneo_ID = torneo
	UNION
	select distinct Logo, v.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante then 2 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante then 1 
				else 0
			end
		as Puntos, 
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante then 2 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante then 1 
				else 0
			end + Extra_Visitante
		as Reales, Gol_Visitante as GF, Gol_Local as GC,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 1 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 
					case when v.Estatus like '5' then 1 else 0 end
			end
		as Juegos ,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JE,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos v on e.Equipo_ID = v.Visitante_ID 
													and e.Torneo_ID = torneo 
													and v.Torneo_ID = torneo
													and Equipo_ID <> 28
	where e.Fuerza = 1 and e.Torneo_ID = torneo) j
		join (select Equipo_ID, concat(CAST(sum(JG) AS char(20)),'-',CAST(sum(JP) AS char(20)),'-', CAST(sum(JE) AS char(20))) 'last5' from 
	(select l.Jornada_ID, Equipo_ID, 
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JE,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos l on e.Equipo_ID = l.Local_ID and e.Torneo_ID = torneo and l.Torneo_ID = torneo
	where Jornada_ID between (jornada-4) and jornada and Fuerza = 1
		and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)
	UNION
	select distinct v.Jornada_ID, Equipo_ID,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JE,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos v on e.Equipo_ID = v.Visitante_ID and e.Torneo_ID = torneo and v.Torneo_ID = torneo
	where Jornada_ID between (jornada-4) and jornada and Fuerza = 1
		and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)) j
	group by Equipo_ID) f on j.Equipo_ID = f.Equipo_ID
where Jornada_ID <= (select max(Jornada_ID)-2 
						from Jornada 
                        where Torneo_ID = torneo) and Fuerza = 1
	and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)
Group by j.Equipo_ID, Equipo_DESC, Fuerza
order by Sum(Reales) desc, Sum(GF) - Sum(GC) desc, Sum(GF), Equipo_DESC) jj);                        
 
UPDATE Juegos 
SET Local_ID = (select Equipo_ID 
				from PrimSem 
				where rank = 1) 
WHERE Juego_ID = 1 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Visitante_ID = (select Equipo_ID 
					from PrimSem 
                    where rank = 4) 
WHERE Juego_ID = 1 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Local_ID = (select Equipo_ID 
				from PrimSem 
                where rank = 2) 
WHERE Juego_ID = 2 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Visitante_ID = (select Equipo_ID 
					from PrimSem 
                    where rank = 3) 
WHERE Juego_ID = 2 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
                     
DROP TEMPORARY TABLE IF EXISTS SeguSem;
SET @rank:=0;
CREATE TEMPORARY TABLE IF NOT EXISTS SeguSem AS (
SELECT @rank:=@rank+1 AS rank, Logo, Equipo_ID, Equipo_DESC, JJ, JG, JE, JP, GF, GC, DIFF, Puntos, Reales, last5 
from (Select Logo, j.Equipo_ID, Equipo_DESC, fuerza, sum(Juegos) as JJ, sum(JG) as JG, sum(JE) as JE, sum(JP) as JP, sum(Puntos) as Puntos, sum(Reales) as Reales, Sum(GF) as GF, 
	Sum(GC) as GC, Sum(GF) - Sum(GC) as DIFF, last5
	from (
	select distinct Logo, l.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante then 2
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante then 1
				else 0
			end
		as Puntos,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante then 2
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante then 1
				else 0
			end + Extra_Local as Reales, Gol_Local as GF, Gol_Visitante as GC,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 
					case when l.Estatus like '5' then 1 else 0 end
			end
		as Juegos,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JE,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos l on e.Equipo_ID = l.Local_ID  
													and e.Torneo_ID = torneo 
													and l.Torneo_ID = torneo
													and Equipo_ID <> 28 
	where e.Fuerza = 2 and e.Torneo_ID = torneo
	UNION
	select distinct Logo, v.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante then 2 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante then 1 
				else 0
			end
		as Puntos, 
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante then 2 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante then 1 
				else 0
			end + Extra_Visitante
		as Reales, Gol_Visitante as GF, Gol_Local as GC,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 1 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 
					case when v.Estatus like '5' then 1 else 0 end
			end
		as Juegos ,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JE,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos v on e.Equipo_ID = v.Visitante_ID 
													and e.Torneo_ID = torneo 
													and v.Torneo_ID = torneo
													and Equipo_ID <> 28
	where e.Fuerza = 2 and e.Torneo_ID = torneo) j
		join (select Equipo_ID, concat(CAST(sum(JG) AS char(20)),'-',CAST(sum(JP) AS char(20)),'-', CAST(sum(JE) AS char(20))) 'last5' from 
	(select l.Jornada_ID, Equipo_ID, 
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JE,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos l on e.Equipo_ID = l.Local_ID and e.Torneo_ID = torneo and l.Torneo_ID = torneo
	where Jornada_ID between (jornada-4) and jornada and Fuerza = 2
		and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)
	UNION
	select distinct v.Jornada_ID, Equipo_ID,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JE,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos v on e.Equipo_ID = v.Visitante_ID and e.Torneo_ID = torneo and v.Torneo_ID = torneo
	where Jornada_ID between (jornada-4) and jornada and Fuerza = 2
		and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)) j
	group by Equipo_ID) f on j.Equipo_ID = f.Equipo_ID
where Jornada_ID <= (select max(Jornada_ID)-2 
						from Jornada 
                        where Torneo_ID = torneo) and Fuerza = 2
	and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)
Group by j.Equipo_ID, Equipo_DESC, Fuerza
order by Sum(Reales) desc, Sum(GF) - Sum(GC) desc, Sum(GF), Equipo_DESC) jj);                        
 
UPDATE Juegos 
SET Local_ID = (select Equipo_ID 
				from SeguSem 
				where rank = 1) 
WHERE Juego_ID = 3 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Visitante_ID = (select Equipo_ID 
					from SeguSem 
                    where rank = 4) 
WHERE Juego_ID = 3 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Local_ID = (select Equipo_ID 
				from SeguSem 
                where rank = 2) 
WHERE Juego_ID = 4 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Visitante_ID = (select Equipo_ID 
					from SeguSem 
                    where rank = 3) 
WHERE Juego_ID = 4 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
        
DROP TEMPORARY TABLE IF EXISTS TercSem;
SET @rank:=0;
CREATE TEMPORARY TABLE IF NOT EXISTS TercSem AS (
SELECT @rank:=@rank+1 AS rank, Logo, Equipo_ID, Equipo_DESC, JJ, JG, JE, JP, GF, GC, DIFF, Puntos, Reales, last5 
from (Select Logo, j.Equipo_ID, Equipo_DESC, fuerza, sum(Juegos) as JJ, sum(JG) as JG, sum(JE) as JE, sum(JP) as JP, sum(Puntos) as Puntos, sum(Reales) as Reales, Sum(GF) as GF, 
	Sum(GC) as GC, Sum(GF) - Sum(GC) as DIFF, last5
	from (
	select distinct Logo, l.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante then 2
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante then 1
				else 0
			end
		as Puntos,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante then 2
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante then 1
				else 0
			end + Extra_Local as Reales, Gol_Local as GF, Gol_Visitante as GC,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 
					case when l.Estatus like '5' then 1 else 0 end
			end
		as Juegos,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JE,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos l on e.Equipo_ID = l.Local_ID  
													and e.Torneo_ID = torneo 
													and l.Torneo_ID = torneo
													and Equipo_ID <> 28 
	where e.Fuerza = 3 and e.Torneo_ID = torneo
	UNION
	select distinct Logo, v.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante then 2 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante then 1 
				else 0
			end
		as Puntos, 
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante then 2 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante then 1 
				else 0
			end + Extra_Visitante
		as Reales, Gol_Visitante as GF, Gol_Local as GC,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 1 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 
					case when v.Estatus like '5' then 1 else 0 end
			end
		as Juegos ,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JE,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos v on e.Equipo_ID = v.Visitante_ID 
													and e.Torneo_ID = torneo 
													and v.Torneo_ID = torneo
													and Equipo_ID <> 28
	where e.Fuerza = 3 and e.Torneo_ID = torneo) j
		join (select Equipo_ID, concat(CAST(sum(JG) AS char(20)),'-',CAST(sum(JP) AS char(20)),'-', CAST(sum(JE) AS char(20))) 'last5' from 
	(select l.Jornada_ID, Equipo_ID, 
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JE,
			case 
				when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos l on e.Equipo_ID = l.Local_ID and e.Torneo_ID = torneo and l.Torneo_ID = torneo
	where Jornada_ID between (jornada-4) and jornada and Fuerza = 3
		and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)
	UNION
	select distinct v.Jornada_ID, Equipo_ID,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
				else 0
			end
		as JG,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JE,
			case 
				when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
				else 0
			end
		as JP
	from Equipos e
		left outer join Juegos v on e.Equipo_ID = v.Visitante_ID and e.Torneo_ID = torneo and v.Torneo_ID = torneo
	where Jornada_ID between (jornada-4) and jornada and Fuerza = 3
		and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)) j
	group by Equipo_ID) f on j.Equipo_ID = f.Equipo_ID
where Jornada_ID <= (select max(Jornada_ID)-2 
						from Jornada 
                        where Torneo_ID = torneo) and Fuerza = 3
	and ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from Jornada where Torneo_ID = torneo)
Group by j.Equipo_ID, Equipo_DESC, Fuerza
order by Sum(Reales) desc, Sum(GF) - Sum(GC) desc, Sum(GF), Equipo_DESC) jj);                        
 
UPDATE Juegos 
SET Local_ID = (select Equipo_ID 
				from TercSem 
				where rank = 1) 
WHERE Juego_ID = 5 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Visitante_ID = (select Equipo_ID 
					from TercSem 
                    where rank = 4) 
WHERE Juego_ID = 5 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Local_ID = (select Equipo_ID 
				from TercSem 
                where rank = 2) 
WHERE Juego_ID = 6 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
UPDATE Juegos 
SET Visitante_ID = (select Equipo_ID 
					from TercSem 
                    where rank = 3) 
WHERE Juego_ID = 6 AND 
		Jornada_ID = (select max(Jornada_ID)-1 
						from Jornada 
                        where Torneo_ID = torneo) AND 
		Torneo_ID = torneo;
        
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CategoryCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `CategoryCreate`(
	IN inUserName varchar(45),
	IN incategoryname varchar(45),
	IN incategoryorden int(11),
	IN incategoryinicial int(11),
	IN incategoryfinal int(11),
	IN incategorycolor varchar(45),
	IN incategorycalendar int(11),
	IN incategoryrounds int(1),
	IN incategoryseason int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Categorias
		(Categoria_Desc,
		Categoria_Orden,
		Edad_Inicial,
		Edad_Final,
		Color,
        Calendario_ID,
        Rondas,
        Torneo_ID)
	VALUES
		(incategoryname,
        incategoryorden,
        incategoryinicial,
        incategoryfinal,
        incategorycolor,
        incategorycalendar,
        incategoryrounds,
        incategoryseason);

            
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'CATEGORY', CONCAT('INSERT INTO Categorias (Categoria_Desc, Categoria_Orden, Edad_Inicial, Edad_Final, Color, Calendario_ID, Rondas, Torneo_ID) VALUES (''' , incategoryname , ''',' , incategoryorden , ',' , incategoryinicial , ',' , incategoryfinal , ',''' , incategorycolor , ''',' , incategorycalendar , ', ' , incategoryrounds , ',' , incategoryseason , ');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CategoryUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `CategoryUpdate`(
	IN inUserName varchar(45),
	IN incategoryid int(11),
	IN incategoryname varchar(45),
	IN incategoryorden int(11),
	IN incategoryinicial int(11),
	IN incategoryfinal int(11),
	IN incategorycolor varchar(45),
	IN incategorycalendar int(11),
	IN incategoryrounds int(1),
	IN incategoryseason int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    Update Categorias
	SET Categoria_Desc = incategoryname,
		Categoria_Orden = incategoryorden,
		Edad_Inicial = incategoryinicial,
		Edad_Final = incategoryfinal,
		Color = incategorycolor,
        Calendario_ID = incategorycalendar,
        Rondas = incategoryrounds
	where Categoria_ID = incategoryid 
		and Torneo_ID = incategoryseason;

            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'CATEGORY', CONCAT('Update Categorias SET Categoria_Desc = ''' , incategoryname , ''', Categoria_Orden = ' , incategoryorden , ', Edad_Inicial = ' , incategoryinicial , ', Edad_Final = ' , incategoryfinal , ', Color = ''' , incategorycolor , ''', Calendario_ID = ' , incategorycalendar , ', Rondas = ' , incategoryrounds , ' where Categoria_ID = ' , incategoryid , ' and Torneo_ID = ' , incategoryseason , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ColorCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `ColorCreate`(
	IN inUserName varchar(45),
	IN incolorname varchar(45),
	IN incolorhex varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Colores
			(Color_HEX,
			Color_DESC)
	VALUES
			(incolorhex, 
            CAP_FIRST(incolorname));

            
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'COLOUR', CONCAT('INSERT INTO Colores (Color_HEX,	Color_DESC) VALUES (' , incolorhex , ',CAP_FIRST(' , incolorname , '));'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ColorUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `ColorUpdate`(
	IN inUserName varchar(45),
    IN incolorid int(11),
	IN incolorname varchar(45),
	IN incolorhex varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Colores
	SET Color_HEX = incolorhex,
			Color_DESC = CAP_FIRST(incolorname)
	WHERE Color_ID = incolorid;

            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total inserts: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total inserts: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'COLOUR', CONCAT('UPDATE Colores SET Color_HEX = ' , incolorhex , ', Color_DESC = CAP_FIRST(' , incolorname , ') WHERE Color_ID = ' , incolorid , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ConfigAlertUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `ConfigAlertUpdate`(
	IN inUserName varchar(45),
	IN inalert MEDIUMTEXT,
	OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Configuration
		SET AvisosTemplete = inalert
	WHERE id = 0;
            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'CONFIG ALERT', concat('UPDATE Configuration SET AvisosTemplete = ', inalert, ' WHERE id = 0;'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ConfigGeneralUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `ConfigGeneralUpdate`(	
	IN inUserName VARCHAR(45),
	IN inempatespenales VARCHAR(1),
	IN injugadorjugado VARCHAR(1),
	IN injuegocedulas VARCHAR(1),
	IN inmarcadorarbitro VARCHAR(1),
	IN inmarcadorfecha VARCHAR(1),
	IN inmarcadordiadefault VARCHAR(1),
	IN injornadacedulas VARCHAR(1),
	IN inidioma VARCHAR(5),
	IN inshowidcolumn VARCHAR(1),
	IN inbyeweekpoints VARCHAR(1),
	IN inbyeweekpointsgoals VARCHAR(2),
	IN inunjuegosemana VARCHAR(1),
	IN intressets VARCHAR(1),
	IN intperfiljugador VARCHAR(1),
	IN intjugadoresapellidos1 VARCHAR(1),
	IN intjuegosxnombre VARCHAR(1),
	IN intcoachjuegos VARCHAR(1),
	IN intcoachjuegosdiainicial VARCHAR(1),
	IN intcoachjuegosdiafinal VARCHAR(1),
	IN inhorario varchar(20),
	IN intcoachjuegoshorariofinal varchar(20),
	IN inttarjetacambios VARCHAR(1),
	IN invollbyeweeksets varchar(1),
	IN invollbyeweekpoints VARCHAR(2),
	IN invollbyeweeksetpoints VARCHAR(2),
	OUT out_number INT(11)
)
BEGIN
    DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;  
        ROLLBACK;   
    END; 
    UPDATE Configuration
		SET EmpatesPenales = inempatespenales,
			JugadorJugado = injugadorjugado,
			JuegoCedulas = injuegocedulas,
			MarcadorArbitro = inmarcadorarbitro,
			MarcadorFecha = inmarcadorfecha,
			MarcadorDiaDefault = inmarcadordiadefault,
			JornadaCedulas = injornadacedulas,
			Idioma = inidioma,
			ShowIDColumn = inshowidcolumn,
			ByeWeekPoints = inbyeweekpoints,
			ByeWeekPointsGoals = inbyeweekpointsgoals,
			UnJuegoSemana = inunjuegosemana,
			TresSets = intressets,
			PerfilJugadores = intperfiljugador,
			JugadoresApellidos1 = intjugadoresapellidos1,
			JuegosxNombre = intjuegosxnombre,
			CoachJuegos = intcoachjuegos,
			CoachJuegosDiaInicial = intcoachjuegosdiainicial,
			CoachJuegosDiaFinal = intcoachjuegosdiafinal,
            MarcadorHoraDefault = inhorario,
            CoachJuegosHoraFinal = intcoachjuegoshorariofinal,
            TarjetaCambios = inttarjetacambios,
            VollByeWeekSets = invollbyeweeksets,
            VollByeWeekPoints = invollbyeweekpoints,
            VollByeWeekSetPoints = invollbyeweeksetpoints
	WHERE id = 0;
            
    SELECT ROW_COUNT() INTO count_update;
    
    IF (count_update > 0) THEN
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'CONFIG GENERAL', CONCAT('UPDATE Configuration SET EmpatesPenales = ', inempatespenales, ', JugadorJugado = ', injugadorjugado, ', JuegoCedulas = ', injuegocedulas, ', MarcadorArbitro = ', inmarcadorarbitro, ', MarcadorFecha = ', inmarcadorfecha, ', MarcadorDiaDefault = ', inmarcadordiadefault, ', JornadaCedulas = ', injornadacedulas, ', Idioma = ''', inidioma, ''', ShowIDColumn = ', inshowidcolumn, ', ByeWeekPoints = ', inshowidcolumn, ', ByeWeekPointsGoals = ', inbyeweekpointsgoals, ' , UnJuegoSemana = ' , inunjuegosemana , ' , TresSets = ' , intressets , ' , PerfilJugadores = ' , intperfiljugador , ', JugadoresApellidos1 = ', intjugadoresapellidos1, ', JuegosxNombre = ', intjuegosxnombre, ', CoachJuegos = ', intcoachjuegos, ', CoachJuegosDiaInicial = ', intcoachjuegosdiainicial, ', CoachJuegosDiaFinal = ', intcoachjuegosdiafinal, ',
            MarcadorHoraDefault = ', inhorario ,', CoachJuegosHoraFinal = ' , intcoachjuegoshorariofinal , ', TarjetaCambios = ' , inttarjetacambios , ' , VollByeWeekSets = ' , invollbyeweeksets , ', VollByeWeekPoints = ' , invollbyeweekpoints , ', VollByeWeekSetPoints = ' , invollbyeweeksetpoints , ' WHERE id = 0;'), status_update);
	SET out_number = count_update;
	
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ConfigInfoUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `ConfigInfoUpdate`(
	IN inUserName varchar(45),
	IN inleaguename varchar(100),
	IN inlatitude varchar(45),
	IN inlongitude varchar(45),
	IN inlogo varchar(100),
	IN inlogox int(11),
	IN inlogoy int(11),
	IN inheaderhex varchar(45),
	IN inbodyhex varchar(45),
	IN infooterhex varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Configuration
		SET Logo = inlogo,
			LogoX = inlogox,
			LogoY = inlogoy,
			ColorHeader = inheaderhex,
			ColorBody = inbodyhex,
			ColorFooter = infooterhex,
			LeagueName = inleaguename,
			Latitude = inlatitude,
			Longitude = inlongitude
	WHERE id = 0;
            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'CONFIG INFO', concat('UPDATE Configuration SET Logo = ', inlogo, ' ,LogoX = ', inlogox, ' ,LogoY = ', inlogoy, ' ,ColorHeader = ', inheaderhex, ' ,ColorBody = ', inbodyhex, ' ,ColorFooter = ', infooterhex, ' ,LeagueName = ', inleaguename, ' ,Latitude = ', inlatitude, ' ,Longitude = ', inlongitude, ' WHERE id = 0;'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `FieldCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `FieldCreate`(
	IN inUserName varchar(45),
	IN infieldname varchar(45),
	IN infieldlat decimal(11,8),
	IN infieldlon decimal(11,8),
	IN infieldzoom int(11),
	IN infieldgoogle varchar(500),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Campos
	(Campo_DESC,
		Google,
		Lat,
		Lon,
		Zoom)
	VALUES
    (infieldname
		,infieldgoogle
		,infieldlat
		,infieldlon
		,infieldzoom);
  
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'CATEGORY', CONCAT('INSERT INTO Campos (Campo_DESC, Google, Lat, Lon, Zoom) VALUES (''' , infieldname , ''' ,''' , infieldgoogle , ''' ,' , infieldlat , ' ,' , infieldlon , ' ,' , infieldzoom , ');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `FieldUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `FieldUpdate`(
	IN inUserName varchar(45),
	IN infieldid int(11),
	IN infieldname varchar(45),
	IN infieldlat decimal(11,8),
	IN infieldlon decimal(11,8),
	IN infieldzoom int(11),
	IN infieldgoogle varchar(500),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    Update Campos
	SET Campo_DESC = infieldname,
		Google = infieldgoogle,
		Lat = infieldlat,
		Lon = infieldlon,
		zoom = infieldzoom
	where Campo_ID = infieldid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'FIELD', CONCAT('Update Campos SET Campo_DESC = ''' , infieldname , ''', Google = ''' , infieldgoogle , ''', Lat = ' , infieldlat , ', Lon = ' , infieldlon , ', zoom = ' , infieldzoom , ' where Campo_ID = ' , infieldid , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameCreate`(
	IN inUserName varchar(45),
	IN inlocal int(11),
	IN invisitante int(11),
    IN inweek bigint(20),
    IN inseason bigint(20),
    IN indate varchar(45),
    IN injugado int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    DECLARE max_juegoID bigint(20);
    select max(juego_ID)+1 into max_juegoID 
    from Juegos;
    
    INSERT INTO Juegos
		(Juego_ID,
        Visitante_ID,
        Gol_Local,
        Gol_Visitante,
        Arbitro,
        Comentarios,
        Jornada_ID,
		Torneo_ID,
        Local_ID,
        Jugado,
        Penal_Local,
        Penal_Visitante,
        Estatus,
        Extra_Local,
		Extra_Visitante,
        Campo_ID,
        Horario,
        Fecha)
	VALUES (
		max_juegoID, 
        invisitante,
        0,
        0,
        '',
        '', 
        inweek,
        inseason,
        inlocal,
        injugado,
        0,
        0,
        '',
        0,
        0,
        0,
        (select MarcadorHoraDefault from Configuration),
        (select DATE_ADD(Fecha_Inicio, INTERVAL (select MarcadorDiaDefault from Configuration) DAY) from Jornada where Jornada_Id = inweek));

            
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'GAME', CONCAT('INSERT INTO Juegos (Juego_ID,Visitante_ID,Gol_Local,Gol_Visitante,Arbitro,Comentarios,Jornada_ID,Torneo_ID,Local_ID,Jugado,Penal_Local,Penal_Visitante,Estatus,Extra_Local,Extra_Visitante,Campo_ID,Horario,Fecha) VALUES (' , max_juegoID , ',' , ifnull(invisitante,'NULL') , ',0,0,'','',' , inweek , ',' , inseason , ',' , inlocal , ',' , injugado , ',0,0,'',0,0,0,(select MarcadorHoraDefault from Configuration),,
        (select DATE_ADD(Fecha_Inicio, INTERVAL (select MarcadorDiaDefault from Configuration) DAY) from Jornada where Jornada_Id = ' , inweek , '));'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameDelete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameDelete`(
	IN inUserName varchar(45),
	IN ingameid int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_delete INT;
    DECLARE status_delete VARCHAR(55);
    
    DELETE FROM Juegos
    WHERE Juego_ID = ingameid;

    SELECT ROW_COUNT() into count_delete;
    
    DELETE FROM Amonestados
	WHERE Juego_ID = ingameid;
    
    DELETE FROM Expulsados
	WHERE Juego_ID = ingameid;

	DELETE FROM Goles
	WHERE Juego_ID = ingameid;

	DELETE FROM JugadorJugado
	WHERE Juego_ID = ingameid;
    
    IF (count_delete > 0) then
		SET status_delete = CONCAT('OK, total deletes: ',count_delete);
	ELSE
		SET status_delete = CONCAT('NO OK, total deletes: ',count_delete);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'DELETE', 'GAME', CONCAT('DELETE FROM Juegos WHERE Juego_ID = ' , ingameid , ';'), status_delete);
	set out_number = count_delete;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameUpdate`(
	IN inUserName varchar(45),
	IN ingolesl int(11),
	IN ingolesv int(11),
	IN infecha varchar(45),
	IN injugado int(11),
	IN inpenaltiesl int(11),
	IN inpenaltiesv int(11),
	IN inhorario varchar(20),
	IN incampo bigint(20),
	IN injuego bigint(20),
	OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Juegos 
		SET Gol_Local = ingolesl, 
			Gol_Visitante = ingolesv, 
			Fecha = infecha, 
			Jugado = injugado, 
			Penal_Local = inpenaltiesl, 
			Penal_Visitante = inpenaltiesv, 
			Estatus = '', 
			Horario = inhorario, 
			Campo_ID = incampo
		WHERE Juego_ID = injuego;
            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'GAME', concat('UPDATE Juegos SET Gol_Local = ', ingolesl , ', Gol_Visitante = ', ingolesv , ', Fecha = ', infecha , ', Jugado = ', injugado , ', Penal_Local = ', inpenaltiesl , ', Penal_Visitante = ', inpenaltiesv , ', Estatus = '', Horario = ', inhorario , ', Campo_ID = ', incampo , ' WHERE Juego_ID = ', injuego , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameUpdateDetailGoals` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameUpdateDetailGoals`(
	IN inUserName varchar(45),
	IN inplayerid int(11),
	IN inteamid int(11),
	IN ingameid int(11),
	IN inweekid int(11),
	IN inseasonid int(11),
	IN ingoals int(11),
	OUT out_number int(11)
)
BEGIN

	DECLARE count INT;
    DECLARE status_update VARCHAR(55);
    
    INSERT INTO Goles
			(Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Goles) 
		VALUES 
			(inplayerid, inteamid, ingameid, inweekid, inseasonid, ingoals) 
	ON DUPLICATE KEY UPDATE 
		Goles = ingoals;
            
    SELECT ROW_COUNT() into count;
    
    IF (count = 0) then
		SET status_update = CONCAT('No OK, total updates/inserts: 0');
    END IF;
    
    IF (count = 1) then
		SET status_update = CONCAT('No OK, total inserts: ', count);
    END IF;
    
    IF (count = 2) then
		SET status_update = CONCAT('No OK, total updates: ', count);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'GAME DETAIL PLAYER GOALS', concat('INSERT INTO Goles (Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Goles) VALUES (', inplayerid , ', ' , inteamid , ', ' , ingameid , ', ' , inweekid , ', ' , inseasonid , ', ' , ingoals , ') ON DUPLICATE KEY UPDATE Goles = ' , ingoals , ';'), status_update);
	set out_number = count;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameUpdateDetailPlayed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameUpdateDetailPlayed`(
	IN inUserName varchar(45),
	IN inplayerid int(11),
	IN inteamid int(11),
	IN ingameid int(11),
	IN inweekid int(11),
	IN inseasonid int(11),
	IN inplayed int(11),
	OUT out_number int(11)
)
BEGIN

	DECLARE count INT;
    DECLARE status_update VARCHAR(55);
    
    INSERT INTO JugadorJugado
			(Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Jugado) 
		VALUES 
			(inplayerid, inteamid, ingameid, inweekid, inseasonid, inplayed) 
	ON DUPLICATE KEY UPDATE 
		Jugado = inplayed;
            
    SELECT ROW_COUNT() into count;
    
    IF (count = 0) then
		SET status_update = CONCAT('No OK, total updates/inserts: 0');
    END IF;
    
    IF (count = 1) then
		SET status_update = CONCAT('No OK, total inserts: ', count);
    END IF;
    
    IF (count = 2) then
		SET status_update = CONCAT('No OK, total updates: ', count);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'GAME DETAIL PLAYER PLAYED', concat('INSERT INTO JugadorJugado (Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Jugado) VALUES (' , inplayerid , ', ' , inteamid , ', ' , ingameid , ', ' , inweekid , ', ' , inseasonid , ', ' , inplayed , ') ON DUPLICATE KEY UPDATE Jugado = ' , inplayed , ';'), status_update);
	set out_number = count;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameUpdateDetailRed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameUpdateDetailRed`(
	IN inUserName varchar(45),
	IN inplayerid int(11),
	IN inteamid int(11),
	IN ingameid int(11),
	IN inweekid int(11),
	IN inseasonid int(11),
	IN inred int(11),
	IN inreddays int(11),
	IN inredfee int(11),
	IN inreddouble int(11),
	IN inredcomment varchar(500),
	OUT out_number int(11)
)
BEGIN

	DECLARE count INT;
    DECLARE status_update VARCHAR(55);
    
    INSERT INTO Expulsados
			(Comentario, Doble, Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Dias_Castigo, Multa, Cantidad) 
		VALUES 
			(inredcomment, inreddouble, inplayerid, inteamid, ingameid, inweekid, inseasonid, inreddays, inredfee, inred) 
	ON DUPLICATE KEY UPDATE 
		Doble = inreddouble,  Cantidad = inred, Comentario = inredcomment, Dias_Castigo = inreddays, Multa = inredfee;
            
    SELECT ROW_COUNT() into count;
    
    IF (count = 0) then
		SET status_update = CONCAT('No OK, total updates/inserts: 0');
    END IF;
    
    IF (count = 1) then
		SET status_update = CONCAT('No OK, total inserts: ', count);
    END IF;
    
    IF (count = 2) then
		SET status_update = CONCAT('No OK, total updates: ', count);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'GAME DETAIL PLAYER RED', concat('INSERT INTO Expulsados (Comentario, Doble, Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Dias_Castigo, Multa, Cantidad) VALUES (', inredcomment , ', ' , inreddouble, ', ', inplayerid , ', ' , inteamid , ', ' , ingameid , ', ' , inweekid , ', ' , inseasonid , ', ' , inreddays , ', ' , inredfee , ', ' , inred , ') ON DUPLICATE KEY UPDATE Doble = ' , inreddouble , ',  Cantidad = ' , inred , ', Comentario = ' , inredcomment , ', Dias_Castigo = ' , inreddays , ', Multa = ' , inredfee , ';'), status_update);
	set out_number = count;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameUpdateDetails` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameUpdateDetails`(
	IN inUserName varchar(45),
	IN inarbitro varchar(45),
	IN incomentarios varchar(500),
	IN inextral int(11),
	IN inextrav int(11),
	IN injuego bigint(20),
	OUT out_number int(11)
)
BEGIN
	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Juegos 
		SET Arbitro = inarbitro, 
			Comentarios = incomentarios, 
			Extra_Local = inextral, 
			Extra_Visitante = inextrav
		WHERE Juego_ID = injuego;
            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'GAMEDETAILS', concat('UPDATE Juegos SET Arbitro = ', inarbitro , ', Comentarios = ', incomentarios , ', Extra_Local = ', inextral , ', Extra_Visitante = ', inextrav , ' WHERE Juego_ID = ', injuego , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameUpdateDetailYellow` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameUpdateDetailYellow`(
	IN inUserName varchar(45),
	IN inplayerid int(11),
	IN inteamid int(11),
	IN ingameid int(11),
	IN inweekid int(11),
	IN inseasonid int(11),
	IN inyellow int(11),
	OUT out_number int(11)
)
BEGIN

	DECLARE count INT;
    DECLARE status_update VARCHAR(55);
    
    INSERT INTO Amonestados
			(Comentario, Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Dias_Castigo, Multa, Cantidad) 
		VALUES 
			('', inplayerid, inteamid, ingameid, inweekid, inseasonid, 0, 0, inyellow) 
	ON DUPLICATE KEY UPDATE 
		Cantidad = inyellow;
            
    SELECT ROW_COUNT() into count;
    
    IF (count = 0) then
		SET status_update = CONCAT('No OK, total updates/inserts: 0');
    END IF;
    
    IF (count = 1) then
		SET status_update = CONCAT('No OK, total inserts: ', count);
    END IF;
    
    IF (count = 2) then
		SET status_update = CONCAT('No OK, total updates: ', count);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'GAME DETAIL PLAYER YELLOW', concat('INSERT INTO Amonestados (Comentario, Jugador_ID,Equipo_ID, Juego_ID, Jornada_ID, Torneo_ID, Dias_Castigo, Multa, Cantidad) VALUES (', char(39), char(39), ', ', inplayerid , ', ' , inteamid , ', ' , ingameid , ', ' , inweekid , ', ' , inseasonid , ', 0, 0, ' , inyellow , ') ON DUPLICATE KEY UPDATE Cantidad = ' , inyellow , ';'), status_update);
	set out_number = count;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GameUpdateSets` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `GameUpdateSets`(
	IN inUserName varchar(45),
	IN ins1l int(11),
	IN ins2l int(11),
	IN ins3l int(11),
	IN ins4l int(11),
	IN ins5l int(11),
	IN ins1v int(11),
	IN ins2v int(11),
	IN ins3v int(11),
	IN ins4v int(11),
	IN ins5v int(11),
	IN injuego bigint(20),
	OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    IF (SELECT 1 = 1 FROM Juegos_Set WHERE Juego_ID = injuego) THEN
	BEGIN
		UPDATE Juegos_Set 
		SET Set1_L = ins1l, 
			Set2_L = ins2l, 
			Set3_L = ins3l, 
			Set4_L = ins4l, 
			Set5_L = ins5l, 
			Set1_v = ins1v, 
			Set2_v = ins2v, 
			Set3_v = ins3v, 
			Set4_v = ins4v, 
			Set5_v = ins5v
		WHERE Juego_ID = injuego;

		SELECT ROW_COUNT() into count_update;
		
		IF (count_update > 0) then
			SET status_update = CONCAT('OK, total updates: ',count_update);
		ELSE
			SET status_update = CONCAT('NO OK, total updates: ',count_update);
		END IF;
		
		CALL insertIntoControlTable(inUserName, 'UPDATE', 'GAMEDETAILSSETS', concat('UPDATE Juegos_Set SET Set1_L = ' , ins1l , ', Set2_L = ' , ins2l , ', Set3_L = ' , ins3l , ', Set4_L = ' , ins4l , ', Set5_L = ' , ins5l , ', Set1_v = ' , ins1v , ', Set2_v = ' , ins2v , ', Set3_v = ' , ins3v , ', Set4_v = ' , ins4v , ', Set5_v = ' , ins5v , ' WHERE Juego_ID = ', injuego , ';'), status_update);
		set out_number = count_update;
	END;
	ELSE
	BEGIN
		INSERT INTO Juegos_Set
			(`Juego_ID`,`Set1_L`,`Set1_V`,`Set2_L`,`Set2_V`,`Set3_L`,`Set3_V`,`Set4_L`,`Set4_V`,`Set5_L`,`Set5_V`)
		VALUES
			(injuego,ins1l,ins1v,ins2l,ins2v,ins3l,ins3v,ins4l,ins4v,ins5l,ins5v);

		SELECT ROW_COUNT() into count_update;
		
		IF (count_update > 0) then
			SET status_update = CONCAT('OK, total inserts: ',count_update);
		ELSE
			SET status_update = CONCAT('NO OK, total inserts: ',count_update);
		END IF;
		
		CALL insertIntoControlTable(inUserName, 'INSERT', 'GAMEDETAILSSETS', concat('INSERT INTO `aztechn1_demoligapremier`.`Juegos_Set`(`Juego_ID`,`Set1_L`,`Set1_V`,`Set2_L`,`Set2_V`,`Set3_L`,`Set3_V`,`Set4_L`,`Set4_V`,`Set5_L`,`Set5_V`)VALUES(' , injuego , ',' , ins1l , ',' , ins1v , ',' , ins2l , ',' , ins2v , ',' , ins3l , ',' , ins3v , ',' , ins4l , ',' , ins4v , ',' , ins5l , ',' , ins5v , ');'), status_update);
		set out_number = count_update;
	END;
	END IF;
            
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Generate_Equipo_Stats` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `Generate_Equipo_Stats`(
	in IN_Torneo_ID bigint(20),
    in IN_Jornada_ID bigint(20),
    in IN_Fuerza_ID bigint(20))
BEGIN

	declare current_Jornada_ID bigint(20);
	declare penalties varchar(1);
	declare byeWeek varchar(1);
	declare byeWeekGoals varchar(2);

	declare Jornadas cursor for 
		select distinct Jornada_ID from Jornada where Torneo_ID = IN_Torneo_ID and Jornada_ID <= IN_Jornada_ID
		order by Jornada_ID asc;
								
		SELECT EmpatesPenales, 	ByeWeekPoints, ByeWeekPointsGoals INTO penalties, byeWeek, byeWeekGoals FROM Configuration;
		
		delete from Equipo_Stats where Torneo_ID = IN_Torneo_ID and Equipo_ID in (select Equipo_ID 
																								from Equipos 
																								where Fuerza = IN_Fuerza_ID);
		open Jornadas;
		
		start_loop: loop
			fetch Jornadas into current_Jornada_ID;
				
				SET @rankf:=0;
				SET @rankg:=0;
				SET @rank:=0;
				SET @fuerza:=0;

				
				INSERT INTO Equipo_Stats
				(Torneo_ID,
				Jornada_ID,
				Juego_ID,
				Equipo_ID,
				Last5,
				Pts,
				Reales,
				PosGrupo,
				PosGeneral)
				select distinct a.Torneo_ID, a.Jornada_ID, 1 as Juego_ID, a.Equipo_ID, c.last5, a.Puntos, a.Reales, a.rankF, b.rankG
				From (
						select  *, 
								@rank:=CASE WHEN @fuerza <> fuerza THEN 1 ELSE @rank+1 END AS rankF, 
								@fuerza:=fuerza 
						from(
								SELECT 	IN_Torneo_ID AS Torneo_ID, 
										current_Jornada_ID as Jornada_ID, 
										Equipo_ID,
										fuerza AS Fuerza, 
										Equipo_DESC, 
										ifnull(sum(JJ),0) as JJ, 
										ifnull(sum(JG),0) as JG, 
										ifnull(sum(JE),0) as JE, 
										ifnull(sum(JP),0) as JP, 
										ifnull(sum(Puntos),0) as Puntos, 
										ifnull(sum(Reales),0) as Reales, 
										ifnull(Sum(GF),0) as GF,
										ifnull(Sum(GC),0) as GC, 
										ifnull(Sum(GF),0) - ifnull(Sum(GC),0) as DIFF, 
										Juego_ID
								from (
										Select 	j.Jornada_ID, 
												Logo, 
												j.Fuerza, 
												j.Equipo_ID, 
												Equipo_DESC, 
												ifnull(sum(Juegos),0) as JJ, 
												ifnull(sum(JG),0) as JG, 
												ifnull(sum(JE),0) as JE, 
												ifnull(sum(JP),0) as JP, 
												ifnull(sum(Puntos),0) as Puntos, 
												ifnull(sum(Reales),0) as Reales, 
												ifnull(Sum(GF),0) as GF,
												ifnull(Sum(GC),0) as GC, 
												ifnull(Sum(GF),0) - ifnull(Sum(GC),0) as DIFF, 
												Juego_ID
										from (
												select 	distinct Logo, 
														l.Jornada_ID, 
														Equipo_ID, 
														Equipo_DESC, 
														Fuerza, 
														Juego_ID,
														case 
															when byeWeek = 1 and l.Visitante_ID is null then 3
															when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 and l.Visitante_ID is not null then 3 
															when penalties = 1 then 
																case 
																	when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 2 
																	when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1 
																end
															when penalties = 0 then 
																case 
																	when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1 
																end
															else 0
														end as Puntos,
														case 
															when byeWeek = 1 and l.Visitante_ID is null then 3
															when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 and l.Visitante_ID is not null then 3 
															when penalties = 1 then 
																case 
																	when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 2 
																	when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1 
																end
															when penalties = 0 then 
																case 
																	when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1 
																end
															else 0
														end + Extra_Local as Reales, 
														case 
															when byeWeek = 1 and l.Visitante_ID is null then byeWeekGoals
                                                            else Gol_Local 
														end as GF, 
														Gol_Visitante as GC,
														case 
															when byeWeek = 1 and l.Visitante_ID is null then 1
															when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
															when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
															when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
															when l.Jugado = 2 then 1
															else 0
														end as Juegos,
														case 
															when byeWeek = 1 and l.Visitante_ID is null then 1
															when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
															else 0
														end as JG,
														case 
															when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
															else 0
														end as JE,
														case 
															when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
															when l.Jugado = 2 then 1
															else 0
														end as JP
												from Equipos e
													left outer join Juegos l on e.Equipo_ID = l.Local_ID and e.Torneo_ID = l.Torneo_ID and l.Jugado <> 0 
												where e.Torneo_ID = IN_Torneo_ID and Jornada_ID <= IN_Jornada_ID and e.Fuerza = IN_Fuerza_ID
												UNION
												select 	distinct Logo, 
														v.Jornada_ID, 
														Equipo_ID, 
														Equipo_DESC, 
														Fuerza, 
														Juego_ID, 
														case 
															when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
															when penalties = 1 then 
																case 
																	when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 2 
																	when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 
																end
															when penalties = 0 then 
																case 
																	when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
																end
															else 0
														end as Puntos, 
														case 
															when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
															when penalties = 1 then 
																case 
																	when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 2 
																	when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 
																end
															when penalties = 0 then 
																case 
																	when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
																end
															else 0
														end + Extra_Visitante as Reales, 
														Gol_Visitante as GF, 
														Gol_Local as GC,
														case 
															when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
															when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
															when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
															when v.Jugado = 2 then 1
															else 0
														end as Juegos ,
														case 
															when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
															else 0
														end as JG,
														case 
															when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
															else 0
														end as JE,
														case 
															when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
															when v.Jugado = 2 then 1
															else 0
														end as JP
												from Equipos e
													left outer join Juegos v on e.Equipo_ID = v.Visitante_ID and e.Torneo_ID = v.Torneo_ID and v.Jugado <> 0
												where e.Torneo_ID = IN_Torneo_ID and Jornada_ID <= IN_Jornada_ID and e.Fuerza = IN_Fuerza_ID) j
												where Jornada_ID <= current_Jornada_ID
												Group by Fuerza, jornada_ID, Juego_ID, j.Equipo_ID
												order by j.Jornada_ID, (Reales) desc, Sum(GF) - Sum(GC) desc, Sum(GF) desc
							
									) jj 
						group by Torneo_ID, Equipo_ID
						order by Torneo_ID, Jornada_ID, Fuerza, Reales desc, Diff desc, GF desc) a
					) a 
					left outer join (
							SELECT 	IN_Torneo_ID AS Torneo_ID, 
									current_Jornada_ID as Jornada_ID, 
									Equipo_ID,
									@rankg:=CASE WHEN @fuerzag <> 1 THEN 1 ELSE @rankg+1 END AS rankG,
									@fuerzag:=1 AS Fuerza, 
									Equipo_DESC, 
									DIFF, 
									Puntos, 
									Reales
							from (
									Select 	Logo, 
											j.Fuerza, 
											j.Equipo_ID, 
											Equipo_DESC, 
											ifnull(sum(Juegos),0) as JJ, 
											ifnull(sum(JG),0) as JG, 
											ifnull(sum(JE),0) as JE, 
											ifnull(sum(JP),0) as JP, 
											ifnull(sum(Puntos),0) as Puntos, 
											ifnull(sum(Reales),0) as Reales, 
											ifnull(Sum(GF),0) as GF, 
											ifnull(Sum(GC),0) as GC, 
											ifnull(Sum(GF),0) - ifnull(Sum(GC),0) as DIFF
									from (
											select 	distinct Logo, 
													l.Jornada_ID, 
													Equipo_ID, 
													Equipo_DESC, 
													Fuerza, 
													case 
														when byeWeek = 1 and l.Visitante_ID is null then 3
														when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 and l.Visitante_ID is not null then 3 
														when penalties = 1 then 
															case 
																when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 2 
																when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1 
															end
														when penalties = 0 then 
															case 
																when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1 
															end
														else 0
													end as Puntos,
													case 
														when byeWeek = 1 and l.Visitante_ID is null then 3
														when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 and l.Visitante_ID is not null then 4 
														when penalties = 1 then 
															case 
																when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 2 
																when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1 
															end
														when penalties = 0 then 
															case 
																when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1 
															end
														else 0
													end + Extra_Local as Reales, 
													case 
														when byeWeek = 1 and l.Visitante_ID is null then byeWeekGoals
														else Gol_Local 
													end as GF, 
													Gol_Visitante as GC,
													case  
														when byeWeek = 1 and l.Visitante_ID is null then 1
														when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
														when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
														when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
														when l.Jugado = 2 then 1
														else 0
													end as Juegos,
													case 
														when byeWeek = 1 and l.Visitante_ID is null then 1
                                                        when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
														else 0
													end as JG,
													case 
														when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
														else 0
													end as JE,
													case 
														when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
														when l.Jugado = 2 then 1
														else 0
													end as JP
											from Equipos e
												left outer join Juegos l on e.Equipo_ID = l.Local_ID and e.Torneo_ID = l.Torneo_ID and l.Jugado <> 0
											where e.Torneo_ID = IN_Torneo_ID and Jornada_ID <= IN_Jornada_ID and e.Fuerza = IN_Fuerza_ID
											UNION
											select 	distinct Logo, 
													v.Jornada_ID, 
													Equipo_ID, 
													Equipo_DESC, 
													Fuerza, 
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
														when penalties = 1 then 
															case 
																when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 2 
																when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 
															end
														when penalties = 0 then 
															case 
																when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
															end
														else 0
													end as Puntos, 
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante then 3 
														when penalties = 1 then 
															case 
																when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 2 
																when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 
															end
														when penalties = 0 then 
															case 
																when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
															end
														else 0
													end + Extra_Visitante as Reales, 
													Gol_Visitante as GF, 
													Gol_Local as GC,
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
														when v.Jugado = 2 then 1
														else 0
													end as Juegos ,
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
														else 0
													end as JG,
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
														else 0
													end as JE,
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
														when v.Jugado = 2 then 1
														else 0
													end as JP
											from Equipos e
												left outer join Juegos v on e.Equipo_ID = v.Visitante_ID and e.Torneo_ID = v.Torneo_ID and v.Jugado <> 0
											where e.Torneo_ID = IN_Torneo_ID and Jornada_ID <= IN_Jornada_ID and e.Fuerza = IN_Fuerza_ID
										) j
									where Jornada_ID <= current_Jornada_ID
									Group by Fuerza, j.Equipo_ID
									order by Sum(Reales) desc, Sum(GF) - Sum(GC) desc
								) jj 
							order by Fuerza, Reales desc, Diff desc
						) b on a.Torneo_ID = b.Torneo_ID and
								a.Jornada_ID = b.Jornada_ID and
								a.Equipo_ID = b.Equipo_ID
					join (
							select 	IN_Torneo_ID Torneo_ID, 
									current_Jornada_ID Jornada_ID,
									Equipo_ID, 
									concat(CAST(sum(JG) AS char(20)),'-',CAST(sum(JP) AS char(20)),'-', CAST(sum(JE) AS char(20))) as 'last5' 
							from(
									select 	l.Jornada_ID, 
											Equipo_ID, 
											case 
												when byeWeek = 1 and l.Visitante_ID is null then 1
                                                when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
												else 0
											end as JG,
											case 
												when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
												else 0
											end as JE,
											case 
												when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
												when l.Jugado = 2 then 1
												else 0
											end as JP
									from Equipos e
										left outer join Juegos l on e.Equipo_ID = l.Local_ID  and e.Torneo_ID = l.Torneo_ID and l.Jugado <> 0
									where e.Torneo_Id = IN_Torneo_ID and Jornada_ID between (current_Jornada_ID-4) and current_Jornada_ID and e.Fuerza = IN_Fuerza_ID
									UNION
									select 	distinct v.Jornada_ID, 
											Equipo_ID,
											case 
												when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
												else 0
											end as JG,
											case 
												when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
												else 0
											end as JE,
											case 
												when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
												when v.Jugado = 2 then 1
												else 0
											end as JP
									from Equipos e
										left outer join Juegos v on e.Equipo_ID = v.Visitante_ID and e.Torneo_ID = v.Torneo_ID and v.Jugado <> 0
									where e.Torneo_ID = IN_Torneo_ID and Jornada_ID between (current_Jornada_ID-4) and current_Jornada_ID and e.Fuerza = IN_Fuerza_ID
								) j
							group by Equipo_ID) c on a.Torneo_ID = c.Torneo_ID and
													a.Jornada_ID = c.Jornada_ID and
													a.Equipo_ID = c.Equipo_ID
				order by a.Torneo_ID desc, a.Jornada_ID desc, a.Equipo_ID desc, c.last5 desc, a.Puntos desc, a.Reales desc, a.rankF desc, b.rankG desc;
				
    end loop;
    close Jornadas;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `insertIntoControlTable` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `insertIntoControlTable`(
	IN inUserID VARCHAR(55),
	IN inAction VARCHAR(45),
	IN inModule VARCHAR(45),
	IN inSQLString VARCHAR(2000),
	IN inStatus VARCHAR(45)
)
BEGIN
	INSERT INTO Control_Table
			(User_ID,
			Action,
			Module,
			SQLString,
			Status,
			FechayHora)
		VALUES
			(inUserID,
			inAction,
			inModule,
			inSQLString,
			inStatus,
			now());
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `MemoCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `MemoCreate`(
	IN inUserName varchar(45),
	IN inmemotitle varchar(45),
	IN inmemodate date,
    IN inmemomessage MEDIUMTEXT,
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
	INSERT INTO Minutas
		(Minuta_Fecha,
		Minuta_Contenido,
		Minuta_Titulo)
	VALUES
		(inmemodate,
		inmemomessage,
		inmemotitle); 

     SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'MEMO', CONCAT('INSERT INTO Minutas (Minuta_Fecha, Minuta_Contenido, Minuta_Titulo) VALUES (''' , inmemodate , ''', ''' , inmemomessage , ''', ''' , inmemotitle , ''');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `MemoUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `MemoUpdate`(
	IN inUserName varchar(45),
	IN inmemoid int(11),
	IN inmemotitle varchar(45),
	IN inmemodate date,
    IN inmemomessage MEDIUMTEXT,
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
   Update Minutas
	SET Minuta_Fecha = inmemodate,
		Minuta_Contenido = inmemomessage,
		Minuta_Titulo = inmemotitle
	where Minuta_ID = inmemoid; 
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'MEMO', CONCAT('Update Minutas SET Minuta_Fecha = ' , inmemodate , ', Minuta_Contenido = ''' , inmemomessage , ''', Minuta_Titulo = ''' , inmemotitle , ''' where Minuta_ID = ' , inmemoid , '; '), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `PlayerCopy` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `PlayerCopy`(
	IN inUserName VARCHAR(45),
	IN incurp VARCHAR(45),
	IN inequipoid BIGINT(20),
	OUT out_number INT(11)
    
   
)
BEGIN
    DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    DECLARE LAST_INSERT_ID BIGINT(20);
		 INSERT INTO `Jugadores`
            (`Clave`,
             `Nombre`,
             `Apellido_P`,
             `Apellido_M`,
             `Fecha_Nacimiento`,
             `Estatus`,
             `Equipo_ID`,
             `Validado`,
             `Comentarios`,
             `FechaAlta`,
             `FechaCambio`,
             `Curp`,
             `Numero`,
             `Telefono`,
             `correo`,
             `Apodo`,
             `Foto`,
             `Identificacion`,
             `Firma`,
             `FechaValidacionCurp`,
             `IntentosValidacionCurp`,
             `Actualizado`,
             `ValidacionCurpComentario`,
             `Sexo`)
		 
		SELECT `Clave`,
		  `Nombre`,
		  `Apellido_P`,
		  `Apellido_M`,
		  `Fecha_Nacimiento`,
		  `Estatus`,
		  inequipoid,
		  0,
		  `Comentarios`,
		  `FechaAlta`,
		  `FechaCambio`,
		  `Curp`,
		  (	SELECT max(Numero) + 1
			FROM Jugadores
            WHERE Equipo_ID = inequipoid),
		  `Telefono`,
		  `correo`,
		  `Apodo`,
		  `Foto`,
		  `Identificacion`,
		  `Firma`,
		  `FechaValidacionCurp`,
		  `IntentosValidacionCurp`,
		  `Actualizado`,
		  `ValidacionCurpComentario`,
		  `Sexo`
		FROM Jugadores 
		WHERE Curp =incurp ORDER BY FechaAlta ASC LIMIT 1;
		
		SELECT ROW_COUNT() INTO count_insert;
    
    SELECT LAST_INSERT_ID() INTO LAST_INSERT_ID;
    
    SET out_number = count_insert;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `PlayerCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `PlayerCreate`(
	IN inUserName varchar(45),
	IN inclave varchar(45),
	IN innombre varchar(45),
	IN inapellidop varchar(45),
	IN inapellidom varchar(45),
	IN inapodo varchar(45),
	IN infechanacimiento date,
	IN inestatus varchar(1),
	IN incurp varchar(45),
	IN innumero varchar(45),
	IN inequipoid bigint(20),
	IN invalidado varchar(45),
	IN incomentarios varchar(500),
	IN intelefono varchar(45),
	IN incorreo varchar(45),
	IN infoto longblob,
	IN inidentificacion longblob,
	IN infirma longblob,
	IN insexo varchar(1),
	IN intype int(11),
	IN infechaalta datetime,
	IN infechacambio datetime,
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    DECLARE last_insert_id bigint(20);
    
	DECLARE count_update_p INT;
    DECLARE count_update_i INT;
    DECLARE count_update_s INT;
    
    INSERT INTO Jugadores
			(Clave,
			Nombre,
			Apellido_P,
			Apellido_M,
			Apodo,
			Fecha_Nacimiento,
			Estatus,
			Curp,
			Numero,
			Equipo_ID,
			Validado,
			Comentarios,
			Telefono,
			Correo,
			Sexo,
            Jugador_tipo,
			FechaAlta,
			FechaCambio)
			VALUES (inclave
			,CAP_FIRST(innombre)
			,CAP_FIRST(inapellidop)
			,CAP_FIRST(inapellidom)
			,inapodo
			,infechanacimiento
			,inestatus
			,incurp
			,innumero
			,inequipoid
			,invalidado
			,incomentarios
			,intelefono
			,incorreo
			,insexo
            ,intype
			,infechaalta
			,infechacambio);
            
    SELECT ROW_COUNT() into count_insert;
    
    SELECT LAST_INSERT_ID() into last_insert_id;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
        
        IF (CHAR_LENGTH(infoto) > 0) THEN
			UPDATE Jugadores
				SET Foto = infoto,
				FechaCambio = infechacambio
			WHERE Jugador_ID = last_insert_id;
					
			SELECT ROW_COUNT() into count_update_p;
		END IF;
										
		IF (CHAR_LENGTH(inidentificacion) > 0) THEN
			UPDATE Jugadores
				SET Identificacion = inidentificacion,
				FechaCambio = infechacambio
			WHERE Jugador_ID = last_insert_id;
					
			SELECT ROW_COUNT() into count_update_i;
		END IF;
										
		IF (CHAR_LENGTH(infirma) > 0) THEN
			UPDATE Jugadores
				SET Firma = infirma,
				FechaCambio = infechacambio
			WHERE Jugador_ID = last_insert_id;
					
			SELECT ROW_COUNT() into count_update_s;
		END IF;
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'PLAYERS', CONCAT('INSERT INTO Jugadores (Clave, Nombre, Apellido_P, Apellido_M, Apodo, Fecha_Nacimiento, Estatus, Curp, Numero, Equipo_ID, Validado, Comentarios, Telefono, Correo, Foto, Identificacion, Firma, Sexo, Jugador_tipo, FechaAlta, FechaCambio) VALUES (' , inclave , ',' , innombre , ',' , inapellidop , ',' , inapellidom , ',' , inapodo , ',' , infechanacimiento , ',' , inestatus , ',' , incurp , ',' , innumero , ',' , inequipoid , ',' , invalidado , ',' , incomentarios , ',' , intelefono , ',' , incorreo , ','','','',' , insexo , ',' , intype , ',' , infechaalta , ',' , infechacambio , ');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `PlayerTeamUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `PlayerTeamUpdate`(
	IN inUserName varchar(45),
	IN injugadorid varchar(45),
	IN innombre varchar(45),
	IN inapellidop varchar(45),
	IN inapellidom varchar(45),
	IN inapodo varchar(45),
	IN infechanacimiento date,
	IN inestatus varchar(1),
	IN incurp varchar(45),
	IN innumero varchar(45),
	IN inequipoid bigint(20),
	IN invalidado varchar(45),
	IN incomentarios varchar(500),
	IN intelefono varchar(45),
	IN incorreo varchar(45),
	IN infoto longblob,
	IN inidentificacion longblob,
	IN infirma longblob,
	IN insexo varchar(1),
	IN intype int(11),
	IN infechacambio datetime,
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update_p INT;
    DECLARE count_update_i INT;
    DECLARE count_update_s INT;
    DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    IF (CHAR_LENGTH(infoto) > 0) THEN
		UPDATE Jugadores
			SET Foto = infoto,
			Validado = 0,
			FechaCambio = infechacambio
		WHERE Jugador_ID = injugadorid;
				
		SELECT ROW_COUNT() into count_update_p;
	END IF;
                                    
	IF (CHAR_LENGTH(inidentificacion) > 0) THEN
		UPDATE Jugadores
			SET Identificacion = inidentificacion,
			Validado = 0,
			FechaCambio = infechacambio
		WHERE Jugador_ID = injugadorid;
				
		SELECT ROW_COUNT() into count_update_i;
	END IF;
                                    
	IF (CHAR_LENGTH(infirma) > 0) THEN
		UPDATE Jugadores
			SET Firma = infirma,
			Validado = 0,
			FechaCambio = infechacambio
		WHERE Jugador_ID = injugadorid;
				
		SELECT ROW_COUNT() into count_update_s;
	END IF;
                                    
	UPDATE Jugadores
		SET Estatus = inestatus,
			Comentarios = incomentarios,
			FechaCambio = infechacambio,
			Numero = innumero,
			Telefono = intelefono,
			correo = incorreo,
			Apodo = inapodo,
            Jugador_tipo = intype
	WHERE Jugador_ID = injugadorid;
            
    SELECT ROW_COUNT() into count_update;
    
    SET out_number = count_update;
    
    IF (ROW_COUNT() > 0) then
		SET status_update = CONCAT('OK, total updates: ',ROW_COUNT());
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',ROW_COUNT());
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'PLAYERS TEAM', CONCAT('UPDATE Jugadores SET Estatus = ''' , inestatus , ''', Comentarios = ''' , incomentarios , ''', FechaCambio = ''' , infechacambio , ''', Numero = ''' , innumero , ''', Telefono = ''' , intelefono , ''', correo = ''' , incorreo , ''', Apodo = ''' , inapodo , ''', Foto = '', Identificacion = '', Firma = '' WHERE Jugador_ID = ' , injugadorid , ';'), status_update);
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `PlayerUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `PlayerUpdate`(
	IN inUserName varchar(45),
	IN injugadorid varchar(45),
	IN innombre varchar(45),
	IN inapellidop varchar(45),
	IN inapellidom varchar(45),
	IN inapodo varchar(45),
	IN infechanacimiento date,
	IN inestatus varchar(1),
	IN incurp varchar(45),
	IN innumero varchar(45),
	IN inequipoid bigint(20),
	IN invalidado varchar(45),
	IN incomentarios varchar(500),
	IN intelefono varchar(45),
	IN incorreo varchar(45),
	IN infoto longblob,
	IN inidentificacion longblob,
	IN infirma longblob,
	IN insexo varchar(1),
	IN intype int(11),
	IN infechacambio datetime,
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update_p INT;
    DECLARE count_update_i INT;
    DECLARE count_update_s INT;
    DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    IF (CHAR_LENGTH(infoto) > 0) THEN
		UPDATE Jugadores
			SET Foto = infoto,
			FechaCambio = infechacambio
		WHERE Jugador_ID = injugadorid;
				
		SELECT ROW_COUNT() into count_update_p;
	END IF;
                                    
	IF (CHAR_LENGTH(inidentificacion) > 0) THEN
		UPDATE Jugadores
			SET Identificacion = inidentificacion,
			FechaCambio = infechacambio
		WHERE Jugador_ID = injugadorid;
				
		SELECT ROW_COUNT() into count_update_i;
	END IF;
                                    
	IF (CHAR_LENGTH(infirma) > 0) THEN
		UPDATE Jugadores
			SET Firma = infirma,
			FechaCambio = infechacambio
		WHERE Jugador_ID = injugadorid;
				
		SELECT ROW_COUNT() into count_update_s;
	END IF;
                                    
	UPDATE Jugadores
		SET Nombre = CAP_FIRST(innombre),
			Apellido_P = CAP_FIRST(inapellidop),
			Apellido_M = CAP_FIRST(inapellidom),
            Fecha_Nacimiento = infechanacimiento,
			Estatus = inestatus,
			Curp = incurp,
            Equipo_ID = inequipoid,
			Validado = invalidado,
			Comentarios = incomentarios,
			Numero = innumero,
			Telefono = intelefono,
			correo = incorreo,
			Sexo = insexo,
			Apodo = inapodo,
            Jugador_tipo = intype
	WHERE Jugador_ID = injugadorid;
            
    SELECT ROW_COUNT() into count_update;
    
    SET out_number = count_update;
    
    IF (ROW_COUNT() > 0) then
		SET status_update = CONCAT('OK, total updates: ',ROW_COUNT());
		
		UPDATE Jugadores
			SET FechaCambio = infechacambio
		WHERE Jugador_ID = injugadorid;
		
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',ROW_COUNT());
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'PLAYERS', CONCAT('UPDATE Jugadores SET Nombre = ''' , innombre , ''', Apellido_P = ''' , inapellidop , ''', Apellido_M = ''' , inapellidom , ''', Fecha_Nacimiento = ''' , infechanacimiento , ''', Estatus = ''' , inestatus , ''', Curp = ''' , incurp , ''', Equipo_ID = ' , inequipoid , ', Validado = ' , invalidado , ', Comentarios = ''' , incomentarios , ''', FechaCambio = ''' , infechacambio , ''', Numero = ''' , innumero , ''', Telefono = ''' , intelefono , ''', correo = ''' , incorreo , ''', Sexo = ''' , insexo , ''', Jugador_tipo = ', intype , ', Apodo = ''' , inapodo , ''', Foto = '', Identificacion = '', Firma = '' WHERE Jugador_ID = ' , injugadorid , ';'), status_update);
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `RefereeCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `RefereeCreate`(
	IN inUserName varchar(45),
	IN inclave varchar(45),
	IN innombre varchar(45),
	IN inapellidop varchar(45),
	IN inapellidom varchar(45),
	IN inapodo varchar(45),
	IN infechanacimiento date,
	IN inestatus varchar(1),
	IN incurp varchar(45),
	IN invalidado varchar(45),
	IN incomentarios varchar(500),
    IN inhistorial varchar(500),
    IN incursos varchar(500),
	IN intelefono varchar(45),
	IN incorreo varchar(45),
	IN infoto longblob,
	IN inidentificacion longblob,
	IN infirma longblob,
	IN insexo varchar(1),
	IN infechaalta datetime,
	IN infechacambio datetime,
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    DECLARE last_insert_id bigint(20);
    
	DECLARE count_update_p INT;
    DECLARE count_update_i INT;
    DECLARE count_update_s INT;
    
    INSERT INTO Arbitro
			(Nombre,
			Apellido_P,
			Apellido_M,
			Apodo,
			Fecha_Nacimiento,
			Estatus,
			Curp,
			Validado,
			Comentarios,
            Historial,
            Cursos,
			Telefono,
			Correo,
			Sexo,
            FechaAlta,
			FechaCambio)
			VALUES (CAP_FIRST(innombre)
			,CAP_FIRST(inapellidop)
			,CAP_FIRST(inapellidom)
			,inapodo
			,infechanacimiento
			,inestatus
			,incurp
			,invalidado
			,incomentarios
            ,inhistorial
            ,incursos
			,intelefono
			,incorreo
			,insexo
			,infechaalta
			,infechacambio);
            
    SELECT ROW_COUNT() into count_insert;
    
    SELECT LAST_INSERT_ID() into last_insert_id;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
        
        IF (CHAR_LENGTH(infoto) > 0) THEN
			UPDATE Arbitro
				SET Foto = infoto,
				FechaCambio = infechacambio
			WHERE Arbitro_ID = last_insert_id;
					
			SELECT ROW_COUNT() into count_update_p;
		END IF;
										
		IF (CHAR_LENGTH(inidentificacion) > 0) THEN
			UPDATE Arbitro
				SET Identificacion = inidentificacion,
				FechaCambio = infechacambio
			WHERE Arbitro_ID = last_insert_id;
					
			SELECT ROW_COUNT() into count_update_i;
		END IF;
										
		IF (CHAR_LENGTH(infirma) > 0) THEN
			UPDATE Arbitro
				SET Firma = infirma,
				FechaCambio = infechacambio
			WHERE Arbitro_ID = last_insert_id;
					
			SELECT ROW_COUNT() into count_update_s;
		END IF;
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'REFEREE', CONCAT('INSERT INTO Jugadores (Clave, Nombre, Apellido_P, Apellido_M, Apodo, Fecha_Nacimiento, Estatus, Curp, Validado, Comentarios, Telefono, Correo, Historial, Cursos, Foto, Identificacion, Firma, Sexo, FechaAlta, FechaCambio) VALUES (' , inclave , ',' , innombre , ',' , inapellidop , ',' , inapellidom , ',' , inapodo , ',' , infechanacimiento , ',' , inestatus , ',' , incurp , ',' , invalidado , ',' , incomentarios , ',', inhistorial , ',', incursos, ',' , intelefono , ',' , incorreo , ','','','',' , insexo , ',' , infechaalta , ',' , infechacambio , ');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `RefereeUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `RefereeUpdate`(
	IN inUserName varchar(45),
	IN inarbitroid varchar(45),
	IN innombre varchar(45),
	IN inapellidop varchar(45),
	IN inapellidom varchar(45),
	IN inapodo varchar(45),
	IN infechanacimiento date,
	IN inestatus varchar(1),
	IN incurp varchar(45),
	IN invalidado varchar(45),
	IN incomentarios varchar(500),
    IN inhistorial varchar(500),
    IN incursos varchar (500),
	IN intelefono varchar(45),
	IN incorreo varchar(45),
	IN infoto longblob,
	IN inidentificacion longblob,
	IN infirma longblob,
	IN insexo varchar(1),
	IN infechacambio datetime,
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update_p INT;
    DECLARE count_update_i INT;
    DECLARE count_update_s INT;
    DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    IF (CHAR_LENGTH(infoto) > 0) THEN
		UPDATE Arbitro
			SET Foto = infoto,
			FechaCambio = infechacambio
		WHERE Arbitro_ID = inarbitroid;
				
		SELECT ROW_COUNT() into count_update_p;
	END IF;
                                    
	IF (CHAR_LENGTH(inidentificacion) > 0) THEN
		UPDATE Arbitro
			SET Identificacion = inidentificacion,
			FechaCambio = infechacambio
		WHERE Arbitro_ID = inarbitroid;
				
		SELECT ROW_COUNT() into count_update_i;
	END IF;
                                    
	IF (CHAR_LENGTH(infirma) > 0) THEN
		UPDATE Arbitro
			SET Firma = infirma,
			FechaCambio = infechacambio
		WHERE Arbitro_ID = inarbitroid;
				
		SELECT ROW_COUNT() into count_update_s;
	END IF;
                                    
	UPDATE Arbitro
		SET Nombre = CAP_FIRST(innombre),
			Apellido_P = CAP_FIRST(inapellidop),
			Apellido_M = CAP_FIRST(inapellidom),
            Fecha_Nacimiento = infechanacimiento,
			Estatus = inestatus,
			Curp = incurp,
            Validado = invalidado,
			Comentarios = incomentarios,
            Historial = inhistorial,
            Cursos = incursos,
			Telefono = intelefono,
			correo = incorreo,
			Sexo = insexo,
			Apodo = inapodo
            
	WHERE Arbitro_ID = inarbitroid;
            
    SELECT ROW_COUNT() into count_update;
    
    SET out_number = count_update;
    
    IF (ROW_COUNT() > 0) then
		SET status_update = CONCAT('OK, total updates: ',ROW_COUNT());
		
		UPDATE Arbitro
			SET FechaCambio = infechacambio
		WHERE Arbitro_ID = inarbitroid;
		
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',ROW_COUNT());
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'REFEREE', CONCAT('UPDATE Arbitro SET Nombre = ''' , innombre , ''', Apellido_P = ''' , inapellidop , ''', Apellido_M = ''' , inapellidom , ''', Fecha_Nacimiento = ''' , infechanacimiento , ''', Estatus = ''' , inestatus , ''', Curp = ''' , incurp , ', Validado = ' , invalidado , ', Comentarios = ''' , incomentarios , ', Historial = ''' , inhistorial , ', Cursos = ''' , incursos, ''', FechaCambio = ''' , infechacambio , ''', Telefono = ''' , intelefono , ''', correo = ''' , incorreo , ''', Sexo = ''' , insexo , ', Apodo = ''' , inapodo , ''', Foto = '', Identificacion = '', Firma = '' FROM Arbitro WHERE Arbitro_ID = ' , inarbitroid , ';'), status_update);
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `StandarJugadorTextos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `StandarJugadorTextos`()
BEGIN
	UPDATE `Jugadores`
	SET
	`Nombre` = CAP_FIRST(Nombre),
	`Apellido_P` = CAP_FIRST(Apellido_P),
	`Apellido_M` = CAP_FIRST(Apellido_M),
	`Comentarios` = CAP_FIRST(Comentarios),
	`Curp` = UPPER(Curp),
	`Apodo` = CAP_FIRST(Apodo);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `TeamCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45), 
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM $schema.Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `TeamUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM $schema.Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `TournamentCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `TournamentCreate`(
	IN inUserName varchar(45),
	IN intournamrntname varchar(45),
	IN intournamrntactual varchar(1),
	IN intournamrntinscr int(11),
	IN intournamrntvsall int(11),
	IN intournamrntweeks int(11),
    OUT out_number int(11)
)
BEGIN
	
    DECLARE prev_TorneoID INT;
    DECLARE curr_TorneoID INT;
	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    IF (intournamrntactual = 'S') then
		Update Torneos
			SET Actual = 'N',
				FechaCambio = NOW();
    END IF;
    
    IF (intournamrntinscr > 0) then
		Update Torneos
			SET Inscripciones = 0,
				FechaCambio = NOW();
    END IF;

    
    INSERT INTO Torneos
		(Torneo_Desc, 
		Actual, 
		Inscripciones, 
		TodosVsTodos, 
		FechaAlta, 
		FechaCambio, 
		Jornadas)
	VALUES
		(intournamrntname,
        intournamrntactual,
        intournamrntinscr,
        intournamrntvsall,
        NOW(),
        NOW(),
        intournamrntweeks);

            
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
        
        SELECT max(Torneo_ID) into @curr_TorneoID 
        FROM Torneos;
        
        SELECT max(Torneo_ID) into @prev_TorneoID 
        FROM Torneos
        where Torneo_ID <> @curr_TorneoID;
        
        insert into Equipos
		SELECT a.Equipo_ID,
			Equipo_DESC,
			Activo,
			Fuerza,
			Logo,
			Equipo_FULLDESC,
			@curr_TorneoID  Torneo_ID,
			Campo_ID,
			Short,
			Playera,
			Calcetas,
            Equipo_DESC3
		FROM Equipos a
		WHERE Torneo_ID = @prev_TorneoID;
        
        insert into Categorias
		SELECT a.Categoria_ID,
			Categoria_Desc,
			Categoria_Orden,
			Edad_Inicial,
			Edad_Final,
			Color,
			@curr_TorneoID  Torneo_ID,
			Calendario_ID
		FROM Categorias a
		WHERE Torneo_ID = @prev_TorneoID;
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TOURNAMENT', CONCAT('INSERT INTO Torneos (Torneo_Desc, Actual, Inscripciones, TodosVsTodos, FechaAlta, FechaCambio, Jornadas) VALUES (''' , intournamrntname , ''',' , intournamrntactual , ',' , intournamrntinscr , ',' , intournamrntvsall , ',NOW(),NOW(),' , intournamrntweeks , ');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `TournamentUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `TournamentUpdate`(
	IN inUserName varchar(45),
	IN intournamrntid int(11),
	IN intournamrntname varchar(45),
	IN intournamrntactual varchar(1),
	IN intournamrntinscr int(11),
	IN intournamrntvsall int(11),
	IN intournamrntweeks int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    IF (intournamrntactual = 'S') then
		Update Torneos
			SET Actual = 'N',
				FechaCambio = NOW();
    END IF;
    
    IF (intournamrntinscr > 0) then
		Update Torneos
			SET Inscripciones = 0,
				FechaCambio = NOW();
    END IF;

    
    UPDATE Torneos
	Set Torneo_Desc = intournamrntname, 
		Actual = intournamrntactual,
        Inscripciones = intournamrntinscr, 
        TodosVsTodos = intournamrntvsall,
		FechaCambio = NOW(), 
        Jornadas = intournamrntweeks
	where Torneo_ID = intournamrntid;

            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);

		insert into Equipos
		select a.* 
		from
			(
			SELECT a.Equipo_ID,
				Equipo_DESC,
				Activo,
				Fuerza,
				Logo,
				Equipo_FULLDESC,
				(select max(Torneo_ID) Torneo_ID from Torneos) Torneo_ID,
				Campo_ID,
				Short,
				Playera,
				Calcetas,
                Equipo_DESC3
			FROM Equipos a
				join 
					(
					SELECT Equipo_ID, max(Torneo_ID) Torneo_ID 
					FROM Equipos
					group by Equipo_ID
					) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID
			) a
			left join (
				SELECT Equipo_ID,
					Equipo_DESC,
					Activo,
					Fuerza,
					Logo,
					Equipo_FULLDESC,
					Torneo_ID,
					Campo_ID,
					Short,
					Playera,
					Calcetas,
                    Equipo_DESC3
				FROM Equipos
				where Torneo_ID = (select max(Torneo_ID) Torneo_ID from Torneos)) c on a.equipo_Id = c.Equipo_ID
			where c.Equipo_Id is null;
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TOURNAMENT', CONCAT('UPDATE Torneos Set Torneo_Desc = ''' , intournamrntname , ''', Actual = ''' , intournamrntactual, ''', Inscripciones = ' , intournamrntinscr , ', TodosVsTodos = ' , intournamrntvsall , ', FechaCambio = NOW(), Jornadas = ' , intournamrntweeks , ' where Torneo_ID = ' , intournamrntid , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `UserDelete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `UserDelete`(
	IN inUserName VARCHAR(55),
	IN inid_user INT(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_delete INT;
    DECLARE status_delete VARCHAR(55);
    
    DELETE FROM usuarios_equipo
    WHERE username in (select username FROM usuarios
						WHERE id_user = inid_user);
    
	DELETE FROM transactions
	WHERE id_user = inid_user;	
    
    DELETE FROM registeredBrowsers
	WHERE id_user = inid_user;	
    
    DELETE FROM usuarios
	WHERE id_user = inid_user;
    
    SELECT ROW_COUNT() into count_delete;
    
    SET out_number = count_delete;
    
    IF (ROW_COUNT() > 0) then
		SET status_delete = CONCAT('OK, total deletes: ',ROW_COUNT());
	ELSE
		SET status_delete = CONCAT('NO OK, total deletes: ',ROW_COUNT());
    END IF;
    
    CALL insertIntoControlTable(inUserName, 'DELETE', 'USERS', CONCAT('DELETE FROM usuarios WHERE id_user = ',inid_user), status_delete);

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `WeekCreate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `WeekCreate`(
	IN inUserName varchar(45),
	IN inseason int(11),
	IN inweekdesc int(11),
	IN inweekdescshort varchar(45),
	IN inweeksort int(11),
	IN inweekdate varchar(45),
	IN inweekstart varchar(45),
	IN inweekend varchar(45),
	IN inweekcal int(11),
    IN inweektype int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);

	INSERT INTO Jornada
		(Fecha,
		Fecha_Inicio,
		Fecha_Fin,
		Torneo_ID,
		Jornada_Desc,
		Jornada_DescCorta,
		Jornada_Orden,
        Calendario_ID,
        Jornada_Type)
	VALUES
		(cast(inweekdate as Date),
		cast(inweekstart as Date),
		cast(inweekend as Date),
		inseason,
		inweekdescshort,
		inweekdesc,
		inweeksort,
        inweekcal,
        inweektype);

            
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'WEEK', CONCAT('INSERT INTO Jornada (Fecha, Fecha_Inicio, Fecha_Fin, Torneo_ID, Jornada_Desc, Jornada_DescCorta, Jornada_Orden, Jornada_Type) VALUES (cast(''' , inweekdate , ''' as Date),cast(''' , inweekstart , ''' as Date),cast(''' , inweekend , ''' as Date),' , inseason , ',''' , inweekdescshort , ''',' , inweekdesc , ',' , inweeksort , ',' , inweekcal , ',' , inweektype , ');'), status_insert);
	set out_number = count_insert;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `WeekUpdate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=CURRENT_USER PROCEDURE `WeekUpdate`(
	IN inUserName varchar(45),
	IN inweekid int(11),
	IN inseason int(11),
	IN inweekdesc int(11),
	IN inweekdescshort varchar(45),
	IN inweeksort int(11),
	IN inweekdate varchar(45),
	IN inweekstart varchar(45),
	IN inweekend varchar(45),
    IN inweektype int(11),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Jornada
	SET Fecha = cast(inweekdate as Date),
		Fecha_Inicio = cast(inweekstart as Date),
		Fecha_Fin = cast(inweekend as Date),
		Jornada_Desc = inweekdescshort,
		Jornada_DescCorta = inweekdesc,
		Jornada_Orden = inweeksort,
        Jornada_Type = inweektype
	WHERE Jornada_ID = inweekid AND Torneo_ID = inseason;

            
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'CATEGORY', CONCAT('UPDATE Jornada SET Fecha = cast(''' , inweekdate , ''' as Date), Fecha_Inicio = cast(''' , inweekstart , ''' as Date), Fecha_Fin = cast(''' , inweekend , ''' as Date), Jornada_Desc = ''' , inweekdescshort , ''', Jornada_DescCorta = ' , inweekdesc , ', Jornada_Orden = ' , inweeksort , ', Jornada_Type = ' , inweektype , ' WHERE Jornada_ID = ' , inweekid , ' AND Torneo_ID = ' , inseason , ';'), status_update);
	set out_number = count_update;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-02 21:00:09


-- Reset all AUTO_INCREMENT counters to 1 (empty tables)
ALTER TABLE `Arbitro` AUTO_INCREMENT = 1;
ALTER TABLE `Avisos` AUTO_INCREMENT = 1;
ALTER TABLE `Calendario` AUTO_INCREMENT = 1;
ALTER TABLE `Campos` AUTO_INCREMENT = 1;
ALTER TABLE `Categorias` AUTO_INCREMENT = 1;
ALTER TABLE `Colores` AUTO_INCREMENT = 1;
ALTER TABLE `Control_Table` AUTO_INCREMENT = 1;
ALTER TABLE `Convocatoria` AUTO_INCREMENT = 1;
ALTER TABLE `Equipos` AUTO_INCREMENT = 1;
ALTER TABLE `Instituciones` AUTO_INCREMENT = 1;
ALTER TABLE `Jornada` AUTO_INCREMENT = 1;
ALTER TABLE `Juego_Estatus` AUTO_INCREMENT = 1;
ALTER TABLE `Juegos` AUTO_INCREMENT = 1;
ALTER TABLE `Jugadores` AUTO_INCREMENT = 1;
ALTER TABLE `Minutas` AUTO_INCREMENT = 1;
ALTER TABLE `Range_Age` AUTO_INCREMENT = 1;
ALTER TABLE `Torneos` AUTO_INCREMENT = 1;
ALTER TABLE `registeredBrowsers` AUTO_INCREMENT = 1;
ALTER TABLE `transactions` AUTO_INCREMENT = 1;
ALTER TABLE `usuarios` AUTO_INCREMENT = 1;

DELIMITER ;;
DROP TRIGGER IF EXISTS `Jugadores_Before_Insert_Dates`;;
CREATE TRIGGER `Jugadores_Before_Insert_Dates`
BEFORE INSERT ON `Jugadores`
FOR EACH ROW
BEGIN
	IF NEW.Estatus = 'A' THEN SET NEW.Fecha_Alta = NOW(); END IF;
	IF NEW.Estatus = 'B' THEN SET NEW.Fecha_Baja = NOW(); END IF;
	IF NEW.Validado = '1' THEN SET NEW.Fecha_Validacion = NOW(); END IF;
END;;
DROP TRIGGER IF EXISTS `Jugadores_Before_Update_Dates`;;
CREATE TRIGGER `Jugadores_Before_Update_Dates`
BEFORE UPDATE ON `Jugadores`
FOR EACH ROW
BEGIN
	IF NEW.Estatus = 'A' AND (OLD.Estatus IS NULL OR OLD.Estatus <> 'A' OR OLD.Fecha_Alta IS NULL) THEN SET NEW.Fecha_Alta = NOW(); END IF;
	IF NEW.Estatus = 'B' AND (OLD.Estatus IS NULL OR OLD.Estatus <> 'B' OR OLD.Fecha_Baja IS NULL) THEN SET NEW.Fecha_Baja = NOW(); END IF;
	IF NEW.Validado = '1' AND (OLD.Validado IS NULL OR OLD.Validado <> '1' OR OLD.Fecha_Validacion IS NULL) THEN SET NEW.Fecha_Validacion = NOW(); END IF;
END;;
DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;
