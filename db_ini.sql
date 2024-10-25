-- CREATE DATABASE api_example CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- CREATE USER 'api_user'@'localhost' identified by 'api_password';
-- GRANT ALL on api_example.* to 'api_user'@'localhost';

CREATE TABLE IF NOT EXISTS person (
    id INT NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    firstparent_id INT DEFAULT NULL,
    secondparent_id INT DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (firstparent_id)
        REFERENCES person(id)
        ON DELETE SET NULL,
    FOREIGN KEY (secondparent_id)
        REFERENCES person(id)
        ON DELETE SET NULL
) ENGINE=INNODB;

INSERT INTO person (id, firstname, lastname, firstparent_id, secondparent_id)
VALUES
    (1, 'Krasimir', 'Hristozov', NULL, NULL),
    (2, 'Maria', 'Hristozova', NULL, NULL),
    (3, 'Masha', 'Hristozova', 1, 2),
    (4, 'Jane', 'Smith', NULL, NULL),
    (5, 'John', 'Smith', NULL, NULL),
    (6, 'Richard', 'Smith', 4, 5),
    (7, 'Donna', 'Smith', 4, 5),
    (8, 'Josh', 'Harrelson', NULL, NULL),
    (9, 'Anna', 'Harrelson', 7, 8);

CREATE TABLE IF NOT EXISTS account (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL
) ENGINE=INNODB;

INSERT INTO account (id, email, username, firstname, lastname, password)
VALUES
    (1, 'mitadima@gmail.com', 'mita1234', 'Dimitrije', 'Stojanov', '$2y$10$2DAcI0ikcnfMqU51sx4vhO1G7GfyPDIPdofYU3NeEohEhUHZ5LamO'),
    (2, 'mitimaa@gmail.com', 'mitos1234', 'Dimitrije', 'Stojanov', '$2y$10$X/XOZWfOjQ.9do0Ylr8Rxuf8cYQ.j6XIGr5KCS79qkZyf65VBk27.');


-- CREATE TABLE IF NOT EXISTS account (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   username VARCHAR(50) NOT NULL,
--   email VARCHAR(100) NOT NULL,
--   password VARCHAR(255) NOT NULL
-- );

-- INSERT INTO account (username, email, password)
-- VALUES ('user1', 'user1@example.com', 'password1');