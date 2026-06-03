-- =============================================================================
-- Create schema aztechn1_lidepread (same structure as all league sites)
-- Source template: aztechn1_lidep on www.aztechnologies.tech
-- Generated: 2026-06-02
--
-- Includes: 39 tables, 3 functions, 41 stored procedures (structure only, no rows)
-- All AUTO_INCREMENT counters start at 1 (CREATE + ALTER TABLE at end of script)
--
-- cPanel / Bluehost notes:
--   1. Create database "aztechn1_lidepread" in cPanel if CREATE DATABASE fails.
--   2. User aztechn1_admin already exists on this host — assign it to the new DB
--      in cPanel (All Privileges), or run the GRANT block below if you have rights.
--   3. Import this file in phpMyAdmin or MySQL Workbench (may take ~1 minute).
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- Allow creating functions without SUPER (shared hosting; ignore error if denied)
SET GLOBAL log_bin_trust_function_creators = 1;

CREATE DATABASE IF NOT EXISTS `aztechn1_lidepread`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- User already exists on production; (re)grant access to the new schema.
-- If CREATE USER fails, create/link the user in cPanel → MySQL Databases instead.
CREATE USER IF NOT EXISTS 'aztechn1_admin'@'localhost' IDENTIFIED BY 'RTd*jaey)Y@5';
GRANT ALL PRIVILEGES ON `aztechn1_lidepread`.* TO 'aztechn1_admin'@'localhost';
FLUSH PRIVILEGES;

USE `aztechn1_lidepread`;
