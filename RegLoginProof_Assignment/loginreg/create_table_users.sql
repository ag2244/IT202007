/* Creates a table called `Users` if none exists, adds primary key id which automatically increments by 1, unique attribute email, password and created attributes, all of which cannot be null.*/

CREATE TABLE IF NOT EXISTS `Users` (
	`id` INT NOT NULL AUTO_INCREMENT
	,`email` VARCHAR(100) NOT NULL
	,`password` VARCHAR(60) NOT NULL
	,`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
	,PRIMARY KEY (`id`)
	,UNIQUE (`email`)
	)