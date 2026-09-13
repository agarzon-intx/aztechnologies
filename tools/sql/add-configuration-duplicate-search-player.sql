-- Adds Configuration.duplicatePlayer and Configuration.searchPlayer on all site schemas.
--   duplicatePlayer : feature switch for duplicating a player (0 = off).
--   searchPlayer    : feature switch for searching a player (0 = off).
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `duplicatePlayer` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `searchPlayer` int(11) NOT NULL DEFAULT '0';
