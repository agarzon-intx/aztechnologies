-- Adds the player ID PDF support columns on all site schemas.
--   Configuration.playerIDPDF : feature switch for the player ID PDF (0 = off).
--   Jugadores.IdentificacionPDF : stored PDF document for the player ID.
-- Run with a database user that has ALTER privileges.

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_demomina`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_elite`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_huskies`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_lidep`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_nuestrodeporte`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_vollidep`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_voleibolmetepec`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `playerIDPDF` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `aztechn1_aztflag`.`Jugadores`
  ADD COLUMN `IdentificacionPDF` longblob DEFAULT NULL;
