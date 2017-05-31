/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.1.10-MariaDB : Database - gts_data
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`gts_data` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

/*Table structure for table `gg_action` */

DROP TABLE IF EXISTS `gg_action`;

CREATE TABLE `gg_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `actor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(250) NOT NULL,
  `action` varchar(250) NOT NULL,
  `date` datetime NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8235 DEFAULT CHARSET=utf8;

/*Data for the table `gg_action` */

/*Table structure for table `gg_auth_assignment` */

DROP TABLE IF EXISTS `gg_auth_assignment`;

CREATE TABLE `gg_auth_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`id`,`itemid`,`userid`),
  KEY `itemid` (`itemid`),
  CONSTRAINT `gg_auth_assignment_ibfk_1` FOREIGN KEY (`itemid`) REFERENCES `gg_auth_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

/*Data for the table `gg_auth_assignment` */

/*Table structure for table `gg_auth_item` */

DROP TABLE IF EXISTS `gg_auth_item`;

CREATE TABLE `gg_auth_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8;

/*Data for the table `gg_auth_item` */

/*Table structure for table `gg_auth_item_child` */

DROP TABLE IF EXISTS `gg_auth_item_child`;

CREATE TABLE `gg_auth_item_child` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `child_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`parent_id`,`child_id`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  CONSTRAINT `gg_auth_item_child_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `gg_auth_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gg_auth_item_child_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `gg_auth_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=596 DEFAULT CHARSET=utf8;

/*Data for the table `gg_auth_item_child` */

/*Table structure for table `gg_bug` */

DROP TABLE IF EXISTS `gg_bug`;

CREATE TABLE `gg_bug` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `module_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模块ID',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目id',
  `story_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '需求id',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务id',
  `title` varchar(250) NOT NULL COMMENT 'bug标题',
  `steps` text NOT NULL COMMENT '重现步骤',
  `type` varchar(250) NOT NULL COMMENT '类型',
  `status` varchar(250) NOT NULL COMMENT 'bug状态',
  `level` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `opened_by` int(10) unsigned NOT NULL,
  `opened_date` datetime NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL COMMENT '当前指派',
  `assigned_date` datetime NOT NULL,
  `resolved_by` int(10) unsigned NOT NULL,
  `resolution` varchar(250) NOT NULL,
  `resolved_date` datetime NOT NULL,
  `closed_by` int(10) unsigned NOT NULL,
  `close_date` datetime NOT NULL,
  `last_edited_by` int(10) unsigned NOT NULL,
  `last_edited_date` datetime NOT NULL,
  `is_deleted` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8;

/*Data for the table `gg_bug` */

/*Table structure for table `gg_department` */

DROP TABLE IF EXISTS `gg_department`;

CREATE TABLE `gg_department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `path` varchar(250) NOT NULL DEFAULT '',
  `grade` tinyint(4) NOT NULL DEFAULT '1',
  `order` tinyint(4) DEFAULT '0',
  `is_enable` enum('0','1') NOT NULL DEFAULT '1' COMMENT '是否启用 0.停用 1.启用',
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `last_edited_by` int(11) NOT NULL DEFAULT '0',
  `last_edited_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `gg_department` */

/*Table structure for table `gg_duty` */

DROP TABLE IF EXISTS `gg_duty`;

CREATE TABLE `gg_duty` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `duty_user` int(10) unsigned DEFAULT '0' COMMENT '值班人',
  `create_time` datetime DEFAULT NULL COMMENT '开始值班时间',
  `end_time` datetime DEFAULT NULL COMMENT '下线时间',
  `duty_status` smallint(6) DEFAULT '1' COMMENT '值班状态(1表示离线;2表示上线)',
  `support_content` tinyint(3) unsigned DEFAULT '1' COMMENT '支持内容 1.网址  2.系统',
  `support_range` varchar(500) DEFAULT '' COMMENT '支持范围',
  `is_update` tinyint(4) DEFAULT NULL COMMENT '是否更新 1.是2.否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `gg_duty` */

/*Table structure for table `gg_file` */

DROP TABLE IF EXISTS `gg_file`;

CREATE TABLE `gg_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(250) NOT NULL,
  `title` varchar(250) NOT NULL DEFAULT '',
  `extension` varchar(250) NOT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(250) NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `added_by` int(10) unsigned NOT NULL DEFAULT '0',
  `added_date` datetime NOT NULL,
  `downloads` int(10) unsigned NOT NULL DEFAULT '0',
  `extra` varchar(255) NOT NULL,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=573 DEFAULT CHARSET=utf8;

