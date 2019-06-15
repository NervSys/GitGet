#GitGet - 自动化部署工具
##简介
**可以通过url方式快速部署任意分支到服务器。在多人多分支开发中，代码部署到服务器都只能通过合并到主分支后部署才能生效，使用GitGet可以通过url快速切换到指定分支，立即生效。**
##基本用法
### 配置
```
[git]
local_path = /www/wwwroot/git.zhjapp.com                //配置项目目录
download_url = git@gitee.com:testZhj/zhj_new.git        //配置仓库地址


[acc]
name = "vickywang06"                                           //配置git用户名
email = "904428723@qq.com"                                //配置git邮箱

```
### url访问
http://域名/api.php/git/deploy-init      初始化克隆代码

http://域名/api.php/git/deploy-get_remote_branch      获取远程分支

http://域名/api.php/git/deploy-get_local_branch      获取当前所在分支

http://域名/api.php/git/deploy-checkout?branch=分支名      获取当前所在分支

##注意事项
1. 使用groupadd www  && useradd www 添加www用户

2. 打开执行者登录权限和无密码权限，vi /etc/sudoers，添加一行www ALL=(ALL) NOPASSWD: ALL，注释掉default requiretty

3. 修改 /etc/passwd ， www:x:1000:1000:root:/www/wwwroot:/bin/bash

4. 通过ps -ef | grep nginx和ps -ef | grep php查看nginx/apache、php执行者，修改对应的配置文件改变用户为www 

5. 给执行站点目录和git仓库站点目录设置用户为www，并给与777权限(chmod -R www:www 777 对应目录）  

6. 为 www 用户的根目录增加操作权限，便于写入 .gitconfig 用户数据，否则stash无法执行

7. 配置ssh秘钥时要注意 ssh文件要放在www 用户的根目录下 并且外层.ssh的权限设置755，里面id_rsa和id_rsa.pub设置600，known_hosts设置644




