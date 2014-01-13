
CREATE DATABASE phoenixWorld;
use phoenixWorld;
CREATE TABLE `users` (
  `id`         int(10)  unsigned NOT NULL AUTO_INCREMENT,
  `user_id`    int(10)  unsigned DEFAULT 0,
  `user_name`  varchar(50) DEFAULT NULL,
  `user_avatar`  varchar(50) DEFAULT NULL,       
  `room_id`    char(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`user_name`) 
) ENGINE=Memory DEFAULT CHARSET=utf8 COMMENT='Users in Virtual World table';

CREATE TABLE `activity` (
  `id`          int(10)  unsigned NOT NULL AUTO_INCREMENT,
  `user_id`     int(10)  unsigned DEFAULT 0,
  `room_id`     char(5) DEFAULT NULL,  
  `comment`     varchar(200) DEFAULT NULL, 
  `time`        int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users Activities in Virtual World';

CREATE TABLE `dialogs` (
  `id`          int(10)  unsigned NOT NULL AUTO_INCREMENT,
  `teller_id`   int(10)  unsigned DEFAULT 0,
  `listener_id` int(10)  unsigned DEFAULT 0,
  `command`     varchar(200) DEFAULT NULL,
  `dialog`      varchar(200) DEFAULT NULL,    
  `room_id`     char(5) DEFAULT NULL,  
  `comment`     varchar(200) DEFAULT NULL,
  `displayed`   smallint(1),   
  `time`        int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dialogs in Virtual World';

INSERT INTO `users` (`user_id`, `user_name`, `room_id`, `user_avatar` ) VALUES
(100, 'Alien'   , '1_4' , ''),
(200, 'Lea'     , '1_4' , ''),
(300, 'Fish'    , '1_4' , ''),
(400, 'Kid'     , '1_4' , ''),
(500, 'Dud'     , '1_4' , ''),

(600, 'Ditto'   , '6_3' , ''),
(700, 'Nina'    , '6_3' , ''),
(800, 'Kate'    , '6_3' , ''),
(900, 'Mosh'    , '6_3' , ''),

(20, 'Nilo'     , '3_1' , ''),
(30, 'Misha'    , '3_1' , ''),
(40, 'Sofi'     , '3_1' , ''),

(110, 'Ali'     , '4_6' , ''),
(220, 'Sim'     , '4_6' , ''),
(330, 'Nobody'  , '4_6' , ''),
(440, 'Mat'     , '4_6' , ''),
(550, 'Hel'     , '4_6' , '');
