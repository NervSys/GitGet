#!/bin/bash
while :; do echo
    read -e -p "请输入开发者姓名: " git_user
    if [ ! $git_user ]; then
        echo "必须输入开发者姓名"
    else
        git config --global user.name "$git_user"
        break
    fi
done

while :; do echo
    read -e -p "请输入开发者邮箱: " git_email
    if [ ! $git_pwd ]; then
        echo "请输入开发者邮箱"
    else
        git config --global user.email  "$git_email"
        break
    fi
done

while :; do echo
    read -e -p "请输入服务器操作用户: (www)" user
    if [ ! $user ];then
        user="www"
    fi
    break
done

while :; do echo
    read -e -p "请输入项目父目录: (/data/wwwroot)" local_path
    if [ ! $local_path ];then
        local_path="/data/wwwroot"
    fi
    break
done

#添加组和用户
#sed -i '/'"$user"'/d' /etc/group
#echo $user:x:2000: >> /etc/group
#sed -i '/'"$user"'/d' /etc/passwd
#echo $user:x:2000:2000:root:$local_path:/bin/bash >> /etc/passwd

#权限处理
#if [ ! -d $local_path/.ssh ]; then
#mkdir $local_path/.ssh

while :; do echo
    read -e -p "请输入git公钥: (自动生成)" git_ssh_key
    if [ ! $git_ssh_key ]; then
        ssh-keygen
        mv ~/.ssh/id_rsa $local_path/.ssh
        mv ~/.ssh/id_rsa.pub $local_path/.ssh
    else
        echo $git_ssh_key >
    fi
done

chmod 777 -R $local_path
chown $user:$user -R $local_p