/*Data for the table `gg_file` */

/*Table structure for table `gg_grade` */

DROP TABLE IF EXISTS `gg_grade`;

CREATE TABLE `gg_grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT '',
  `grade_by` int(11) unsigned DEFAULT NULL,
  `grade_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_graded` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1017 DEFAULT CHARSET=utf8 COMMENT='评分表';

/*Data for the table `gg_grade` */

/*Table structure for table `gg_grade_description` */

DROP TABLE IF EXISTS `gg_grade_description`;

CREATE TABLE `gg_grade_description` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `grade_setting_id` int(11) unsigned NOT NULL DEFAULT '0',
  `desc` varchar(255) NOT NULL DEFAULT '',
  `score` float NOT NULL DEFAULT '0',
  `review_required` enum('0','1') NOT NULL DEFAULT '1',
  `level` int(11) NOT NULL DEFAULT '0',
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8;

/*Data for the table `gg_grade_description` */

/*Table structure for table `gg_grade_score` */

DROP TABLE IF EXISTS `gg_grade_score`;

CREATE TABLE `gg_grade_score` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `setting_id` int(11) unsigned NOT NULL DEFAULT '0',
  `grade_id` int(11) unsigned NOT NULL DEFAULT '0',
  `description_id` int(11) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2600 DEFAULT CHARSET=utf8 COMMENT='评价分数表';

/*Data for the table `gg_grade_score` */

/*Table structure for table `gg_grade_setting` */

DROP TABLE IF EXISTS `gg_grade_setting`;

CREATE TABLE `gg_grade_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` enum('story','task') NOT NULL DEFAULT 'story',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '评价内容',
  `create_by` int(11) unsigned NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

/*Data for the table `gg_grade_setting` */

/*Table structure for table `gg_history` */

DROP TABLE IF EXISTS `gg_history`;

CREATE TABLE `gg_history` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `action_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `field` varchar(30) NOT NULL DEFAULT '',
  `old` text NOT NULL,
  `new` text NOT NULL,
  `diff` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17353 DEFAULT CHARSET=utf8;

/*Data for the table `gg_history` */

/*Table structure for table `gg_module` */

DROP TABLE IF EXISTS `gg_module`;

CREATE TABLE `gg_module` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` char(60) NOT NULL DEFAULT '',
  `parent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `path` char(255) NOT NULL DEFAULT '',
  `grade` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `type` char(30) NOT NULL,
  `owner` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `gg_module` */

/*Table structure for table `gg_overtime` */

DROP TABLE IF EXISTS `gg_overtime`;

CREATE TABLE `gg_overtime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proposer` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请人',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '务任id',
  `overtime_time` tinyint(4) NOT NULL COMMENT '加班时段',
  `reason` varchar(200) NOT NULL DEFAULT '' COMMENT '加班理由',
  `is_days_off` tinyint(4) DEFAULT NULL COMMENT '是否调休',
  `begin` datetime NOT NULL COMMENT '加班起始时间',
  `end` datetime NOT NULL COMMENT '加班终止时间',
  `hour_counts` float NOT NULL DEFAULT '0' COMMENT '当天加班的小时数',
  `create_time` datetime NOT NULL COMMENT '申请时间',
  `auditor` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `audit_time` datetime NOT NULL COMMENT '审核时间',
  `audit_status` smallint(6) NOT NULL DEFAULT '0' COMMENT '审核状态(0未审核;1表示已审核;2表示驳回)',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `gg_overtime` */

/*Table structure for table `gg_product` */

DROP TABLE IF EXISTS `gg_product`;

