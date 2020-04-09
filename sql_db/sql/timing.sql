CREATE TABLE `git_timing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proj_id` int(10) unsigned NOT NULL COMMENT '项目id',
  `time` int(10) unsigned NOT NULL COMMENT '更新时间戳',
  `branch_id` int(10) unsigned NOT NULL COMMENT '分支id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态（0待执行1已执行2已删除）',
  `remaking` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `proj_id` (`proj_id`),
  KEY `branch_id` (`branch_id`),
  KEY `status` (`status`)
) COMMENT='定时处理';