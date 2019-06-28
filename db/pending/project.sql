CREATE TABLE IF NOT EXISTS `git_project` (
    `proj_id` INT (10) NOT NULL AUTO_INCREMENT COMMENT '项目 ID',
    `proj_name` VARCHAR (32) NOT NULL COMMENT '项目名称',
    `proj_desc` VARCHAR (256) NOT NULL COMMENT '项目介绍',
    `proj_git_url` VARCHAR (128) NOT NULL COMMENT 'Git 地址',
    `proj_local_path` VARCHAR (64) NOT NULL COMMENT '本地路径',
    `proj_user_name` VARCHAR (32) NOT NULL COMMENT '开发者名称',
    `proj_user_email` VARCHAR (32) NOT NULL COMMENT '开发者邮箱',
    `proj_backup_files` JSON COMMENT '备份文件',
    `active_branch` varchar(100) NOT NULL DEFAULT 'master' COMMENT '当前分支',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0删除1启动）',
    `env_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '环境（0开发环境1生产环境）',
    `add_time` INT (10) UNSIGNED NOT NULL COMMENT '添加时间',
    PRIMARY KEY (`proj_id`),
    INDEX (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='项目表';

CREATE TABLE IF NOT EXISTS `git_project_log` (
    `proj_id` INT (10) NOT NULL COMMENT '项目 ID',
    `proj_log` VARCHAR (256) NOT NULL COMMENT '执行日志',
    `user_id` INT (10) NOT NULL COMMENT '操作人 ID',
    `log_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作类型（1pull,2checkout,3reset）',
    `branch` varchar(50) NOT NULL COMMENT '当前分支',
    `add_time` INT (10) UNSIGNED NOT NULL COMMENT '操作时间',
    INDEX (`proj_id`, `user_id`),
    INDEX (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='项目日志表';

CREATE TABLE IF NOT EXISTS `git_project_team` (
    `proj_id` INT (10) NOT NULL COMMENT '项目 ID',
    `user_id` INT (10) NOT NULL COMMENT '成员 ID',
    `add_time` INT (10) UNSIGNED NOT NULL COMMENT '操作时间',
    INDEX (`proj_id`),
    INDEX (`user_id`),
    INDEX (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='项目团队表';