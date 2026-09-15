-- Adds Juegos.LoadInfo (basketball game JSON) on all site schemas.
-- Safe to skip a schema if the column already exists.
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_demomina`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_elite`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_huskies`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_lidep`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_nuestrodeporte`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_vollidep`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_voleibolmetepec`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_voleymvp`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_aztflag`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_binde`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;

ALTER TABLE `aztechn1_mapaches`.`Juegos`
  ADD COLUMN `LoadInfo` longtext COLLATE utf8_unicode_ci NULL;
