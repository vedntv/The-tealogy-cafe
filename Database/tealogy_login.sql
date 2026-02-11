-- SQL script to create a complete login database for Tealogy Cafe with roles and extended user profiles

CREATE DATABASE IF NOT EXISTS tealogy_login;
USE tealogy_login;

-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default roles
INSERT IGNORE INTO roles (id, role_name) VALUES 
(1, 'root'), 
(2, 'sales_staff');

-- Create users table with extended profile fields
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    age INT,
    sex VARCHAR(20),
    state VARCHAR(100),
    address VARCHAR(255),
    role_id INT DEFAULT 2,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Indexes for performance
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_role_id ON users(role_id);

-- Example: Default root admin user (password: admin123 - hashed with PASSWORD('admin123'))
-- INSERT INTO users (username, email, password, phone, age, sex, state, address, role_id) 
-- VALUES ('root', 'root@tealogy.local', '$2y$10$...hashed_password_here...', '9999999999', 30, 'Admin', 'Admin State', 'Admin Address', 1);
