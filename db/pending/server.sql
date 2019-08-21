CREATE TABLE `git_server` (
  `srv_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '服务器id',
  `ip` varchar(50) NOT NULL COMMENT 'IP地址',
  `port` int(11) NOT NULL DEFAULT '80' COMMENT '端口',
  `srv_name` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0停用1启用2删除',
  PRIMARY KEY (`srv_id`),
  UNIQUE KEY `srv` (`ip`,`port`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;