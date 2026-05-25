-- Migration 011: Create leave tables
-- Creates tables for leave types, leave requests, and leave balances

-- Create leave_types table
CREATE TABLE IF NOT EXISTS leave_types (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    credits DECIMAL(5,2) NOT NULL DEFAULT 15.00,
    max_duration INT NOT NULL DEFAULT 30,
    eligibility_rule TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create leave_requests table
CREATE TABLE IF NOT EXISTS leave_requests (
    id VARCHAR(50) PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    leave_type_id VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration INT NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected', 'Cancelled') DEFAULT 'Pending',
    approved_by VARCHAR(100) NULL,
    approved_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    INDEX idx_employee (employee_id),
    INDEX idx_leave_type (leave_type_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create leave_balances table
CREATE TABLE IF NOT EXISTS leave_balances (
    id VARCHAR(50) PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    leave_type_id VARCHAR(50) NOT NULL,
    accrued DECIMAL(5,2) NOT NULL DEFAULT 15.00,
    used DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    balance DECIMAL(5,2) NOT NULL DEFAULT 15.00,
    last_accrual_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_emp_leave_type (employee_id, leave_type_id),
    INDEX idx_emp_bal (employee_id),
    INDEX idx_type_bal (leave_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default leave types
INSERT INTO leave_types (id, name, credits, max_duration, eligibility_rule) VALUES 
('LVT-VACATION', 'Vacation Leave', 15.00, 30, 'All regular employees are eligible after completing 3 months of probationary period.'),
('LVT-SICK', 'Sick Leave', 15.00, 30, 'All regular employees are eligible immediately upon hire.'),
('LVT-EMERGENCY', 'Emergency Leave', 5.00, 5, 'Available for urgent family/personal emergencies.'),
('LVT-MATERNITY', 'Maternity Leave', 105.00, 105, 'All female employees regardless of employment status.'),
('LVT-PATERNITY', 'Paternity Leave', 7.00, 7, 'All married male employees in active cohabitation with spouse.'),
('LVT-BEREAVEMENT', 'Bereavement Leave', 5.00, 5, 'Available upon the death of an immediate family member.')
ON DUPLICATE KEY UPDATE 
    credits = VALUES(credits),
    max_duration = VALUES(max_duration),
    eligibility_rule = VALUES(eligibility_rule);

SELECT 'Migration 011 completed successfully - Leave tables created and seeded' as status;
