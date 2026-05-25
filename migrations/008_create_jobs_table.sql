-- Migration 008: Create jobs table for organizational structure
-- This table stores job positions within departments

CREATE TABLE IF NOT EXISTS jobs (
    id VARCHAR(50) PRIMARY KEY,
    department_id VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    level VARCHAR(50) NULL COMMENT 'Job level: Director, Manager, Senior, Mid-Level, Junior, Entry',
    description TEXT NULL,
    requirements TEXT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    INDEX idx_department (department_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Migration 008: Jobs table created successfully' as status;
