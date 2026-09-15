-- Adds flyer letter colors and font sizes on all site schemas.
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_elite`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ADD COLUMN `flyerTextColor1` varchar(7) NOT NULL DEFAULT '#0098AF',
  ADD COLUMN `flyerTextColor2` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  ADD COLUMN `flyerFontWeek` int(11) NOT NULL DEFAULT '75',
  ADD COLUMN `flyerFontCategory` int(11) NOT NULL DEFAULT '60',
  ADD COLUMN `flyerFontDate` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontHour` int(11) NOT NULL DEFAULT '35',
  ADD COLUMN `flyerFontField` int(11) NOT NULL DEFAULT '35';
