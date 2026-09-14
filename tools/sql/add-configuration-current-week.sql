-- Adds Configuration.currentWeek on all site schemas.
--   currentWeek : 1 = coach games default to the current week; 0 = next week.
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `currentWeek` int(11) NOT NULL DEFAULT '0';
