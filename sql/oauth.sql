/*
 Navicat Premium Data Transfer

 Source Server         : OAuth
 Source Server Type    : MySQL
 Source Server Version : 100414
 Source Host           : 000.000.000.000:3306
 Source Schema         : oauth

 Target Server Type    : MySQL
 Target Server Version : 100414
 File Encoding         : 65001

 Date: 13/12/2020 03:55:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for oauth_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens`  (
  `access_token` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `expires` datetime(0) DEFAULT NULL,
  `scope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime(0) DEFAULT NULL,
  `updated_at` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`access_token`) USING BTREE,
  INDEX `client_id`(`client_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `oauth_access_tokens_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `oauth_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `oauth_access_tokens_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for oauth_authorization_codes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_authorization_codes`;
CREATE TABLE `oauth_authorization_codes`  (
  `authorization_code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `expires` datetime(0) DEFAULT NULL,
  `redirect_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime(0) DEFAULT NULL,
  `updated_at` datetime(0) DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`authorization_code`) USING BTREE,
  INDEX `client_id`(`client_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `oauth_authorization_codes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `oauth_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `oauth_authorization_codes_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for oauth_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients`  (
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `redirect_uri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `grant_types` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime(0) DEFAULT NULL,
  `updated_at` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`client_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `oauth_clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `oauth_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of oauth_clients
-- ----------------------------
INSERT INTO `oauth_clients` VALUES ('general-3426', 'General', 'iytds43wert67y8urrdfg6766dfg676drf7', 'http://ebtps.net', 'password,client_credentials,authorization_code,implicit,refresh_token', NULL, NULL, '2020-12-05 20:11:30', NULL);

-- ----------------------------
-- Table structure for oauth_jwt
-- ----------------------------
DROP TABLE IF EXISTS `oauth_jwt`;
CREATE TABLE `oauth_jwt`  (
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `subject` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `public_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime(0) DEFAULT NULL,
  `updated_at` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`client_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for oauth_refresh_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens`  (
  `refresh_token` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `expires` datetime(0) DEFAULT NULL,
  `scope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime(0) DEFAULT NULL,
  `updated_at` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`) USING BTREE,
  INDEX `client_id`(`client_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `oauth_refresh_tokens_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `oauth_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `oauth_refresh_tokens_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for oauth_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_scopes`;
CREATE TABLE `oauth_scopes`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scope` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `created_at` datetime(0) DEFAULT NULL,
  `updated_at` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `scope`(`scope`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of oauth_scopes
-- ----------------------------
INSERT INTO `oauth_scopes` VALUES (1, 'profile', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for oauth_users
-- ----------------------------
DROP TABLE IF EXISTS `oauth_users`;
CREATE TABLE `oauth_users`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime(0) DEFAULT NULL,
  `updated_at` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
