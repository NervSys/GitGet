CREATE TABLE IF NOT EXISTS `git_user` (
    `user_id` INT (10) NOT NULL AUTO_INCREMENT COMMENT '用户主键',
    `user_acc` VARCHAR (16) NOT NULL COMMENT '用户账号',
    `user_pwd` CHAR (32) NOT NULL COMMENT '用户密码',
    `user_entry` CHAR (32) NOT NULL COMMENT '用户密钥',
    `add_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`user_id`),
    INDEX (`user_acc`),
    INDEX (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';