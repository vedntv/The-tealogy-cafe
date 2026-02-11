-- Add extended profile fields to users table
ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER password;
ALTER TABLE users ADD COLUMN age INT AFTER phone;
ALTER TABLE users ADD COLUMN sex VARCHAR(20) AFTER age;
ALTER TABLE users ADD COLUMN state VARCHAR(100) AFTER sex;
ALTER TABLE users ADD COLUMN address VARCHAR(255) AFTER state;
