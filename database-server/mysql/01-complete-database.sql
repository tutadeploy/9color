-- MySQL dump 10.13  Distrib 5.7.44, for Linux (x86_64)
--
-- Host: localhost    Database: 6ui
-- ------------------------------------------------------
-- Server version	5.7.44-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `system_auth`
--

DROP TABLE IF EXISTS `system_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_auth` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL COMMENT '权限名称',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '权限状态',
  `sort` bigint(20) unsigned DEFAULT '0' COMMENT '排序权重',
  `desc` varchar(255) DEFAULT '' COMMENT '备注说明',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_auth_status` (`status`) USING BTREE,
  KEY `index_system_auth_title` (`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='系统-权限';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_auth_node`
--

DROP TABLE IF EXISTS `system_auth_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_auth_node` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auth` bigint(20) unsigned DEFAULT NULL COMMENT '角色',
  `node` varchar(200) DEFAULT NULL COMMENT '节点',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_auth_auth` (`auth`) USING BTREE,
  KEY `index_system_auth_node` (`node`(191)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2802 DEFAULT CHARSET=utf8mb4 COMMENT='系统-权限-授权';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_auth_node_copy`
--

DROP TABLE IF EXISTS `system_auth_node_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_auth_node_copy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auth` bigint(20) unsigned DEFAULT NULL COMMENT '角色',
  `node` varchar(200) DEFAULT NULL COMMENT '节点',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_auth_auth` (`auth`) USING BTREE,
  KEY `index_system_auth_node` (`node`(191)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=766 DEFAULT CHARSET=utf8mb4 COMMENT='系统-权限-授权';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_config`
--

DROP TABLE IF EXISTS `system_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '' COMMENT '配置名',
  `value` varchar(500) DEFAULT '' COMMENT '配置值',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_config_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COMMENT='系统-配置';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_data`
--

DROP TABLE IF EXISTS `system_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_data` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '配置名',
  `value` longtext COMMENT '配置值',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_data_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='系统-数据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_log`
--

DROP TABLE IF EXISTS `system_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(200) NOT NULL DEFAULT '' COMMENT '当前操作节点',
  `geoip` varchar(15) NOT NULL DEFAULT '' COMMENT '操作者IP地址',
  `action` varchar(200) NOT NULL DEFAULT '' COMMENT '操作行为名称',
  `content` varchar(1024) NOT NULL DEFAULT '' COMMENT '操作内容描述',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '操作人用户名',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1912 DEFAULT CHARSET=utf8mb4 COMMENT='系统-日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_menu`
--

DROP TABLE IF EXISTS `system_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned DEFAULT '0' COMMENT '父ID',
  `title` varchar(100) DEFAULT '' COMMENT '名称',
  `node` varchar(200) DEFAULT '' COMMENT '节点代码',
  `icon` varchar(100) DEFAULT '' COMMENT '菜单图标',
  `url` varchar(400) DEFAULT '' COMMENT '链接',
  `params` varchar(500) DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) DEFAULT '_self' COMMENT '打开方式',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '菜单排序',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_menu_node` (`node`(191)) USING BTREE,
  KEY `index_system_menu_status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COMMENT='系统-菜单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_queue`
--

