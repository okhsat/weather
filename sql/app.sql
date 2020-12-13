/*
 Navicat Premium Data Transfer

 Source Server         : App
 Source Server Type    : MySQL
 Source Server Version : 100414
 Source Host           : 141.136.41.1:3306
 Source Schema         : u352767641_app

 Target Server Type    : MySQL
 Target Server Version : 100414
 File Encoding         : 65001

 Date: 13/12/2020 03:54:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for city
-- ----------------------------
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `temprature` int(3) DEFAULT NULL,
  `weather` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A',
  `created` datetime(0) DEFAULT NULL,
  `updated` datetime(0) DEFAULT NULL,
  `deleted` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of city
-- ----------------------------
INSERT INTO `city` VALUES (1, 'Istanbul', 18, 'Mostly cloudy', 'A', '2020-12-10 14:40:32', '2020-12-10 02:46:35', NULL);
INSERT INTO `city` VALUES (2, 'Moscow', -6, 'Mostly sunny', 'A', '2020-12-10 14:41:05', NULL, NULL);
INSERT INTO `city` VALUES (3, 'Beijing', 2, 'Clear with periodic clouds', 'A', '2020-12-10 14:42:14', '2020-12-10 14:45:01', NULL);
INSERT INTO `city` VALUES (4, 'Berlin', 0, 'Cloudy', 'A', '2020-12-10 14:42:48', NULL, NULL);
INSERT INTO `city` VALUES (5, 'Paris', 6, 'Mostly cloudy', 'A', '2020-12-10 14:45:37', NULL, NULL);
INSERT INTO `city` VALUES (6, 'New York', 4, 'Partly cloudy', 'A', '2020-12-10 14:46:11', NULL, NULL);
INSERT INTO `city` VALUES (7, 'Vienna', 3, 'Light rain showers', 'A', '2020-12-10 14:46:47', '2020-12-10 14:46:55', NULL);
INSERT INTO `city` VALUES (8, 'Rome', 12, 'Partly cloudy', 'A', '2020-12-10 14:48:37', NULL, NULL);
INSERT INTO `city` VALUES (9, 'London', 7, 'Cloudy', 'A', '2020-12-10 14:49:08', NULL, NULL);
INSERT INTO `city` VALUES (10, 'Delhi', 24, 'Haze', 'A', '2020-12-10 14:50:05', '2020-12-10 14:50:09', NULL);

-- ----------------------------
-- Table structure for coupon
-- ----------------------------
DROP TABLE IF EXISTS `coupon`;
CREATE TABLE `coupon`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A',
  `created` datetime(0) DEFAULT NULL,
  `deleted` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of coupon
-- ----------------------------
INSERT INTO `coupon` VALUES (1, 'ttfghffgiohgf9ih', 'A', '2020-11-25 13:26:09', '2020-12-10 15:49:41');
INSERT INTO `coupon` VALUES (2, 'thtthg5y4y55y5t', 'A', '2020-12-09 15:48:56', NULL);
INSERT INTO `coupon` VALUES (3, 'fdsuhf98sdy8fsd', 'A', '2020-12-09 15:48:56', NULL);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `gender` varchar(7) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `role` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `city_ids` varchar(511) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `coupon_added` datetime(0) DEFAULT NULL,
  `timezone` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '+03:00',
  `cid` varchar(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'tr',
  `os` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'A',
  `created` datetime(0) DEFAULT NULL,
  `updated` datetime(0) DEFAULT NULL,
  `deleted` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `Ind_cid`(`cid`) USING BTREE,
  INDEX `Ind_username`(`username`) USING BTREE,
  INDEX `Ind_status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
