/* Competitions table should have the following columns (id, name, created, duration, expires, reward, cost, participants, paid_out, min_score, first_place_per, second_place_per, third_place_per, fee) */

CREATE TABLE IF NOT EXISTS `Competitions` (
	`id` INT NOT NULL AUTO_INCREMENT
	, `name` VARCHAR(100) NOT NULL
	, `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	
	, `duration` INT DEFAULT 3 /* Duration in days */
	, `expires` TIMESTAMP
	
	, `cost` INT DEFAULT 1 /* total spent to create (base price [1] + reward) */
	, `reward` INT NOT NULL
	
	, `participants` INT NOT NULL DEFAULT 0/* Number of participants */
	
	, `paid_out` BOOLEAN NOT NULL DEFAULT FALSE
	
	, `min_score` INT NOT NULL DEFAULT 0 /* Min score to qualify */
	
	, `first_place_per` FLOAT NOT NULL
	, `second_place_per` FLOAT NOT NULL
	, `third_place_per` FLOAT NOT NULL
	
	, `fee` INT NOT NULL DEFAULT 0
	, `user_id` INT
	
	, PRIMARY KEY (`id`)
	, FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`)
	);

/* Will need an association table CompetitionParticipants (id, comp_id, user_id, created): Comp_id and user_id should be a composite unique key (user can only join a competition once) */

CREATE TABLE IF NOT EXISTS `CompetitionParticipants` (
	`id` INT NOT NULL AUTO_INCREMENT
	, `comp_id` INT NOT NULL
	, `user_id` INT NOT NULL
	, `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
	, `modified`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	
	, PRIMARY KEY (`id`)
	, FOREIGN KEY (`comp_id`) REFERENCES `Competitions`(`id`)
	, FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`)
	);