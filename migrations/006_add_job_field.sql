-- Migration 006: Add job field to employees table
-- This field stores the job title as text for display purposes
-- while job_id is the foreign key reference

ALTER TABLE employees 
ADD COLUMN job VARCHAR(255) NULL COMMENT 'Job title for display' 
AFTER job_id;

SELECT 'Migration 006 completed successfully' as status;
