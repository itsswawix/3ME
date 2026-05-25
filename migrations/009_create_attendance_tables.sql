-- Migration 009: Create attendance tables
-- Creates tables for rosters, corrections, and import history

-- Create rosters table
CREATE TABLE IF NOT EXISTS rosters (
    id VARCHAR(50) PRIMARY KEY,
    shift_name VARCHAR(100) NOT NULL,
    company_id VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    break_duration INT DEFAULT 0 COMMENT 'Break duration in minutes',
    overtime_rule VARCHAR(255) NOT NULL,
    late_grace_period INT DEFAULT 0 COMMENT 'Late grace period in minutes',
    effective_date DATE NOT NULL,
    notes TEXT,
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_effective_date (effective_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create corrections table
CREATE TABLE IF NOT EXISTS corrections (
    id VARCHAR(50) PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    type ENUM('Late', 'Early Departure', 'Missed Entry', 'Overtime Discrepancy') NOT NULL,
    original_date DATE NOT NULL,
    time_in TIME,
    time_out TIME,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    requested_by VARCHAR(100),
    requested_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_by VARCHAR(100),
    approved_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_employee (employee_id),
    INDEX idx_status (status),
    INDEX idx_original_date (original_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create import_history table
CREATE TABLE IF NOT EXISTS import_history (
    id VARCHAR(50) PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    import_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    imported_by VARCHAR(100) NOT NULL,
    total_records INT DEFAULT 0,
    successful INT DEFAULT 0,
    failed INT DEFAULT 0,
    status ENUM('Success', 'Partial', 'Failed') DEFAULT 'Success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_import_date (import_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create import_data table (stores the actual imported attendance data)
CREATE TABLE IF NOT EXISTS import_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    import_id VARCHAR(50) NOT NULL,
    employee_id VARCHAR(50),
    employee_name VARCHAR(255),
    date DATE,
    time_in TIME,
    time_out TIME,
    total_hours DECIMAL(5,2),
    status VARCHAR(50),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_id) REFERENCES import_history(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_import (import_id),
    INDEX idx_employee (employee_id),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Migration 009 completed successfully - Attendance tables created' as status;
