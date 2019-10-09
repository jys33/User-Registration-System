CREATE DATABASE bwp DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE bwp;

DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS forgot_password;

CREATE TABLE user (
	user_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	last_name VARCHAR(50) NOT NULL,
	first_name VARCHAR(50) NOT NULL,
	user_email VARCHAR(100) NOT NULL,
	password VARCHAR(255) NOT NULL,
	activation CHAR(32) NOT NULL,
	created_on DATETIME NOT NULL,
	last_modified_on DATETIME NOT NULL,
    deleted TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY user_email_unique (user_email)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE forgot_password (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id INT(11) UNSIGNED NOT NULL,
	reset_key CHAR(32) NOT NULL,
	time INT(11) NOT NULL,
	status VARCHAR(7) NOT NULL,
	created_on DATETIME NOT NULL, 
	last_modified_on DATETIME NOT NULL
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;