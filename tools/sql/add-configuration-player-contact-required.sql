-- Adds Configuration.playerContactRequired on all site schemas.
--   playerContactRequired : email and phone required on player create/edit (1 = required, default).
-- elite, huskies, huskiesbasket, lidep and vollidep start with it off.
-- Run with a database user that has ALTER privileges.

ALTER TABLE `aztechn1_demomina`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_elite`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_huskies`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_huskiesb`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_lidep`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_vollidep`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';
ALTER TABLE `aztechn1_voleymvp`.`Configuration` ADD COLUMN `playerContactRequired` int(11) NOT NULL DEFAULT '1';

UPDATE `aztechn1_elite`.`Configuration` SET `playerContactRequired` = 0 WHERE id = 0;
UPDATE `aztechn1_huskies`.`Configuration` SET `playerContactRequired` = 0 WHERE id = 0;
UPDATE `aztechn1_huskiesb`.`Configuration` SET `playerContactRequired` = 0 WHERE id = 0;
UPDATE `aztechn1_lidep`.`Configuration` SET `playerContactRequired` = 0 WHERE id = 0;
UPDATE `aztechn1_vollidep`.`Configuration` SET `playerContactRequired` = 0 WHERE id = 0;
