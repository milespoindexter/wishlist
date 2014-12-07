########################################################################

# to execute:  mysql -p -u root --local-infile<createGiftsDB.sql

# columns: SEQUENCE	TITLE	DESCRIPTION	LINK	FIRSTNAME	LASTNAME

########################################################################

CREATE DATABASE IF NOT EXISTS selfprop_spc;
USE selfprop_spc;

DROP TABLE IF EXISTS gifts;
DROP TABLE IF EXISTS owners;


CREATE TABLE gifts ( 
	rank INT NOT NULL,
	title VARCHAR(128) NOT NULL, 
	description VARCHAR(256), 
	link VARCHAR(200), 
	ownerID	INT
	);
	
	
CREATE TABLE owners ( 
	PRIMARY KEY(ownerID), 
	firstname VARCHAR(120), 
	lastname VARCHAR(120), 
	ownerID INT NOT NULL AUTO_INCREMENT 
	);

INSERT INTO owners (firstname,lastname) VALUES ("Miles","Poindexter");
INSERT INTO owners (firstname,lastname) VALUES ("Lycel","Villanueva");
INSERT INTO owners (firstname,lastname) VALUES ("Grace","Kulthongkham");
