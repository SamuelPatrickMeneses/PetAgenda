SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `phone` varchar(11) UNIQUE NOT NULL,
  `encrypted_password` varchar(255) NOT NULL COMMENT 'BCrypt',
  `is_active` boolean DEFAULT true,
  `last_login` timestamp,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

DROP TABLE IF EXISTS `user_rules`;

CREATE TABLE `user_rules` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `rule_type` varchar(12) NOT NULL
);

DROP TABLE IF EXISTS `account_rules`;

CREATE TABLE `account_rules` (
  `user_id` int NOT NULL,
  `rule_id` int NOT NULL,
  PRIMARY KEY (user_id, rule_id),
  FOREIGN KEY (user_id) REFERENCES users (id),
  FOREIGN KEY (rule_id) REFERENCES user_rules (id)
);

DROP TABLE IF EXISTS `pets`;

CREATE TABLE `pets` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'Tutor',
  `name` varchar(50) NOT NULL,
  `specie` enum('cachorro','gato','ave','roedor','reptil','outro') NOT NULL,
  `breed` varchar(50) COMMENT 'Raça (opcional)',
  `description` text COMMENT 'Detalhes físicos/comportamentais',
  `weight` decimal(5,2) COMMENT 'Em kg',
  `birth_date` date,
  `photo_url` varchar(255),
  `active` boolean DEFAULT 1,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now()),
  FOREIGN KEY (user_id) REFERENCES users (id)
);


DROP TABLE IF EXISTS `employee_shifts`;

CREATE TABLE `employee_shifts` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `auth_id` int COMMENT 'FK com validação de role_type=''staff''',
  `day_of_week` enum('seg','ter','qua','qui','sex','sab','dom') NOT NULL, 
  `start_time` time NOT NULL COMMENT 'Formato HH:MM',
  `end_time` time NOT NULL,
  `is_recurring` boolean DEFAULT true COMMENT 'Se aplica semanalmente'
);

DROP TABLE IF EXISTS `employee_absences`;

CREATE TABLE `employee_absences` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `auth_id` int,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL COMMENT 'Inclusivo',
  `reason` enum('ferias','licenca','treinamento','outro') NOT NULL,
  `description` text
);
SET foreign_key_checks = 1;
