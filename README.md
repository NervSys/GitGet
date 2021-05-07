# GitGet - Automated deployment tools

## version：4.0

## Introduction

**GitGet is an easy tool for multi project and multi server automatic deployment. developer can complete project branch
switch, code update, version rollback and view historical submitted version through interface buttons, without writing a
large number of commands, and easy one click operation**

## Function List

* Server Manager
* Project Manager

## Dependency

* windows/linux

* Nervsys 8.0.0+

* PHP v7.4+

* MYSQL v5.6+

* GIT v1.8+

## Init

1. php.ini must support these functions: exec, shell_exec, proc_open, proc_get_status.

2. edit conf/prod.ini, connect mysql and redis.

3. edit app/app.ini, modify php path

4. new database "git", run sql_db/sql/*.sql.

5. execute init.sh. (you MUST enter php process owner).

6. run this project, edit user_name, user_email, id_rsa, id_ras.pub in system_setting.