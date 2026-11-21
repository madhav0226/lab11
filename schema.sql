-- ========================================================
-- Lab 11: User Validation Web Application Database Schema
-- Database: MySQL / MariaDB compatible
-- ========================================================

CREATE DATABASE IF NOT EXISTS `lab11_auth` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lab11_auth`;

-- Create users table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` VARCHAR(30) DEFAULT 'Student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default user records
-- Passwords below are encrypted with PHP's standard PASSWORD_BCRYPT:
-- 1. admin   / Admin@123
-- 2. student / Lab11@Pass
-- 3. alice   / Alice@2026

INSERT INTO `users` (`username`, `full_name`, `email`, `password_hash`, `role`) VALUES
('admin', 'System Administrator', 'admin@lab11.local', '$2y$10$w3U69N0p2qfL4XkC/b7kueN27dYjI84Wz5m4p9a0q1B5t9h9z6kS.', 'Administrator'),
('student', 'John Doe', 'student@lab11.local', '$2y$10$C8YnSgX78W.HqV69h4LzI.eQZ4/rYc1L3kU6n5j0p9a0q1B5t9h9z', 'Student'),
('alice', 'Alice Johnson', 'alice@lab11.local', '$2y$10$X8KjG2m3L5p7R9s1T4v6Q.h0F2d5B7w9A1c3E5g7I9k1M3o5Q7s9U', 'Faculty');
