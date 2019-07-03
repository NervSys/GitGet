#GitGet - 自动化部署工具
##简介

**gitget是一个轻松实现多项目自动化部署的工具，提供了用户模块和项目模块界面，容易上手。项目人员可以通过界面按钮完成项目分支切换，代码更新，版本回滚以及查看历史提交版本，不需要书写大量的命令，轻松一键操作。**

##环境依赖
* windows/linux

* PHP版本7.2+

* MYSQL版本5.6+

* GIT版本1.8+

##包依赖

* NervSys

### 环境配置

1、clone时带子模块，git clone --recursive https://github.com/NervSys/GitGet.git

2、nginx配置地址要直接到html/api.php文件所在目录;

3、要给nginx的用户操作该项目的权限;

4、修改conf/mysql.ini文件

5、修改NervSys/core/system.ini的[INIT]模块下添加 Start = start

### 用法介绍

1、新建数据库，运行db/pending/*.sql文件初始化数据表

2、访问 <http://域名/api.php/user/ctrl-init>，初始化后台用户

3、访问页面，<http://域名/index.php>，通过admin:admin登录

4、添加项目：
   * 备份文件地址：如conf/mysql.ini，添加后，无论什么操作不会改变配置文件
   * 回滚：只能回滚到当前分支的某一次更新记录
