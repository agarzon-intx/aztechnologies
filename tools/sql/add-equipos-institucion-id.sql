-- Add Institucion_ID to Equipos (Teams) on all site schemas.
-- Type: bigint(20) NOT NULL DEFAULT 0
-- Safe to run once per schema; re-running fails if the column already exists.

ALTER TABLE `aztechn1_demomina`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;

ALTER TABLE `aztechn1_elite`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;

ALTER TABLE `aztechn1_huskies`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;

ALTER TABLE `aztechn1_lidep`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;

ALTER TABLE `aztechn1_nuestrodeporte`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;

ALTER TABLE `aztechn1_vollidep`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;

ALTER TABLE `aztechn1_voleibolmetepec`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;

ALTER TABLE `aztechn1_aztflag`.`Equipos`
  ADD COLUMN `Institucion_ID` bigint(20) NOT NULL DEFAULT 0;
