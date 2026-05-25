-- Migration: 003_add_id_columns_to_applicants.sql
-- Description: Add company_id, department_id, job_id columns to applicants table
-- Date: 2026-05-15

-- Add ID columns to applicants table
ALTER TABLE applicants 
ADD COLUMN company_id INT NULL COMMENT 'Foreign key to companies table' AFTER company,
ADD COLUMN department_id INT NULL COMMENT 'Foreign key to departments table' AFTER department,
ADD COLUMN job_id INT NULL COMMENT 'Foreign key to jobs table' AFTER position_title;

-- Add indexes for better performance
ALTER TABLE applicants
ADD INDEX idx_company_id (company_id),
ADD INDEX idx_department_id (department_id),
ADD INDEX idx_job_id (job_id);

-- Note: We keep the text fields (company, department, position_title) for backward compatibility
-- and as fallback values. The ID columns are the primary reference for data integrity.
