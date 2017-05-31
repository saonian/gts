# Host: localhost  (Version 5.7.9)
# Date: 2016-06-21 15:24:25
# Generator: MySQL-Front 5.3  (Build 5.39)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "priv"
#

CREATE TABLE `priv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表';

#
# Data for table "priv"
#


#
# Structure for table "role"
#

CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色表';

#
# Data for table "role"
#

INSERT INTO `role` VALUES (1,'网点1客服人员'),(2,'网点1管理员'),(3,'区域1客服人员'),(4,'区域1管理员');

#
# Structure for table "role_priv"
#

CREATE TABLE `role_priv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `privid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限表';

#
# Data for table "role_priv"
#


#
# Structure for table "role_user"
#

CREATE TABLE `role_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `roleid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色用户关联表';

#
# Data for table "role_user"
#


#
# Structure for table "user"
#

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

#
# Data for table "user"
#

