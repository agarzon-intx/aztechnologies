-- Add Configuration.credencialBack (INT, default 0 = disabled).
-- Idempotent across schemas.

SET @db := DATABASE();

SET @exists := (
	SELECT COUNT(*) FROM information_schema.COLUMNS
	WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'Configuration' AND COLUMN_NAME = 'credencialBack'
);

SET @sql := IF(
	@exists = 0,
	'ALTER TABLE `Configuration` ADD COLUMN `credencialBack` INT NOT NULL DEFAULT 0 AFTER `playerSignature`',
	'SELECT ''credencialBack already exists'' AS info'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
