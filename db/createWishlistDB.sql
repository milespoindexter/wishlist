########################################################################

# to execute:  mysql -p -u root --local-infile<createWishlistDB.sql

########################################################################

CREATE DATABASE IF NOT EXISTS wishlist;
USE wishlist;

DROP TABLE IF EXISTS gifts;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS owners;


CREATE TABLE gifts ( 
	rank INT NOT NULL,
	title VARCHAR(128) NOT NULL, 
	description VARCHAR(256), 
	link VARCHAR(200), 
	ownerID	INT
	);


CREATE TABLE groups ( 
	PRIMARY KEY(groupID), 
	name VARCHAR(120), 
	groupOrder INT, 
	groupID INT NOT NULL AUTO_INCREMENT 
	);
	
CREATE TABLE owners ( 
	PRIMARY KEY(ownerID), 
	firstname VARCHAR(120), 
	lastname VARCHAR(120), 
	groupID INT,
	ownerID INT NOT NULL AUTO_INCREMENT 
	);

INSERT INTO owners (firstname,lastname) VALUES ("Miles","Poindexter");
INSERT INTO owners (firstname,lastname) VALUES ("Test","Person");

