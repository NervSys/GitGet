CREATE TABLE `git_system_setting` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` varchar(2000) NOT NULL,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `key` (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';