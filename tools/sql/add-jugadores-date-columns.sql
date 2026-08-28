-- Add player validation and lifecycle dates to Jugadores on all site schemas.
-- All columns use DATETIME and allow NULL values.
-- Run with a database user that has ALTER privileges.

ALTER TABLE `aztechn1_demomina`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;

ALTER TABLE `aztechn1_elite`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;

ALTER TABLE `aztechn1_huskies`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;

ALTER TABLE `aztechn1_lidep`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;

ALTER TABLE `aztechn1_nuestrodeporte`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;

ALTER TABLE `aztechn1_vollidep`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;

ALTER TABLE `aztechn1_voleibolmetepec`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;

ALTER TABLE `aztechn1_aztflag`.`Jugadores`
  ADD COLUMN `Fecha_Validacion` DATETIME NULL,
  ADD COLUMN `Fecha_Alta` DATETIME NULL,
  ADD COLUMN `Fecha_Baja` DATETIME NULL;
