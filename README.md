# Upay

## 一、简介
Upay（全称：USDT Payment）是一个由PHP语言编写的Usdt收付系统，支持私有化部署，支持Trc20和Erc20网络。

Upay 使用 [GPLv3](https://www.gnu.org/licenses/gpl-3.0.html) 开源协议!

## 二、联系我们

Upay客服: https://t.me/UPay_ink

## 三、安装与运行程序
1.Linux服务器安装宝塔

[宝塔安装](https://www.bt.cn/new/download.html)

环境：php7.4+apache（nginx）+mysql>=5.7

2.安装gmp扩展

step1: apt install libgmp-dev

step2: 在宝塔面板，软件商店 -> php7.4 -> 安装拓展 -> 找到gmp并安装

3.配置数据库
在根目录下找到ddl.sql文件，进入mysql中执行sql文件初始化数据库，在.env中配置数据库连接参数

4.配置redis
通过宝塔面板安装redis，在.env中配置redis连接参数

5.网站根目录指向public

6.修改runtime权限为777

7.重启apache（nginx）

8.在.env文件中填入infura的secret

9.在.env文件中填入trc和erc充值地址

10.测试商户后台入口：

>地址：网址/merchant
> 
>账号：测试商户
> 
>密码：123456

11.添加计划任务

`统计任务设置为每天凌晨1点执行,其他任务设置每一分钟执行一次`

任务参见下表：


## 四、执行定时任务
在宝塔面板的计划任务中添加以下6个任务，建议定时任务的时间间隔为1分钟

1.处理订单
#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
cd /www/wwwroot/根目录; su -c "php think deal_order" -s /bin/sh www

2.处理代付
#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
cd /www/wwwroot/根目录; su -c "php think deal_withdraw" -s /bin/sh www

3.订单通知
#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
cd /www/wwwroot/根目录; su -c "php think order_notify" -s /bin/sh www

4.代付通知
#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
cd /www/wwwroot/根目录; su -c "php think withdraw_notify" -s /bin/sh www

5.统计
#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
cd /www/wwwroot/根目录; su -c "php think statistics" -s /bin/sh www

6.处理充值
#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
cd /www/wwwroot/根目录; su -c "php think deal_recharge" -s /bin/sh www