CREATE TABLE `gg_product` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) NOT NULL,
  `code` varchar(45) NOT NULL,
  `status` enum('normal','closed') NOT NULL DEFAULT 'normal',
  `description` text NOT NULL,
  `PO` varchar(30) NOT NULL,
  `QD` varchar(30) NOT NULL,
  `RD` varchar(30) NOT NULL,
  `acl` enum('open','private','custom') NOT NULL DEFAULT 'open',
  `whitelist` varchar(255) NOT NULL,
  `created_by` varchar(30) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_version` varchar(20) NOT NULL,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `gg_product` */

/*Table structure for table `gg_project` */

DROP TABLE IF EXISTS `gg_project`;

CREATE TABLE `gg_project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `manage_by` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `project_code` varchar(250) NOT NULL DEFAULT '',
  `begin_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `available_working_days` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `is_private` enum('0','1') NOT NULL DEFAULT '0',
  `opened_by` int(10) unsigned NOT NULL,
  `opened_date` datetime NOT NULL,
  `closed_by` int(10) unsigned NOT NULL,
  `closed_date` datetime NOT NULL,
  `canced_by` int(10) unsigned NOT NULL,
  `canced_date` datetime NOT NULL,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

/*Data for the table `gg_project` */

/*Table structure for table `gg_project_product` */

DROP TABLE IF EXISTS `gg_project_product`;

CREATE TABLE `gg_project_product` (
  `project_id` mediumint(8) unsigned NOT NULL,
  `product_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`project_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `gg_project_product` */

/*Table structure for table `gg_project_team` */

DROP TABLE IF EXISTS `gg_project_team`;

CREATE TABLE `gg_project_team` (
  `project_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role` varchar(250) NOT NULL DEFAULT '',
  `join_date` datetime NOT NULL,
  PRIMARY KEY (`project_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

/*Data for the table `gg_project_team` */

/*Table structure for table `gg_rating` */

DROP TABLE IF EXISTS `gg_rating`;

CREATE TABLE `gg_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rated_uid` int(11) unsigned DEFAULT NULL COMMENT '得分人uid',
  `rated_name` varchar(50) DEFAULT NULL COMMENT '得分人姓名',
  `rated_account` varchar(250) DEFAULT NULL COMMENT '得分人用户名',
  `rating_uid` int(11) unsigned DEFAULT NULL COMMENT '评分人uid',
  `rating_name` varchar(50) NOT NULL DEFAULT '' COMMENT '评分人姓名',
  `rating_account` varchar(250) DEFAULT NULL COMMENT '评分人用户名',
  `grade` smallint(4) NOT NULL DEFAULT '0' COMMENT '分数',
  `content_id` int(11) DEFAULT NULL COMMENT '那一项的评分',
  `content` varchar(250) DEFAULT NULL,
  `description_id` int(11) DEFAULT NULL,
  `type` enum('quality','loyalty','attitude','discipline') DEFAULT NULL COMMENT '类型',
  `level` enum('差','中','好') DEFAULT NULL,
  `rating_desc` varchar(250) DEFAULT NULL COMMENT '评分事件',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '审核状态1待审核，2已确认，3驳回',
  `remark` varchar(250) DEFAULT NULL COMMENT '备注',
  `audited_by` varchar(50) DEFAULT NULL COMMENT '审核人',
  `audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `addtime` datetime DEFAULT NULL COMMENT '评分的时间',
  `added_by` varchar(50) DEFAULT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `modifytime` datetime DEFAULT NULL,
  `is_added` tinyint(1) DEFAULT '0' COMMENT '是否已经加分',
  PRIMARY KEY (`id`),
  KEY `rated_uid` (`rated_uid`) USING BTREE,
  KEY `rating_uid` (`rating_uid`) USING BTREE,
  KEY `level` (`level`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='评分表';

/*Data for the table `gg_rating` */

/*Table structure for table `gg_rating_content` */

DROP TABLE IF EXISTS `gg_rating_content`;

CREATE TABLE `gg_rating_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint(4) DEFAULT NULL,
  `month` tinyint(2) DEFAULT NULL,
  `content` varchar(250) DEFAULT '' COMMENT '具体关于哪方面的评价',
  `type` enum('quality','discipline','loyalty','attitude') DEFAULT NULL COMMENT '评价类型:quality:个人工作业绩考核-大小项目完成质量及整体协作性，attitude:周边行为考核-协作与态度，\r\nloyalty:周边行为考核-忠诚度\r\ndiscipline:周边行为考核-遵守纪律',
  `added_by` varchar(50) DEFAULT NULL COMMENT '添加人',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_by` varchar(50) DEFAULT NULL COMMENT '修改人',
  `modifytime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='评价内容设定';

