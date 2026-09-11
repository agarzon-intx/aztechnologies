-- Change showGenerateSchedule default from 1 to 0 (hide Generate Schedule menu by default).
-- Use this if the column was already created with DEFAULT 1.
-- Run with a database user that has ALTER privileges (cPanel account owner).

ALTER TABLE `aztechn1_demomina`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_elite`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_huskies`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_lidep`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_nuestrodeporte`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_vollidep`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_voleibolmetepec`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_voleymvp`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

ALTER TABLE `aztechn1_aztflag`.`Configuration`
  ALTER COLUMN `showGenerateSchedule` SET DEFAULT 0;

-- Optional: force existing rows to 0 (uncomment if you want all sites hidden until enabled):
-- UPDATE `aztechn1_demomina`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_elite`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_huskies`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_lidep`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_nuestrodeporte`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_vollidep`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_voleibolmetepec`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_voleymvp`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
-- UPDATE `aztechn1_aztflag`.`Configuration` SET showGenerateSchedule = 0 WHERE id = 0;
