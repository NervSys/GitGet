CREATE TABLE `git_setting`
(
    `id`    int(10) unsigned NOT NULL AUTO_INCREMENT,
    `key`   varchar(50)   NOT NULL,
    `value` varchar(2000) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`) USING BTREE
) COMMENT='系统配置表';