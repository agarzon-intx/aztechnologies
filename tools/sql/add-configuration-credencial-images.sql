-- Adds Configuration flags for whether credential front/back images are stored.
--   credencialFrontImage : 1 if pdf/Credencial.png (or legacy jpg) exists
--   credencialBackImage  : 1 if pdf/CredencialDetras.png (or legacy jpg) exists
-- Distinct from credencialBack (feature switch to print the back side).
-- Run with a database user that has ALTER privileges.
-- After ALTER, open Config → Images (or save images) so PHP syncs 0/1 from disk.

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `credencialFrontImage` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `credencialBackImage` int(11) NOT NULL DEFAULT '0';
