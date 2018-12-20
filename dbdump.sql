SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `quiz` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `quiz`;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE `quizzes` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `quizzes` (`id`, `name`) VALUES
(1,	'Muse test'),
(2,	'Math test');

DROP TABLE IF EXISTS `quiz_answers`;
CREATE TABLE `quiz_answers` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `question_id` int(11) NOT NULL,
                              `option_id` int(11) NOT NULL,
                              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `user_id_question_id` (`user_id`,`question_id`),
                              KEY `question_id_option_id_user_id` (`question_id`,`option_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `quiz_answers` (`id`, `user_id`, `question_id`, `option_id`, `created_at`) VALUES
(51,	43,	4,	13,	'2018-12-20 14:13:25'),
(52,	43,	5,	17,	'2018-12-20 14:13:30'),
(53,	43,	6,	21,	'2018-12-20 14:13:33');

DROP TABLE IF EXISTS `quiz_options`;
CREATE TABLE `quiz_options` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `question_id` int(11) NOT NULL,
                              `order_nr` int(11) NOT NULL,
                              `option` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `correct_option` tinyint(1) NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `question_id_order_nr` (`question_id`,`order_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `quiz_options` (`id`, `question_id`, `order_nr`, `option`, `correct_option`) VALUES
(1,	1,	1,	'Intro',	1),
(2,	1,	2,	'Apocalypse Please',	0),
(3,	1,	3,	'Stockholm Syndrome',	0),
(4,	1,	4,	'Starlight',	0),
(5,	2,	1,	'New Born',	0),
(6,	2,	2,	'Bliss',	1),
(7,	3,	1,	'2000',	0),
(8,	3,	2,	'1996',	0),
(9,	3,	3,	'1994',	1),
(10,	4,	1,	'1',	0),
(11,	4,	2,	'2',	0),
(12,	4,	3,	'3',	0),
(13,	4,	4,	'4',	1),
(14,	5,	1,	'1',	0),
(15,	5,	2,	'2',	0),
(16,	5,	3,	'3',	0),
(17,	5,	4,	'6',	1),
(18,	6,	1,	'1',	0),
(19,	6,	2,	'10',	0),
(20,	6,	3,	'1000',	0),
(21,	6,	4,	'100',	1);

DROP TABLE IF EXISTS `quiz_questions`;
CREATE TABLE `quiz_questions` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `quiz_id` int(11) NOT NULL,
                                `order_nr` int(11) NOT NULL,
                                `question` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `quiz_id_order_nr` (`quiz_id`,`order_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `order_nr`, `question`) VALUES
(1,	1,	1,	'What\'s the first track of Absolution?'),
(2,	1,	2,	'What\'s the second track of Origin of Symmetry?'),
(3,	1,	3,	'What year did Muse form?'),
(4,	2,	1,	'2 + 2'),
(5,	2,	2,	'5 + 1'),
(6,	2,	3,	'10 * 10');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                       `id` int(11) NOT NULL AUTO_INCREMENT,
                       `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
                       `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                       PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `created_at`) VALUES
(43,	'Mikes',	'2018-12-20 14:13:22');
