-- Create database
CREATE DATABASE IF NOT EXISTS user_management_system;
USE user_management_system;

-- Create user table
CREATE TABLE IF NOT EXISTS user_master (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    user_status ENUM('active', 'inactive') DEFAULT 'active',
    user_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_last_login TIMESTAMP NULL,
    user_deleted BOOLEAN DEFAULT false
) ENGINE=InnoDB;

-- Add index for faster searches
CREATE INDEX idx_user_email ON user_master(user_email);
ALTER TABLE user_master ADD COLUMN user_last_login DATETIME NULL DEFAULT NULL;


ALTER TABLE users
ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN reset_token_expires DATETIME DEFAULT NULL;
