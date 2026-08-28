-- Add institution/team color fields to Equipos on all site schemas.
-- Both columns use VARCHAR(45).
-- Run once with a database user that has ALTER privileges.

ALTER TABLE `aztechn1_demomina`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;

ALTER TABLE `aztechn1_elite`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;

ALTER TABLE `aztechn1_huskies`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;

ALTER TABLE `aztechn1_lidep`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;

ALTER TABLE `aztechn1_nuestrodeporte`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;

ALTER TABLE `aztechn1_vollidep`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;

ALTER TABLE `aztechn1_voleibolmetepec`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;

ALTER TABLE `aztechn1_aztflag`.`Equipos`
  ADD COLUMN `Nombre_Color` VARCHAR(45) NULL,
  ADD COLUMN `Credencial_Color` VARCHAR(45) NULL;
