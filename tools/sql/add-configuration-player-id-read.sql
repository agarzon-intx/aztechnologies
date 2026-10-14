-- Adds Configuration.playerIDRead on the site schemas.
--   playerIDRead : shows the "Read info from ID" button on the player forms (0 = off / default).
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskiesb`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `playerIDRead` int(11) NOT NULL DEFAULT '0';
