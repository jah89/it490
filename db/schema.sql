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

CREATE TABLE chat_messages (
    message_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE players (
    player_id INT NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    team_id INT,
    season YEAR,
    country VARCHAR(100)
);

CREATE TABLE player_stats (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    season YEAR,
    game_date DATE NOT NULL,
    points INT,
    rebounds INT,
    assists INT,
    blocks INT,
    steals INT,
    FOREIGN KEY (player_id) REFERENCES players(player_id)
);

CREATE TABLE teams (
    team_id INT NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    conference VARCHAR(50),
    division VARCHAR(50)
);

CREATE TABLE games (
    game_id INT NOT NULL PRIMARY KEY,
    home_team_id INT NOT NULL,
    visitor_team_id INT NOT NULL,
    home_team_points INT NOT NULL,
    visitor_team_points INT NOT NULL,
    game_date DATETIME NOT NULL,
    FOREIGN KEY (home_team_id) REFERENCES teams(team_id),
    FOREIGN KEY (visitor_team_id) REFERENCES teams(team_id)
);



-- TABLES NEEDED:
-- LEAGUES
-- PLAYERS
-- PLAYER DATA
-- GAMES
-- LEAGUE MEMBERS
