CREATE DATABASE IF NOT EXISTS auction_system;
USE auction_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255),
    role ENUM('buyer','seller','moderator','admin') DEFAULT 'buyer'
);

CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT,
    title VARCHAR(255),
    description TEXT,
    starting_price DECIMAL(10,2),
    current_bid DECIMAL(10,2),
    end_datetime DATETIME,
    status VARCHAR(50) DEFAULT 'active'
);

CREATE TABLE bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT,
    buyer_id INT,
    amount DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
