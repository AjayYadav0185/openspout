SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE DATABASE IF NOT EXISTS school_db;
USE school_db;

-- =========================
-- STUDENTS TABLE
-- =========================
DROP TABLE IF EXISTS students;
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================
-- SUBJECTS TABLE
-- =========================
DROP TABLE IF EXISTS subjects;
CREATE TABLE subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================
-- MARKS TABLE
-- =========================
DROP TABLE IF EXISTS marks;
CREATE TABLE marks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  subject_id INT,
  marks INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================
-- INSERT SUBJECTS
-- =========================
INSERT INTO subjects (subject_name) VALUES
('Maths'),
('Science'),
('English'),
('Computer'),
('History');

-- =========================
-- TEMP TABLE FOR NUMBER SERIES
-- =========================
DROP TEMPORARY TABLE IF EXISTS temp_numbers;
CREATE TEMPORARY TABLE temp_numbers (num INT);

INSERT INTO temp_numbers (num)
SELECT a.N + b.N * 10 + c.N * 100 + d.N * 1000
FROM
(SELECT 0 N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a,
(SELECT 0 N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b,
(SELECT 0 N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c,
(SELECT 0 N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) d;

-- =========================
-- INSERT STUDENTS (1000)
-- =========================
INSERT INTO students (name, email, phone)
SELECT 
    CONCAT('Student ', num + 1),
    CONCAT('student', num + 1, '@mail.com'),
    CONCAT('9000', LPAD(num, 6, '0'))
FROM temp_numbers
WHERE num < 10000;

-- =========================
-- INSERT MARKS (ALL STUDENTS × ALL SUBJECTS)
-- =========================
INSERT INTO marks (student_id, subject_id, marks)
SELECT 
    s.id,
    sub.id,
    FLOOR(40 + (RAND() * 60))  -- Marks between 40–100
FROM students s
CROSS JOIN subjects sub;

COMMIT;