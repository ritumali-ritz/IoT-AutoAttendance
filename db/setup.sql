CREATE DATABASE IF NOT EXISTS attendance_db;
USE attendance_db;

CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS students (
  roll_no VARCHAR(50) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  class_id INT,
   department VARCHAR(100),
    card_uid VARCHAR(100) UNIQUE,
  mobile VARCHAR(20) DEFAULT NULL,
  FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  roll_no VARCHAR(50),
  date DATE,
  time TIME,
  status ENUM('Present','Absent') DEFAULT 'Present',
  FOREIGN KEY (roll_no) REFERENCES students(roll_no) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS scans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rfid_uid VARCHAR(32) NOT NULL,
  received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  processed TINYINT(1) DEFAULT 0
);

-- sample data
INSERT INTO classes (class_name) VALUES ('BCA-1'),('BCA-2'),('BTech-1');
INSERT INTO students (roll_no,name,class_id,mobile) VALUES
  ('BCA1001','Rahul Sharma',1,NULL),
  ('BCA1002','Priya Deshmukh',1,NULL),
  ('BCA2001','Amit Kumar',2,NULL);