/*Data for the table `gg_rating_content` */

/*Table structure for table `gg_rating_content1` */

DROP TABLE IF EXISTS `gg_rating_content1`;

CREATE TABLE `gg_rating_content1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint(4) DEFAULT NULL,
  `month` tinyint(2) DEFAULT NULL,
  `content` varchar(250) DEFAULT '' COMMENT '具体关于哪方面的评价',
  `type` enum('quality','discipline','loyalty','attitude') DEFAULT NULL COMMENT '评价类型:quality:个人工作业绩考核-大小项目完成质量及整体协作性，attitude:周边行为考核-协作与态度，\r\nloyalty:周边行为考核-忠诚度\r\ndiscipline:周边行为考核-遵守纪律',
  `added_by` varchar(50) DEFAULT NULL COMMENT '添加人',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_by` varchar(50) DEFAULT NULL COMMENT '修改人',
  `modifytime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='评价内容设定';

/*Data for the table `gg_rating_content1` */

/*Table structure for table `gg_rating_description` */

DROP TABLE IF EXISTS `gg_rating_description`;

CREATE TABLE `gg_rating_description` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) unsigned DEFAULT '0' COMMENT '年',
  `desc` varchar(100) DEFAULT '0' COMMENT '评价说明',
  `start_value` smallint(3) DEFAULT '0' COMMENT '开始值,即最小值',
  `level` enum('差','中','好') DEFAULT NULL COMMENT '评分级别',
  `end_value` smallint(3) DEFAULT '0' COMMENT '结束值，即最大值',
  `review_required` tinyint(1) DEFAULT '1' COMMENT '评价是否必须,1表示必须，2表示非必须',
  `added_by` varchar(50) DEFAULT NULL COMMENT '添加人',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_by` varchar(50) DEFAULT NULL COMMENT '修改人',
  `modifytime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='评价内容详细描述';

/*Data for the table `gg_rating_description` */

/*Table structure for table `gg_rating_description1` */

DROP TABLE IF EXISTS `gg_rating_description1`;

CREATE TABLE `gg_rating_description1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) unsigned DEFAULT '0' COMMENT '年',
  `desc` varchar(100) DEFAULT '0' COMMENT '评价说明',
  `start_value` smallint(3) DEFAULT '0' COMMENT '开始值,即最小值',
  `level` enum('差','中','好') DEFAULT NULL COMMENT '评分级别',
  `end_value` smallint(3) DEFAULT '0' COMMENT '结束值，即最大值',
  `review_required` tinyint(1) DEFAULT '1' COMMENT '评价是否必须,1表示必须，2表示非必须',
  `added_by` varchar(50) DEFAULT NULL COMMENT '添加人',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_by` varchar(50) DEFAULT NULL COMMENT '修改人',
  `modifytime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='评价内容详细描述';

/*Data for the table `gg_rating_description1` */

/*Table structure for table `gg_rating_gradesetting` */

DROP TABLE IF EXISTS `gg_rating_gradesetting`;

