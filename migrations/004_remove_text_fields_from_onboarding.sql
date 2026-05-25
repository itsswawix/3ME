-- Migration 004: Remove text fields from onboarding_records table
-- These fields are replaced by foreign key IDs (job_id, company_id, department_id)

-- Check if columns exist before dropping them
SET @dbname = DATABASE();
SET @tablename = 'onboarding_records';

-- Drop job column if exists
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname
   AND TABLE_NAME = @tablename
   AND COLUMN_NAME = 'job') > 0,
  'ALTER TABLE onboarding_records DROP COLUMN job;',
  'SELECT ''Column job does not exist, skipping'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- Drop position column if exists
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname
   AND TABLE_NAME = @tablename
   AND COLUMN_NAME = 'position') > 0,
  'ALTER TABLE onboarding_records DROP COLUMN position;',
  'SELECT ''Column position does not exist, skipping'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- Drop department column if exists
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname
   AND TABLE_NAME = @tablename
   AND COLUMN_NAME = 'department') > 0,
  'ALTER TABLE onboarding_records DROP COLUMN department;',
  'SELECT ''Column department does not exist, skipping'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- Drop company column if exists
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname
   AND TABLE_NAME = @tablename
   AND COLUMN_NAME = 'company') > 0,
  'ALTER TABLE onboarding_records DROP COLUMN company;',
  'SELECT ''Column company does not exist, skipping'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- Verify the changes
SELECT 
    'Migration 004 Complete' as status,
    'Removed text fields: job, position, department, company' as changes,
    'Now using only: job_id, company_id, department_id' as note;

-- Show remaining columns
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'onboarding_records'
ORDER BY ORDINAL_POSITION;
