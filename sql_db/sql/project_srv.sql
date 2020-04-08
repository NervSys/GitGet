CREATE TABLE `git_project_srv` (
  `proj_id` int(11) NOT NULL COMMENT '项目id',
  `srv_id` int(11) NOT NULL COMMENT '服务器id',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '锁',
  KEY `proj_id` (`proj_id`) USING BTREE,
  KEY `srv_id` (`srv_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;