-- Migration: 007_add_suffix_to_personal_details.sql
-- Description: Add suffix field to all tables with personal details
-- Date: 2026-05-15

-- ============================================
-- ADD SUFFIX COLUMN TO TABLES
-- ============================================

-- Add suffix to applicants table
ALTER TABLE applicants 
ADD COLUMN suffix VARCHAR(20) NULL COMMENT 'Name suffix (Jr., Sr., III, etc.)' 
AFTER surname;

-- Add suffix to employees table (if exists)
ALTER TABLE employees 
ADD COLUMN suffix VARCHAR(20) NULL COMMENT 'Name suffix (Jr., Sr., III, etc.)' 
AFTER surname;

-- Add suffix to onboarding_records table (if exists)
ALTER TABLE onboarding_records 
ADD COLUMN suffix VARCHAR(20) NULL COMMENT 'Name suffix (Jr., Sr., III, etc.)' 
AFTER employee_name;

-- Add suffix to users table (if exists)
ALTER TABLE users 
ADD COLUMN suffix VARCHAR(20) NULL COMMENT 'Name suffix (Jr., Sr., III, etc.)' 
AFTER surname;

-- ============================================
-- NOTES
-- ============================================
-- Common suffix values:
-- - Jr. (Junior)
-- - Sr. (Senior)
-- - II, III, IV (Second, Third, Fourth)
-- - Esq. (Esquire)
-- - PhD, MD, DDS (Academic/Professional titles)
-- 
-- The suffix field is optional and can be left NULL
