-- Migration 012: Create performance/disciplinary tables
-- Creates disciplinary_records table referencing employees

CREATE TABLE IF NOT EXISTS disciplinary_records (
    id VARCHAR(50) PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    offense_type VARCHAR(100) NOT NULL,
    severity ENUM('Minor', 'Moderate', 'Major', 'Critical') NOT NULL DEFAULT 'Minor',
    severity_score INT NOT NULL DEFAULT 1,
    date DATE NOT NULL,
    status ENUM('Pending Review', 'Under Investigation', 'Action Taken', 'Closed') NOT NULL DEFAULT 'Pending Review',
    reported_by VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    action_taken TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_employee (employee_id),
    INDEX idx_status (status),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
