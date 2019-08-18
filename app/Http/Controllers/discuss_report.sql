/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : short_video

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2019-08-18 23:43:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for discuss_report
-- ----------------------------
DROP TABLE IF EXISTS `discuss_report`;
CREATE TABLE `discuss_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discuss_id` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of discuss_report
-- ----------------------------
INSERT INTO `discuss_report` VALUES ('1', '33', '7777777777777777777777777777777777777', '2019-08-13 14:56:27');
INSERT INTO `discuss_report` VALUES ('2', '33', '7777777777777777777777777777777777777', '2019-08-13 15:01:14');
INSERT INTO `discuss_report` VALUES ('3', '33', '7777777777777777777777777777777777777', '2019-08-13 15:01:15');
INSERT INTO `discuss_report` VALUES ('4', '33', '7777777777777777777777777777777777777', '2019-08-13 15:01:22');
INSERT INTO `discuss_report` VALUES ('5', '33', '7777777777777777777777777777777777777', '2019-08-13 15:01:26');
INSERT INTO `discuss_report` VALUES ('6', '33', '7777777777777777777777777777777777777', '2019-08-13 15:01:26');

-- ----------------------------
-- Table structure for popular_list
-- ----------------------------
DROP TABLE IF EXISTS `popular_list`;
CREATE TABLE `popular_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `popular_num` varchar(10) DEFAULT NULL COMMENT '推广号',
  `popular_uid` int(11) DEFAULT NULL COMMENT '推广人id',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `popular_num` (`popular_num`) USING BTREE,
  KEY `popular_uid` (`popular_uid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='推广列表';

-- ----------------------------
-- Records of popular_list
-- ----------------------------
INSERT INTO `popular_list` VALUES ('11', '30', 'aUUV', '8', '2019-08-14 09:55:09');

