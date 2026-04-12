-- ============================================
-- Student Registration System - Database Setup
-- Run this in phpMyAdmin > SQL tab
-- ============================================

-- Step 1: Create the database
CREATE DATABASE IF NOT EXISTS student_db;

-- Step 2: Use it
USE student_db;

-- Step 3: Create the students table
CREATE TABLE IF NOT EXISTS students (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    last_name   VARCHAR(50)  NOT NULL,
    roll_no     VARCHAR(30)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    contact     VARCHAR(15)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- (Optional) Insert sample test data
INSERT INTO students (first_name, last_name, roll_no, password, contact)
VALUES 
  ('Ravi',    'Sharma',  'CS2024001', '$2y$10$examplehash1', '9876543210'),
  ('Priya',   'Patel',   'CS2024002', '$2y$10$examplehash2', '9123456780'),
  ('Aakash',  'Mehta',   'CS2024003', '$2y$10$examplehash3', '8765432109');
