-- Track player status and validation dates on all site schemas.
-- Fecha_Alta is set when Estatus becomes A.
-- Fecha_Baja is set when Estatus becomes B.
-- Fecha_Validacion is set when Validado becomes 1.
-- Safe to re-run: triggers are dropped and recreated.

DELIMITER ;;

USE `aztechn1_demomina`;;
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

USE `aztechn1_elite`;;
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

USE `aztechn1_huskies`;;
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

USE `aztechn1_lidep`;;
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

USE `aztechn1_nuestrodeporte`;;
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

USE `aztechn1_vollidep`;;
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

USE `aztechn1_voleibolmetepec`;;
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

USE `aztechn1_aztflag`;;
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
