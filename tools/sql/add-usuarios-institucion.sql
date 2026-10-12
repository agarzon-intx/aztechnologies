-- Adds usuarios.Institucion_ID on all site schemas (applied 2026-10-08 to all 25 schemas).
--   Institucion_ID : institution of the user (same type as Equipos.Institucion_ID), NULL = none.
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_amexvoleibol`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_binde`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_copaavispas`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_copaavispasbk`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_demomina`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_deportivolasminas`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_elite`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_estatal2023VM`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_hbtmp`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_hectorbarraza`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_huskies`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_huskiesb`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_jubal`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_libredeportivolasminas`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_lidep`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_ligamayoredomex`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_ligapremier`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_ligapremier60`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_manchester`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_mapaches`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_migration`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_nuestrodeporte`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_voleibolmetepec`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_voleymvp`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;

ALTER TABLE `aztechn1_vollidep`.`usuarios`
  ADD COLUMN `Institucion_ID` bigint(20) NULL DEFAULT NULL;
