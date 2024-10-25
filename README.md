# UPay (USDT收款支付网关)

<p align="center">
<img src="https://raw.githubusercontent.com/UPay-USDT/USDT-Payment-Gateway/main/UPay-CN.png?22" witdh="100%" height="auto">
</p>
<p align="center">
<a href="https://www.gnu.org/licenses/gpl-3.0.html"><img src="https://img.shields.io/badge/license-GPLV3-blue" alt="license GPLV3"></a>
<a href="https://github.com/UPay-USDT/USDT-Payment-Gateway/releases/tag/v1.1.2"><img src="https://img.shields.io/badge/version-v1.1.2-green" alt="version v1.1.2"></a>
</p>

🇺🇸 [Switch to English version](https://github.com/UPay-USDT/USDT-Payment-Gateway/blob/main/README_en.md)

## 项目简介
**UPay**（全称：USDT Payment Gateway）是一个由<code>PHP语言</code>编写的 **USDT** 收款代付系统。提供即时、稳定的 **USDT** 收款服务，支持私有化部署，支持<code>TRC20</code>和<code>ERC20</code>网络。

UPay 使用 [GPLv3](https://www.gnu.org/licenses/gpl-3.0.html) 开源协议!


 
## 项目特点

* 支持私有化部署
* 多钱包地址轮询，提高订单撞单率
* 支持商户添加自己钱包地址，直接收款
* 异步队列响应，优雅及高性能
* 加密货币风险预警系统 (避免商户收到黑U或标记U)
* 覆盖全范围支付场景
* 多币种实时换
* 强大的后台管理系统



## 安装与运行程序
1. Linux 服务器安装宝塔

   [宝塔安装](https://www.bt.cn/new/download.html)

   环境：php7.4 + Apache(nginx) + mysql>=5.7

2. 安装 gmp 扩展

   Step1：apt install libgmp-dev

   Step2：在宝塔面板，软件商店 -> php7.4 -> 安装拓展 -> 找到 gmp 并安装

3. 配置数据库

   在根目录下找到<code>ddl.sql</code>文件，进入<code>mysql</code>中执行<code>sql</code>文件初始化数据库，在<code>.env</code>中配置数据库连接参数

4. 配置 redis

   通过宝塔面板安装<code>redis</code>，在<code>.env</code>中配置<code>redis</code>连接参数

5. 网站根目录指向<code>public</code>

6. 修改<code>runtime</code>权限为 777

7. 重启 Apache（nginx）

8. 在<code>.env</code>文件中填入<code>infura</code>的<code>secret</code>

9. 在`.env`文件中填入`TRC20`和`ERC20`充值地址

10. 测试商户后台入口：

    * 地址：网址/merchant
    * 账号：测试商户
    * 密码：123456 

11. 添加计划任务

    `统计任务设置为每天凌晨1点执行，其他任务设置每一分钟执行一次`

    任务参见下表：



## 执行定时任务

在宝塔面板的计划任务中添加以下6个任务，建议定时任务的时间间隔为1分钟

1. 处理订单
   * #!/bin/bash
   * PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
   * export PATH
   * cd /www/wwwroot/根目录; su -c "php think deal_order" -s /bin/sh www

2. 处理代付
   * #!/bin/bash
   * PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
   * export PATH
   * cd /www/wwwroot/根目录; su -c "php think deal_withdraw" -s /bin/sh www

3. 订单通知
   * #!/bin/bash
   * PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
   * export PATH
   * cd /www/wwwroot/根目录; su -c "php think order_notify" -s /bin/sh www

4. 代付通知
   * #!/bin/bash
   * PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
   * export PATH
   * cd /www/wwwroot/根目录; su -c "php think withdraw_notify" -s /bin/sh www

5. 统计
   * #!/bin/bash
   * PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
   * export PATH
   * cd /www/wwwroot/根目录; su -c "php think statistics" -s /bin/sh www

6. 处理充值
   * #!/bin/bash
   * PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
   * export PATH
   * cd /www/wwwroot/根目录; su -c "php think deal_recharge" -s /bin/sh www



## 开源申明

* UPay 为开源的产品，仅用于学习交流使用！
* 不可用于任何违反中华人民共和国(含台湾省)或使用者所在地区法律法规的用途；
* 项目中所涉及区块链代币均为学习用途，作者并不赞成区块链所繁衍出代币的金融属性；
* 亦不鼓励和支持任何"挖矿"，"炒币"，"虚拟币ICO"等非法行为；
* 虚拟币市场行为不受监管要求和控制，投资交易需谨慎，仅供学习区块链知识。



## 项目演示

https://app.upay.ink/#/payment/index?id=joz4QnND

<img src="https://raw.githubusercontent.com/UPay-USDT/USDT-Payment-Gateway/main/demo-qrcode.png?2" witdh="160" height="160">

## API文档
https://docs.upay.ink/api_v1


## 联系我们
* 官方网站：https://upay.ink
* Telegram：https://t.me/UPay_ink
* Email：support@UPay.ink

