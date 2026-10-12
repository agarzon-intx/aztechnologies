-- Adds Configuration.deleteTeam on all site schemas (including aztechn1_huskiesb).
--   deleteTeam : feature switch for deleting a team (0 = off).
-- aztechn1_aztflag has no Configuration table and is not included.
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_amexvoleibol`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_binde`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_copaavispas`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_copaavispasbk`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_deportivolasminas`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_estatal2023VM`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_hbtmp`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_hectorbarraza`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskiesb`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_jubal`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_libredeportivolasminas`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_ligamayoredomex`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_ligapremier`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_ligapremier60`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_manchester`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_mapaches`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_migration`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `deleteTeam` int(11) NOT NULL DEFAULT '0';

