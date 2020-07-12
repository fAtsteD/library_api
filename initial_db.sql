-- DB
CREATE DATABASE library;

-- User for db
CREATE USER 'api_library' @'localhost' IDENTIFIED BY '1234567890';
GRANT ALL PRIVILEGES ON library.* TO 'api_library' @'localhost';

-- Table book
CREATE TABLE book (
    id INT UNSIGNED AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    edition_id INTEGER,
    PRIMARY KEY (id),
    UNIQUE(name)
);

-- Table author
CREATE TABLE author (
    id INT UNSIGNED AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE(name)
);

-- Table book_author
CREATE TABLE book_author (
    book_id INT UNSIGNED,
    author_id INT UNSIGNED,
    PRIMARY KEY (book_id, author_id)
);

-- Table edition
CREATE TABLE edition (
    id INT UNSIGNED AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE(name)
);

-- Test data
-- Editions
INSERT INTO edition(id, name) VALUES
(1, 'Upper Saddle River'),
(2, 'Addison-Wesley, Addison Wesley longman, Inc.'),
(3, 'Farnham');
-- Authors
INSERT INTO author(id, name) VALUES
(1, 'Stuart Jonathan Russell'),
(2, 'Peter Norvig'),
(3, 'Robert Cecil Martin'),
(4, 'Martin Fowler'),
(5, 'Kent Beck'),
(6, 'Douglas Crockford');
-- Books
INSERT INTO book(id, name, edition_id) VALUES
(1, 'Artificial intelligence : a modern approach', 1),
(2, 'Clean code : a handbook of agile software craftsmanship', 1),
(3, 'Refactoring : improving the design of existing code', 2),
(4, 'The good parts : working with the shallow grain of JavaScript', 3);
-- Book author
INSERT INTO book_author(book_id, author_id) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(3, 5),
(4, 6);