CREATE TABLE `git_branch`
(
    `id`      int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name`    varchar(255) NOT NULL DEFAULT '' COMMENT '分支名称',
    `proj_id` int(10) unsigned NOT NULL COMMENT '所属项目',
    `active`  tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在当前分支',
    PRIMARY KEY (`id`) USING BTREE,
    KEY       `proj_id` (`proj_id`) USING BTREE,
    KEY       `active` (`active`) USING BTREE,
    KEY       `name` (`name`) USING BTREE
) COMMENT='分支列表';