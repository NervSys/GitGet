#!/bin/bash
while :; do echo
    read -e -p "请输入服务器操作用户: (www)" user
    if [ ! $user ];then
        user="www"
    fi
    break
done

while :; do echo
    read -e -p "请输入用户home目录: (/data/wwwroot)" local_path
    if [ ! $local_path ];then
        local_path="/data/wwwroot"
    fi
    break
done

#添加组和用户
sed -i '/'"2000"'/d' /etc/group
sed -i '/'"www"'/d' /etc/group
echo $user:x:2000: >> /etc/group
sed -i '/'"2000"'/d' /etc/passwd
sed -i '/'"www"'/d' /etc/passwd
echo $user:x:2000:2000:root:$local_path:/bin/bash >> /etc/passwd

#设置ssh配置
sed -i '/'"StrictHostKeyChecking no"'/d' /etc/ssh/ssh_config
echo StrictHostKeyChecking no >> /etc/ssh/ssh_config
sed -i '/'"UserKnownHostsFile"'/d' /etc/ssh/ssh_config
echo UserKnownHostsFile /dev/null >> /etc/ssh/ssh_config

#权限处理
if [ ! -d $local_path/.ssh ]; then
mkdir $local_path/.ssh -p
fi

chmod 777 -R "$local_path"
chown $user:$user -R "$local_path/.."

php html/api.php -c"project/setting-set_home_path" -d"home_path=$local_path"