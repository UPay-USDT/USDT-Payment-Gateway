# Upay (USDT Payment Gateway)

<p align="center">
<img src="https://raw.githubusercontent.com/UPay-USDT/USDT-Payment-Gateway/main/UPay-EN.png?22" witdh="100%" height="auto">
</p>
<p align="center">
<a href="https://www.gnu.org/licenses/gpl-3.0.html"><img src="https://img.shields.io/badge/license-GPLV3-blue" alt="license GPLV3"></a>
<a href="https://github.com/UPay-USDT/USDT-Payment-Gateway/releases/tag/v1.1.2"><img src="https://img.shields.io/badge/version-v1.1.2-green" alt="version v1.1.2"></a>
</p>

🇨🇳 [切换中文版本](https://github.com/UPay-USDT/USDT-Payment-Gateway/blob/main/README.md) 


## Introduction
**UPay** (Full name: USDT Payment Gateway) is a **USDT** payment system written in `PHP` language. Provide instant and stable USDT collection services. Supports privatized deployment and supports `TRC20` and `ERC20` networks.

UPay uses [GPLv3](https://www.gnu.org/licenses/gpl-3.0.html) open source license!

## Project Features
* Support privatized deployment.
* Multiple wallet addresses circulate to improve order concurrency rate.
* Support merchants to add their own wallet addresses and receive payments directly.
* Asynchronous queue response, elegant and high performance.
* Support USDT risk control system.
* Powerful back-end financial management system.

* Support privatized deployment.
* Smart order collision avoidance system.
* Support merchants to add their own wallet addresses and receive payments directly.
* Asynchronous queue response, elegant and high performance.
* Cryptocurrency risk warning system (to prevent merchants from receiving risky cryptocurrencies).
* Covering the full range of payment scenarios.
* Real-time exchange of multiple currencies.
* Powerful backend management system.

## Install and run the program

1. Linux server installation pagoda

   [Pagoda Installation](https://www.bt.cn/new/download.html)

   Environment: php7.4 + Apache (nginx) + mysql>=5.7
  
2. Install gmp extension
   
   Step1：apt install libgmp-dev

   Step2: In the Pagoda panel, software store -> php7.4 -> Install extension -> Find gmp and install it

3. Configure database

   Find the <code>ddl.sql</code> file in the root directory, enter <code>mysql</code> and execute the <code>sql</code> file to initialize the database.In <code>.env</code> Configure database connection parameters in

4. Configure redis

   Install <code>redis</code> through the Pagoda panel and configure the <code>redis</code> connection parameters in <code>.env</code>

5. The website root directory points to <code>public</code>

6. Modify <code>runtime</code> permissions to 777

7. Restart Apache (nginx)

8. Fill in the <code>secret</code> of <code>infura</code> in the <code>.env</code> file

9. Fill in the `TRC20` and `ERC20` deposit addresses in the `.env` file

10. Test the merchant backend entrance:
     * Address: URL/merchant
     * Account: 测试商户
     * Password: 123456

11. Add scheduled tasks

    `The statistical task is set to be executed at 1 am every day, and other tasks are set to be executed every minute`

    See the table below for tasks:



## Execute scheduled tasks

Add the following 6 tasks in the scheduled tasks of the Pagoda panel. It is recommended that the time interval of the scheduled tasks is 1 minute.

1. Process the order

   #!/bin/bash PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin export PATH cd /www/wwwroot/Website; su -c "php think deal_order" -s /bin/sh www

2. Process payment

   #!/bin/bash PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin export PATH cd /www/wwwroot/Website; su -c "php think deal_withdraw" -s /bin/sh www

3. Order notification

   #!/bin/bash PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin export PATH cd /www/wwwroot/Website; su -c "php think order_notify" -s /bin/sh www

4. Payment notice

   #!/bin/bash PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin export PATH cd /www/wwwroot/Website; su -c "php think withdraw_notify" -s /bin/sh www

5. Statistics

   #!/bin/bash PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin export PATH cd /www/wwwroot/Website; su -c "php think statistics" -s /bin/sh www

6. Process recharge

   #!/bin/bash PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin export PATH cd /www/wwwroot/Website; su -c "php think deal_recharge" -s /bin/sh www


## Open source statement
* UPay is an open source project and is only used for learning and communication!
* It cannot be used for any purpose that violates the laws and regulations of the People's Republic of China (including Taiwan Province) or the region where the user is located;
* The blockchain tokens involved in the project are for learning purposes, and the author does not agree with the financial attributes of the tokens derived from the blockchain;
* We also do not encourage or support any illegal activities such as "mining", "currency speculation", "virtual currency ICO";
* Virtual currency market behavior is not subject to regulatory requirements and control, investment transactions must be cautious, and it is only for learning blockchain knowledge.

## Project Demonstration
https://app.upay.ink/#/payment/index?id=joz4QnND

<img src="https://raw.githubusercontent.com/UPay-USDT/USDT-Payment-Gateway/main/demo-qrcode.png?2" witdh="160" height="160">

## API documentation
https://docs.upay.ink/api_v1/v/en

## Contact us
Website：https://upay.ink

Telegram：https://t.me/UPay_ink

Email：support@UPay.ink
