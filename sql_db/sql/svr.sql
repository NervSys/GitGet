CREATE TABLE `git_svr`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '服务器名称',
  `url` varchar(255) NOT NULL COMMENT '服务器地址',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0停用1启用2删除',
  PRIMARY KEY (`id`)
) COMMENT = '服务器列表';