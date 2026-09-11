-- Adds Configuration.showGenerateSchedule on all site schemas (1 = show menu / default).
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `showGenerateSchedule` int(11) NOT NULL DEFAULT '1';
