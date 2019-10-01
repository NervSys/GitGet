# GitGet - 自动化部署工具

## 当前版本：2.0

## 简介

**gitget是一个轻松实现多项目多服务器自动化部署的工具。项目人员可以通过界面按钮完成项目分支切换，代码更新，版本回滚以及查看历史提交版本，不需要书写大量的命令，轻松一键操作。**

## 功能列表
* 用户管理（已完成）
* 权限管理（开发中）
* 服务器管理（已完成）
* 项目管理（已完成）

## 环境依赖
* windows/linux

* PHP版本7.2+

* MYSQL版本5.6+

* GIT版本1.8+

## 包依赖

* NervSys

### 环境配置

1、clone时带子模块，git clone --recursive https://github.com/NervSys/GitGet.git

2、nginx配置地址要直接到html/api.php文件所在目录;

3、要给nginx的用户操作该项目的权限;

4、修改conf/mysql.ini文件

5、开放php.ini的exec、shell_exec、proc_open等方法
### 用法介绍

1、新建数据库，运行db/pending/*.sql文件初始化数据表

2、linux系统需要先执行init.sh脚本（请确保输入的用户是php进程的用户）

3、第一次登录的账号密码会成为你的账号密码

4、初始配置>服务器列表(需要填写服务器间通信的IP)

5、初始配置>系统设置(挨个填写git设置)

6、添加项目：
   * 备份文件地址：如conf/mysql.ini，添加后，无论什么操作不会改变配置文件
   * 回滚：只能回滚到当前分支的某一次更新记录
