CREATE TABLE `git_project` (
  `proj_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '项目 ID',
  `proj_name` varchar(32) NOT NULL COMMENT '项目名称',
  `proj_desc` varchar(256) NOT NULL COMMENT '项目介绍',
  `proj_git_url` varchar(128) NOT NULL COMMENT 'Git 地址',
  `proj_local_path` varchar(64) NOT NULL COMMENT '本地路径',
  `proj_backup_files` json DEFAULT NULL COMMENT '备份文件',
  `srv_list` json DEFAULT NULL COMMENT '所在服务器',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0停用1启动2删除）',
  `is_lock` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否加锁（0未锁1锁2只锁git）',
  PRIMARY KEY (`proj_id`),
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='项目表';