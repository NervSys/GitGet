CREATE TABLE `git_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户主键',
  `user_acc` varchar(16) NOT NULL COMMENT '用户账号',
  `user_pwd` char(32) NOT NULL COMMENT '用户密码',
  `user_entry` char(32) NOT NULL COMMENT '用户密钥',
  `add_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`),
  KEY `user_acc` (`user_acc`),
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';