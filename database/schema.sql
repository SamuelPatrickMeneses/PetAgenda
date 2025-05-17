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
SET foreign_key_checks = 1;
