CREATE DATABASE IF NOT EXISTS vento;
USE vento;

-- Drop tables for clean reset (excluding HR to retain hr@vento-corp.com)
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS rejection_history;

-- Table for HR (The Main Admin)
CREATE TABLE IF NOT EXISTS hr (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for other Admins
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('operations_admin', 'it_admin', 'compensation_manager', 'inventory_admin') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    application_file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for Employees
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('stock_holder', 'inventory_clerk', 'it_security') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    application_file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert a default HR user for initial login access (ignores if already exists)
INSERT IGNORE INTO hr (first_name, last_name, email, password) 
VALUES ('Main', 'HR', 'hr@vento-corp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password is 'password'

-- Table for Rejection History
CREATE TABLE IF NOT EXISTS rejection_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    application_file VARCHAR(255),
    applied_at TIMESTAMP,
    rejected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admins
INSERT IGNORE INTO admins (first_name, last_name, email, password, role, status) VALUES ('Operations', 'Admin', 'operations_admin@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'operations_admin', 'approved');
INSERT IGNORE INTO admins (first_name, last_name, email, password, role, status) VALUES ('IT', 'Admin', 'it_admin@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'it_admin', 'approved');
INSERT IGNORE INTO admins (first_name, last_name, email, password, role, status) VALUES ('Compensation', 'Manager', 'compensation_manager@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'compensation_manager', 'approved');
INSERT IGNORE INTO admins (first_name, last_name, email, password, role, status) VALUES ('Inventory', 'Admin', 'inventory_admin@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'inventory_admin', 'approved');

-- Insert Default Employees
INSERT IGNORE INTO employees (first_name, last_name, email, password, role, status) VALUES ('Stock', 'Holder', 'stock_holder@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'stock_holder', 'approved');
INSERT IGNORE INTO employees (first_name, last_name, email, password, role, status) VALUES ('Inventory', 'Clerk', 'inventory_clerk@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'inventory_clerk', 'approved');
INSERT IGNORE INTO employees (first_name, last_name, email, password, role, status) VALUES ('IT', 'Security', 'it_security@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'it_security', 'approved');

-- Table for Inventory
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    quantity INT DEFAULT 0,
    status VARCHAR(20) GENERATED ALWAYS AS (CASE WHEN quantity = 0 THEN 'Out of Stock' WHEN quantity < 15 THEN 'Low' ELSE 'Good' END) STORED,
    last_verification_image VARCHAR(255) DEFAULT NULL,
    updated_by VARCHAR(100) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Default Inventory Items
INSERT IGNORE INTO inventory (name, quantity) VALUES ('SOFA', 0);
INSERT IGNORE INTO inventory (name, quantity) VALUES ('STOOL', 0);
INSERT IGNORE INTO inventory (name, quantity) VALUES ('FOLDING CHAIR', 0);
INSERT IGNORE INTO inventory (name, quantity) VALUES ('ARM CHAIR', 0);
INSERT IGNORE INTO inventory (name, quantity) VALUES ('RECLINER', 0);
