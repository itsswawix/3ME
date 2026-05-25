-- Migration 010: Add file storage columns to import_history
-- Stores the path and size of uploaded attendance files

ALTER TABLE import_history 
    ADD COLUMN file_path VARCHAR(500) DEFAULT NULL COMMENT 'Server path to uploaded file',
    ADD COLUMN file_size BIGINT DEFAULT 0 COMMENT 'File size in bytes',
    ADD COLUMN original_name VARCHAR(255) DEFAULT NULL COMMENT 'Original uploaded file name';

SELECT 'Migration 010 completed successfully - File storage columns added to import_history' as status;
