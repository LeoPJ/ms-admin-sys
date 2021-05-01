/*
Navicat MySQL Data Transfer

Source Server         : test
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : ms-admin-sys

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2021-04-30 00:59:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jw_goods
-- ----------------------------
DROP TABLE IF EXISTS `jw_goods`;
CREATE TABLE `jw_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增序号',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `kucun` int(11) DEFAULT NULL COMMENT '库存',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of jw_goods
-- ----------------------------
INSERT INTO `jw_goods` VALUES ('1', '商品1', '10');
INSERT INTO `jw_goods` VALUES ('2', '商品2', '5');
INSERT INTO `jw_goods` VALUES ('3', '商品3', '8');

-- ----------------------------
-- Table structure for jw_order
-- ----------------------------
DROP TABLE IF EXISTS `jw_order`;
CREATE TABLE `jw_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增序号',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品ID',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `num` int(11) DEFAULT NULL COMMENT '数量',
  `status` int(2) DEFAULT '0' COMMENT '状态：0-未支付；1-已支付',
  `trade_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=62925 DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
-- Records of jw_order
-- ----------------------------
INSERT INTO `jw_order` VALUES ('62920', '3', '4', '3', '0', 'ssh3_2021043000390924119', '1619714354');
INSERT INTO `jw_order` VALUES ('62921', '3', '4', '3', '0', 'ssh3_2021043000390933784', '1619714354');
INSERT INTO `jw_order` VALUES ('62922', '1', '6', '3', '0', 'ssh1_2021043000404713025', '1619714452');
INSERT INTO `jw_order` VALUES ('62923', '1', '6', '3', '0', 'ssh1_2021043000404742897', '1619714453');
INSERT INTO `jw_order` VALUES ('62924', '1', '6', '3', '0', 'ssh1_2021043000404636319', '1619714454');

-- ----------------------------
-- Table structure for jw_user
-- ----------------------------
DROP TABLE IF EXISTS `jw_user`;
CREATE TABLE `jw_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增序号',
  `name` varchar(50) DEFAULT NULL COMMENT '姓名',
  `phone` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of jw_user
-- ----------------------------
