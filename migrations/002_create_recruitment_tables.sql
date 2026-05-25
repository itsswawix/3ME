-- Migration: 002_create_recruitment_tables.sql
-- Description: Create clean recruitment tables with proper structure
-- Date: 2026-05-13

-- ============================================
-- DROP EXISTING TABLES (if you want a fresh start)
-- ============================================
-- Uncomment these lines if you want to start fresh:
-- DROP TABLE IF EXISTS job_offers;
-- DROP TABLE IF EXISTS applicants;
-- DROP TABLE IF EXISTS job_requisitions;

-- ============================================
-- CREATE TABLES
-- ============================================

-- Job Requisitions Table
CREATE TABLE IF NOT EXISTS job_requisitions (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    department VARCHAR(255),
    position_level VARCHAR(100),
    employment_type ENUM('Full-time', 'Part-time', 'Contract', 'Internship') DEFAULT 'Full-time',
    salary_min DECIMAL(15,2),
    salary_max DECIMAL(15,2),
    description TEXT,
    requirements TEXT,
    status ENUM('Open', 'Closed', 'On Hold') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_req_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Applicants Table
CREATE TABLE IF NOT EXISTS applicants (
    id VARCHAR(50) PRIMARY KEY,
    requisition_id VARCHAR(50) NULL,
    position_id VARCHAR(50) NULL COMMENT 'Foreign key to jobs table',
    position_title VARCHAR(255) NULL COMMENT 'Job title text',
    firstname VARCHAR(100) NOT NULL,
    middlename VARCHAR(100),
    surname VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    company VARCHAR(255) NULL COMMENT 'Company name as text',
    department VARCHAR(255) NULL COMMENT 'Department name as text',
    contact_number VARCHAR(50),
    resume_filename VARCHAR(255),
    application_status ENUM('Applied', 'Under Review', 'Interview Scheduled', 'Rejected', 'Hired') DEFAULT 'Applied',
    application_date DATE NOT NULL,
    interview_date DATETIME NULL,
    interview_type ENUM('Virtual', 'Phone', 'In-Person') NULL,
    interview_location TEXT NULL,
    notes TEXT,
    avatar VARCHAR(10),
    color VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_app_status (application_status),
    INDEX idx_app_date (application_date),
    INDEX idx_app_position (position_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job Offers Table
CREATE TABLE IF NOT EXISTS job_offers (
    id VARCHAR(50) PRIMARY KEY,
    applicant_id VARCHAR(50) NOT NULL,
    position VARCHAR(255) NOT NULL,
    salary_offer DECIMAL(15,2) NOT NULL,
    contract_terms TEXT,
    hire_date DATE NOT NULL,
    offer_status ENUM('Pending', 'Accepted', 'Declined', 'Expired') DEFAULT 'Pending',
    employee_id VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE,
    INDEX idx_offer_status (offer_status),
    INDEX idx_offer_date (hire_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- NOTES
-- ============================================
-- The applicants table stores:
-- - position_id: Reference to jobs.id (for linking)
-- - position_title: Job title as text (for display)
-- - company: Company name as text (for display)
-- - department: Department name as text (for display)
--
-- This hybrid approach allows flexibility while maintaining data integrity