-- ----------------------------
-- Table structure for temp_data
-- ----------------------------
DROP TABLE IF EXISTS `temp_data`;
CREATE TABLE `temp_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `temp_key` varchar(50) DEFAULT NULL,
  `temp_value` varchar(255) DEFAULT NULL,
  `expire_time` int(11) DEFAULT NULL COMMENT '过期时间',
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`temp_key`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of temp_data
-- ----------------------------
INSERT INTO `temp_data` VALUES ('5', '8', '13122859371', '9536', '1565865082', '2019-08-15 09:31:22');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `uuid` varchar(255) DEFAULT NULL COMMENT '唯一id 每个手机默认游客登录uuid用于识别登录',
  `username` varchar(255) DEFAULT NULL COMMENT '用户名字',
  `password` varchar(255) DEFAULT NULL COMMENT '用户密码',
  `api_token` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL COMMENT '手机号',
  `vip_level` int(10) unsigned DEFAULT '0' COMMENT 'vip等级',
  `vip_expired_time` int(11) DEFAULT '0' COMMENT 'vip 过期时间',
  `is_phone_login` tinyint(4) DEFAULT '0' COMMENT '0 未手机登录 1 手机登录',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`) USING BTREE,
  UNIQUE KEY `phone` (`phone`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('8', '756235', '游客账号_6733162833', '25d55ad283aa400af464c76d713c07ad', null, '13122859371', '0', '0', '1', '2019-08-14 09:30:44');
INSERT INTO `users` VALUES ('9', '7562', '游客账号_2534355814', null, null, null, '0', '0', '0', '2019-08-14 09:30:50');
INSERT INTO `users` VALUES ('10', '75623', '游客账号_3357400155', null, null, null, '0', '0', '0', '2019-08-14 09:30:53');
INSERT INTO `users` VALUES ('11', '756234', '游客账号_1241089348', null, null, null, '0', '0', '0', '2019-08-14 09:30:55');
INSERT INTO `users` VALUES ('12', '7562345', '游客账号_5171882380', null, null, null, '0', '0', '0', '2019-08-14 09:30:57');
INSERT INTO `users` VALUES ('13', '75623456', '游客账号_387233371', null, null, null, '0', '0', '0', '2019-08-14 09:30:59');
INSERT INTO `users` VALUES ('14', '756234564', '游客账号_4490660894', null, null, null, '0', '0', '0', '2019-08-14 09:31:25');
INSERT INTO `users` VALUES ('15', '756234564d', '游客账号_7442026368', null, null, null, '0', '0', '0', '2019-08-14 09:31:28');
INSERT INTO `users` VALUES ('16', '756234564de', '游客账号_2387106897', null, null, null, '0', '0', '0', '2019-08-14 09:31:29');
INSERT INTO `users` VALUES ('17', '756234564def', '游客账号_5859403201', null, null, null, '0', '0', '0', '2019-08-14 09:32:11');
INSERT INTO `users` VALUES ('18', '756234564def3', '游客账号_774122737', null, null, null, '0', '0', '0', '2019-08-14 09:32:13');
INSERT INTO `users` VALUES ('19', '756234564def34', '游客账号_3732477110', null, null, null, '0', '0', '0', '2019-08-14 09:32:15');
INSERT INTO `users` VALUES ('20', '756234564def345', '游客账号_9688426786', null, null, null, '0', '0', '0', '2019-08-14 09:32:17');
INSERT INTO `users` VALUES ('21', '756234564def3451', '游客账号_4424547336', null, null, null, '0', '0', '0', '2019-08-14 09:32:18');
INSERT INTO `users` VALUES ('22', '756234564def34512', '游客账号_3925454203', null, null, null, '0', '0', '0', '2019-08-14 09:32:20');
INSERT INTO `users` VALUES ('23', '756234564def345123', '游客账号_1247503522', null, null, null, '0', '0', '0', '2019-08-14 09:32:22');
INSERT INTO `users` VALUES ('24', '756234564def3451234', '游客账号_5672505253', null, null, null, '0', '0', '0', '2019-08-14 09:32:23');
INSERT INTO `users` VALUES ('25', '756234564def34512345', '游客账号_5382126742', null, null, null, '0', '0', '0', '2019-08-14 09:32:25');
INSERT INTO `users` VALUES ('26', '756234564def345123451', '游客账号_3235452544', null, null, null, '0', '0', '0', '2019-08-14 09:32:26');
INSERT INTO `users` VALUES ('27', '756234564def3451234512', '游客账号_9814236052', null, null, null, '0', '0', '0', '2019-08-14 09:32:28');
INSERT INTO `users` VALUES ('28', '756234564def34512345123', '游客账号_1231362489', null, null, null, '0', '0', '0', '2019-08-14 09:32:30');
INSERT INTO `users` VALUES ('29', '756234564def345123451234', '游客账号_6475781781', null, null, null, '0', '0', '0', '2019-08-14 09:32:32');
INSERT INTO `users` VALUES ('30', '756234564def3451234512341', '游客账号_9170393347', null, null, null, '0', '1565949194', '0', '2019-08-14 09:32:33');
INSERT INTO `users` VALUES ('31', '33', '游客账号_2438548997', null, null, null, '0', '0', '0', '2019-08-18 07:46:21');
INSERT INTO `users` VALUES ('32', '333132', '游客账号_9369488865', null, null, null, '0', '0', '0', '2019-08-18 07:47:56');
INSERT INTO `users` VALUES ('33', '333132333', '游客账号_5940911915', null, null, null, '0', '0', '0', '2019-08-18 07:48:26');
INSERT INTO `users` VALUES ('34', '333132333333', '游客账号_9708657879', null, null, null, '0', '0', '0', '2019-08-18 07:49:51');
INSERT INTO `users` VALUES ('35', '333132333333dddd', '游客账号_7021791046', null, null, null, '0', '0', '0', '2019-08-18 07:50:09');
INSERT INTO `users` VALUES ('36', '333132333333ddddd', '游客账号_2877069891', null, null, null, '0', '0', '0', '2019-08-18 07:51:13');
INSERT INTO `users` VALUES ('37', 'dad sd ', '游客账号_5149940669', null, null, null, '0', '0', '0', '2019-08-18 07:52:37');
INSERT INTO `users` VALUES ('38', 'dad sd ffff', '游客账号_7600098330', null, null, null, '0', '0', '0', '2019-08-18 07:52:42');

-- ----------------------------
-- Table structure for users_detail
-- ----------------------------
DROP TABLE IF EXISTS `users_detail`;
CREATE TABLE `users_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户详情id',
  `user_id` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
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
  `popular_num` varchar(255) DEFAULT NULL COMMENT '推广码',
  `orther_popular_num` varchar(5) DEFAULT NULL COMMENT '别人邀请的码',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`detail_id`),
  KEY `popular_num` (`popular_num`) USING BTREE,
  KEY `uid` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COMMENT='用户详情表';

-- ----------------------------
-- Records of users_detail
-- ----------------------------
INSERT INTO `users_detail` VALUES ('6', '8', 'a.png', '12345678', '1', '1dadadada', '0', '上海', '0', '0', '0', '1', '2', '0', 'aUUV', null, '2019-08-14 09:30:44');
INSERT INTO `users_detail` VALUES ('7', '9', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'wPej', null, '2019-08-14 09:30:50');
INSERT INTO `users_detail` VALUES ('8', '10', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'W5sF', null, '2019-08-14 09:30:53');
INSERT INTO `users_detail` VALUES ('9', '11', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'Xh47', null, '2019-08-14 09:30:55');
INSERT INTO `users_detail` VALUES ('10', '12', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'UM5C', null, '2019-08-14 09:30:57');
INSERT INTO `users_detail` VALUES ('11', '13', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'BxNc', null, '2019-08-14 09:30:59');
INSERT INTO `users_detail` VALUES ('12', '14', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', '9PEp', null, '2019-08-14 09:31:25');
INSERT INTO `users_detail` VALUES ('13', '15', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'h7tb', null, '2019-08-14 09:31:28');
INSERT INTO `users_detail` VALUES ('14', '16', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'eP4H', null, '2019-08-14 09:31:29');
INSERT INTO `users_detail` VALUES ('15', '17', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'ZB5f', null, '2019-08-14 09:32:11');
INSERT INTO `users_detail` VALUES ('16', '18', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', '3Ucm', null, '2019-08-14 09:32:13');
INSERT INTO `users_detail` VALUES ('17', '19', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'zXnm', null, '2019-08-14 09:32:15');
INSERT INTO `users_detail` VALUES ('18', '20', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', '82jP', null, '2019-08-14 09:32:17');
INSERT INTO `users_detail` VALUES ('19', '21', 'a.png', null, null, null, '0', '深圳', '0', '4', '0', '0', '0', '0', 'Bxb7', null, '2019-08-14 09:32:19');
INSERT INTO `users_detail` VALUES ('20', '22', 'a.png', null, null, null, '0', '深圳', '0', '1', '0', '0', '0', '0', 'bKzX', null, '2019-08-14 09:32:20');
INSERT INTO `users_detail` VALUES ('21', '23', 'a.png', null, null, null, '0', '深圳', '0', '1', '0', '0', '0', '0', 'wfvr', null, '2019-08-14 09:32:22');
INSERT INTO `users_detail` VALUES ('22', '24', 'a.png', null, null, null, '0', '深圳', '0', '1', '0', '0', '0', '0', 'ffKg', null, '2019-08-14 09:32:23');
INSERT INTO `users_detail` VALUES ('23', '25', 'a.png', null, null, null, '0', '深圳', '0', '1', '0', '0', '0', '0', '5Ta5', null, '2019-08-14 09:32:25');
INSERT INTO `users_detail` VALUES ('24', '26', 'a.png', null, null, null, '0', '深圳', '0', '1', '0', '0', '0', '0', '8cjp', null, '2019-08-14 09:32:26');
INSERT INTO `users_detail` VALUES ('25', '27', 'a.png', null, null, null, '0', '深圳', '0', '1', '0', '0', '0', '0', 'CEYq', null, '2019-08-14 09:32:28');
INSERT INTO `users_detail` VALUES ('26', '28', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'DWWk', null, '2019-08-14 09:32:30');
INSERT INTO `users_detail` VALUES ('27', '29', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'sptM', null, '2019-08-14 09:32:32');
INSERT INTO `users_detail` VALUES ('28', '30', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'yS3E', null, '2019-08-14 09:32:33');
INSERT INTO `users_detail` VALUES ('29', '31', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'yamZ', null, '2019-08-18 07:46:21');
INSERT INTO `users_detail` VALUES ('30', '32', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'PXEB', null, '2019-08-18 07:47:56');
INSERT INTO `users_detail` VALUES ('31', '33', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'tMC2', null, '2019-08-18 07:48:26');
INSERT INTO `users_detail` VALUES ('32', '34', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'PNaN', null, '2019-08-18 07:49:51');
INSERT INTO `users_detail` VALUES ('33', '35', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'HBBk', null, '2019-08-18 07:50:09');
INSERT INTO `users_detail` VALUES ('34', '36', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'WWV2', null, '2019-08-18 07:51:13');
INSERT INTO `users_detail` VALUES ('35', '37', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'ytkG', null, '2019-08-18 07:52:37');
INSERT INTO `users_detail` VALUES ('36', '38', 'a.png', null, null, null, '0', '深圳', '0', '0', '0', '0', '0', '0', 'Zb3B', null, '2019-08-18 07:52:42');

-- ----------------------------
-- Table structure for users_fans
-- ----------------------------
DROP TABLE IF EXISTS `users_fans`;
CREATE TABLE `users_fans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `fans_id` int(11) DEFAULT NULL COMMENT '关注id',
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `fans_id` (`fans_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='粉丝关注列表';

-- ----------------------------
-- Records of users_fans
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='视频评论';

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
  `video_id` int(11) DEFAULT NULL COMMENT '视频id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '0 未点爱心 1 点击爱心',
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='喜欢视频列表';

-- ----------------------------
-- Records of video_favorite_list
-- ----------------------------
INSERT INTO `video_favorite_list` VALUES ('5', '28', '8', '1', '2019-08-15 12:43:08');
INSERT INTO `video_favorite_list` VALUES ('6', '29', '8', '1', '2019-08-15 12:43:20');
INSERT INTO `video_favorite_list` VALUES ('7', '30', '8', '1', '2019-08-15 12:43:25');
INSERT INTO `video_favorite_list` VALUES ('8', '31', '8', '1', '2019-08-15 12:43:29');
INSERT INTO `video_favorite_list` VALUES ('9', '32', '8', '1', '2019-08-15 12:43:33');
INSERT INTO `video_favorite_list` VALUES ('10', '33', '8', '1', '2019-08-15 12:43:37');
INSERT INTO `video_favorite_list` VALUES ('11', '34', '8', '1', '2019-08-15 12:43:40');
INSERT INTO `video_favorite_list` VALUES ('12', '35', '8', '1', '2019-08-15 12:43:44');

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
  `is_recommend` tinyint(3) unsigned DEFAULT '0' COMMENT '是否推荐',
  `favorite_num` int(11) DEFAULT '0' COMMENT '喜爱数量',
  `reply_num` int(11) DEFAULT '0' COMMENT '回复数量',
  `play_num` int(11) DEFAULT '0' COMMENT '播放次数',
  `price` int(11) DEFAULT '0' COMMENT '视频价格',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COMMENT='视频列表';

-- ----------------------------
-- Records of video_list
-- ----------------------------
INSERT INTO `video_list` VALUES ('28', '21', '123456', '156578772779.png', '1565787727756.mp4', 'wocaocoaocaocoocaosdada', '1', '1', '4', '0', null, '0', null);
INSERT INTO `video_list` VALUES ('29', '23', '的空间啊', '1565787728332.png', '156578772818.mp4', 'wocaocoaocaocoocaosdada', '1', '1', '7', '0', null, '0', null);
INSERT INTO `video_list` VALUES ('30', '23', '的空间啊32321', '156578772885.png', '1565787728250.mp4', 'wocaocoaocaocoocaosdada', '1', null, '8', '9', null, '0', null);
INSERT INTO `video_list` VALUES ('31', '24', '发的挂号费', '1565787729422.png', '1565787729403.mp4', 'wocaocoaocaocoocaosdada', '1', null, '9', '0', null, '0', null);
INSERT INTO `video_list` VALUES ('32', '25', '给对方搞定', '1565787767126.png', '1565787767945.mp4', 'wocaocoaocaocoocaosdada', '1', null, '10', '0', null, '0', '2019-08-14 13:02:47');
INSERT INTO `video_list` VALUES ('33', '26', '3322多喝点多个2', '1565787768532.png', '1565787768460.mp4', 'wocaocoaocaocoocaosdada', '1', null, '11', '0', null, '0', '2019-08-14 13:02:48');
INSERT INTO `video_list` VALUES ('34', '26', '  多个发的', '1565787768714.png', '1565787768428.mp4', 'wocaocoaocaocoocaosdada', '1', null, '12', '0', null, '0', '2019-08-14 13:02:48');
INSERT INTO `video_list` VALUES ('35', '8', '好的鬼地方个', '1565787847211.png', '1565787847952.mp4', 'wocaocoaocaocoocaosdada', '1', null, '12', '0', null, '0', '2019-08-14 13:04:07');
INSERT INTO `video_list` VALUES ('36', '8', '回复回复给对方', '1565787848546.png', '1565787848268.mp4', 'wocaocoaocaocoocaosdada', '1', null, '12', '0', null, '0', '2019-08-14 13:04:08');

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
