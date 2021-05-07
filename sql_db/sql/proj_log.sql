CREATE TABLE `git_proj_log`
(
    `id`        int(11) NOT NULL AUTO_INCREMENT,
    `proj_id`   int(10) NOT NULL COMMENT '项目 ID',
    `log`       varchar(255) NOT NULL COMMENT '执行日志',
    `log_type`  tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作类型（1pull,2checkout,3reset）',
    `commit_id` varchar(255) NOT NULL COMMENT '提交id',
    `active`    tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否当前分支',
    `branch_id` int(10) NOT NULL DEFAULT '0' COMMENT '分支id',
    `add_time`  timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增时间',
    PRIMARY KEY (`id`),
    KEY         `add_time` (`add_time`),
    KEY         `proj_id` (`proj_id`) USING BTREE,
    KEY         `branch_id` (`branch_id`) USING BTREE,
    KEY         `commit_id` (`commit_id`) USING BTREE,
    KEY         `active` (`active`) USING BTREE
) COMMENT='项目日志表';