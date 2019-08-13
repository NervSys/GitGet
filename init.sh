#!/bin/bash
while :; do echo
    read -e -p "请输入服务器操作用户: (www)" user
    if [ ! $user ];then
        user="www"
    fi
    break
done

while :; do echo
    read -e -p "请输入项目父目录: (/root)" local_path
    if [ ! $local_path ];then
        local_path="/root"
    fi
    break
done

#添加组和用户
sed -i '/'"$user"'/d' /etc/group
echo $user:x:2000: >> /etc/group
sed -i '/'"$user"'/d' /etc/passwd
echo $user:x:2000:2000:root:$local_path:/bin/bash >> /etc/passwd

#权限处理
if [ ! -d $local_path/.ssh ]; then
mkdir $local_path/.ssh
chmod 777 -R $local_path
chown $user:$user -R $local_path