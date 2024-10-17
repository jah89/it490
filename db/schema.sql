-- Create the new database and set it to be used
CREATE DATABASE nba; --db name is work in progress
USE nba;

-- Create user 'miz' and grant privileges
CREATE USER 'eait490'@'172.30.17.239' IDENTIFIED BY 'teamfantasy';
GRANT ALL PRIVILEGES ON nba.* TO 'eait490'@'172.30.17.239';

-- Create the 'users' table
CREATE TABLE users (
    user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    hashed_password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create the 'sessions' table
CREATE TABLE sessions (
    session_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    session_token VARCHAR(255) NOT NULL,
    timestamp INT NOT NULL,
    user_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (email) REFERENCES users(email)
);
