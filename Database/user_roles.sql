-- User roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default roles
INSERT INTO roles (role_name) VALUES ('root'), ('sales_staff');

-- Add role_id to users table (assuming users table exists)
ALTER TABLE users ADD COLUMN role_id INT DEFAULT 2;
ALTER TABLE users ADD CONSTRAINT fk_role FOREIGN KEY (role_id) REFERENCES roles(id);

-- Example: create users table if not exists
-- CREATE TABLE IF NOT EXISTS users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(100) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,
--     email VARCHAR(100) NOT NULL UNIQUE,
--     role_id INT DEFAULT 2,
--     FOREIGN KEY (role_id) REFERENCES roles(id)
-- );
