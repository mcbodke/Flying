-- schema.sql
CREATE DATABASE IF NOT EXISTS my_flying CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_flying;

-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(20),
  city VARCHAR(50),
  state VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins (separate)
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Flights
CREATE TABLE flights (
  id INT AUTO_INCREMENT PRIMARY KEY,
  airline VARCHAR(100) NOT NULL,
  flight_no VARCHAR(50) NOT NULL,
  from_city VARCHAR(100) NOT NULL,
  to_city VARCHAR(100) NOT NULL,
  depart DATETIME NOT NULL,
  arrive DATETIME NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  seats_total INT NOT NULL DEFAULT 100,
  seats_available INT NOT NULL DEFAULT 100,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings
CREATE TABLE book_flight (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  flight_id INT NOT NULL,
  passengers INT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  booking_status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (flight_id) REFERENCES flights(id) ON DELETE CASCADE
);

-- Payments
CREATE TABLE payment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  user_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  payment_method VARCHAR(50),
  payment_status ENUM('pending','success','failed') DEFAULT 'pending',
  transaction_id VARCHAR(100),
  paid_at TIMESTAMP NULL,
  FOREIGN KEY (booking_id) REFERENCES book_flight(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- sample admin
INSERT INTO admins (username, password_hash, full_name)
VALUES ('admin', '$2y$10$z3Yw7nGg0eKXQnJYQ0jGzeZB5SK3YyU3vQx0t6J2y4xYQwD5cQ0a6', 'Site Admin');
-- password for above: Demo@123  (bcrypt hash produced by password_hash('Demo@123', PASSWORD_DEFAULT))

-- sample flights
INSERT INTO flights (airline, flight_no, from_city, to_city, depart, arrive, price, seats_total, seats_available)
VALUES
('AirBlue','AB101','Mumbai','Delhi','2025-10-10 08:00:00','2025-10-10 10:00:00', 4500, 150, 150),
('IndiGo','IG205','Delhi','Bengaluru','2025-10-11 06:30:00','2025-10-11 09:30:00', 5200, 150, 150);
