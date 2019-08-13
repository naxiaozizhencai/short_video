/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50726
Source Host           : 127.0.0.1:3306
Source Database       : short_video

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2019-08-13 18:34:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for temp_data
-- ----------------------------
DROP TABLE IF EXISTS `temp_data`;
CREATE TABLE `temp_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `temp_key` varchar(50) DEFAULT NULL,
  `temp_value` varchar(255) DEFAULT NULL,
  `expire_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '过期时间',
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`temp_key`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of temp_data
-- ----------------------------
INSERT INTO `temp_data` VALUES ('1', '2', 'view_max_id', '6', '2019-08-12 17:53:36', '2019-08-12 09:40:48');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `uuid` varchar(11) DEFAULT NULL COMMENT '唯一id 每个手机默认游客登录uuid用于识别登录',
  `username` varchar(255) DEFAULT NULL COMMENT '用户名字',
  `password` varchar(255) DEFAULT NULL COMMENT '用户密码',
  `api_token` varchar(255) DEFAULT NULL,
  `phone_number` int(11) DEFAULT NULL COMMENT '手机号',
  `vip_level` int(10) unsigned DEFAULT '0' COMMENT 'vip等级',
  `vip_expired_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'vip 过期时间',
  `is_phone_login` tinyint(4) DEFAULT '0' COMMENT '0 未手机登录 1 手机登录',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('2', '123456', '游客账号_6562010378', '123456', null, null, '0', null, null, '2019-08-12 02:53:12');
INSERT INTO `users` VALUES ('3', 'dadad', '游客账号_4140359465', null, null, null, '0', null, null, '2019-08-12 03:25:36');
INSERT INTO `users` VALUES ('4', '1234567', '游客账号_9021638735', null, null, null, '0', '2019-08-13 17:55:42', '0', '2019-08-12 07:28:59');

-- ----------------------------
-- Table structure for users_detail
-- ----------------------------
DROP TABLE IF EXISTS `users_detail`;
CREATE TABLE `users_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_id` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `phone_number` int(11) DEFAULT NULL COMMENT '手机号',
  `sign` varchar(255) DEFAULT NULL COMMENT '签名',
  `sex` varchar(2) DEFAULT NULL COMMENT '性别',
  `birthday` varchar(255) DEFAULT NULL COMMENT '生日',
  `age` int(11) DEFAULT '0' COMMENT '年龄',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `fans_num` int(10) unsigned DEFAULT '0' COMMENT '粉丝数量',
  `follow_num` int(10) unsigned DEFAULT '0' COMMENT '关注数量',
  `support_num` int(10) unsigned DEFAULT '0' COMMENT '获赞数量',
  `invitation_num` int(11) DEFAULT '0' COMMENT '邀请数量',
  `upload_num` int(11) DEFAULT '0' COMMENT '上传数量',
  `coin_num` int(11) DEFAULT '0' COMMENT '金币数量',
  `popularize_number` varchar(255) DEFAULT NULL COMMENT '推广码',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='用户详情表';

-- ----------------------------
-- Records of users_detail
-- ----------------------------
INSERT INTO `users_detail` VALUES ('4', '2', 'a.png', null, null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'Xe6r', '2019-08-12 07:28:02');

-- ----------------------------
-- Table structure for video_discuss
-- ----------------------------
DROP TABLE IF EXISTS `video_discuss`;
CREATE TABLE `video_discuss` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL COMMENT '视频id',
  `parent_id` int(11) DEFAULT NULL COMMENT '父id',
  `content` varchar(255) DEFAULT NULL COMMENT '评论内容',
  `favorite_number` int(11) DEFAULT NULL COMMENT '喜欢这条评论的数量',
  `discuss_time` int(11) DEFAULT NULL COMMENT '评论时间',
  `from_uid` int(11) DEFAULT NULL COMMENT '回复用户id',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COMMENT='视频评论';

-- ----------------------------
-- Records of video_discuss
-- ----------------------------
INSERT INTO `video_discuss` VALUES ('10', '1', '0', 'wooooooo', '0', '1565664627', '1', '2019-08-13 02:50:27');
INSERT INTO `video_discuss` VALUES ('11', '1', '0', '爱打打看', '0', '1565664646', '1', '2019-08-13 02:50:46');
INSERT INTO `video_discuss` VALUES ('12', '1', '11', '31312', '0', '1565664650', '2', '2019-08-13 02:50:50');
INSERT INTO `video_discuss` VALUES ('13', '1', '12', '大大大', '0', '1565664707', '2', '2019-08-13 02:51:47');

-- ----------------------------
-- Table structure for video_favorite_list
-- ----------------------------
DROP TABLE IF EXISTS `video_favorite_list`;
CREATE TABLE `video_favorite_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '0 未点爱心 1 点击爱心',
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of video_favorite_list
-- ----------------------------

-- ----------------------------
-- Table structure for video_list
-- ----------------------------
DROP TABLE IF EXISTS `video_list`;
CREATE TABLE `video_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `video_title` varchar(255) DEFAULT NULL,
  `video_image` varchar(255) DEFAULT NULL COMMENT '视频封面',
  `video_url` varchar(255) DEFAULT NULL COMMENT '视频地址',
  `video_label` varchar(255) DEFAULT NULL COMMENT '视频标签',
  `is_check` tinyint(4) DEFAULT '1' COMMENT '是否审核0 未审核 1审核',
  `favorite_number` int(11) DEFAULT NULL COMMENT '喜爱数量',
  `reply_number` int(11) DEFAULT NULL COMMENT '回复数量',
  `price` int(11) DEFAULT NULL COMMENT '视频价格',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='视频列表';

-- ----------------------------
-- Records of video_list
-- ----------------------------
INSERT INTO `video_list` VALUES ('1', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('2', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('3', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('4', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('5', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('6', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('7', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('8', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');
INSERT INTO `video_list` VALUES ('9', '1', '2222', '333.jpg', '123.MP4', '55555555555555 # aaaaaaaaaaa #aaaaaaaaaaaaaaa', '1', '1', '2', '0', '2019-08-12 14:57:29');

-- ----------------------------
-- Table structure for video_message
-- ----------------------------
DROP TABLE IF EXISTS `video_message`;
CREATE TABLE `video_message` (
  `id` int(11) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `message_type` varchar(255) DEFAULT NULL,
  `send_id` int(11) DEFAULT NULL,
  `receive_id` int(11) DEFAULT NULL,
  `is_read` tinyint(4) DEFAULT NULL,
  `send_time` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of video_message
-- ----------------------------
