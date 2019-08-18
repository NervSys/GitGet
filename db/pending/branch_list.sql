CREATE TABLE `git_branch_list` (
  `branch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分支名称',
  `proj_id` int(10) unsigned NOT NULL COMMENT '所属项目',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在当前分支',
  PRIMARY KEY (`branch_id`) USING BTREE,
  KEY `proj_id` (`proj_id`) USING BTREE,
  KEY `active` (`active`) USING BTREE,
  KEY `branch_name` (`branch_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='分支列表';