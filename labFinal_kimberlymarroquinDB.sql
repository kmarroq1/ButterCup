DROP DATABASE IF EXISTS buttercup;
CREATE DATABASE butterCup;



CREATE TABLE customers (
customerID	INT	NOT NULL	AUTO_INCREMENT,
firstName	VARCHAR(60)	NOT NULL,
lastName	VARCHAR(60)	NOT NULL,
phone	VARCHAR(60),
email	VARCHAR(60),
username	VARCHAR(60)	NOT NULL	UNIQUE,
password	VARCHAR(60)	NOT NULL,

PRIMARY KEY (customerID)
);

INSERT INTO customers (firstName, lastName, phone, email, username, password)
VALUES ('David', 'Wilkinson', '123-456-7890', 'something@gmail.com', 'dwilk1', '$2y$10$7zg261PCqRFVNXvKLpMh1ORamWQPiMJCH10ejmrfp3wqEunnBIHsC'),
('Kimberly', 'Marroquin', NULL, 'something@gmail.com', 'kmarroq1', '$2y$10$7zg261PCqRFVNXvKLpMh1ORamWQPiMJCH10ejmrfp3wqEunnBIHsC'),
('Xeniah', 'Sillie', '123-456-7890', NULL, 'xsilie1', '$2y$10$7zg261PCqRFVNXvKLpMh1ORamWQPiMJCH10ejmrfp3wqEunnBIHsC');



CREATE TABLE products (
cupID INT NOT NULL	AUTO_INCREMENT,
cup_option	VARCHAR(60)	NOT NULL,

PRIMARY KEY (cupID)
);

INSERT INTO products (cup_option)
VALUES ('cup1'),
('cup2'),
('cup3');



CREATE TABLE order_history (
orderID	INT	NOT NULL	AUTO_INCREMENT,
customerID	INT	NOT NULL	REFERENCES customers (customerID),
cupID	INT	NOT NULL	REFERENCES products (cupID),
orderedDate	VARCHAR(60)	NOT NULL,

PRIMARY KEY (orderID, customerID, cupID)
);

INSERT INTO order_history (customerID, cupID, orderedDate)
VALUES ('3', '2', '11/11/2011'),
('2', '1', '11/21/2021'),
('1', '3', '3/31/2009');



GRANT SELECT, INSERT, UPDATE, DELETE
ON butterCup.*
TO mgs_user IDENTIFIED BY 'pa55word';