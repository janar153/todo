CREATE TABLE `works` (
  `work_id` mediumint(11) NOT NULL AUTO_INCREMENT,
  `work_name` varchar(255) NOT NULL,
  `work_desc` text,
  `work_priority` mediumint(1) DEFAULT '0',
  `work_deadline` datetime DEFAULT '0000-00-00 00:00:00',
  `work_status` mediumint(1) DEFAULT '0',
  `work_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deletion_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`work_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