DROP TABLE IF EXISTS `system_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名称',
  `data` longtext NOT NULL COMMENT '执行参数',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '任务状态(1新任务,2处理中,3成功,4失败)',
  `preload` varchar(500) DEFAULT '' COMMENT '执行内容',
  `time` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '执行时间',
  `double` tinyint(1) DEFAULT '1' COMMENT '单例模式',
  `desc` varchar(500) DEFAULT '' COMMENT '状态描述',
  `start_at` varchar(20) DEFAULT '' COMMENT '开始时间',
  `end_at` varchar(20) DEFAULT '' COMMENT '结束时间',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_queue_double` (`double`) USING BTREE,
  KEY `index_system_queue_time` (`time`) USING BTREE,
  KEY `index_system_queue_title` (`title`) USING BTREE,
  KEY `index_system_queue_create_at` (`create_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统-任务';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_user`
--

DROP TABLE IF EXISTS `system_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT '' COMMENT '用户账号',
  `password` varchar(32) DEFAULT '' COMMENT '用户密码',
  `qq` varchar(16) DEFAULT '' COMMENT '联系QQ',
  `mail` varchar(32) DEFAULT '' COMMENT '联系邮箱',
  `phone` varchar(16) DEFAULT '' COMMENT '联系手机',
  `login_at` datetime DEFAULT NULL COMMENT '登录时间',
  `login_ip` varchar(255) DEFAULT '' COMMENT '登录IP',
  `login_num` bigint(20) unsigned DEFAULT '0' COMMENT '登录次数',
  `authorize` varchar(255) DEFAULT '' COMMENT '权限授权',
  `tags` varchar(255) DEFAULT '' COMMENT '用户标签',
  `desc` varchar(255) DEFAULT '' COMMENT '备注说明',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(0禁用,1启用)',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除(1删除,0未删)',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_system_user_username` (`username`) USING BTREE,
  KEY `index_system_user_status` (`status`) USING BTREE,
  KEY `index_system_user_deleted` (`is_deleted`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10118 DEFAULT CHARSET=utf8mb4 COMMENT='系统-用户';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_auto_dispatch_log`
--

DROP TABLE IF EXISTS `xy_auto_dispatch_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_auto_dispatch_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '订单ID',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `commission` decimal(10,2) DEFAULT NULL COMMENT '佣金金额',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '处理状态: success/error/insufficient_balance',
  `error_msg` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '错误信息',
  `execution_time` int(11) DEFAULT NULL COMMENT '执行耗时(毫秒)',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_create_time` (`create_time`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='自动派单处理日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_balance_log`
--

DROP TABLE IF EXISTS `xy_balance_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_balance_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '会员ID',
  `sid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '交易对象id',
  `oid` char(18) NOT NULL COMMENT '交易单号',
  `num` decimal(10,2) NOT NULL COMMENT '交易金额',
  `type` int(2) NOT NULL COMMENT '交易类型 0系统 1充值 2交易 3返佣 4强制交易 5推广返佣 6下级交易返佣  7提现,11彩金',
  `status` int(1) DEFAULT '1' COMMENT '收入1 支出2',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `f_lv` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oid` (`oid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='会员-收支明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_bank_list`
--

DROP TABLE IF EXISTS `xy_bank_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_bank_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(5) NOT NULL,
  `bankcode` varchar(15) NOT NULL COMMENT '银行编号',
  `bankname` varchar(255) NOT NULL COMMENT '银行名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COMMENT='提现银行编码表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_bankinfo`
--

DROP TABLE IF EXISTS `xy_bankinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_bankinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `bankname` varchar(100) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bankcode` varchar(20) NOT NULL,
  `cardnum` varchar(50) NOT NULL DEFAULT '' COMMENT '卡号',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `site` varchar(255) NOT NULL DEFAULT '' COMMENT '开户行地址',
  `tel` varchar(20) NOT NULL COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，1启用，0禁用',
  `address` varchar(255) DEFAULT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `ifsc` varchar(11) NOT NULL,
  `remark` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Uid` (`uid`),
  KEY `Cardnum` (`cardnum`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='会员-银行卡信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_banner`
--

DROP TABLE IF EXISTS `xy_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` text,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='首页轮播图';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_convey`
--

DROP TABLE IF EXISTS `xy_convey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_convey` (
  `id` char(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uid` int(10) NOT NULL COMMENT '会员ID',
  `ubalance` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `endtime` int(10) NOT NULL DEFAULT '0' COMMENT '完成交易时间',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '订单状态 0待付款 1交易完成 2用户取消  3强制完成 4强制取消  5交易冻结',
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金',
  `c_status` int(1) NOT NULL DEFAULT '0' COMMENT '佣金发放状态 0未发放 1已发放 2账号冻结',
  `add_id` int(11) NOT NULL COMMENT '收货地址',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `goods_count` int(2) NOT NULL DEFAULT '1' COMMENT '商品数量',
  `grouping_id` int(5) NOT NULL,
  `order_num` int(3) NOT NULL,
  `auto_dispatch` tinyint(1) DEFAULT NULL COMMENT '订单自动派单开关(NULL=使用用户默认设置)',
  `cooling_end_time` int(11) DEFAULT '0' COMMENT '冷却期结束时间',
  `dispatch_status` tinyint(1) DEFAULT '0' COMMENT '派单状态: 0=冷却中, 1=已派单, 2=手动派单',
  `manual_dispatch` tinyint(1) DEFAULT '0' COMMENT '是否手动派单',
  `version` int(11) DEFAULT '1' COMMENT '版本号，用于乐观锁',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员-订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_cs`
--

DROP TABLE IF EXISTS `xy_cs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_cs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tel` varchar(20) NOT NULL COMMENT '手机号',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `pwd` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(255) NOT NULL DEFAULT '' COMMENT '盐',
  `qq` varchar(20) NOT NULL COMMENT 'QQ号',
  `wechat` varchar(150) NOT NULL COMMENT '微信号',
  `qr_code` varchar(150) NOT NULL COMMENT '微信二维码',
  `btime` char(5) NOT NULL DEFAULT '0' COMMENT '上班时间',
  `etime` char(5) NOT NULL COMMENT '下班时间',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '账号状态 1启用 2禁用',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `url` varchar(255) DEFAULT NULL,
  `ico` varchar(50) NOT NULL,
  `remark` varchar(50) NOT NULL,
  `linktext` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='客服-用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_deal_elog`
--

DROP TABLE IF EXISTS `xy_deal_elog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_deal_elog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oid` char(18) NOT NULL COMMENT '相关订单',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `errmsg` varchar(255) NOT NULL COMMENT '错误信息',
  `addtime` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `oid` (`oid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易错误日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_deposit`
--

DROP TABLE IF EXISTS `xy_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_deposit` (
  `id` char(18) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '提现用户',
  `bk_id` int(11) NOT NULL COMMENT '银行卡信息',
  `num` decimal(12,2) NOT NULL COMMENT '提现金额',
  `addtime` int(10) NOT NULL COMMENT '提交时间',
  `endtime` int(10) NOT NULL DEFAULT '0' COMMENT '审核时间',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '订单状态 1待处理 2审核通过 3审核不通过',
  `type` varchar(36) DEFAULT NULL,
  `real_num` decimal(12,2) DEFAULT NULL,
  `shouxu` varchar(255) DEFAULT NULL,
  `trc20_address` varchar(34) NOT NULL,
  `erc20_address` varchar(34) NOT NULL,
  `tradeResult` varchar(10) NOT NULL,
  `tradeNo` varchar(30) NOT NULL,
  `applyDate` varchar(30) NOT NULL,
  `notifyDate` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员-余额提现表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_dispatch_monitor`
--

DROP TABLE IF EXISTS `xy_dispatch_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_dispatch_monitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `found_orders` int(11) DEFAULT '0' COMMENT '发现待处理订单数',
  `processed_orders` int(11) DEFAULT '0' COMMENT '成功处理订单数',
  `failed_orders` int(11) DEFAULT '0' COMMENT '失败订单数',
  `execution_time` int(11) DEFAULT NULL COMMENT '执行耗时(毫秒)',
  PRIMARY KEY (`id`),
  KEY `idx_check_time` (`check_time`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='自动派单监控记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_goods_cate`
--

DROP TABLE IF EXISTS `xy_goods_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_goods_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '商店名称',
  `bili` varchar(255) NOT NULL COMMENT '商品名称',
  `cate_info` varchar(255) DEFAULT '' COMMENT '商品描述',
  `goods_price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `cate_pic` varchar(120) DEFAULT '' COMMENT '商品展示图片',
  `cat_ico` varchar(50) NOT NULL,
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `status` int(1) DEFAULT '0' COMMENT '上架状态 0不上架 1上架',
  `min` varchar(255) DEFAULT NULL COMMENT '最小金额限制',
  `level_id` int(11) DEFAULT NULL,
  `deal_min_num` int(2) NOT NULL,
  `deal_max_num` int(3) NOT NULL,
  `deal_min_numbaifenbi` int(2) NOT NULL DEFAULT '0',
  `deal_max_numbaifenbi` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_goods_list`
--

DROP TABLE IF EXISTS `xy_goods_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_goods_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) NOT NULL COMMENT '商店名称',
  `en_shop_name` varchar(200) NOT NULL,
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `en_goods_name` varchar(200) NOT NULL,
  `goods_info` varchar(255) DEFAULT '' COMMENT '商品描述',
  `en_goods_info` varchar(255) NOT NULL,
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_pic` varchar(120) DEFAULT NULL COMMENT '商品展示图片',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '上架状态 0不上架 1上架',
  `cid` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1973 DEFAULT CHARSET=utf8mb4 COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_grouping`
--

DROP TABLE IF EXISTS `xy_grouping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_grouping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NOT NULL,
  `title` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_index_msg`
--

DROP TABLE IF EXISTS `xy_index_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_index_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '',
  `en_title` varchar(20) NOT NULL,
  `fr_title` varchar(30) NOT NULL,
  `es_title` varchar(30) NOT NULL,
  `pt_title` varchar(30) NOT NULL,
  `content` text NOT NULL COMMENT '文本内容',
  `en_content` text NOT NULL,
  `fr_content` text NOT NULL,
  `es_content` text NOT NULL,
  `pt_content` text NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1平台公告 2平台简介 3抢单规则 4代理合作 5常见问题',
  `addtime` int(10) NOT NULL COMMENT '发表时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0发布 1不发布',
  `author` varchar(10) NOT NULL DEFAULT '' COMMENT '作者',
  `kr_title` varchar(30) NOT NULL,
  `kr_content` text NOT NULL,
  `jp_title` varchar(30) NOT NULL,
  `jp_content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='首页内容表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_io_log`
--

DROP TABLE IF EXISTS `xy_io_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_io_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oid` char(18) NOT NULL,
  `amount` decimal(7,2) NOT NULL COMMENT '支付金额',
  `tran_amount` decimal(7,2) NOT NULL COMMENT '实收金额',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '1收入(用户充值) 2支出(用户提现)',
  `addtime` int(10) unsigned NOT NULL COMMENT '交易时间',
  PRIMARY KEY (`id`),
  KEY `oid` (`oid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='平台收支记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_level`
--

DROP TABLE IF EXISTS `xy_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `order_num` int(12) DEFAULT NULL COMMENT '接单限制',
  `num` decimal(18,2) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `bili` decimal(18,4) DEFAULT NULL COMMENT '比例',
  `level` int(11) DEFAULT NULL COMMENT 'd等级',
  `tixian_ci` int(11) DEFAULT NULL COMMENT '提现次数',
  `tixian_min` decimal(18,2) DEFAULT NULL,
  `tixian_max` decimal(18,2) DEFAULT NULL COMMENT '提现最大金额',
  `num_min` decimal(18,2) DEFAULT NULL,
  `cids` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tixian_nim_order` int(11) DEFAULT NULL COMMENT '提现最少完成订单数',
  `auto_vip_xu_num` int(11) DEFAULT NULL COMMENT '自动升级vip需要邀请的人',
  `tixian_shouxu` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' COMMENT '提现手续费',
  `pic` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_lixibao`
--

DROP TABLE IF EXISTS `xy_lixibao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_lixibao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `num` decimal(18,5) DEFAULT NULL,
  `addtime` int(10) DEFAULT NULL,
  `endtime` int(10) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `yuji_num` decimal(18,5) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `is_qu` int(11) DEFAULT '0',
  `shouxu` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `real_num` decimal(18,5) DEFAULT '0.00000',
  `is_sy` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_lixibao_list`
--

DROP TABLE IF EXISTS `xy_lixibao_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_lixibao_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `bili` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `min_num` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_num` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addtime` int(10) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `shouxu` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_member_address`
--

DROP TABLE IF EXISTS `xy_member_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_member_address` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '收货姓名',
  `tel` varchar(20) NOT NULL DEFAULT '' COMMENT '收货手机',
  `area` varchar(255) NOT NULL COMMENT '地区',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址-详情',
  `is_default` tinyint(1) unsigned DEFAULT '0' COMMENT '默认地址',
  `addtime` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_xy_member_address_uid` (`uid`) USING BTREE,
  KEY `index_xy_member_address_is_default` (`is_default`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='会员收货地址';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_message`
--

DROP TABLE IF EXISTS `xy_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '接收人ID',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '发送人ID',
  `title` varchar(150) NOT NULL COMMENT '信息标题',
  `content` text NOT NULL COMMENT '正文内容',
  `addtime` int(10) NOT NULL COMMENT '发表时间',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '消息类型 1公告 2通知',
  `status` int(1) NOT NULL COMMENT '是否阅读状态',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='会员-消息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_msg`
--

DROP TABLE IF EXISTS `xy_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '',
  `content` text NOT NULL COMMENT '文本内容',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1平台公告 2平台简介 3抢单规则 4代理合作 5常见问题',
  `addtime` int(10) NOT NULL COMMENT '发表时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0发布 1不发布',
  `author` varchar(10) NOT NULL DEFAULT '' COMMENT '作者',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_pay`
--

DROP TABLE IF EXISTS `xy_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ico` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `min` double(18,2) DEFAULT NULL,
  `max` double(18,2) DEFAULT NULL,
  `ewm` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(2) DEFAULT NULL,
  `tuijian` int(1) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(2) NOT NULL,
  `charge` int(3) NOT NULL COMMENT '手续费',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_reads`
--

DROP TABLE IF EXISTS `xy_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_reads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消息ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '读取时间',
  PRIMARY KEY (`id`),
  KEY `mid-uid` (`mid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员-消息读取记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_recharge`
--

DROP TABLE IF EXISTS `xy_recharge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_recharge` (
  `id` char(18) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `real_name` varchar(15) NOT NULL DEFAULT '' COMMENT '充值姓名',
  `tel` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `num` decimal(12,2) NOT NULL COMMENT '充值金额',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '支付方式 1微信 2支付宝 3qq',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '打款凭证',
  `addtime` int(10) NOT NULL COMMENT '下单时间',
  `endtime` int(10) NOT NULL DEFAULT '0' COMMENT '处理时间',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '订单状态 1下单成功 2充值成功 3充值失败',
  `pay_name` varchar(255) DEFAULT NULL,
  `is_vip` int(11) DEFAULT '0',
  `level` int(11) DEFAULT NULL,
  `pay_type` varchar(36) DEFAULT NULL,
  `charge` int(3) NOT NULL COMMENT '充值手续费',
  `payInfo` varchar(200) NOT NULL,
  `orderNo` varchar(20) NOT NULL,
  `orderDate` varchar(20) NOT NULL,
  `notifyDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员-充值表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_reward_log`
--

DROP TABLE IF EXISTS `xy_reward_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_reward_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oid` char(18) NOT NULL COMMENT '订单号',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产生交易用户',
  `sid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '交易对象',
  `num` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易数额',
  `lv` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '级差',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '订单类型 1充值订单(推广返佣) 2交易订单(交易返佣)',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '佣金发放状态 0自动发放 1未发放 2已发放',
  `addtime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '处理时间',
  PRIMARY KEY (`id`),
  KEY `oid` (`oid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='订单佣金发放记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_script`
--

DROP TABLE IF EXISTS `xy_script`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_script` (
  `script` text NOT NULL COMMENT '代码块',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_shop_goods_cate`
--

DROP TABLE IF EXISTS `xy_shop_goods_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_shop_goods_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '商店名称',
  `bili` varchar(255) NOT NULL COMMENT '商品名称',
  `cate_info` varchar(255) DEFAULT '' COMMENT '商品描述',
  `goods_price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `cate_pic` varchar(120) DEFAULT '' COMMENT '商品展示图片',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `status` int(1) DEFAULT '0' COMMENT '上架状态 0不上架 1上架',
  `min` varchar(255) DEFAULT NULL COMMENT '最小金额限制',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_shop_goods_list`
--

DROP TABLE IF EXISTS `xy_shop_goods_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_shop_goods_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) NOT NULL COMMENT '商店名称',
  `en_shop_name` varchar(255) NOT NULL,
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `en_goods_name` varchar(255) NOT NULL,
  `goods_info` varchar(5000) DEFAULT '' COMMENT '商品描述',
  `en_goods_info` varchar(255) NOT NULL,
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_pic` varchar(120) DEFAULT '' COMMENT '商品展示图片',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '上架状态 0不上架 1上架',
  `cid` int(11) DEFAULT '1',
  `is_tj` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1973 DEFAULT CHARSET=utf8mb4 COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_shop_order`
--

DROP TABLE IF EXISTS `xy_shop_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_shop_order` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL COMMENT '商品id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `price` decimal(15,3) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '上架状态 0不上架 1上架',
  `num` int(11) DEFAULT NULL,
  `price2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id` char(18) CHARACTER SET utf8mb4 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_signlog`
--

DROP TABLE IF EXISTS `xy_signlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_signlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `signtime` int(10) NOT NULL,
  `money` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1104 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_user_error`
--

DROP TABLE IF EXISTS `xy_user_error`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_user_error` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `addtime` int(11) NOT NULL COMMENT '记录时间',
  `oid` char(18) DEFAULT '' COMMENT '交易单号',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '记录类型 1解封 2违规操作 3冻结',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COMMENT='会员-违规操作记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_users`
--

DROP TABLE IF EXISTS `xy_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tel` varchar(20) NOT NULL,
  `username` varchar(36) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(36) NOT NULL DEFAULT '' COMMENT '昵称',
  `email` varchar(20) NOT NULL COMMENT '邮箱',
  `pwd` char(40) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(16) NOT NULL DEFAULT '' COMMENT '密码盐',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级ID',
  `signiture` varchar(120) NOT NULL DEFAULT '' COMMENT '个性签名',
  `pwd_error_num` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '密码错误次数',
  `allow_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许登录时间',
  `real_name` varchar(36) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `id_card_num` char(18) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `top_pic` varchar(96) NOT NULL DEFAULT '' COMMENT '身份证正面图',
  `bot_pic` varchar(96) NOT NULL DEFAULT '' COMMENT '身份证背面图',
  `id_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '实名认证状态，0未审核，1审核通过，2审核不通过',
  `invite_code` char(6) NOT NULL DEFAULT '' COMMENT '邀请码',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '状态，1启用，2禁用',
  `real_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '实名时间',
  `pwd2` char(40) NOT NULL DEFAULT '' COMMENT '提现密码',
  `salt2` char(16) NOT NULL DEFAULT '' COMMENT '提现密码盐',
  `headpic` varchar(3000) NOT NULL DEFAULT '' COMMENT '头像',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit_score` int(11) NOT NULL DEFAULT '100' COMMENT '信用分',
  `freeze_balance` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '账号冻结金额',
  `login_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否登录状态，1：是，0否',
  `recharge_num` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT '日充值金额',
  `deposit_num` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT '日提现金额',
  `deal_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '交易状态，0交易冻结，1停止交易，2等待交易，3交易中',
  `deal_error` tinyint(1) NOT NULL DEFAULT '0' COMMENT '违规次数',
  `deal_reward_count` int(11) NOT NULL DEFAULT '0' COMMENT '奖励交易次数',
  `deal_count` int(4) NOT NULL DEFAULT '0' COMMENT '当日交易次数',
  `deal_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后交易日期(年月日)',
  `active` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '激活状态，0未激活(首次充值发放推广奖励)，1已激活',
  `childs` int(11) NOT NULL DEFAULT '0' COMMENT '直推用户数量',
  `kouchu_balance` decimal(15,2) DEFAULT NULL COMMENT '扣除金额',
  `kouchu_balance_uid` int(11) DEFAULT NULL,
  `show_td` int(11) DEFAULT '1',
  `show_cz` int(11) DEFAULT '1',
  `show_tx` int(11) DEFAULT '1',
  `show_tel` int(11) DEFAULT '1',
  `show_num` int(11) DEFAULT '1',
  `show_tel2` int(11) DEFAULT '1',
  `wx_ewm` varchar(255) DEFAULT NULL,
  `zfb_ewm` varchar(255) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `lixibao_balance` decimal(15,4) DEFAULT '0.0000' COMMENT '利息宝金额',
  `lixibao_dj_balance` decimal(15,4) DEFAULT '0.0000' COMMENT '利息宝冻结金额',
  `ip` varchar(128) DEFAULT NULL,
  `is_jia` int(11) DEFAULT '0',
  `activetime` int(11) NOT NULL,
  `deal_min_num` int(4) NOT NULL,
  `deal_max_num` int(4) NOT NULL,
  `trc20` varchar(34) NOT NULL,
  `pipei_dan` text NOT NULL,
  `pipei_type` int(1) NOT NULL,
  `pipei_grouping` int(4) NOT NULL,
  `autoorder` int(1) NOT NULL DEFAULT '0' COMMENT '联单开关',
  `default_auto_dispatch` tinyint(1) DEFAULT '1' COMMENT '用户默认自动派单设置',
  `version` int(11) DEFAULT '1' COMMENT '版本号，用于乐观锁',
  PRIMARY KEY (`id`),
  UNIQUE KEY `invite_code` (`invite_code`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='会员-用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xy_verify_msg`
--

DROP TABLE IF EXISTS `xy_verify_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xy_verify_msg` (
  `tel` char(11) NOT NULL DEFAULT '' COMMENT '用户ID',
  `msg` char(5) NOT NULL DEFAULT '' COMMENT '验证码',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型，1注册,2修改密码，3修改二级密码',
  PRIMARY KEY (`tel`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database '6ui'
--
/*!50003 DROP PROCEDURE IF EXISTS `ProcessAllExpiredOrders` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`app`@`%` PROCEDURE `ProcessAllExpiredOrders`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_order_id VARCHAR(20);
    DECLARE v_found_count INT DEFAULT 0;
    DECLARE v_processed_count INT DEFAULT 0;
    DECLARE v_failed_count INT DEFAULT 0;
    DECLARE v_start_time BIGINT;
    DECLARE v_execution_time INT;
    
    DECLARE order_cursor CURSOR FOR 
        SELECT id FROM xy_convey 
        WHERE auto_dispatch = 1 
        AND dispatch_status = 0 
        AND cooling_end_time > 0 
        AND cooling_end_time <= UNIX_TIMESTAMP() 
        AND status = 0;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    SET v_start_time = UNIX_TIMESTAMP(NOW(3)) * 1000;
    
    SELECT COUNT(*) INTO v_found_count FROM xy_convey 
    WHERE auto_dispatch = 1 
    AND dispatch_status = 0 
    AND cooling_end_time > 0 
    AND cooling_end_time <= UNIX_TIMESTAMP() 
    AND status = 0;
    
    OPEN order_cursor;
    
    process_loop: LOOP
        FETCH order_cursor INTO v_order_id;
        IF done THEN
            LEAVE process_loop;
        END IF;
        
        CALL ProcessAutoDispatchOrder(v_order_id);
        
        IF (SELECT COUNT(*) FROM xy_auto_dispatch_log 
            WHERE order_id = v_order_id AND status = 'success' 
            AND create_time >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)) > 0 THEN
            SET v_processed_count = v_processed_count + 1;
        ELSE
            SET v_failed_count = v_failed_count + 1;
        END IF;
        
    END LOOP;
    
    CLOSE order_cursor;
    
    SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
    
    INSERT INTO xy_dispatch_monitor (found_orders, processed_orders, failed_orders, execution_time) 
    VALUES (v_found_count, v_processed_count, v_failed_count, v_execution_time);
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ProcessAutoDispatchOrder` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`app`@`%` PROCEDURE `ProcessAutoDispatchOrder`(IN order_id VARCHAR(20))
BEGIN
    DECLARE v_user_id INT DEFAULT NULL;
    DECLARE v_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_commission DECIMAL(10,2) DEFAULT 0;
    DECLARE v_balance DECIMAL(10,2) DEFAULT 0;
    DECLARE v_version INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_start_time BIGINT DEFAULT 0;
    DECLARE v_execution_time INT DEFAULT 0;
    
    SET v_start_time = UNIX_TIMESTAMP(NOW(3)) * 1000;
    
    START TRANSACTION;
    
    SELECT uid, num, commission, version INTO v_user_id, v_amount, v_commission, v_version
    FROM xy_convey 
    WHERE id = order_id 
    AND auto_dispatch = 1 
    AND dispatch_status = 0 
    AND cooling_end_time > 0
    AND cooling_end_time <= UNIX_TIMESTAMP() 
    AND status = 0
    FOR UPDATE;
    
    IF v_user_id IS NOT NULL THEN
        UPDATE xy_convey 
        SET dispatch_status = 999, version = version + 1 
        WHERE id = order_id AND version = v_version;
        
        SET v_affected_rows = ROW_COUNT();
        
        IF v_affected_rows > 0 THEN
            SELECT balance INTO v_balance FROM xy_users WHERE id = v_user_id FOR UPDATE;
            
            IF v_balance >= v_amount THEN
                UPDATE xy_users 
                SET balance = balance - v_amount,
                    freeze_balance = freeze_balance + v_amount + v_commission
                WHERE id = v_user_id;
                
                UPDATE xy_users 
                SET balance = balance + v_amount + v_commission,
                    freeze_balance = freeze_balance - v_amount - v_commission,
                    deal_status = 1
                WHERE id = v_user_id;
                
                UPDATE xy_convey 
                SET status = 1, dispatch_status = 1, c_status = 1, 
                    endtime = UNIX_TIMESTAMP(), version = version + 1
                WHERE id = order_id;
                
                INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                VALUES (v_user_id, order_id, v_amount, 2, 2, UNIX_TIMESTAMP());
                
                INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                VALUES (v_user_id, order_id, v_commission, 3, 1, UNIX_TIMESTAMP());
                
                INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
                VALUES (order_id, v_user_id, v_amount, UNIX_TIMESTAMP(), 2);
                
                COMMIT;
                
                SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
                
                INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time) 
                VALUES (order_id, v_user_id, v_amount, v_commission, 'success', v_execution_time, NOW());
                
            ELSE
                UPDATE xy_convey 
                SET dispatch_status = 0, version = version + 1 
                WHERE id = order_id;
                
                UPDATE xy_users 
                SET deal_status = 1
                WHERE id = v_user_id;
                
                COMMIT;
                
                SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
                
                INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_msg, execution_time, create_time) 
                VALUES (order_id, v_user_id, v_amount, v_commission, 'insufficient_balance', 
                       CONCAT('需要:', v_amount, ', 余额:', v_balance), v_execution_time, NOW());
            END IF;
        ELSE
            ROLLBACK;
            INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
            VALUES (order_id, 'skipped', '订单已被其他进程处理', 
                    ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time), NOW());
        END IF;
    ELSE
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
        VALUES (order_id, 'not_found', '未找到符合条件的订单', 
                ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time), NOW());
    END IF;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-22 14:06:07

-- ===========================================
-- 基础配置数据插入
-- ===========================================

-- 插入自动派单相关配置
INSERT INTO system_config (name, value) VALUES 
('auto_dispatch_enabled', '1'),
('cooling_period_minutes', '30')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- 插入默认管理员账号
INSERT INTO system_user (id, username, password, status, authorize) VALUES 
(10000, 'admin', 'f3281083d698050945218976ac2f6e4a', 1, '1')
ON DUPLICATE KEY UPDATE password = VALUES(password);

-- 启用MySQL事件调度器
SET GLOBAL event_scheduler = ON;

-- 创建自动派单事件
DROP EVENT IF EXISTS auto_dispatch_event;
CREATE EVENT auto_dispatch_event
ON SCHEDULE EVERY 1 MINUTE
STARTS NOW()
DO CALL ProcessAllExpiredOrders();

-- 创建日志清理事件
DROP EVENT IF EXISTS cleanup_dispatch_logs;
CREATE EVENT cleanup_dispatch_logs
ON SCHEDULE EVERY 1 DAY
STARTS NOW()
DO DELETE FROM xy_auto_dispatch_log WHERE create_time < DATE_SUB(NOW(), INTERVAL 7 DAY);

SELECT '✅ 9Color数据库初始化完成！' as status;
