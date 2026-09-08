-- Adds Configuration.credencialBack on all site schemas (0 = off / default).
-- Run with a database user that has ALTER privileges.

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `credencialBack` int(11) NOT NULL DEFAULT '0';
