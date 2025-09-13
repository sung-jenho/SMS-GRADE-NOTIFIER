-- SQL schema for SMS Grade Notification System

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NULL,
    email VARCHAR(150) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    course VARCHAR(20),
    year_level INT,
    phone_number VARCHAR(20) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL
);

CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) NOT NULL,
    subject_title VARCHAR(100) NOT NULL,
    units DECIMAL(3,2),
    schedule VARCHAR(50),
    days VARCHAR(20),
    room VARCHAR(20)
);

CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    grade VARCHAR(10),
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Track outbound SMS notifications related to grade updates
CREATE TABLE IF NOT EXISTS sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    grade_snapshot VARCHAR(10) NOT NULL,
    parent_phone VARCHAR(20) NOT NULL,
    status ENUM('Sent','Pending','Failed') NOT NULL DEFAULT 'Pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Settings table for system configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description VARCHAR(255),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- SMS message templates for different grade ranges
CREATE TABLE IF NOT EXISTS sms_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100) NOT NULL,
    grade_range VARCHAR(20) NOT NULL,
    message_template TEXT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add profile picture column to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL;

-- Insert default settings
INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES
('timezone', 'Asia/Manila', 'System timezone'),
('date_format', 'Y-m-d H:i:s', 'Default date format'),
('backup_retention_days', '30', 'Number of days to keep backup files'),
('auto_backup_enabled', '1', 'Enable automatic database backups');

-- Insert default SMS templates
INSERT IGNORE INTO sms_templates (template_name, grade_range, message_template) VALUES
('Excellent Grade', '1.0-1.5', 'Hi! Your child {student_name} received an EXCELLENT grade of {grade} in {subject}. Keep up the great work! - CTUCC'),
('Good Grade', '1.6-2.0', 'Hi! Your child {student_name} received a GOOD grade of {grade} in {subject}. Well done! - CTUCC'),
('Satisfactory Grade', '2.1-2.5', 'Hi! Your child {student_name} received a SATISFACTORY grade of {grade} in {subject}. - CTUCC'),
('Needs Improvement', '2.6-3.0', 'Hi! Your child {student_name} received a grade of {grade} in {subject}. Please encourage more study time. - CTUCC'),
('Poor Grade', '3.1-5.0', 'Hi! Your child {student_name} received a grade of {grade} in {subject}. Please contact the teacher for support. - CTUCC');
