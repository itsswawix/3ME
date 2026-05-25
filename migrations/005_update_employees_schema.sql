-- Migration 005: Update employees table schema
-- Changes:
-- 1. Remove employee_number column (use id as employee_number)
-- 2. Ensure position_id is renamed to job_id (if exists)
-- 3. Remove job_title references (already using job_id foreign key)

-- Step 1: Check if employee_number column exists and drop it
SET @dbname = DATABASE();
SET @tablename = 'employees';
SET @columnname = 'employee_number';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'ALTER TABLE employees DROP COLUMN employee_number;',
  'SELECT 1;'
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Step 2: Check if position_id exists and rename to job_id
SET @columnname = 'position_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'ALTER TABLE employees CHANGE COLUMN position_id job_id VARCHAR(50) NULL;',
  'SELECT 1;'
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Note: job_title was never a column in employees table, it comes from JOIN with jobs table
-- No action needed for job_title removal

-- Verify the changes
SELECT 'Migration 005 completed successfully' as status;
