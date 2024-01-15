/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50738
 Source Host           : localhost:3306
 Source Schema         : upay

 Target Server Type    : MySQL
 Target Server Version : 50738
 File Encoding         : 65001

 Date: 08/03/2023 14:42:03
*/

CREATE DATABASE `upay` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ea_address
-- ----------------------------
DROP TABLE IF EXISTS `ea_address`;
CREATE TABLE `ea_address` (
                              `id` int NOT NULL AUTO_INCREMENT,
                              `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '地址',
                              `address_hex` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'hex地址',
                              `img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '图片地址',
                              `private_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '私钥 加密后的',
                              `public_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '公钥',
                              `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型 1-TRC   2-ETH  3-OMNI',
                              `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0-关  1-开',
                              `is_lock` tinyint NOT NULL DEFAULT '0' COMMENT '是否锁定 0-否   1-是',
                              `merchant_id` int NOT NULL DEFAULT '0' COMMENT '商户id',
                              `merchantname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户名称',
                              `trx_balance` decimal(15,6) DEFAULT NULL COMMENT 'trx余额',
                              `eth_balance` decimal(15,10) DEFAULT NULL COMMENT 'eth余额',
                              `usdt_balance` decimal(15,6) DEFAULT NULL COMMENT 'usdt余额',
                              `allocation_time` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1' COMMENT '最近分配订单的时间',
                              `transfer_trx_time` int DEFAULT NULL COMMENT '最近转trx的时间 防止重复转',
                              `create_time` int DEFAULT NULL,
                              `update_time` int DEFAULT NULL,
                              `delete_time` int DEFAULT NULL,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of ea_address
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_address_moneychange
-- ----------------------------
DROP TABLE IF EXISTS `ea_address_moneychange`;
CREATE TABLE `ea_address_moneychange` (
                                          `id` int unsigned NOT NULL AUTO_INCREMENT,
                                          `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '地址',
                                          `before_money` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '原金额',
                                          `money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
                                          `after_money` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '变动后金额',
                                          `change_order_id` int DEFAULT NULL COMMENT '订单号',
                                          `change_order_sn` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '订单编号',
                                          `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型 1-入账 2-出账  ',
                                          `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '备注',
                                          `create_time` int NOT NULL DEFAULT '0' COMMENT '创建时间',
                                          `update_time` int DEFAULT NULL COMMENT '更新时间',
                                          `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                          PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='码商资金变化表';

-- ----------------------------
-- Records of ea_address_moneychange
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_address_transfer
-- ----------------------------
DROP TABLE IF EXISTS `ea_address_transfer`;
CREATE TABLE `ea_address_transfer` (
                                       `id` int unsigned NOT NULL AUTO_INCREMENT,
                                       `transaction_id` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '交易id',
                                       `from_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '付款地址',
                                       `to_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '收款地址',
                                       `money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
                                       `change_order_id` int DEFAULT NULL COMMENT '订单号',
                                       `change_order_sn` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '订单编号',
                                       `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型 1-订单 2-退款 3-代付',
                                       `chain_type` tinyint(1) DEFAULT NULL COMMENT '链路  1-TRC  2-ETH',
                                       `is_confirm` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已确认 0-未确认 1-已确认',
                                       `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '备注',
                                       `transfer_time` int DEFAULT NULL COMMENT '交易时间',
                                       `create_time` int NOT NULL DEFAULT '0' COMMENT '创建时间',
                                       `update_time` int DEFAULT NULL COMMENT '更新时间',
                                       `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                       PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='码商资金变化表';

-- ----------------------------
-- Records of ea_address_transfer
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_budan
-- ----------------------------
DROP TABLE IF EXISTS `ea_budan`;
CREATE TABLE `ea_budan` (
                            `id` int unsigned NOT NULL AUTO_INCREMENT,
                            `merchant_id` int DEFAULT NULL,
                            `merchantname` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
                            `transaction_id` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '交易id',
                            `from_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '付款地址',
                            `to_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '收款地址',
                            `money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
                            `chain_type` tinyint(1) DEFAULT NULL COMMENT '链路  1-TRC  2-ETH',
                            `transfer_time` int DEFAULT NULL COMMENT '交易时间',
                            `create_time` int NOT NULL DEFAULT '0' COMMENT '创建时间',
                            `update_time` int DEFAULT NULL COMMENT '更新时间',
                            `delete_time` int DEFAULT NULL COMMENT '删除时间',
                            PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='码商资金变化表';

-- ----------------------------
-- Records of ea_budan
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_merchant_address
-- ----------------------------
DROP TABLE IF EXISTS `ea_merchant_address`;
CREATE TABLE `ea_merchant_address` (
                                       `id` int NOT NULL AUTO_INCREMENT,
                                       `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '地址',
                                       `address_hex` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'hex地址',
                                       `img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '图片地址',
                                       `private_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '私钥 加密后的',
                                       `public_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '公钥',
                                       `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型 1-TRC   2-ETH  3-OMNI',
                                       `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0-关  1-开',
                                       `is_lock` tinyint NOT NULL DEFAULT '0' COMMENT '是否锁定 0-否   1-是',
                                       `merchant_id` int NOT NULL DEFAULT '0' COMMENT '商户id',
                                       `merchantname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户名称',
                                       `trx_balance` decimal(15,6) DEFAULT NULL COMMENT 'trx余额',
                                       `eth_balance` decimal(15,10) DEFAULT NULL COMMENT 'eth余额',
                                       `usdt_balance` decimal(15,6) DEFAULT NULL COMMENT 'usdt余额',
                                       `allocation_time` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1' COMMENT '最近分配订单的时间',
                                       `create_time` int DEFAULT NULL,
                                       `update_time` int DEFAULT NULL,
                                       `delete_time` int DEFAULT NULL,
                                       PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of ea_merchant_address
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_merchant_log_202401
-- ----------------------------
DROP TABLE IF EXISTS `ea_merchant_log_202401`;
CREATE TABLE `ea_merchant_log_202401` (
                                          `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                          `user_id` int unsigned DEFAULT '0' COMMENT '码商ID',
                                          `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
                                          `method` varchar(50) NOT NULL COMMENT '请求方法',
                                          `title` varchar(100) DEFAULT '' COMMENT '日志标题',
                                          `content` text NOT NULL COMMENT '内容',
                                          `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
                                          `useragent` varchar(255) DEFAULT '' COMMENT 'User-Agent',
                                          `create_time` int DEFAULT NULL COMMENT '操作时间',
                                          PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=632 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='码商操作日志表 - 202401';

-- ----------------------------
-- Table structure for ea_merchant_menu
-- ----------------------------
DROP TABLE IF EXISTS `ea_merchant_menu`;
CREATE TABLE `ea_merchant_menu` (
                                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                    `pid` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父id',
                                    `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '名称',
                                    `icon` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
                                    `href` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '链接',
                                    `params` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '' COMMENT '链接参数',
                                    `target` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
                                    `sort` int DEFAULT '0' COMMENT '菜单排序',
                                    `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
                                    `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
                                    `create_time` int DEFAULT NULL COMMENT '创建时间',
                                    `update_time` int DEFAULT NULL COMMENT '更新时间',
                                    `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                    PRIMARY KEY (`id`) USING BTREE,
                                    KEY `title` (`title`) USING BTREE,
                                    KEY `href` (`href`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='商户后台菜单表';

-- ----------------------------
-- Records of ea_merchant_menu
-- ----------------------------
BEGIN;
INSERT INTO `ea_merchant_menu` VALUES (227, 99999999, '后台首页', 'fa fa-home', 'index/welcome', '', '_self', 0, 1, NULL, NULL, 1573120497, NULL);
INSERT INTO `ea_merchant_menu` VALUES (228, 0, '后台首页', 'fa fa-cog', '', '', '_self', 0, 1, '', NULL, 1623506574, NULL);
INSERT INTO `ea_merchant_menu` VALUES (234, 228, '商户主页', 'fa fa-home', 'index/welcome', '', '_self', 999, 1, '', NULL, 1588228555, NULL);
INSERT INTO `ea_merchant_menu` VALUES (246, 228, '订单', 'fa fa-list', '', '', '_self', 20, 0, '', 1573435919, 1623576563, NULL);
INSERT INTO `ea_merchant_menu` VALUES (247, 228, '订单管理', 'fa fa-asterisk', 'order.order/index', '', '_self', 8, 1, '', 1573457448, 1623660495, NULL);
INSERT INTO `ea_merchant_menu` VALUES (251, 228, '提款记录', 'fa fa-check-square', 'df.withdraw/index', '', '_self', 3, 1, '', 1573542953, 1623660474, NULL);
INSERT INTO `ea_merchant_menu` VALUES (253, 228, '余额账变', 'fa fa-random', 'merchant.merchantmoneychange/index', '', '_self', 1, 1, '', 1573542953, 1623660474, NULL);
INSERT INTO `ea_merchant_menu` VALUES (255, 228, '申请提款', 'fa fa-arrow-up', 'merchant.financial/applywithdraw', '', '_self', 30, 1, NULL, NULL, NULL, NULL);
INSERT INTO `ea_merchant_menu` VALUES (256, 228, '商户设置', 'fa fa-cog', 'merchant.config/index', '', '_self', 0, 1, NULL, NULL, NULL, NULL);
INSERT INTO `ea_merchant_menu` VALUES (259, 228, '收款钱包地址', 'fa fa-vcard', 'order.ownaddress/index', '', '_self', 0, 1, '', 1573457448, 1623660495, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ea_merchant_merchant
-- ----------------------------
DROP TABLE IF EXISTS `ea_merchant_merchant`;
CREATE TABLE `ea_merchant_merchant` (
                                        `id` int unsigned NOT NULL AUTO_INCREMENT,
                                        `merchantname` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '商户名',
                                        `appid` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '商户号',
                                        `appsecret` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户密钥',
                                        `password` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '密码',
                                        `usdt_balance` decimal(15,2) DEFAULT '0.00' COMMENT 'usdt余额',
                                        `trc_balance` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'trc可用余额',
                                        `erc_balance` decimal(15,2) DEFAULT '0.00' COMMENT 'ethbalance',
                                        `merchant_rate` decimal(10,4) DEFAULT '0.0000' COMMENT '收款费率',
                                        `realname` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '姓名',
                                        `sfznumber` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '身份证号',
                                        `mobile` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '联系电话',
                                        `qq` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'QQ',
                                        `address` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '联系地址',
                                        `paypassword` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '支付密码',
                                        `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态 {select} (0:未激活, 1:激活)',
                                        `is_auto_tixian` tinyint(1) DEFAULT '0' COMMENT '是否自动提现 ',
                                        `tixian_usdt` decimal(15,2) DEFAULT NULL COMMENT '提现USDT阈值',
                                        `trc_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'TRC地址',
                                        `erc_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'ETH地址',
                                        `login_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '登录IP',
                                        `login_num` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
                                        `last_login_time` int DEFAULT NULL COMMENT '最后登录时间',
                                        `is_xiadan` tinyint(1) DEFAULT '1' COMMENT '是否有下单权限 0-无1-是',
                                        `is_api_df` tinyint(1) DEFAULT '1' COMMENT '是否有api提款权限 0-无 1-是',
                                        `is_sd_df` tinyint(1) DEFAULT '1' COMMENT '是否有手动提款权限  0-无  1-是',
                                        `listion_url` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '地址监控的回调地址',
                                        `create_time` int unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
                                        `update_time` int DEFAULT NULL COMMENT '更新时间',
                                        `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                        PRIMARY KEY (`id`) USING BTREE,
                                        UNIQUE KEY `appid` (`appid`) USING BTREE,
                                        UNIQUE KEY `merchantname` (`merchantname`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=244 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='商户表';

-- ----------------------------
-- Records of ea_merchant_merchant
-- ----------------------------
BEGIN;
INSERT INTO `ea_merchant_merchant` VALUES (1, '测试商户', 'ciofh5fe', '6966000', 'ed696eb5bba1f7460585cc6975e6cf9bf24903dd', 1000.00, 0.00, 0.00, 0.0500, '', NULL, '', '', NULL, NULL, 1, 0, 200.00, '', '', '', 36, 1704944230, 1, 1, 1, '11222', 1646893802, 1704944230, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ea_merchant_moneychange
-- ----------------------------
DROP TABLE IF EXISTS `ea_merchant_moneychange`;
CREATE TABLE `ea_merchant_moneychange` (
                                           `id` int unsigned NOT NULL AUTO_INCREMENT,
                                           `merchant_id` int unsigned NOT NULL DEFAULT '0' COMMENT '码商id',
                                           `merchantname` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '商户名字',
                                           `before_money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '原金额',
                                           `money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
                                           `after_money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '变动后金额',
                                           `change_order_id` int DEFAULT NULL COMMENT '订单号',
                                           `change_order_sn` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '订单编号',
                                           `type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '类型 1-订单收款 2-提款 3-其他',
                                           `chain_type` tinyint(1) DEFAULT NULL COMMENT '链路类型 1-TRC  2-ETH',
                                           `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '备注',
                                           `operate_id` int DEFAULT NULL COMMENT '操作人id',
                                           `operate_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '操作人名字',
                                           `create_time` int NOT NULL DEFAULT '0' COMMENT '创建时间',
                                           `update_time` int DEFAULT NULL COMMENT '更新时间',
                                           `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                           PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='码商资金变化表';

-- ----------------------------
-- Records of ea_merchant_moneychange
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_order
-- ----------------------------
DROP TABLE IF EXISTS `ea_order`;
CREATE TABLE `ea_order` (
                            `id` int unsigned NOT NULL AUTO_INCREMENT,
                            `merchant_id` int NOT NULL COMMENT '商户id',
                            `merchantname` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户名称',
                            `appid` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户appid',
                            `plat_order_sn` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '系统订单号',
                            `out_order_sn` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '三方平台订单号',
                            `merchant_order_sn` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '商户订单号',
                            `pay_money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '实际金额',
                            `pay_usdt` decimal(15,2) DEFAULT '0.00' COMMENT '需要支付的usdt',
                            `usdt_cny` decimal(15,2) DEFAULT NULL COMMENT 'usdt的当前cny价格',
                            `usdt_inr` decimal(15,2) DEFAULT NULL COMMENT 'usdt-inr',
                            `usdt_jpy` decimal(15,2) DEFAULT NULL COMMENT 'usdt-jpy',
                            `usdt_krw` decimal(15,2) DEFAULT NULL COMMENT 'usdt-krw',
                            `usdt_php` decimal(15,2) DEFAULT NULL COMMENT 'usdt-php',
                            `usdt_eur` decimal(15,2) DEFAULT NULL COMMENT 'usdt_eur',
                            `usdt_gbp` decimal(15,2) DEFAULT NULL COMMENT 'usdt_gbp',
                            `usdt_chf` decimal(15,2) DEFAULT NULL COMMENT 'usdt_chf',
                            `usdt_twd` decimal(15,2) DEFAULT NULL COMMENT 'usdt_twd',
                            `usdt_hkd` decimal(15,2) DEFAULT NULL COMMENT 'usdt_hkd',
                            `usdt_mop` decimal(15,2) DEFAULT NULL COMMENT 'usdt_mop',
                            `usdt_sgd` decimal(15,2) DEFAULT NULL COMMENT 'usdt_sgd',
                            `usdt_nzd` decimal(15,2) DEFAULT NULL COMMENT 'usdt_nzd',
                            `usdt_thb` decimal(15,2) DEFAULT NULL COMMENT 'usdt_thb',
                            `usdt_cad` decimal(15,2) DEFAULT NULL COMMENT 'usdt_cad',
                            `actual_usdt` decimal(15,2) DEFAULT NULL COMMENT '实际支付的usdt',
                            `merchant_rate` decimal(10,2) DEFAULT NULL COMMENT '当前商户费率',
                            `merchant_money` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商户实得金额',
                            `merchant_usdt` decimal(15,2) DEFAULT NULL COMMENT '商户实得usdt',
                            `poundage_money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '手续费金额',
                            `poundage_usdt` decimal(15,2) DEFAULT NULL COMMENT '手续费usdt',
                            `plat_money` decimal(15,2) DEFAULT '0.00' COMMENT '平台利润',
                            `keys_id` int DEFAULT '0',
                            `keys_money` decimal(15,2) DEFAULT '0.00',
                            `keys_money2` decimal(15,2) DEFAULT '0.00',
                            `chain_type` tinyint(1) DEFAULT NULL COMMENT '链类型 \r\n1-TRC20 \r\n2-ERC20',
                            `pay_time` int unsigned NOT NULL DEFAULT '0' COMMENT '订单支付成功时间',
                            `pay_notifyurl` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '商家异步通知地址',
                            `pay_callbackurl` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商家页面通知地址',
                            `order_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '订单状态: 0 未支付 1 已支付 2 超时订单  3-失败订单',
                            `product_name` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商品名称',
                            `product_desc` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商品描述',
                            `product_num` int DEFAULT NULL COMMENT '商品数量',
                            `apply_ip` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '提交的ip',
                            `notice_flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否通知成功',
                            `notice_num` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '异步通知次数',
                            `attach` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '商家附加字段,原样返回',
                            `last_notify_time` int NOT NULL DEFAULT '11' COMMENT '最后补发时间',
                            `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型  0-未回调 1-系统回调  2-手动回调',
                            `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '备注',
                            `address_id` int DEFAULT NULL COMMENT '地址id',
                            `address` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '地址',
                            `img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '地址二维码路径',
                            `pay_username` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '付款人姓名',
                            `part_pay_time` int DEFAULT NULL COMMENT '部分付款时间',
                            `create_time` int DEFAULT NULL COMMENT '创建时间',
                            `update_time` int DEFAULT NULL COMMENT '更新时间',
                            `delete_time` int DEFAULT NULL COMMENT '删除时间',
                            PRIMARY KEY (`id`) USING BTREE,
                            UNIQUE KEY `plat_order_sn` (`plat_order_sn`) USING BTREE,
                            UNIQUE KEY `merchant_order_sn` (`merchant_order_sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of ea_order
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_own_address
-- ----------------------------
DROP TABLE IF EXISTS `ea_own_address`;
CREATE TABLE `ea_own_address` (
                                  `id` int NOT NULL AUTO_INCREMENT,
                                  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '地址',
                                  `merchant_id` int NOT NULL DEFAULT '0' COMMENT '商户id',
                                  `merchantname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户名称',
                                  `chain_type` tinyint(1) DEFAULT NULL COMMENT '1-TRC20  2-ERC20',
                                  `img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '二维码',
                                  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
                                  `allocation_time` int DEFAULT NULL,
                                  `create_time` int DEFAULT NULL,
                                  `update_time` int DEFAULT NULL,
                                  `delete_time` int DEFAULT NULL,
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of ea_own_address
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_recharge_order
-- ----------------------------
DROP TABLE IF EXISTS `ea_recharge_order`;
CREATE TABLE `ea_recharge_order` (
                                     `id` int unsigned NOT NULL AUTO_INCREMENT,
                                     `merchant_id` int NOT NULL COMMENT '商户id',
                                     `merchantname` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户名称',
                                     `appid` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户appid',
                                     `recharge_order_sn` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '充值订单号',
                                     `pay_usdt` decimal(15,2) DEFAULT '0.00' COMMENT '需要支付的usdt',
                                     `actual_usdt` decimal(15,2) DEFAULT NULL COMMENT '实际支付的usdt',
                                     `chain_type` tinyint(1) DEFAULT NULL COMMENT '链类型 \r\n1-TRC20 \r\n2-ERC20',
                                     `pay_time` int unsigned NOT NULL DEFAULT '0' COMMENT '订单支付成功时间',
                                     `order_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '订单状态: 0 未支付 1 已支付 2 超时订单  3-失败订单',
                                     `apply_ip` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '提交的ip',
                                     `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '备注',
                                     `address_id` int DEFAULT NULL COMMENT '地址id',
                                     `from_address` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '付款地址',
                                     `receive_address` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '收款地址',
                                     `img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '地址二维码路径',
                                     `create_time` int DEFAULT NULL COMMENT '创建时间',
                                     `update_time` int DEFAULT NULL COMMENT '更新时间',
                                     `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                     PRIMARY KEY (`id`) USING BTREE,
                                     UNIQUE KEY `recharge_order_sn` (`recharge_order_sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of ea_recharge_order
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_statistics
-- ----------------------------
DROP TABLE IF EXISTS `ea_statistics`;
CREATE TABLE `ea_statistics` (
                                 `id` int unsigned NOT NULL AUTO_INCREMENT,
                                 `merchant_id` int unsigned DEFAULT '0' COMMENT '商户id',
                                 `merchantname` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户名字',
                                 `statistics_time` int NOT NULL DEFAULT '0' COMMENT '日期',
                                 `in_money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '总收款额',
                                 `in_poundage` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '总收款手续费',
                                 `out_money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '总代付金额',
                                 `out_poundage` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '总代付手续费',
                                 `cz_money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '冲正金额',
                                 `cz_poundage` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '冲正手续费',
                                 `create_time` int NOT NULL DEFAULT '0' COMMENT '创建时间',
                                 `update_time` int DEFAULT NULL COMMENT '更新时间',
                                 `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='统计表';

-- ----------------------------
-- Records of ea_statistics
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_system_admin
-- ----------------------------
DROP TABLE IF EXISTS `ea_system_admin`;
CREATE TABLE `ea_system_admin` (
                                   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                   `auth_ids` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '角色权限ID',
                                   `head_img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '头像',
                                   `username` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '用户登录名',
                                   `password` char(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '用户登录密码',
                                   `phone` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '联系手机号',
                                   `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '' COMMENT '备注说明',
                                   `login_num` bigint unsigned DEFAULT '0' COMMENT '登录次数',
                                   `login_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'ip白名单',
                                   `sort` int DEFAULT '0' COMMENT '排序',
                                   `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用,)',
                                   `create_time` int DEFAULT NULL COMMENT '创建时间',
                                   `update_time` int DEFAULT NULL COMMENT '更新时间',
                                   `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                   PRIMARY KEY (`id`) USING BTREE,
                                   UNIQUE KEY `username` (`username`) USING BTREE,
                                   KEY `phone` (`phone`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='系统用户表';

-- ----------------------------
-- Records of ea_system_admin
-- ----------------------------
BEGIN;
INSERT INTO `ea_system_admin` VALUES (1, '1', '/static/admin/images/head.jpg', 'admin', 'ed696eb5bba1f7460585cc6975e6cf9bf24903dd', 'admin', 'admin', 33, NULL, 0, 1, 1623506530, 1704962951, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ea_system_auth
-- ----------------------------
DROP TABLE IF EXISTS `ea_system_auth`;
CREATE TABLE `ea_system_auth` (
                                  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                  `title` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '权限名称',
                                  `sort` int DEFAULT '0' COMMENT '排序',
                                  `status` tinyint unsigned DEFAULT '1' COMMENT '状态(1:禁用,2:启用)',
                                  `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '备注说明',
                                  `create_time` int DEFAULT NULL COMMENT '创建时间',
                                  `update_time` int DEFAULT NULL COMMENT '更新时间',
                                  `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                  PRIMARY KEY (`id`) USING BTREE,
                                  UNIQUE KEY `title` (`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='系统权限表';

-- ----------------------------
-- Records of ea_system_auth
-- ----------------------------
BEGIN;
INSERT INTO `ea_system_auth` VALUES (1, '超管', 1, 1, '超管', 1588921753, 1624888010, NULL);
INSERT INTO `ea_system_auth` VALUES (6, '游客权限', 0, 1, '', 1588227513, 1589591751, 1589591751);
INSERT INTO `ea_system_auth` VALUES (7, '测试', 0, 1, '测试', 1623808641, 1649258961, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ea_system_config
-- ----------------------------
DROP TABLE IF EXISTS `ea_system_config`;
CREATE TABLE `ea_system_config` (
                                    `id` int unsigned NOT NULL AUTO_INCREMENT,
                                    `name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '变量名',
                                    `group` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '分组',
                                    `value` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT '变量值',
                                    `remark` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '' COMMENT '备注信息',
                                    `sort` int DEFAULT '0',
                                    `create_time` int DEFAULT NULL COMMENT '创建时间',
                                    `update_time` int DEFAULT NULL COMMENT '更新时间',
                                    PRIMARY KEY (`id`) USING BTREE,
                                    UNIQUE KEY `name` (`name`) USING BTREE,
                                    KEY `group` (`group`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='系统配置表';

-- ----------------------------
-- Records of ea_system_config
-- ----------------------------
BEGIN;
INSERT INTO `ea_system_config` VALUES (65, 'logo_title', 'site', 'UPay', 'LOGO标题', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (66, 'logo_image', 'site', '/favicon.ico', 'logo图片', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (67, 'site_url', 'site', 'web.upay.ink', '站点地址', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (68, 'site_name', 'site', 'UPay', '站点名称', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (88, 'single_max_money', 'riskconfig', '49999', '单笔最大金额', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (89, 'day_max_money', 'riskconfig', '5000000000', '单日总金额', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (92, 'pay_min_money', 'riskconfig', '0.01', '商户订单单笔最小金额', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (93, 'time_out', 'riskconfig', '30', '订单超时时间', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (94, 'pay_max_money', 'riskconfig', '1000000', '商户订单单笔最大金额', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (95, 'df_min_money', 'riskconfig', '1', '代付单笔最小金额', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (96, 'df_max_money', 'riskconfig', '10000', '代付单笔最大金额', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (97, 'min_trx', 'riskconfig', '13.9', 'TRX转账最低能量', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (98, 'min_eth', 'riskconfig', '0.0035', 'ETH转账最低能量', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (101, 'merchant_rate', 'riskconfig', '0.02', '商户默认费率', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (109, 'withdraw_poundage', 'riskconfig', '0.51', '提现手续费', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (113, 'shiyongfei', 'riskconfig', '0.5', '收款手续费', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (115, 'erc_withdraw_poundage', 'riskconfig', '12', 'ERC20提现手续费', 0, NULL, NULL);
INSERT INTO `ea_system_config` VALUES (118, 'money_model', 'riskconfig', '2', '货币显示', 0, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ea_system_log_202401
-- ----------------------------
DROP TABLE IF EXISTS `ea_system_log_202401`;
CREATE TABLE `ea_system_log_202401` (
                                        `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
                                        `admin_id` int unsigned DEFAULT '0' COMMENT '管理员ID',
                                        `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
                                        `method` varchar(50) NOT NULL COMMENT '请求方法',
                                        `title` varchar(100) DEFAULT '' COMMENT '日志标题',
                                        `content` text NOT NULL COMMENT '内容',
                                        `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
                                        `useragent` varchar(255) DEFAULT '' COMMENT 'User-Agent',
                                        `create_time` int DEFAULT NULL COMMENT '操作时间',
                                        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=636 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='后台操作日志表 - 202401';

-- ----------------------------
-- Table structure for ea_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `ea_system_menu`;
CREATE TABLE `ea_system_menu` (
                                  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                  `pid` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父id',
                                  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '名称',
                                  `icon` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
                                  `href` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '链接',
                                  `params` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '' COMMENT '链接参数',
                                  `target` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
                                  `sort` int DEFAULT '0' COMMENT '菜单排序',
                                  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
                                  `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
                                  `create_time` int DEFAULT NULL COMMENT '创建时间',
                                  `update_time` int DEFAULT NULL COMMENT '更新时间',
                                  `delete_time` int DEFAULT NULL COMMENT '删除时间',
                                  PRIMARY KEY (`id`) USING BTREE,
                                  KEY `title` (`title`) USING BTREE,
                                  KEY `href` (`href`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=292 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='系统菜单表';

-- ----------------------------
-- Records of ea_system_menu
-- ----------------------------
BEGIN;
INSERT INTO `ea_system_menu` VALUES (227, 99999999, '后台首页', 'fa fa-home', 'index/welcome', '', '_self', 0, 1, NULL, 1573185011, 1573120497, NULL);
INSERT INTO `ea_system_menu` VALUES (228, 0, '系统管理', 'fa fa-cog', '', '', '_self', 10, 1, '', 1573185011, 1623506574, NULL);
INSERT INTO `ea_system_menu` VALUES (244, 228, '管理员管理', 'fa fa-user-circle-o', 'system.admin/index', '', '_self', 12, 1, '', 1573185011, 1624126712, NULL);
INSERT INTO `ea_system_menu` VALUES (248, 228, '上传管理', 'fa fa-arrow-up', 'system.uploadfile/index', '', '_self', 0, 0, '', 1573542953, 1624536593, NULL);
INSERT INTO `ea_system_menu` VALUES (252, 228, '快捷入口', 'fa fa-bookmark', 'system.quick/index', '', '_self', 0, 0, '', 1589623683, 1624536590, NULL);
INSERT INTO `ea_system_menu` VALUES (253, 228, '日志管理', 'fa fa-file', 'system.log/index', '', '_self', 0, 1, '', 1589623684, 1589623684, NULL);
INSERT INTO `ea_system_menu` VALUES (254, 228, '新版文档地址', 'fa fa-book', 'https://www.upay.ink', '', '_blank', 0, 1, '', 1589623684, 1589623684, NULL);
INSERT INTO `ea_system_menu` VALUES (255, 0, '地址管理', 'fa fa-list-ul', 'address.address/index', '', '_self', 9, 1, '', 1623567345, 1623567345, NULL);
INSERT INTO `ea_system_menu` VALUES (256, 255, '地址列表', 'fa fa-list-ul', 'address.address/index', '', '_self', 10, 1, '', 1623567410, 1623567410, NULL);
INSERT INTO `ea_system_menu` VALUES (258, 0, '商户管理', 'fa fa-list', '', '', '_self', 0, 1, '', 1623567167, 1623567217, NULL);
INSERT INTO `ea_system_menu` VALUES (259, 258, '商户列表', 'fa fa-list-ul', 'merchant.merchant/index', '', '_self', 10, 1, '', 1623567410, 1623567410, NULL);
INSERT INTO `ea_system_menu` VALUES (260, 0, '订单管理', 'fa fa-list', '', '', '_self', 0, 1, '', 1624081422, 1624081422, NULL);
INSERT INTO `ea_system_menu` VALUES (261, 260, '所有订单', 'fa fa-clipboard', 'order.order/index', '', '_self', 9, 1, '', 1623567410, 1623567410, NULL);
INSERT INTO `ea_system_menu` VALUES (267, 255, '转账记录', 'fa fa-file-text', 'address.addresstransfer/index', '', '_self', 9, 1, '', 1573542953, 1623660474, NULL);
INSERT INTO `ea_system_menu` VALUES (268, 258, '商户账变', 'fa fa-exchange', 'merchant.merchantmoneychange/index', '', '_self', 9, 1, '', 1573542953, 1623660474, NULL);
INSERT INTO `ea_system_menu` VALUES (270, 0, '代付提币', 'fa fa-user', '', '', '_self', 0, 1, '', 1573185011, 1623506576, NULL);
INSERT INTO `ea_system_menu` VALUES (271, 270, '所有代付', 'fa fa-th-large', 'df.withdraw/index', '', '_self', 10, 1, '', 1573542953, 1623660474, NULL);
INSERT INTO `ea_system_menu` VALUES (285, 228, '风控设置', 'fa fa-shield', 'system.riskconfig/index', '', '_self', 6, 1, '', 1624081422, 1624081422, NULL);
INSERT INTO `ea_system_menu` VALUES (291, 270, '所有提币', 'fa fa-arrow-circle-up', 'df.tibi/index', '', '_self', 9, 1, '', 1573542953, 1623660474, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ea_tibi
-- ----------------------------
DROP TABLE IF EXISTS `ea_tibi`;
CREATE TABLE `ea_tibi` (
                           `id` int NOT NULL AUTO_INCREMENT,
                           `from_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
                           `to_address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
                           `key` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
                           `status` tinyint(1) NOT NULL DEFAULT '0',
                           `remark` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
                           `create_time` int DEFAULT NULL,
                           `update_time` int DEFAULT NULL,
                           `delete_time` int DEFAULT NULL,
                           PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of ea_tibi
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ea_withdraw
-- ----------------------------
DROP TABLE IF EXISTS `ea_withdraw`;
CREATE TABLE `ea_withdraw` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `appid` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户号',
                               `merchant_withdraw_sn` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户提现订单号',
                               `plat_withdraw_sn` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '平台提现订单号',
                               `merchant_id` int NOT NULL DEFAULT '0' COMMENT '商户id',
                               `merchantname` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '商户名称',
                               `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现类型 1-手动提现  2-API提现 3-自动提现',
                               `chain_type` tinyint(1) DEFAULT NULL COMMENT '链类型 \r\n1-TRC20 \r\n2-ERC20',
                               `money` decimal(8,2) NOT NULL COMMENT '提现金额',
                               `poundage` decimal(10,2) DEFAULT NULL COMMENT '提现手续费',
                               `receive_address` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '收款地址',
                               `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '结算状态  0-处理中 1-代付成功  2--代付失败',
                               `deal_flag` tinyint(1) DEFAULT '0' COMMENT '是否执行过了 0-否 1-是',
                               `flag` int DEFAULT '0',
                               `apply_time` int DEFAULT NULL COMMENT '抢单时间',
                               `submit_time` int DEFAULT NULL COMMENT '提交打款凭证时间',
                               `give_time` int DEFAULT NULL COMMENT '结算时间',
                               `pay_img` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT '付款凭证 以 | 做分割符号',
                               `apply_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '提交的ip',
                               `attach` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT '商家附加字段,原样返回',
                               `withdraw_notifyurl` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '异步通知地址',
                               `notice_flag` tinyint(1) DEFAULT '0' COMMENT '是否通知成功',
                               `notice_num` int DEFAULT '0' COMMENT '异步通知次数',
                               `last_notify_time` int DEFAULT '11' COMMENT '上次推送时间',
                               `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '备注',
                               `check_remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '审核备注',
                               `create_time` int DEFAULT NULL COMMENT '申请时间',
                               `update_time` int DEFAULT NULL COMMENT '更新时间',
                               `delete_time` int DEFAULT NULL COMMENT '删除时间',
                               PRIMARY KEY (`id`) USING BTREE,
                               UNIQUE KEY `plat_withdraw_sn` (`plat_withdraw_sn`) USING BTREE,
                               UNIQUE KEY `merchant_withdraw_sn` (`merchant_withdraw_sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='码商佣金提现表';

-- ----------------------------
-- Records of ea_withdraw
-- ----------------------------
BEGIN;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

