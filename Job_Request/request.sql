-- Create the database
CREATE DATABASE IF NOT EXISTS fablab_db;
USE fablab_db;

-- Job Requests table (from the HTML form)
CREATE TABLE IF NOT EXISTS job_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_title VARCHAR(100) NOT NULL,
    request_date DATE NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    client_profile VARCHAR(50) NOT NULL,
    client_profile_other VARCHAR(50),
    request_description TEXT NOT NULL,
    equipment TEXT,
    priority ENUM('Low', 'Medium', 'High') NOT NULL,
    completion_date DATE NOT NULL,
    reference_file VARCHAR(255),
    status ENUM('Pending', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Equipment table (for tracking equipment)
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    status ENUM('Available', 'In Maintenance', 'Out of Service') DEFAULT 'Available',
    last_maintenance_date DATE,
    next_maintenance_date DATE,
    notes TEXT
);

-- Sample equipment data
INSERT INTO equipment (name, type, status) VALUES
('3D Printer', 'Manufacturing', 'Available'),
('3D Scanner', 'Scanning', 'Available'),
('Laser Cutting Machine', 'Cutting', 'Available'),
('CNC Machine (Big)', 'Milling', 'Available'),
('Embroidery Machine (One Head)', 'Textile', 'Available');