CREATE TABLE `gg_rating_gradesetting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint(4) unsigned DEFAULT '0' COMMENT '年',
  `month` tinyint(2) unsigned DEFAULT '0' COMMENT '月',
  `manage_plus` smallint(4) unsigned DEFAULT '100' COMMENT '管理用户总共给别人能加这么多分',
  `manage_minus` smallint(4) unsigned DEFAULT '100' COMMENT '管理用户总共给别人能减这么多分',
  `common_plus` smallint(4) DEFAULT '50' COMMENT '普通用户总共给别人能加这么多分',
  `common_minus` smallint(4) DEFAULT '50' COMMENT '普通用户总共给别人能减这么多分',
  `added_by` varchar(50) DEFAULT NULL COMMENT '增加人',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_by` varchar(50) DEFAULT NULL COMMENT '修改人',
  `modifytime` datetime DEFAULT NULL COMMENT '修改时间',
  `delay_days` tinyint(1) DEFAULT NULL COMMENT '延迟加分时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='月度加减初始值设定';

/*Data for the table `gg_rating_gradesetting` */

/*Table structure for table `gg_rating_log` */

DROP TABLE IF EXISTS `gg_rating_log`;

CREATE TABLE `gg_rating_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `login_date` date NOT NULL COMMENT '登陆时间',
  `first_login_time` datetime NOT NULL COMMENT '第一次登陆时间',
  `last_login_time` datetime NOT NULL COMMENT '最后一次登陆时间',
  `last_login_ip` varchar(20) NOT NULL COMMENT '最后一次登陆ip',
  PRIMARY KEY (`id`),
  KEY `login_date` (`login_date`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

/*Data for the table `gg_rating_log` */

/*Table structure for table `gg_ratting_grade_summary` */

DROP TABLE IF EXISTS `gg_ratting_grade_summary`;

CREATE TABLE `gg_ratting_grade_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `real_name` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `uid` int(11) DEFAULT '0' COMMENT '用户uid',
  `year` smallint(4) DEFAULT '0' COMMENT '年',
  `month` tinyint(2) DEFAULT '0' COMMENT '月',
  `performance_score` smallint(4) DEFAULT '0' COMMENT '业绩得分',
  `behavior_score` smallint(4) DEFAULT '0' COMMENT '行为得分',
  `plus` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '当月加分',
  `minus` decimal(10,2) DEFAULT '0.00' COMMENT '当月减分',
  `grade` decimal(10,2) DEFAULT '0.00' COMMENT '当月得分',
  `plus_last` smallint(4) unsigned DEFAULT '0' COMMENT '当月加分剩余',
  `minus_last` smallint(4) unsigned DEFAULT '0' COMMENT '当月减分剩余',
  `performance_plus` smallint(4) unsigned DEFAULT '0' COMMENT '业绩加分',
  `performance_minus` smallint(4) DEFAULT '0' COMMENT '业绩减分',
  `behavior_plus` smallint(4) unsigned DEFAULT '0' COMMENT '行为加分',
  `behavior_minus` smallint(4) DEFAULT '0' COMMENT '行为减分',
  `total` int(11) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `year` (`year`) USING BTREE,
  KEY `month` (`month`) USING BTREE,
  KEY `real_name` (`real_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `gg_ratting_grade_summary` */

/*Table structure for table `gg_story` */

DROP TABLE IF EXISTS `gg_story`;

CREATE TABLE `gg_story` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `module_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模块ID',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `description` mediumtext NOT NULL,
  `source` varchar(250) NOT NULL DEFAULT '',
  `level` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `quality` varchar(250) NOT NULL DEFAULT '',
  `status` varchar(250) NOT NULL,
  `stage` varchar(250) NOT NULL,
  `estimate` float unsigned NOT NULL DEFAULT '0',
  `reviewed_by` int(11) NOT NULL DEFAULT '0',
  `reviewed_date` datetime NOT NULL,
  `reviewed_result` varchar(250) NOT NULL DEFAULT '',
  `assigned_to` int(10) unsigned NOT NULL DEFAULT '0',
  `assigned_date` datetime NOT NULL,
  `opened_by` int(10) unsigned NOT NULL DEFAULT '0',
  `opened_date` datetime NOT NULL,
  `finished_date` datetime NOT NULL,
  `last_edited_by` int(10) unsigned NOT NULL DEFAULT '0',
  `last_edited_date` datetime NOT NULL,
  `closed_by` int(10) unsigned NOT NULL DEFAULT '0',
  `closed_date` datetime NOT NULL,
  `closed_reason` varchar(250) NOT NULL DEFAULT '',
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=415 DEFAULT CHARSET=utf8;

/*Data for the table `gg_story` */

/*Table structure for table `gg_task` */

DROP TABLE IF EXISTS `gg_task`;

CREATE TABLE `gg_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `story_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `status` varchar(250) NOT NULL,
  `level` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `difficulty` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `estimate` float unsigned NOT NULL,
  `consumed` float unsigned NOT NULL,
  `deadline` datetime NOT NULL,
  `description` mediumtext NOT NULL,
  `est_started_date` datetime NOT NULL,
  `real_started_date` datetime NOT NULL,
  `opened_by` int(10) unsigned NOT NULL DEFAULT '0',
  `opened_date` datetime NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL DEFAULT '0',
  `assigned_date` datetime NOT NULL,
  `finished_by` int(10) unsigned NOT NULL DEFAULT '0',
  `finished_date` datetime NOT NULL,
  `canceled_by` int(10) unsigned NOT NULL DEFAULT '0',
  `canceled_date` datetime NOT NULL,
  `closed_by` int(10) unsigned NOT NULL DEFAULT '0',
  `closed_date` datetime NOT NULL,
  `closed_reason` varchar(250) NOT NULL,
  `last_edited_by` int(11) NOT NULL DEFAULT '0',
  `last_edited_date` datetime NOT NULL,
  `need_test` enum('0','1') NOT NULL DEFAULT '1',
  `test_by` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(800) DEFAULT NULL COMMENT '总结心得',
  `summary_by` int(10) DEFAULT NULL,
  `need_summary` enum('1','0') DEFAULT '0' COMMENT '1需要总结，0无需总结',
  `test_date` datetime NOT NULL,
  `test_finished_date` datetime NOT NULL,
  `online_date` datetime NOT NULL,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=772 DEFAULT CHARSET=utf8;

/*Data for the table `gg_task` */

/*Table structure for table `gg_task_copy` */

DROP TABLE IF EXISTS `gg_task_copy`;

CREATE TABLE `gg_task_copy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `story_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `status` varchar(250) NOT NULL,
  `level` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `difficulty` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `estimate` float unsigned NOT NULL,
  `consumed` float unsigned NOT NULL,
  `deadline` datetime NOT NULL,
  `description` mediumtext NOT NULL,
  `est_started_date` datetime NOT NULL,
  `real_started_date` datetime NOT NULL,
  `opened_by` int(10) unsigned NOT NULL DEFAULT '0',
  `opened_date` datetime NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL DEFAULT '0',
  `assigned_date` datetime NOT NULL,
  `finished_by` int(10) unsigned NOT NULL DEFAULT '0',
  `finished_date` datetime NOT NULL,
  `canceled_by` int(10) unsigned NOT NULL DEFAULT '0',
  `canceled_date` datetime NOT NULL,
  `closed_by` int(10) unsigned NOT NULL DEFAULT '0',
  `closed_date` datetime NOT NULL,
  `closed_reason` varchar(250) NOT NULL,
  `last_edited_by` int(11) NOT NULL DEFAULT '0',
  `last_edited_date` datetime NOT NULL,
  `need_test` enum('0','1') NOT NULL DEFAULT '1',
  `test_by` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(800) DEFAULT NULL COMMENT '总结心得',
  `summary_by` int(10) DEFAULT NULL,
  `need_summary` enum('1','0') DEFAULT '0' COMMENT '1需要总结，0无需总结',
  `test_date` datetime NOT NULL,
  `test_finished_date` datetime NOT NULL,
  `online_date` datetime NOT NULL,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8;

/*Data for the table `gg_task_copy` */

/*Table structure for table `gg_task_prizes` */

DROP TABLE IF EXISTS `gg_task_prizes`;

CREATE TABLE `gg_task_prizes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL COMMENT '所属项目',
  `name` varchar(250) DEFAULT NULL COMMENT '任务名称,转成正常的需求的时候，对应需求名称',
  `description` mediumtext COMMENT '任务描述',
  `deadline` datetime DEFAULT NULL COMMENT '要求任务结束时间',
  `online_date` datetime DEFAULT NULL COMMENT '上线时间',
  `grade` decimal(6,2) DEFAULT NULL COMMENT '任务积分值',
  `difficulty` tinyint(3) DEFAULT '100' COMMENT '难度百分比，默认100,也就是100%',
  `error` tinyint(3) DEFAULT '100' COMMENT '容错率,默认100，也就是100%',
  `reviewed_by` int(11) DEFAULT NULL COMMENT '审核人',
  `reviewed_date` datetime DEFAULT NULL COMMENT '审核时间',
  `reviewed_result` varchar(250) DEFAULT NULL,
  `minus_grade` decimal(6,2) DEFAULT NULL COMMENT '扣除积分',
  `minus_reason` varchar(250) DEFAULT NULL,
  `grade_basic` decimal(6,2) DEFAULT '100.00' COMMENT '积分基数，默认100，也就是100%',
  `last_grade` decimal(6,2) DEFAULT NULL COMMENT '最后得分',
  `confirm_by` int(11) DEFAULT NULL COMMENT '确认人',
  `confirm_date` datetime DEFAULT NULL COMMENT '确认时间',
  `start_date` datetime DEFAULT NULL COMMENT '任务开始时间',
  `end_date` datetime DEFAULT NULL COMMENT '结束时间',
  `assigned_to` int(11) DEFAULT NULL COMMENT '指派人',
  `assigned_date` datetime DEFAULT NULL,
  `apply_by` int(10) DEFAULT NULL COMMENT '申请人',
  `apply_date` datetime DEFAULT NULL COMMENT '申请时间',
  `create_by` int(11) DEFAULT NULL COMMENT '有谁创建',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `closed_by` int(11) DEFAULT NULL COMMENT '关闭人',
  `closed_date` datetime DEFAULT NULL COMMENT '关闭时间',
  `closed_reason` varchar(250) DEFAULT '' COMMENT '关闭原因',
  `last_edited_by` int(11) DEFAULT NULL COMMENT '最后编辑',
  `last_edited_date` datetime DEFAULT NULL COMMENT '最后编辑时间',
  `consumed` float DEFAULT NULL COMMENT '耗费时间',
  `status` varchar(250) DEFAULT '0' COMMENT '任务状态',
  `is_deleted` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `gg_task_prizes` */

/*Table structure for table `gg_testtask` */

DROP TABLE IF EXISTS `gg_testtask`;

CREATE TABLE `gg_testtask` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `story_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `level` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `status` enum('wait','doing','done','blocked','hang','closed','canceled') NOT NULL DEFAULT 'wait',
  `begin_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `report` text NOT NULL,
  `owner` int(10) unsigned NOT NULL DEFAULT '0',
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `gg_testtask` */

/*Table structure for table `gg_user` */

DROP TABLE IF EXISTS `gg_user`;

CREATE TABLE `gg_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(10) unsigned NOT NULL DEFAULT '0',
  `account` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `real_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL DEFAULT '',
  `join_date` datetime DEFAULT NULL,
  `is_admin` enum('0','1') NOT NULL DEFAULT '0',
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `attention` text COMMENT '关注',
  `is_manage` tinyint(1) DEFAULT NULL COMMENT '是否管理人员',
  `sign` varchar(250) DEFAULT NULL COMMENT '个性签名',
  `image` varchar(250) DEFAULT NULL COMMENT '头像',
  `phone` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`) USING BTREE,
  KEY `real_name` (`real_name`) USING BTREE,
  KEY `account` (`account`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

/*Data for the table `gg_user` */

insert  into `gg_user`(`id`,`department_id`,`account`,`password`,`real_name`,`email`,`join_date`,`is_admin`,`is_deleted`,`attention`,`is_manage`,`sign`,`image`,`phone`) values (1,0,'admin','40e40155dbd60d2ebc05c93cbef95756','admin','admin@gts.com','2015-07-09 07:42:08','1','0',NULL,1,NULL,NULL,'');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
