CREATE TABLE `git_project_log` (
  `proj_id` int(10) NOT NULL COMMENT '项目 ID',
  `proj_log` json NOT NULL COMMENT '执行日志',
  `user_id` int(10) NOT NULL COMMENT '操作人 ID',
  `log_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作类型（1pull,2checkout,3reset）',
  `branch_id` int(10) NOT NULL DEFAULT '0' COMMENT '分支id',
  `branch` varchar(50) NOT NULL COMMENT '当前分支',
  `add_time` int(10) unsigned NOT NULL COMMENT '操作时间',
  KEY `add_time` (`add_time`),
  KEY `proj_id` (`proj_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='项目日志表';