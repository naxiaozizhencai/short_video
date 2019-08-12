/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : short_video

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2019-08-12 23:32:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for video_discuss
-- ----------------------------
DROP TABLE IF EXISTS `video_discuss`;
CREATE TABLE `video_discuss` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `from_uid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `favorite_number` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `discuss_time` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='视频评论';

-- ----------------------------
-- Table structure for video_favorite_list
-- ----------------------------
DROP TABLE IF EXISTS `video_favorite_list`;
CREATE TABLE `video_favorite_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='视频列表';

-- ----------------------------
-- Table structure for video_reply
-- ----------------------------
DROP TABLE IF EXISTS `video_reply`;
CREATE TABLE `video_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) DEFAULT NULL,
  `from_uid` int(11) DEFAULT NULL,
  `to_uid` int(11) DEFAULT NULL,
  `reply_id` int(11) DEFAULT NULL COMMENT '回复id',
  `username` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `favorite_number` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `discuss_time` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='视频回复';
