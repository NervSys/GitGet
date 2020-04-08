CREATE TABLE `git_svr` (
  `svr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '服务器id',
  `svr_name` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器地址',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0停用1启用2删除',
  PRIMARY KEY (`srv_id`),
  UNIQUE KEY `srv` (`ip`,`port`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;