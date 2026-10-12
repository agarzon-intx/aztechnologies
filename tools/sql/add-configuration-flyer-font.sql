-- Adds the flyer font family/style (FPDF style code '', 'B', 'I', 'BI') on all site schemas.
-- Site admin users lack ALTER: grant it temporarily via cPanel (uapi Mysql set_privileges_on_database) and restore after.

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_huskiesb`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `flyerFontFamily` varchar(40) NOT NULL DEFAULT 'Coluna',
  ADD COLUMN `flyerFontStyle` varchar(2) NOT NULL DEFAULT 'B';
