CREATE TABLE `users` (
	`email`	TEXT NOT NULL UNIQUE,
	`password`	TEXT,
	PRIMARY KEY(`email`)
);
CREATE TABLE `user_login_attempts` ( 
	`id` INTEGER PRIMARY KEY AUTOINCREMENT, 
	`user_email_fk` TEXT, `timestamp` TEXT NOT NULL, 
	FOREIGN KEY(`user_email_fk`) REFERENCES `users`(`email`) 
);


INSERT INTO USERS(email, password) VALUES('anders@gmail.com', "password");
INSERT INTO USERS(email, password) VALUES('cosmin@gmail.com', "admin123");
INSERT INTO USERS(email, password) VALUES('pedro@gmail.com', "book1234");