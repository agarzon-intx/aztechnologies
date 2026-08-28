-- Adds Configuration.playerSignature on all site schemas.
--   playerSignature : feature switch for the player signature (0 = off).
-- Run with a database user that has ALTER privileges.

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `playerSignature` int(11) NOT NULL DEFAULT '0';
