# GitRemoteDeploy - 自动化部署工具

## 当前版本：2.0

## 简介

**gitRemoteDeploy是一个轻松实现多项目多服务器自动化部署的工具。项目人员可以通过界面按钮完成项目分支切换，代码更新，版本回滚以及查看历史提交版本，不需要书写大量的命令，轻松一键操作。**

## 功能列表
* 用户管理（已完成）
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
1、配置好操作用户和home目录（init.sh中的脚本可以完成，但请确保输入的用户是php进程的用户）

2、git设置（可以在项目启动后，通过 初始配置>系统设置 来挨个填写）

3、切换到操作用户（如www）clone,请记得带子模块，（git clone --recursive https://github.com/NervSys/GitRemoteDeploy.git GitRemoteDeploy）

4、nginx配置  
    * 配置可接收到消息的ip和端口 （多服务器请注意内外网IP区别和防火墙）
    * 项目地址要直接到html/api.php文件所在目录;
```text
server{
  listen 80;
  server_name 127.0.0.2 www.grd.com;
  root /data/wwwroot/gitRemoteDeploy/html;  
  ****
}
```

5、开放php.ini的exec、shell_exec、proc_open等方法

### 项目配置
1、新建数据库（如grd），运行db/pending/*.sql文件初始化数据表

2、配置conf目录的prod.ini文件（可以新建.env文件，内容填写配置文件的文件名，如dev，这样就会使用dev.ini作为配置文件）

3、修改app/app.ini的php路径

4、定时更新的功能需要在crond脚本添加一条  */1 * * * * source /etc/profile && nohup /usr/local/php/bin/php /data/wwwroot/gitRemoteDeploy/html/api.php queue/master-start > /dev/null &

3、第一次登录的账号密码会成为你的账号密码

4、项目开启后在  初始配置>服务器列表  中挨个填写好各服务器的通知ip和port  

5、添加项目：
   * 备份文件地址：如conf/prod.ini，添加后，无论什么操作不会改变配置文件
   * 回滚：只能回滚到当前分支的某一次更新记录
