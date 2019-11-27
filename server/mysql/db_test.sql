/*
 Navicat Premium Data Transfer

 Source Server         : 宝塔-本地数据库
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : db_test

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 25/10/2019 15:41:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `admin_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理角色id',
  `admin_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理角色名称',
  `admin_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '介绍',
  `authority_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限表id，多个权限以英文逗号隔开。并且前后都要加逗号。如： ,admin_authority_id,admin_authority_id,admin_authority_id,',
  `admin_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序。升序排在前面，并且等于0的排在最后面。',
  `admin_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '管理员状态。0 封禁|1 正常|2等待审核',
  `admin_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间(时间戳，秒)',
  `admin_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳，秒)',
  PRIMARY KEY (`admin_id`) USING BTREE,
  UNIQUE INDEX `admin_id`(`admin_id`) USING BTREE,
  INDEX `admin_name`(`admin_name`(191)) USING BTREE,
  INDEX `admin_sort`(`admin_sort`) USING BTREE,
  INDEX `admin_state`(`admin_state`) USING BTREE,
  INDEX `admin_update_time`(`admin_update_time`) USING BTREE,
  INDEX `admin_insert_time`(`admin_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('admin1', '测试开发', '', ',user,admin,admin_add,admin_read,admin_user_add,admin_user_read,admin_log_read,admin_edit,admin_remove,shop,user_read,user_edit,user_phone_add,user_phone_edit,user_phone_remove,shop_goods_read,shop_goods_add,shop_goods_edit,shop_goods_trash,shop_goods_trash_read,shop_goods_remove,application,shop_goods_type_edit,shop_goods_spu_add,shop_goods_spu_edit,shop_goods_spu_remove,shop_goods_sku_add,shop_goods_sku_edit,shop_goods_sku_remove,shop_goods_image_upload,shop_goods_image_remove,shop_goods_image_edit,shop_goods_file_upload,shop_goods_file_remove,shop_goods_file_edit,merchant,merchant_add,merchant_edit,merchant_read,merchant_remove,merchant_user_add,merchant_user_edit,merchant_user_remove,merchant_user_read,application_type_read,user_identity_edit,user_identity_read,cms,cms_article_add,cms_article_edit,cms_article_read,application_cache_clear,application_cache_read,application_config_pay_read,application_config_pay_edit,merchant_credit_read,merchant_credit_edit,application_slideshow_add,application_slideshow_read,application_slideshow_edit,application_slideshow_remove,merchant_withdraw_read,merchant_withdraw_state,user_credit_read,user_credit_edit,merchant_money_read,merchant_money_edit,user_money_read,user_money_edit,merchant_image_upload,merchant_image_remove,merchant_image_edit,admin_user_edit,admin_user_remove,application_type_add,application_type_remove,application_type_edit,user_config_read,merchant_config_read,user_config_edit,merchant_config_edit,cms_article_trash,cms_article_trash_read,cms_article_trash_edit,cms_article_trash_restore,shop_goods_when_read,shop_goods_when_add,shop_goods_when_edit,shop_goods_when_remove,shop_goods_trash_restore,merchant_tally_read,shop_order_read,shop_order_details_read,shop_order_trash_read,shop_order_trash,shop_order_trash_restore,shop_order_shipping,shop_order_state,user_comment_read,user_comment_remove,user_comment_edit,shop_config_read,shop_config_edit,application_config_read,application_config_edit,shop_goods_administrator,user_phone_verify_code,shop_group_goods_read,shop_group_goods_add,shop_group_goods_edit,shop_group_goods_remove,user_add,application_coupon_add,application_coupon_read,application_coupon_edit,application_coupon_remove,shop_order_administrator,merchant_cashier_list,merchant_cashier_remove,merchant_cashier_state,shop_order_menu,shop_order_region_menu,shop_goods_group_add,shop_goods_group_edit,shop_goods_group_remove,shop_goods_group_read,shop_read,', 0, 1, 1567991459, 1542265548);

-- ----------------------------
-- Table structure for admin_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log`  (
  `admin_log_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户id',
  `api_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '接口ID',
  `session_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '会话id',
  `admin_log_ip` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `admin_log_session` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '该数据结构是一个json数据。保持整个会话信息',
  `admin_log_real_args` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '该数据结构是一个json数据。是一个索引数组，保存传入的实际参数。',
  `admin_log_clear_args` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '该数据结构是一个json数据。是一个索引数组，保存传入的清理后参数。',
  `admin_log_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳，秒)',
  PRIMARY KEY (`admin_log_id`) USING BTREE,
  UNIQUE INDEX `admin_log_id`(`admin_log_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理操作日记表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user`  (
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户id',
  `authority_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '(保留字段)(这个是要跟管理角色合并的字段)权限表id，多个权限以英文逗号隔开。并且前后都要加逗号。如： ,admin_authority_id,admin_authority_id,admin_authority_id,',
  `admin_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理角色id',
  `admin_user_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '介绍',
  `admin_user_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置信息等，是一个json数据',
  `admin_user_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '管理员状态(保留字段)。0 封禁|1 正常',
  `admin_user_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序。升序排在前面，并且等于0的排在最后面。',
  `admin_user_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳，秒)',
  `admin_user_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间(时间戳，秒)',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `admin_id`(`admin_id`) USING BTREE,
  INDEX `admin_user_state`(`admin_user_state`) USING BTREE,
  INDEX `admin_user_sort`(`admin_user_sort`) USING BTREE,
  INDEX `admin_user_insert_time`(`admin_user_insert_time`) USING BTREE,
  INDEX `admin_user_update_time`(`admin_user_update_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES ('1', '', 'admin1', '', '{\"page_size\":\"20\"}', 1, 0, 1542265548, 1551939884);

-- ----------------------------
-- Table structure for agent_region
-- ----------------------------
DROP TABLE IF EXISTS `agent_region`;
CREATE TABLE `agent_region`  (
  `agent_region_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '代理地区ID',
  `agent_region_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '代理地区的简介',
  `agent_region_scope` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '范围。0表示未知，1省级代理，2市级代理，3区级代理',
  `agent_region_province` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '省',
  `agent_region_city` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '市',
  `agent_region_district` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '区',
  `agent_region_details` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `agent_region_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `agent_region_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0封禁，1开启',
  `agent_region_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `agent_region_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `agent_region_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`agent_region_id`) USING BTREE,
  UNIQUE INDEX `agent_region_id`(`agent_region_id`) USING BTREE,
  UNIQUE INDEX `province_city_district`(`agent_region_province`, `agent_region_city`, `agent_region_district`) USING BTREE,
  INDEX `agent_region_province`(`agent_region_province`) USING BTREE,
  INDEX `agent_region_city`(`agent_region_city`) USING BTREE,
  INDEX `agent_region_district`(`agent_region_district`) USING BTREE,
  INDEX `agent_region_details`(`agent_region_details`(191)) USING BTREE,
  INDEX `agent_region_state`(`agent_region_state`) USING BTREE,
  INDEX `agent_region_insert_time`(`agent_region_insert_time`) USING BTREE,
  INDEX `agent_region_update_time`(`agent_region_update_time`) USING BTREE,
  INDEX `agent_region_scope`(`agent_region_scope`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '代理地区' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for agent_user
-- ----------------------------
DROP TABLE IF EXISTS `agent_user`;
CREATE TABLE `agent_user`  (
  `agent_user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '代理用户ID',
  `user_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `agent_region_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '代理地区ID',
  `agent_user_interview_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '面试地址',
  `agent_user_interview_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '面试联系方式',
  `agent_user_interview_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '面试时间',
  `agent_user_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `agent_user_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '审核状态：0未通过审核，1通过审核，2待审核',
  `agent_user_award_state` tinyint(1) NOT NULL DEFAULT 0 COMMENT '奖励状态：0关闭，1开启',
  `agent_user_fail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '审核失败信息',
  `agent_user_state_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间',
  `agent_user_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资料更新时间,时间戳，秒',
  `agent_user_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间,时间戳，秒',
  PRIMARY KEY (`agent_user_id`) USING BTREE,
  UNIQUE INDEX `agent_user_id`(`agent_user_id`) USING BTREE,
  UNIQUE INDEX `user_agent_region`(`user_id`, `agent_region_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `agent_user_state`(`agent_user_state`) USING BTREE,
  INDEX `agent_user_update_time`(`agent_user_update_time`) USING BTREE,
  INDEX `agent_user_insert_time`(`agent_user_insert_time`) USING BTREE,
  INDEX `agent_user_state_time`(`agent_user_state_time`) USING BTREE,
  INDEX `agent_user_interview_time`(`agent_user_interview_time`) USING BTREE,
  INDEX `agent_user_interview_phone`(`agent_user_interview_phone`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '代理用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for agent_user_credit_award_log
-- ----------------------------
DROP TABLE IF EXISTS `agent_user_credit_award_log`;
CREATE TABLE `agent_user_credit_award_log`  (
  `id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '主键ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_credit` bigint(20) NOT NULL DEFAULT 0 COMMENT '用户积分数量',
  `order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID',
  `order_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '订单时间',
  `config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置',
  `province` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '市',
  `district` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '区',
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0未处理1已处理',
  `insert_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '插入时间',
  `update_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `province`(`province`) USING BTREE,
  INDEX `city`(`city`) USING BTREE,
  INDEX `district`(`district`) USING BTREE,
  INDEX `state`(`state`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for brand
-- ----------------------------
DROP TABLE IF EXISTS `brand`;
CREATE TABLE `brand`  (
  `brand_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '品牌ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `brand_logo_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'LOGO，存的是图片表ID',
  `brand_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺名称',
  `brand_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '品牌简介',
  `brand_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `brand_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0隐藏；1显示',
  `brand_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'JSON数据',
  `brand_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `brand_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  PRIMARY KEY (`brand_id`) USING BTREE,
  UNIQUE INDEX `brand_id`(`brand_id`) USING BTREE,
  INDEX `brand_logo_image_id`(`brand_logo_image_id`) USING BTREE,
  INDEX `brand_name`(`brand_name`(191)) USING BTREE,
  INDEX `brand_sort`(`brand_sort`) USING BTREE,
  INDEX `brand_state`(`brand_state`) USING BTREE,
  INDEX `brand_update_time`(`brand_update_time`) USING BTREE,
  INDEX `brand_insert_time`(`brand_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '品牌表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for brand_type
-- ----------------------------
DROP TABLE IF EXISTS `brand_type`;
CREATE TABLE `brand_type`  (
  `brand_type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '品牌分类ID',
  `brand_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '品牌ID',
  `type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `brand_type_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`brand_type_id`) USING BTREE,
  UNIQUE INDEX `brand_type_id`(`brand_type_id`) USING BTREE,
  INDEX `brand_id`(`brand_id`) USING BTREE,
  INDEX `type_id`(`type_id`) USING BTREE,
  INDEX `brand_type_time`(`brand_type_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '品牌分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_article
-- ----------------------------
DROP TABLE IF EXISTS `cms_article`;
CREATE TABLE `cms_article`  (
  `cms_article_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `cms_article_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `cms_article_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '消息简介',
  `cms_article_keywords` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签、关键字，用于微导航、搜索。输入时，多个用英文逗号隔开。而储存时，前后都要加上英文逗号，有利于搜索',
  `cms_article_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容摘要,简介。为空则自动获取文章内容',
  `cms_article_source` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文章来源(为空则是原创，否则是转载，会显示文章来源)',
  `cms_article_click_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击量',
  `cms_article_search_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '搜索次数统计(每被搜索一次，+1)',
  `cms_article_word_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '字数统计。计算文章内容的字数，不包括html标签。',
  `cms_article_comment_limit` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论限制，0允许评论和1禁止评论',
  `cms_article_read_limit` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读限制。 0无限制(公开，所有人都能看)；1 所属应用可见；',
  `cms_article_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文章内容',
  `cms_article_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '状态。0关闭，1发布，2审核中，3编辑中',
  `cms_article_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `cms_article_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `cms_article_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '储存JSON数据',
  `cms_article_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `cms_article_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `cms_article_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  PRIMARY KEY (`cms_article_id`) USING BTREE,
  UNIQUE INDEX `cms_article_id`(`cms_article_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `cms_article_name`(`cms_article_name`(191)) USING BTREE,
  INDEX `cms_article_state`(`cms_article_state`) USING BTREE,
  INDEX `cms_article_update_time`(`cms_article_update_time`) USING BTREE,
  INDEX `cms_article_insert_time`(`cms_article_insert_time`) USING BTREE,
  INDEX `cms_article_keywords`(`cms_article_keywords`(191)) USING BTREE,
  INDEX `cms_article_word_count`(`cms_article_word_count`) USING BTREE,
  INDEX `cms_article_comment_limit`(`cms_article_comment_limit`) USING BTREE,
  INDEX `cms_article_read_limit`(`cms_article_read_limit`) USING BTREE,
  INDEX `cms_article_trash`(`cms_article_trash`) USING BTREE,
  INDEX `cms_article_trash_time`(`cms_article_trash_time`) USING BTREE,
  INDEX `cms_article_sort`(`cms_article_sort`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '内容管理文章表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_article_image
-- ----------------------------
DROP TABLE IF EXISTS `cms_article_image`;
CREATE TABLE `cms_article_image`  (
  `cms_article_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '应用软件文章图片ID',
  `cms_article_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '应用软件文章ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `cms_article_image_main` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否主图。0否，1是',
  `cms_article_image_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用软件文章时间',
  PRIMARY KEY (`cms_article_image_id`) USING BTREE,
  UNIQUE INDEX `cms_article_image_id`(`cms_article_image_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `cms_article_image_main`(`cms_article_image_main`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `cms_article_id`(`cms_article_id`) USING BTREE,
  INDEX `cms_article_image_time`(`cms_article_image_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用软件文章文件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_article_type
-- ----------------------------
DROP TABLE IF EXISTS `cms_article_type`;
CREATE TABLE `cms_article_type`  (
  `cms_article_type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '品牌分类ID',
  `cms_article_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '品牌ID',
  `type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `cms_article_type_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`cms_article_type_id`) USING BTREE,
  UNIQUE INDEX `cms_article_type_id`(`cms_article_type_id`) USING BTREE,
  INDEX `cms_article_id`(`cms_article_id`) USING BTREE,
  INDEX `type_id`(`type_id`) USING BTREE,
  INDEX `cms_article_type_time`(`cms_article_type_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '内容管理文章分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`  (
  `config_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '键',
  `config_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '设置字符串值',
  `config_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '值类型：为空或者\"string\"表示字符串，\"integer\"表示整数，\"float\"表示浮点数，\"json\"、\"serialize\"解码。自动转换数据。',
  `config_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `config_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '描述信息',
  `config_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `config_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `config_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后的修改时间',
  PRIMARY KEY (`config_id`) USING BTREE,
  UNIQUE INDEX `config_id`(`config_id`) USING BTREE,
  INDEX `config_type`(`config_type`(191)) USING BTREE,
  INDEX `config_name`(`config_name`(191)) USING BTREE,
  INDEX `config_sort`(`config_sort`) USING BTREE,
  INDEX `config_insert_time`(`config_insert_time`) USING BTREE,
  INDEX `config_update_time`(`config_update_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `config` VALUES ('admin_page_size', '10', 'integer', '后台分页的每页默认条数', '', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('agent_user_credit_award', '{\"state\":1}', 'json', '代理用户的积分奖励', '参数详解：{\"state\":\"是否开启。为真表示开启，为假表示关闭\"}', 0, 1559025890, 1559025890);
INSERT INTO `config` VALUES ('aip_access', '{\"id\":\"\",\"key\":\"\",\"secret\":\"\"}', 'json', '百度AI开放平台账号配置', '参数详解：{\"id\":\"APP_ID\",\"secret\":\"SECRET_KEY\",\"key\":\"API_KEY\"}', 0, 1547434268, 1553071573);
INSERT INTO `config` VALUES ('alipay_access', '{\"id\":\"\",\"rsa_private_key\":\"\",\"alipayrsa_public_key\":\"\",\"state\":1}', 'json', '支付宝支付配置', '参考详解：{\"id\":\"appId\",\"rsa_private_key\":\"请填写开发者私钥去头去尾去回车，一行字符串\",\"alipayrsa_public_key\":\"请填写支付宝公钥，一行字符串\",\"state\":\"为真表示开启，为假表示关闭\"}', 0, 1551758439, 1567088665);
INSERT INTO `config` VALUES ('alipay_withdraw_access', '{\"id\":\"\",\"rsa_private_key\":\"\",\"alipayrsa_public_key\":\"\",\"state\":1}', 'json', '支付宝提现配置', '参考详解：{\"id\":\"appId\",\"rsa_private_key\":\"请填写开发者私钥去头去尾去回车，一行字符串\",\"alipayrsa_public_key\":\"请填写支付宝公钥，一行字符串\",\"state\":\"为真表示开启，为假表示关闭\"}', 0, 1547434268, 1567088674);
INSERT INTO `config` VALUES ('app_android_version', '{\"name\":\"初版20190228\",\"info\":\"开发测试版本\",\"number\":\"1.2.6\",\"download\":\"https:\\/\\/fir.im\\/8flm\",\"required\":0}', 'json', '安卓APP版本配置', '参数详解：{\"name\":\"版本名称\",\"info\":\"版本描述\",\"number\":\"版本号\",\"required\":\"是否强制更新，0不强制，1强制\",\"download\":\"下载地址\"}', 0, 1547434268, 1566280450);
INSERT INTO `config` VALUES ('credit', '{\"scale\":100,\"precision\":2}', 'json', '积分设置', '参数详解：{\"scale\":\"单位进制，如100，当积分为1230，（1230/100）显示则为12.30\",\"precision\":\"精度，保留的小数位数\"}', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('daily_attendance_earn_user_credit', '{\"credit\":100,\"state\":1}', 'json', '用户每日签到赠送积分', '参数详解：{\"credit\":\"签到赚取的积分数量\",\"state\":\"为真表示开启，为假表示关闭\"}', 0, 1547434268, 1553069161);
INSERT INTO `config` VALUES ('dysms_access', '{\"id\":\"\",\"secret\":\"\",\"product\":\"Dysmsapi\",\"domain\":\"dysmsapi.aliyuncs.com\",\"region\":\"cn-hangzhou\",\"end_point_name\":\"cn-hangzhou\"}', 'json', '阿里云短信权限配置', '阿里云的云通信短信服务API产品。参数详解：{\"id\":\"AccessKey ID\",\"secret\":\"AccessKey Secret\",\"product\":\"产品名称:云通信短信服务API产品,开发者无需替换\",\"domain\":\"产品域名,开发者无需替换\",\"region\":\"地区，暂时不支持多Region\",\"end_point_name\":\"服务结点\"}', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('dysms_send_reset_password', '{\"sign_name\":\"优佳贝母婴\",\"template_code\":\"SMS_95635433\",\"out_id\":\"\",\"sms_up_extend_code\":\"\"}', 'json', '阿里云短信重置密码参数', '参数详解：{\"sign_name\":\"短信签名\",\"template_code\":\"模板CODE\",\"out_id\":\"流水号\",\"sms_up_extend_code\":\"上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）\"}', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('dysms_send_reset_pay_password', '{\"sign_name\":\"优佳贝母婴\",\"template_code\":\"SMS_95635432\",\"out_id\":\"\",\"sms_up_extend_code\":\"\"}', 'json', '阿里云短信重置支付密码参数', '参数详解：{\"sign_name\":\"短信签名\",\"template_code\":\"模板CODE\",\"out_id\":\"流水号\",\"sms_up_extend_code\":\"上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）\"}', 0, 1547434268, 1553071573);
INSERT INTO `config` VALUES ('dysms_send_sign_up', '{\"sign_name\":\"优佳贝母婴\",\"template_code\":\"SMS_95635434\",\"out_id\":\"\",\"sms_up_extend_code\":\"\"}', 'json', '阿里云短信注册参数', '参数详解：{\"sign_name\":\"短信签名\",\"template_code\":\"模板CODE\",\"out_id\":\"流水号\",\"sms_up_extend_code\":\"上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）\"}', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('merchant_data', '{\"type_num\":\"20\",\"goods_num\":\"100\",\"state\":1}', 'json', '商家相关配置', '参数详解：{\"type_num\":\"可添加商家商品分类数量20\",\"goods_num\":\"可上传商品的数量20\",\"state\":\"是否开启限制，1是0否\"}', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('page_size', '12', 'integer', '前台分页的每页默认条数', '', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('parent_recommend_user_credit', '{\"ratio_user_credit\":\"0.01\",\"ratio_merchant_user_credit\":\"0.005\",\"algorithm\":\"floor\",\"state\":1}', 'json', '消费用户推荐人与商家用户推荐人的平台积分奖励配置', '参数详解：{\"ratio_user_credit\":\"[推荐人] 可获得 [被推荐人(无论是普通用户还是其他商家用户)] 与 商家 交易时的所得积分的比值\",\"ratio_merchant_user_credit\":\"[推荐人] 可获得 [被推荐商家用户] 与 [其他普通用户或是其他商家用户] 交易时的送出积分的比值\",\"state\":\"为真表示开启，为假表示关闭\",\"algorithm\":\"round对浮点数进行四舍五入 、ceil进一法取整、floor舍去法取整\"}', 0, 1547434268, 1553071573);
INSERT INTO `config` VALUES ('qiniu_access', '{\"id\":\"\",\"secret\":\"\",\"bucket\":\"\",\"expires\":3600}', 'json', '七牛云配置', '参数详解：{\"id\":\"accessKey\",\"secret\":\"secretKey\",\"bucket\":\"储存空间\",\"expires\":\"有效时间\"}', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('qiniu_domain', 'http://img.eonfox.cc/', 'string', '七牛云域名绑定配置', '', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('rmb_buy_merchant_credit', '{\"ratio_credit\":625,\"ratio_rmb\":100,\"algorithm\":\"ceil\",\"state\":1}', 'json', '人民币购买商家积分配置', '参数详解：{\"ratio_credit\":\"积分比值，单位个\",\"ratio_rmb\":\"人民币比值，单位分\",\"state\":\"为真表示开启，为假表示关闭\",\"algorithm\":\"round对浮点数进行四舍五入 、ceil进一法取整、floor舍去法取整\"}', 0, 1547434268, 1553142274);
INSERT INTO `config` VALUES ('rmb_consume_user_credit', '{\"ratio_credit\":625,\"ratio_rmb\":100,\"algorithm\":\"floor\",\"state\":1}', 'json', '用户消费平台赠送积分配置', '参数详解：{\"ratio_credit\":\"积分比值，单位个\",\"ratio_rmb\":\"人民币比值，单位分\",\"state\":\"为真表示开启，为假表示关闭\",\"algorithm\":\"round对浮点数进行四舍五入 、ceil进一法取整、floor舍去法取整\"}', 0, 1547434268, 1553072814);
INSERT INTO `config` VALUES ('rmb_withdraw_merchant_money', '{\"min_merchant_money\":100,\"state\":1}', 'json', '商家钱包(交易账户)提现配置', '参数详解：{\"min_merchant_money\":\"最小提现金额，单位分\",\"state\":\"为真表示开启，为假表示关闭\"}', 0, 1547434268, 1563759087);
INSERT INTO `config` VALUES ('shop_order', '{\"order_timeout\":86400}', 'json', '购物商城配置', 'order_timeout 订单超时时间，单位秒。', 0, 1554690809, 1554690809);
INSERT INTO `config` VALUES ('shop_order_user_comment', '{\"check\":0,\"state\":1}', 'json', '购物商城订单评论配置', '参数详解：{\"check\":\"评论是否需要审核\",\"state\":\"是否开启评论。为真表示开启，为假表示关闭\"}', 0, 1547434268, 1554790163);
INSERT INTO `config` VALUES ('user_identity', '{\"expire_time\":\"604800\",\"expire_state\":0,\"auto_state\":0}', 'json', '用户实名认证配置', '参数详解：{\"expire_time\":\"认证时间有效期，单位秒。\",\"expire_state\":\"为真表示开启，为假表示关闭\",\"auto_state\":\"是否自动认证，0不自动需要审核(默认)，1自动\"}', 0, 1547434268, 1553843921);
INSERT INTO `config` VALUES ('user_recommend_domain', 'http://xxx.com/', 'string', '用户推广域名配置', '', 0, 1547434268, 1547434268);
INSERT INTO `config` VALUES ('weixin_applet_access', '{\"id\":\"\",\"secret\":\"\"}', 'json', '微信小程序配置', '参数详解：{\"id\":\"appid微信分配的小程序ID \",\"secret\":\"Appsecret\"}', 0, 1547434268, 1568873886);
INSERT INTO `config` VALUES ('weixin_app_access', '{\"id\":\"11\",\"secret\":\"22\"}', 'json', '微信APP应用配置', '参数详解：{\"id\":\"appid微信分配的应用ID \",\"secret\":\"Appsecret\"}', 0, 1547434268, 1567088705);
INSERT INTO `config` VALUES ('weixin_mp_access', '{\"id\":\"\",\"secret\":\"\"}', 'json', '微信公众号、H5网页微信授权配置', '参数详解：{\"id\":\"微信支付分配的公众账号ID（企业号corpid即为此appId）\",\"secret\":\"Appsecret\"}', 0, 1568256265, 1567585303);
INSERT INTO `config` VALUES ('weixin_pay_access', '{\"mch_id\":\"\",\"pay_key\":\"\",\"spbill_create_ip\":\"192.168.0.2\",\"ssl_cert\":\"\",\"state\":1,\"service_appid\":\"\",\"service_mch_id\":\"\"}', 'json', '微信支付商户配置', '参数详解：{\"pay_key\":\"支付密匙\",\"mch_id\":\"微信支付分配的商户号\",\"spbill_create_ip\":\"该IP同在商户平台设置的IP白名单中的IP没有关联，该IP可传用户端或者服务端的IP。\",\"ssl_cert\":\"证书cert\",\"ssl_key\":\"证书key\",\"state\":\"为真表示开启，为假表示关闭\",\"service_mch_id\":\"服务商的微信支付分配的商户号\",\"service_appid\":\"服务商商户的APPID\"}', 0, 1547434268, 1567149151);

-- ----------------------------
-- Table structure for coupon
-- ----------------------------
DROP TABLE IF EXISTS `coupon`;
CREATE TABLE `coupon`  (
  `coupon_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '主键ID',
  `coupon_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `coupon_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '描述信息',
  `coupon_module` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所属模块',
  `coupon_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '不同优惠模块的主键。当coupon_module是shop模块，那么coupon_key就是shop_id的值',
  `coupon_label` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签',
  `coupon_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '注释信息',
  `coupon_property` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属类型，0人民币分，1积分',
  `coupon_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型。0未知|1满减|2代金|3抵扣|4折扣',
  `coupon_limit_min` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最小金额，0表示不限制。\r\n1）满减，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。\r\n2）代金，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。\r\n3）折扣，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。\r\n4）抵扣，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。',
  `coupon_limit_max` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最大金额，0表示不限制',
  `coupon_discount` bigint(20) NOT NULL DEFAULT 0 COMMENT '整数，当是整数，单位百分。当是人民币，单位分    当是折扣券时,表示多少折（百分）；当是代金券是,表示券价值多少分（单位分）',
  `coupon_start_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期—开始，0则领取后计时',
  `coupon_end_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期—结束，0则永久有效',
  `coupon_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0失效，1正常',
  `coupon_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `coupon_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`coupon_id`) USING BTREE,
  UNIQUE INDEX `coupon_id`(`coupon_id`) USING BTREE,
  INDEX `coupon_name`(`coupon_name`) USING BTREE,
  INDEX `coupon_module`(`coupon_module`) USING BTREE,
  INDEX `coupon_label`(`coupon_label`) USING BTREE,
  INDEX `coupon_type`(`coupon_type`) USING BTREE,
  INDEX `coupon_limit_min`(`coupon_limit_min`) USING BTREE,
  INDEX `coupon_limit_max`(`coupon_limit_max`) USING BTREE,
  INDEX `coupon_start_time`(`coupon_start_time`) USING BTREE,
  INDEX `coupon_end_time`(`coupon_end_time`) USING BTREE,
  INDEX `coupon_state`(`coupon_state`) USING BTREE,
  INDEX `coupon_insert_time`(`coupon_insert_time`) USING BTREE,
  INDEX `coupon_update_time`(`coupon_update_time`) USING BTREE,
  INDEX `coupon_key`(`coupon_key`) USING BTREE,
  INDEX `coupon_property`(`coupon_property`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '优惠券' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for event
-- ----------------------------
DROP TABLE IF EXISTS `event`;
CREATE TABLE `event`  (
  `event_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '事件ID',
  `event_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '事件名称。与时间标记合成唯一值',
  `event_stamp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标记。与事件名称合成唯一值',
  `event_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '执行信息',
  `event_error` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '错误信息',
  `event_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据，储存配置等信息',
  `event_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0未完成，1完成',
  `event_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间。时间戳，秒',
  `event_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`event_id`) USING BTREE,
  UNIQUE INDEX `event_id`(`event_id`) USING BTREE,
  UNIQUE INDEX `event_name_stamp`(`event_name`, `event_stamp`) USING BTREE,
  INDEX `event_name`(`event_name`) USING BTREE,
  INDEX `event_timestamp`(`event_stamp`) USING BTREE,
  INDEX `event_state`(`event_state`) USING BTREE,
  INDEX `event_update_time`(`event_update_time`) USING BTREE,
  INDEX `event_insert_time`(`event_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '事件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for express_order
-- ----------------------------
DROP TABLE IF EXISTS `express_order`;
CREATE TABLE `express_order`  (
  `express_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '快递--订单ID',
  `user_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_parent_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '代理人ID--父级ID',
  `user_coupon_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '是否使用优惠券--用户优惠券ID',
  `express_order_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件物品类型',
  `express_order_rebate_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '折扣金额',
  `express_order_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单金额',
  `express_order_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户信息（寄件人信息\\收件人信息\\骑手信息\\代理人的提成配置）',
  `shipping_sign` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '快递的类型，快递名称英文',
  `shipping_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配送物流ID',
  `shipping_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '快递名称',
  `express_order_shipping_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '运单号',
  `express_order_shipping_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0未发货，等待发货；1确认收货; 2已发货，运送中',
  `express_order_shipping_send_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发货时间，时间戳，秒',
  `express_order_shipping_take_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '确认收货、拿货时间，时间戳，秒',
  `express_rider_user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '骑手的用户ID',
  `express_rider_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '骑手电话',
  `user_address_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件人地址id',
  `user_address_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件人姓名',
  `user_address_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件人电话',
  `user_address_province` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件人省',
  `user_address_city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件人市',
  `user_address_district` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件人区',
  `user_address_details` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '寄件人详细地址',
  `express_order_get_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收件人姓名',
  `express_order_get_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收件人电话',
  `express_order_get_province` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收件人省',
  `express_order_get_city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收件人市',
  `express_order_get_district` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收件人区',
  `express_order_get_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收件人详细地址',
  `order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资金订单ID，当支付成功后产生的订单数据',
  `express_order_pay_method` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '支付方式',
  `express_order_pay_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付费用，单位人民币分  express_order_money - express_order_rebate_money = express_order_pay_money',
  `express_order_pay_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '支付： 0=》寄件人支付（现付）   1=》收件人支付（到付）',
  `express_order_pay_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0未支付；1支付成功; ',
  `express_order_pay_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付时间',
  `express_order_agent_royalty` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '代理人的提成',
  `express_order_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `express_order_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `express_order_delete_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户删除状态。0未删，1已删',
  `express_order_delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户删除时间',
  `express_order_delete_msg` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '删除原因',
  `express_order_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `express_order_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `express_order_insured_state` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否保价   0：否   1：是',
  `express_order_receive_start` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '接单起时间',
  `express_order_receive_end` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '接单结束时间',
  `express_order_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '订单状态   0=》撤销    1=》已完成  2=》下单  3=》已接单  4=》已取件',
  `express_order_pick_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '取件时间  express_order_state = 4 的时候',
  `express_order_access_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接单时间',
  `express_order_coupon_forbidden_state` tinyint(1) NOT NULL DEFAULT 0 COMMENT '邀请奖励优惠券禁用状态，1 禁用 0 可用',
  PRIMARY KEY (`express_order_id`) USING BTREE,
  UNIQUE INDEX `express_order_id`(`express_order_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_parent_id`(`user_parent_id`) USING BTREE,
  INDEX `user_coupon_id`(`user_coupon_id`) USING BTREE,
  INDEX `express_order_type`(`express_order_type`) USING BTREE,
  INDEX `express_order_rebate_money`(`express_order_rebate_money`) USING BTREE,
  INDEX `express_order_money`(`express_order_money`) USING BTREE,
  INDEX `shipping_sign`(`shipping_sign`) USING BTREE,
  INDEX `shipping_id`(`shipping_id`) USING BTREE,
  INDEX `express_order_shipping_state`(`express_order_shipping_state`) USING BTREE,
  INDEX `express_order_shipping_send_time`(`express_order_shipping_send_time`) USING BTREE,
  INDEX `express_order_shipping_take_time`(`express_order_shipping_take_time`) USING BTREE,
  INDEX `express_rider_user_id`(`express_rider_user_id`) USING BTREE,
  INDEX `user_address_id`(`user_address_id`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE,
  INDEX `express_order_state`(`express_order_state`) USING BTREE,
  INDEX `express_order_pick_time`(`express_order_pick_time`) USING BTREE,
  INDEX `express_order_access_time`(`express_order_access_time`) USING BTREE,
  INDEX `express_order_trash`(`express_order_trash`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '快递订单与快递单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for express_rider
-- ----------------------------
DROP TABLE IF EXISTS `express_rider`;
CREATE TABLE `express_rider`  (
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `express_rider_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `express_rider_info` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '简介',
  `express_rider_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电话',
  `express_rider_province` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分配的省份',
  `express_rider_city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分配的城市',
  `express_rider_district` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分配的地区',
  `express_rider_on_off` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '由骑手操作，是否开启接单。0关闭，1开启',
  `express_rider_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态。0 审核失败，1已认证，2等待审核',
  `express_rider_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `express_rider_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `express_rider_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `express_rider_name`(`express_rider_name`(191)) USING BTREE,
  INDEX `express_rider_province`(`express_rider_province`) USING BTREE,
  INDEX `express_rider_city`(`express_rider_city`) USING BTREE,
  INDEX `express_rider_district`(`express_rider_district`) USING BTREE,
  INDEX `express_rider_on_off`(`express_rider_on_off`) USING BTREE,
  INDEX `express_rider_state`(`express_rider_state`) USING BTREE,
  INDEX `express_rider_update_time`(`express_rider_update_time`) USING BTREE,
  INDEX `express_rider_insert_time`(`express_rider_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '快递骑手' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for file
-- ----------------------------
DROP TABLE IF EXISTS `file`;
CREATE TABLE `file`  (
  `file_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `file_storage` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '储存位置。如“qiniu”七牛',
  `file_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `file_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `file_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '储存地址。如果 storage是 qiniu 则该值边储存的是bucket名称',
  `file_format` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件的格式、后缀',
  `file_click` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击量',
  `file_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型、格式，小写',
  `file_size` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '大小，字节',
  `file_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `file_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0上传中，1已上传完成回调',
  `file_hash` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件的hash值',
  `file_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `file_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `file_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `file_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`file_id`) USING BTREE,
  UNIQUE INDEX `file_id`(`file_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `file_type`(`file_type`(191)) USING BTREE,
  INDEX `file_size`(`file_size`) USING BTREE,
  INDEX `file_sort`(`file_sort`) USING BTREE,
  INDEX `file_state`(`file_state`) USING BTREE,
  INDEX `file_storage`(`file_storage`(191)) USING BTREE,
  INDEX `file_format`(`file_format`(191)) USING BTREE,
  INDEX `file_name`(`file_name`(191)) USING BTREE,
  INDEX `file_hash`(`file_hash`(191)) USING BTREE,
  INDEX `file_insert_time`(`file_insert_time`) USING BTREE,
  INDEX `file_update_time`(`file_update_time`) USING BTREE,
  INDEX `file_trash`(`file_trash`) USING BTREE,
  INDEX `file_trash_time`(`file_trash_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件表。包括文档、视频等文件数据' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_client
-- ----------------------------
DROP TABLE IF EXISTS `house_client`;
CREATE TABLE `house_client`  (
  `house_client_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `merchant_user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所属的商家用户表ID',
  `house_client_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户姓名',
  `house_client_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户手机号',
  `house_client_sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户性别。0保密，1男，2女',
  `house_client_birthdate` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '出生年月日',
  `house_client_age` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户年龄',
  `house_client_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `house_client_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`house_client_id`) USING BTREE,
  UNIQUE INDEX `merchant_user_client_phone`(`merchant_user_id`, `house_client_phone`) USING BTREE,
  UNIQUE INDEX `house_agent_client_id`(`house_client_id`) USING BTREE,
  INDEX `house_agent_client_name`(`house_client_name`) USING BTREE,
  INDEX `house_agent_client_phone`(`house_client_phone`) USING BTREE,
  INDEX `house_agent_client_insert_time`(`house_client_insert_time`) USING BTREE,
  INDEX `house_agent_client_update_time`(`house_client_update_time`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `merchant_user_id`(`merchant_user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家楼盘经纪人的客户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_filing
-- ----------------------------
DROP TABLE IF EXISTS `house_filing`;
CREATE TABLE `house_filing`  (
  `house_filing_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '报备ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `house_product_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘项目ID',
  `house_filing_agent_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '报备人',
  `house_filing_agent_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '报备电话',
  `house_filing_agent_company` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '报备公司',
  `house_filing_client_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户姓名',
  `house_filing_client_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户手机号。135****1234',
  `house_filing_client_sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户性别。0保密，1男，2女',
  `house_filing_client_age` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户年龄',
  `house_filing_client_birthdate` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户出生年月日',
  `house_filing_visit_time` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '到访时间',
  `house_filing_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未读1已读',
  `house_filing_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `house_filing_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `house_filing_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  UNIQUE INDEX `house_filing_id`(`house_filing_id`) USING BTREE,
  INDEX `house_filing_insert_time`(`house_filing_insert_time`) USING BTREE,
  INDEX `house_filing_update_time`(`house_filing_update_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `house_filing_client_name`(`house_filing_client_name`(191)) USING BTREE,
  INDEX `house_product_id`(`house_product_id`) USING BTREE,
  INDEX `house_filing_read`(`house_filing_read`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家楼盘经纪人的客户报备表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_order
-- ----------------------------
DROP TABLE IF EXISTS `house_order`;
CREATE TABLE `house_order`  (
  `house_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘订单表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `house_product_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘项目ID',
  `house_product_user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘项目发布者用户ID',
  `house_product_verify_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '楼盘项目核实状态。0假，1真，2未处理',
  `house_product_verify_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '楼盘项目核实时间',
  `house_order_client_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户姓名',
  `house_order_client_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户手机号',
  `house_order_agent_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经纪人姓名',
  `house_order_agent_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经纪人手机号',
  `house_order_trader_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易人姓名',
  `house_order_trader_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易人手机号',
  `house_order_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '状态。0拒绝，1成交，2编辑中，3初始化',
  `house_order_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `house_order_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `house_order_insert_time` bigint(20) UNSIGNED NOT NULL COMMENT '插入时间',
  `house_order_update_time` bigint(20) UNSIGNED NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`house_order_id`) USING BTREE,
  UNIQUE INDEX `house_order_id`(`house_order_id`) USING BTREE,
  INDEX `merchant_id`(`house_product_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `house_order_client_name`(`house_order_client_name`) USING BTREE,
  INDEX `house_order_client_phone`(`house_order_client_phone`) USING BTREE,
  INDEX `house_order_agent_name`(`house_order_agent_name`) USING BTREE,
  INDEX `house_order_agent_phone`(`house_order_agent_phone`) USING BTREE,
  INDEX `house_order_trader_name`(`house_order_trader_name`) USING BTREE,
  INDEX `house_order_trader_phone`(`house_order_trader_phone`) USING BTREE,
  INDEX `house_order_state`(`house_order_state`) USING BTREE,
  INDEX `house_order_insert_time`(`house_order_insert_time`) USING BTREE,
  INDEX `house_order_update_time`(`house_order_update_time`) USING BTREE,
  INDEX `house_order_trash`(`house_order_trash`) USING BTREE,
  INDEX `house_order_trash_time`(`house_order_trash_time`) USING BTREE,
  INDEX `house_product_user_id`(`house_product_user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家楼盘订单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_order_image
-- ----------------------------
DROP TABLE IF EXISTS `house_order_image`;
CREATE TABLE `house_order_image`  (
  `house_order_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城商品图片表ID',
  `house_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘订单表ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `house_order_image_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `house_order_image_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`house_order_image_id`) USING BTREE,
  UNIQUE INDEX `house_order_image_id`(`house_order_image_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `house_order_image_time`(`house_order_image_time`) USING BTREE,
  INDEX `house_order_image_type`(`house_order_image_type`(191)) USING BTREE,
  INDEX `house_order_id`(`house_order_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城楼盘订单图片表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_product
-- ----------------------------
DROP TABLE IF EXISTS `house_product`;
CREATE TABLE `house_product`  (
  `house_product_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘产品表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `house_product_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '项目简介',
  `house_product_developer` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '开发商',
  `house_product_manage_company` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '物业公司',
  `house_product_manage_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '物业费。分/平方',
  `house_product_recommend_level` tinyint(1) UNSIGNED NOT NULL DEFAULT 5 COMMENT '推荐指数。单位星。最小0个星，最大5个星',
  `house_product_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘—项目名称',
  `house_product_type` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘—类型',
  `house_product_total_area` decimal(20, 3) UNSIGNED NOT NULL DEFAULT 0.000 COMMENT '楼盘—总面积，单位平方米',
  `house_product_total_floor` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '楼盘—总层高',
  `house_product_total_floor_area` decimal(20, 2) NOT NULL DEFAULT 0.00 COMMENT '楼盘—总建筑面积',
  `house_product_total_room` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '楼盘—总房子数',
  `house_product_plot_ratio` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '楼盘—容积率，单位%',
  `house_product_greening_rate` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '楼盘—绿化率，单位%',
  `house_product_country` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '中国' COMMENT '地址—国',
  `house_product_province` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '地址—省',
  `house_product_city` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '地址—市',
  `house_product_district` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '地址—区',
  `house_product_address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '地址—详细地址',
  `house_product_longitude` decimal(13, 10) NOT NULL DEFAULT 0.0000000000 COMMENT '地址—经度',
  `house_product_latitude` decimal(13, 10) NOT NULL DEFAULT 0.0000000000 COMMENT '地址—纬度',
  `house_product_address_sale` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '地址—销售处',
  `house_product_room_floor` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '房子—楼层，层高',
  `house_product_room_height` decimal(20, 1) UNSIGNED NOT NULL DEFAULT 0.0 COMMENT '房子—高度，单位米',
  `house_product_room_rate` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '房子—得房率。单位%',
  `house_product_room_price` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '房子—价格',
  `house_product_room_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '房子—销售状态。0停止，1在售',
  `house_product_property_right` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '房子—产权年限，单位年',
  `house_product_ladder_ratio` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '房子—梯户比',
  `house_product_agent_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经纪人—对接人姓名',
  `house_product_agent_company` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经纪人—销售公司',
  `house_product_agent_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经纪人—渠道公司电话',
  `house_product_agent_commision` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经纪人—参考佣金',
  `house_product_time_land` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '拿地时间',
  `house_product_time_sale` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '开盘时间',
  `house_product_time_delivery` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交房时间',
  `house_product_remark` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `house_product_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据。比如：{\"项目分析\":[\"项目分析1\",\"项目分析2\"],\"项目动态\":[{\"time\":0000000,\"content\":\"项目动态1内容\"},{\"time\":0000000,\"content\":\"项目动态2内容\"}]}',
  `house_product_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `house_product_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 4 COMMENT '状态。0审核失败；1审核通过；2待审核；3编辑中; 4初始化',
  `house_product_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `house_product_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `house_product_delete_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0未删除1已删除',
  `house_product_delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  `house_product_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `house_product_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `wechat_group_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信群ID',
  PRIMARY KEY (`house_product_id`) USING BTREE,
  UNIQUE INDEX `house_product_id`(`house_product_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `house_type_id`(`house_product_type`) USING BTREE,
  INDEX `house_product_name`(`house_product_name`(191)) USING BTREE,
  INDEX `house_product_country`(`house_product_country`(191)) USING BTREE,
  INDEX `house_product_province`(`house_product_province`(191)) USING BTREE,
  INDEX `house_product_city`(`house_product_city`(191)) USING BTREE,
  INDEX `house_product_district`(`house_product_district`(191)) USING BTREE,
  INDEX `house_product_developers`(`house_product_developer`(191)) USING BTREE,
  INDEX `house_product_manage_company`(`house_product_manage_company`(191)) USING BTREE,
  INDEX `house_product_manage_money`(`house_product_manage_money`) USING BTREE,
  INDEX `house_product_longitude`(`house_product_longitude`) USING BTREE,
  INDEX `house_product_latitude`(`house_product_latitude`) USING BTREE,
  INDEX `house_product_open_time`(`house_product_time_sale`(191)) USING BTREE,
  INDEX `house_product_handover_time`(`house_product_time_delivery`(191)) USING BTREE,
  INDEX `house_product_land_time`(`house_product_time_land`(191)) USING BTREE,
  INDEX `house_product_sales_company`(`house_product_agent_company`(191)) USING BTREE,
  INDEX `house_product_agent_name`(`house_product_agent_name`(191)) USING BTREE,
  INDEX `house_product_agent_phone`(`house_product_agent_phone`(191)) USING BTREE,
  INDEX `house_product_insert_time`(`house_product_insert_time`) USING BTREE,
  INDEX `house_product_update_time`(`house_product_update_time`) USING BTREE,
  INDEX `house_product_state`(`house_product_state`) USING BTREE,
  INDEX `house_product_trash`(`house_product_trash`) USING BTREE,
  INDEX `house_product_trash_time`(`house_product_trash_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家楼盘项目表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_product_image
-- ----------------------------
DROP TABLE IF EXISTS `house_product_image`;
CREATE TABLE `house_product_image`  (
  `house_product_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城商品图片表ID',
  `house_product_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘产品表ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `house_product_image_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `house_product_image_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`house_product_image_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `house_product_image_time`(`house_product_image_time`) USING BTREE,
  INDEX `house_product_image_type`(`house_product_image_type`(191)) USING BTREE,
  INDEX `house_product_id`(`house_product_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家楼盘产品图片表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_product_top
-- ----------------------------
DROP TABLE IF EXISTS `house_product_top`;
CREATE TABLE `house_product_top`  (
  `house_product_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘项目表ID',
  `house_product_top_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `house_product_top_start_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '置顶开始时间',
  `house_product_top_end_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '置顶结束时间',
  `house_product_top_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `house_product_top_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`house_product_id`) USING BTREE,
  UNIQUE INDEX `house_product_id`(`house_product_id`) USING BTREE,
  INDEX `house_product_top_sort`(`house_product_top_sort`) USING BTREE,
  INDEX `house_product_top_start_time`(`house_product_top_start_time`) USING BTREE,
  INDEX `house_product_top_end_time`(`house_product_top_end_time`) USING BTREE,
  INDEX `house_product_top_insert_time`(`house_product_top_insert_time`) USING BTREE,
  INDEX `house_product_top_update_time`(`house_product_top_update_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '楼盘项目置顶表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_top_option
-- ----------------------------
DROP TABLE IF EXISTS `house_top_option`;
CREATE TABLE `house_top_option`  (
  `house_top_option_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘置顶表ID',
  `house_top_option_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `house_top_option_info` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '简介',
  `house_top_option_month` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '置顶时间。单位月',
  `house_top_option_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '置顶金额。单位分',
  `house_top_option_remarks` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `house_top_option_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `house_top_option_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `house_top_option_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`house_top_option_id`) USING BTREE,
  UNIQUE INDEX `house_top_name`(`house_top_option_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '楼盘置顶选项' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house_top_order
-- ----------------------------
DROP TABLE IF EXISTS `house_top_order`;
CREATE TABLE `house_top_order`  (
  `house_top_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘置顶订单表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID',
  `house_product_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘项目表ID',
  `house_top_option_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '楼盘置顶选项表ID',
  `house_top_option_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '置顶名称',
  `house_top_option_month` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '置顶时间。单位月',
  `house_top_option_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '置顶价格。单位分',
  `house_top_order_pay_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付状态。0未支付，1已支付',
  `house_top_order_pay_method` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '支付方式',
  `house_top_order_pay_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付金额。单位分',
  `house_top_order_pay_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付时间',
  `house_top_order_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单状态。0取消订单，1确认订单',
  `house_top_order_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `house_top_order_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `house_top_order_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `house_top_order_delete_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户删除状态。0未删，1已删',
  `house_top_order_delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户删除时间',
  `house_top_order_trash_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收状态。0未回收，1已回收',
  `house_top_order_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  PRIMARY KEY (`house_top_order_id`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '楼盘置顶订单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for image
-- ----------------------------
DROP TABLE IF EXISTS `image`;
CREATE TABLE `image`  (
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `image_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `image_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `image_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '储存地址。如果 storage是 qiniu 则该值边储存的是bucket名称',
  `image_storage` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '储存位置。如“qiniu”七牛',
  `image_format` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件的格式、后缀',
  `image_click` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击量',
  `image_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件类型',
  `image_width` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '像素宽',
  `image_height` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '像素高',
  `image_size` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '大小，字节',
  `image_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `image_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0上传中，未成功；1上传成功',
  `image_hash` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片的hash值',
  `image_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `image_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `image_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `image_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`image_id`) USING BTREE,
  UNIQUE INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `image_type`(`image_type`(191)) USING BTREE,
  INDEX `image_width`(`image_width`) USING BTREE,
  INDEX `image_height`(`image_height`) USING BTREE,
  INDEX `image_size`(`image_size`) USING BTREE,
  INDEX `image_sort`(`image_sort`) USING BTREE,
  INDEX `image_state`(`image_state`) USING BTREE,
  INDEX `image_name`(`image_name`(191)) USING BTREE,
  INDEX `image_format`(`image_format`(191)) USING BTREE,
  INDEX `image_hash`(`image_hash`(191)) USING BTREE,
  INDEX `image_insert_time`(`image_insert_time`) USING BTREE,
  INDEX `image_update_time`(`image_update_time`) USING BTREE,
  INDEX `image_trash`(`image_trash`) USING BTREE,
  INDEX `image_trash_time`(`image_trash_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '图片表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for lock
-- ----------------------------
DROP TABLE IF EXISTS `lock`;
CREATE TABLE `lock`  (
  `lock_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '锁ID',
  `lock_key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '字段名称',
  `lock_value` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '字段值',
  `lock_transaction` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '事务，如“merchant_credit积分”、“merchant_money余额”。同一个商家(或用户等其他模块)只能开启同一个事务。',
  `lock_expire_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失效时间',
  `lock_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  PRIMARY KEY (`lock_id`) USING BTREE,
  UNIQUE INDEX `lock_id`(`lock_id`) USING BTREE,
  UNIQUE INDEX `lock_key_value_transaction`(`lock_key`, `lock_value`, `lock_transaction`) USING BTREE,
  INDEX `lock_key`(`lock_key`) USING BTREE,
  INDEX `lock_transaction`(`lock_transaction`) USING BTREE,
  INDEX `lock_insert_time`(`lock_insert_time`) USING BTREE,
  INDEX `lock_expire_time`(`lock_expire_time`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '锁表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant
-- ----------------------------
DROP TABLE IF EXISTS `merchant`;
CREATE TABLE `merchant`  (
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `merchant_logo_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家LOGO，存的是图片表ID',
  `merchant_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家名称',
  `merchant_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `merchant_star` decimal(20, 2) NOT NULL DEFAULT 5.00 COMMENT '商家星星',
  `merchant_phone` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家手机号',
  `merchant_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `merchant_longitude` decimal(13, 10) NOT NULL DEFAULT 0.0000000000 COMMENT '经度',
  `merchant_latitude` decimal(13, 10) NOT NULL DEFAULT 0.0000000000 COMMENT '纬度',
  `merchant_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态。0 审核失败，1已认证，2等待审核，3编辑中',
  `merchant_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `merchant_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `merchant_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `merchant_license_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '营业执照图片',
  `merchant_other_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '其他图片，图片ID ',
  `merchant_province` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '省',
  `merchant_city` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '市',
  `merchant_district` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '区',
  `merchant_self` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否自营。0不是，1是',
  `merchant_weixin` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家微信',
  `merchant_user_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申请用户ID',
  PRIMARY KEY (`merchant_id`) USING BTREE,
  UNIQUE INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `merchant_name`(`merchant_name`(191)) USING BTREE,
  INDEX `merchant_longitude`(`merchant_longitude`) USING BTREE,
  INDEX `merchant_latitude`(`merchant_latitude`) USING BTREE,
  INDEX `merchant_logo_image_id`(`merchant_logo_image_id`) USING BTREE,
  INDEX `merchant_state`(`merchant_state`) USING BTREE,
  INDEX `merchant_update_time`(`merchant_update_time`) USING BTREE,
  INDEX `merchant_insert_time`(`merchant_insert_time`) USING BTREE,
  INDEX `merchant_other_image_id`(`merchant_other_image_id`(191)) USING BTREE,
  INDEX `merchant_license_image_id`(`merchant_license_image_id`(191)) USING BTREE,
  INDEX `merchant_phone`(`merchant_phone`) USING BTREE,
  INDEX `merchant_self`(`merchant_self`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_cashier
-- ----------------------------
DROP TABLE IF EXISTS `merchant_cashier`;
CREATE TABLE `merchant_cashier`  (
  `merchant_cashier_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收银员ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作人的用户ID',
  `merchant_cashier_action_user` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `merchant_cashier_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家收银员名称',
  `merchant_cashier_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商家用户简介',
  `merchant_cashier_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态。0 封禁，1 正常，2审核中',
  `merchant_cashier_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置信息等，是一个json数据',
  `merchant_cashier_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序。升序排在前面，并且等于0的排在最后面。',
  `merchant_cashier_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间(时间戳，秒)',
  `merchant_cashier_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳，秒)',
  PRIMARY KEY (`merchant_cashier_id`) USING BTREE,
  UNIQUE INDEX `merchant_cashier_id`(`merchant_cashier_id`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `merchant_cashier_action_user`(`merchant_cashier_action_user`) USING BTREE,
  INDEX `merchant_cashier_name`(`merchant_cashier_name`) USING BTREE,
  INDEX `merchant_cashier_state`(`merchant_cashier_state`) USING BTREE,
  INDEX `merchant_cashier_update_time`(`merchant_cashier_update_time`) USING BTREE,
  INDEX `merchant_cashier_insert_time`(`merchant_cashier_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家收银员' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_credit
-- ----------------------------
DROP TABLE IF EXISTS `merchant_credit`;
CREATE TABLE `merchant_credit`  (
  `merchant_credit_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家积分ID',
  `merchant_credit_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家积分关联ID，比如A向B账户转账，该A支出和B收入的数据中，就是相互对应的数据ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `merchant_credit_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购买、退款，还有管理员后台的操作：人工收入、人工支出',
  `merchant_credit_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，个。支出等于0，而该值不为0，表示该条数据为收入操作',
  `merchant_credit_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，个。收入等于0，而该值不为0，表示该条数据为支出操作',
  `merchant_credit_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩积分。当前最新所剩积分。单位，个',
  `merchant_credit_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`merchant_credit_id`) USING BTREE,
  UNIQUE INDEX `merchant_credit_id`(`merchant_credit_id`) USING BTREE,
  UNIQUE INDEX `merchant_id_time`(`merchant_id`, `merchant_credit_time`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `merchant_credit_type`(`merchant_credit_type`) USING BTREE,
  INDEX `merchant_credit_plus`(`merchant_credit_plus`) USING BTREE,
  INDEX `merchant_credit_minus`(`merchant_credit_minus`) USING BTREE,
  INDEX `merchant_credit_update_time`(`merchant_credit_time`) USING BTREE,
  INDEX `merchant_credit_join_id`(`merchant_credit_join_id`) USING BTREE,
  INDEX `merchant_credit_value`(`merchant_credit_value`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家积分表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_goods_type
-- ----------------------------
DROP TABLE IF EXISTS `merchant_goods_type`;
CREATE TABLE `merchant_goods_type`  (
  `merchant_goods_type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家--商品--商品分类关联表ID',
  `merchant_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `merchant_goods_type_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家商品分类名称',
  `merchant_goods_type_module` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '模块',
  `merchant_goods_type_state` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态。0隐藏  1显示',
  `merchant_goods_type_sort` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序',
  `update_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `insert_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`merchant_goods_type_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_image
-- ----------------------------
DROP TABLE IF EXISTS `merchant_image`;
CREATE TABLE `merchant_image`  (
  `merchant_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家图片表ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片ID',
  `merchant_image_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `merchant_image_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '时间',
  UNIQUE INDEX `merchant_image_id`(`merchant_image_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `merchant_image_type`(`merchant_image_type`(191)) USING BTREE,
  INDEX `merchant_image_time`(`merchant_image_time`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家图片' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_money
-- ----------------------------
DROP TABLE IF EXISTS `merchant_money`;
CREATE TABLE `merchant_money`  (
  `merchant_money_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户钱包余额ID',
  `merchant_money_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `merchant_money_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `merchant_money_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，元。支出等于0，而该值不为0，表示该条数据为收入操作',
  `merchant_money_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，元。收入等于0，而该值不为0，表示该条数据为支出操作',
  `merchant_money_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩余额。当前最新所剩余额。单位，分',
  `merchant_money_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`merchant_money_id`) USING BTREE,
  UNIQUE INDEX `merchant_money_id`(`merchant_money_id`) USING BTREE,
  UNIQUE INDEX `merchant_id_time`(`merchant_id`, `merchant_money_time`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `merchant_money_join_id`(`merchant_money_join_id`) USING BTREE,
  INDEX `merchant_money_plus`(`merchant_money_plus`) USING BTREE,
  INDEX `merchant_money_minus`(`merchant_money_minus`) USING BTREE,
  INDEX `merchant_money_time`(`merchant_money_time`) USING BTREE,
  INDEX `merchant_money_type`(`merchant_money_type`) USING BTREE,
  INDEX `merchant_money_value`(`merchant_money_value`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家钱包表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_tally
-- ----------------------------
DROP TABLE IF EXISTS `merchant_tally`;
CREATE TABLE `merchant_tally`  (
  `merchant_tally_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `merchant_tally_voucher` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收据，图片ID',
  `merchant_tally_goods_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `merchant_tally_goods_number` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品数量',
  `merchant_tally_goods_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品单价，单位分',
  `merchant_tally_client_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户手机号',
  `merchant_tally_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注信息',
  `merchant_tally_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态，保留字段,2编辑中',
  `merchant_tally_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序（保留字段）',
  `merchant_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置信息等，是一个json数据',
  `merchant_tally_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间(时间戳，秒)',
  `merchant_tally_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳，秒)',
  PRIMARY KEY (`merchant_tally_id`) USING BTREE,
  UNIQUE INDEX `merchant_tally_id`(`merchant_tally_id`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `merchant_tally_voucher`(`merchant_tally_voucher`) USING BTREE,
  INDEX `merchant_tally_goods_name`(`merchant_tally_goods_name`(191)) USING BTREE,
  INDEX `merchant_tally_update_time`(`merchant_tally_update_time`) USING BTREE,
  INDEX `merchant_tally_insert_time`(`merchant_tally_insert_time`) USING BTREE,
  INDEX `merchant_tally_client_phone`(`merchant_tally_client_phone`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家记账、线下订单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_type
-- ----------------------------
DROP TABLE IF EXISTS `merchant_type`;
CREATE TABLE `merchant_type`  (
  `merchant_type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家分类ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `merchant_type_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`merchant_type_id`) USING BTREE,
  UNIQUE INDEX `merchant_type_id`(`merchant_type_id`) USING BTREE,
  INDEX `type_id`(`type_id`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `merchant_type_time`(`merchant_type_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_user
-- ----------------------------
DROP TABLE IF EXISTS `merchant_user`;
CREATE TABLE `merchant_user`  (
  `merchant_user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家用户表ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `merchant_user_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家用户名称',
  `merchant_user_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商家用户简介',
  `merchant_user_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态。0 封禁，1 正常，2审核中',
  `merchant_user_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置信息等，是一个json数据',
  `merchant_user_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序。升序排在前面，并且等于0的排在最后面。',
  `merchant_user_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间(时间戳，秒)',
  `merchant_user_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳，秒)',
  PRIMARY KEY (`merchant_user_id`) USING BTREE,
  UNIQUE INDEX `merchant_user_id`(`merchant_user_id`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `merchant_user_name`(`merchant_user_name`) USING BTREE,
  INDEX `merchant_user_state`(`merchant_user_state`) USING BTREE,
  INDEX `merchant_user_update_time`(`merchant_user_update_time`) USING BTREE,
  INDEX `merchant_user_insert_time`(`merchant_user_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for merchant_withdraw
-- ----------------------------
DROP TABLE IF EXISTS `merchant_withdraw`;
CREATE TABLE `merchant_withdraw`  (
  `merchant_withdraw_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '提现ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID，产生交易的订单而关联的订单',
  `merchant_withdraw_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '公开备注、说明，产生这条数据的方提供信息',
  `merchant_withdraw_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '提现类型。如“merchant_money”即商家钱包',
  `merchant_withdraw_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '提现方式。如：微信支付“weixinpay”、支付宝支付\"alipay\"',
  `merchant_withdraw_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提现金额/数量',
  `merchant_withdraw_rmb` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提现金额，人民币分',
  `merchant_withdraw_admin` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员的用户ID',
  `merchant_withdraw_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态。0审核不通过，1提现成功，2审核中',
  `merchant_withdraw_pass_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间。时间戳，秒',
  `merchant_withdraw_fail_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核不通过时间。时间戳，秒',
  `merchant_withdraw_fail_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '审核不通过原因，由管理员填写',
  `merchant_withdraw_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`merchant_withdraw_id`) USING BTREE,
  UNIQUE INDEX `merchant_withdraw_id`(`merchant_withdraw_id`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE,
  INDEX `merchant_withdraw_type`(`merchant_withdraw_type`) USING BTREE,
  INDEX `merchant_withdraw_method`(`merchant_withdraw_method`) USING BTREE,
  INDEX `merchant_withdraw_value`(`merchant_withdraw_value`) USING BTREE,
  INDEX `merchant_withdraw_admin`(`merchant_withdraw_admin`) USING BTREE,
  INDEX `merchant_withdraw_state`(`merchant_withdraw_state`) USING BTREE,
  INDEX `merchant_withdraw_pass_time`(`merchant_withdraw_pass_time`) USING BTREE,
  INDEX `merchant_withdraw_fail_time`(`merchant_withdraw_fail_time`) USING BTREE,
  INDEX `merchant_withdraw_insert_time`(`merchant_withdraw_insert_time`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `merchant_withdraw_rmb`(`merchant_withdraw_rmb`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家提现表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for order
-- ----------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order`  (
  `order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID',
  `order_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `order_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单公开备注、说明，产生这条订单方提供信息',
  `order_plus_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收款方，收款方式。如积分则是表名称“merchant_credit”',
  `order_plus_account_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收款账户ID。收款方式是商家积分，账户id就是商家id。收款方式是用户积分，账户id就是用户id。',
  `order_plus_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '值，如果收款类型是积分，则该值就是积分，如果是人名币，该值就是单位分',
  `order_plus_transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收款方交易ID。如果收款方式是商家积分表“merchant_credit”那么该值就是“user_credit_id”的值',
  `order_plus_label` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签。用户自定义，用于自定义统计',
  `order_plus_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '收款方私有备注，用户自定义备注',
  `order_plus_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收款方，更新时间。时间戳，秒  如修改label等',
  `order_action_user_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作人ID',
  `order_minus_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '付款方的付款方式，如微信支付“weixinpay”、支付宝支付\"alipay\"、余额支付\"user_money\"、积分支付\"user_credit\"，如用户积分表“user_credit”',
  `order_minus_account_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '付款账户ID',
  `order_minus_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '值，如果支付的是积分，该值即是积分。如果是第三方人民币支付，则是单位分',
  `order_minus_transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '付款方交易ID，如果付款方式是用户积分表“user_credit”那么该值就是“user_credit_id”的值。如果是微信支付，或者其他第三方交易平台，该值则是transaction ID',
  `order_minus_label` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签。用户自定义，用于自定义统计',
  `order_minus_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '付款方私有备注，用户自定义备注',
  `order_minus_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款方，更新时间。时间戳，秒  如修改label等',
  `order_sign` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备用标识，比如保存一些特殊的主键，一些防止重复的标签ID等',
  `order_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '该数据结构是一个json数据。主要储存当前支付的时候配置信息',
  `order_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '订单状态。0取消订单，1确认订单',
  `order_pay_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付状态。0未支付，1已支付',
  `order_pay_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款时间',
  `order_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`order_id`) USING BTREE,
  UNIQUE INDEX `order_id`(`order_id`) USING BTREE,
  INDEX `order_type`(`order_type`) USING BTREE,
  INDEX `order_plus_account_id`(`order_plus_account_id`) USING BTREE,
  INDEX `order_plus_method`(`order_plus_method`) USING BTREE,
  INDEX `order_plus_transaction_id`(`order_plus_transaction_id`) USING BTREE,
  INDEX `order_minus_method`(`order_minus_method`) USING BTREE,
  INDEX `order_minus_transaction_id`(`order_minus_transaction_id`) USING BTREE,
  INDEX `order_state`(`order_state`) USING BTREE,
  INDEX `order_insert_time`(`order_insert_time`) USING BTREE,
  INDEX `order_plus_value`(`order_plus_value`) USING BTREE,
  INDEX `order_minus_value`(`order_minus_value`) USING BTREE,
  INDEX `order_pay_time`(`order_pay_time`) USING BTREE,
  INDEX `order_plus_update_time`(`order_plus_update_time`) USING BTREE,
  INDEX `order_minus_update_time`(`order_minus_update_time`) USING BTREE,
  INDEX `order_pay_state`(`order_pay_state`) USING BTREE,
  INDEX `order_minus_account_id`(`order_minus_account_id`) USING BTREE,
  INDEX `order_plus_label`(`order_plus_label`) USING BTREE,
  INDEX `order_minus_label`(`order_minus_label`) USING BTREE,
  INDEX `order_action_user_id`(`order_action_user_id`) USING BTREE,
  INDEX `order_sign`(`order_sign`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shipping
-- ----------------------------
DROP TABLE IF EXISTS `shipping`;
CREATE TABLE `shipping`  (
  `shipping_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配送物流ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shipping_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配送名称',
  `shipping_sign` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '快递英文标识，如：yuantong',
  `shipping_info` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '信息',
  `shipping_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `shipping_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0隐藏，1显示',
  `shipping_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=>非默认   1=》是默认',
  `shipping_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shipping_property` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '运费类型，0人民币分，1积分',
  `shipping_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '运费，积分或者人民币(分)，根据shop_order_shipping_property判断',
  `shipping_module` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类模块，模块名称',
  `shipping_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '不同优惠模块的主键。当coupon_module是shop模块，那么coupon_key就是shop_id的值',
  `shipping_label` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签',
  `shipping_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '注释信息',
  `shipping_logo_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类别图标，存的是图片表ID',
  `shipping_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `shipping_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`shipping_id`) USING BTREE,
  UNIQUE INDEX `shipping_id`(`shipping_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shipping_name`(`shipping_name`(191)) USING BTREE,
  INDEX `shipping_insert_time`(`shipping_insert_time`) USING BTREE,
  INDEX `shipping_update_time`(`shipping_update_time`) USING BTREE,
  INDEX `shipping_state`(`shipping_state`) USING BTREE,
  INDEX `shipping_module`(`shipping_module`(191)) USING BTREE,
  INDEX `shipping_label`(`shipping_label`(191)) USING BTREE,
  INDEX `shipping_logo_image_id`(`shipping_logo_image_id`) USING BTREE,
  INDEX `shipping_property`(`shipping_property`) USING BTREE,
  INDEX `shipping_price`(`shipping_price`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配送物流表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of shipping
-- ----------------------------
INSERT INTO `shipping` VALUES ('1', '1', '自提', 'myself', '', '', 1, 0, 0, 0, 0, 'shop_order', '', '', '', '', 1556869265, 1556869265);
INSERT INTO `shipping` VALUES ('1554dsf', '2', '圆通速递', 'yuantongsudi', '', '', 1, 0, 0, 0, 0, 'express_order_shipping', '', '', '', '', 1556869265, 1556869265);
INSERT INTO `shipping` VALUES ('515454', '2', '邮政快递包裹', 'youzhengguonei', '', '', 1, 1, 0, 0, 1500, 'shop_order', '', '', '', '', 1556871365, 1556871365);
INSERT INTO `shipping` VALUES ('54f4sgf', '2', '顺丰速运', 'shunfengsuyun', '', '', 1, 0, 0, 0, 0, 'express_order_shipping', '', '', '', '', 1556869275, 1556869275);
INSERT INTO `shipping` VALUES ('564281', '3', '中通快递', 'zhongtong', '', '', 1, 0, 0, 0, 0, 'shop_order', '', '', '', '', 1556871985, 0);
INSERT INTO `shipping` VALUES ('568251', '2', '德邦物流', 'debangwuliu', '', '', 1, 0, 0, 0, 2000, 'shop_order', '', '', '', '', 1556871985, 0);

-- ----------------------------
-- Table structure for shop
-- ----------------------------
DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop`  (
  `shop_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID，创建人',
  `shop_logo_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'LOGO，存的是图片表ID',
  `shop_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺名称',
  `shop_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '店铺简介',
  `shop_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shop_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '状态。0审核失败；1审核成功；2待审核；3编辑中; ',
  `shop_on_off` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否营业。0停止营业，1营业中',
  `shop_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'JSON数据',
  `shop_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `shop_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  PRIMARY KEY (`shop_id`) USING BTREE,
  UNIQUE INDEX `shop_id`(`shop_id`) USING BTREE,
  INDEX `shop_logo_image_id`(`shop_logo_image_id`) USING BTREE,
  INDEX `shop_name`(`shop_name`(191)) USING BTREE,
  INDEX `shop_sort`(`shop_sort`) USING BTREE,
  INDEX `shop_state`(`shop_state`) USING BTREE,
  INDEX `shop_on_off`(`shop_on_off`) USING BTREE,
  INDEX `shop_update_time`(`shop_update_time`) USING BTREE,
  INDEX `shop_insert_time`(`shop_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '店铺表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_cart
-- ----------------------------
DROP TABLE IF EXISTS `shop_cart`;
CREATE TABLE `shop_cart`  (
  `shop_cart_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '购物车ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID。当用户已经登录的时候添加购物车，则为记录用户ID。',
  `session_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '会话ID。当用户没有登录的时候添加购物车则储存会话ID',
  `shop_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城商品ID',
  `shop_goods_sku_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品规格ID',
  `shop_cart_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '保存基础信息。jsons护具，如shop_goods_name，shop_goods_info，shop_goods_sku规格信息等等',
  `shop_cart_number` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '购物商品的数量',
  `shop_cart_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `shop_cart_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`shop_cart_id`) USING BTREE,
  UNIQUE INDEX `shop_cart_id`(`shop_cart_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `session_id`(`session_id`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `shop_cart_insert_time`(`shop_cart_insert_time`) USING BTREE,
  INDEX `shop_cart_update_time`(`shop_cart_update_time`) USING BTREE,
  INDEX `shop_goods_sku_id`(`shop_goods_sku_id`) USING BTREE,
  INDEX `shop_id`(`shop_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城购物车表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods`;
CREATE TABLE `shop_goods`  (
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城商品ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID（此字段不用）',
  `shop_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺ID(就是对应商家ID)',
  `brand_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '品牌ID',
  `shop_goods_sn` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品的货号',
  `shop_goods_property` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品类型。0普通商品，1是积分商品',
  `shop_goods_parent_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父级ID。当父ID不为空，则表示是配件，不能单独购买。子商品必须要与父级商品一起下单。',
  `shop_goods_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `shop_goods_index` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '索引标记。如 1 表示会员商品',
  `shop_goods_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品描述',
  `shop_goods_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '状态。0审核失败；1售卖中；2待审核；3停售编辑中; ',
  `shop_goods_warning` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品的警告，审核不通过的时候，由管理员填写',
  `shop_goods_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品详细资料',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_click` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击量',
  `shop_goods_sales` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销量',
  `shop_goods_stock_warning` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '库存警告。为0表示关闭，即不警告。将商品属性规格中的库存相加，等于或少于该值则发生警告。',
  `shop_goods_stock_mode` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '减库存方式。0表示不减库存；1表示下单预扣，如果买家在保留时间内未付款，那预扣库存会被释放出来重新供大家购买。有效时间以全局配置默认。2表示付款减库存。退款则恢复库存。3表示发货减库存,退货则恢复库存。',
  `shop_goods_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'meta关键字，放在商品页的关键字中，为搜索引擎收录用',
  `shop_goods_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'meta描述，为搜索引擎收录用',
  `shop_goods_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shop_goods_seller_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '卖家备注',
  `shop_goods_admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '管理员备注',
  `shop_goods_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `shop_goods_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `shop_goods_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后修改时间',
  `shop_goods_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`shop_goods_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `shop_goods_name`(`shop_goods_name`(191)) USING BTREE,
  INDEX `shop_goods_insert_time`(`shop_goods_insert_time`) USING BTREE,
  INDEX `shop_goods_update_time`(`shop_goods_update_time`) USING BTREE,
  INDEX `shop_goods_sort`(`shop_goods_sort`) USING BTREE,
  INDEX `shop_goods_state`(`shop_goods_state`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_goods_trash`(`shop_goods_trash`) USING BTREE,
  INDEX `shop_goods_trash_time`(`shop_goods_trash_time`) USING BTREE,
  INDEX `shop_goods_sn`(`shop_goods_sn`(191)) USING BTREE,
  INDEX `shop_goods_stock_mode`(`shop_goods_stock_mode`) USING BTREE,
  INDEX `brand_id`(`brand_id`) USING BTREE,
  INDEX `shop_id`(`shop_id`) USING BTREE,
  INDEX `shop_goods_property`(`shop_goods_property`) USING BTREE,
  INDEX `shop_goods_index`(`shop_goods_index`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城商品表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_file
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_file`;
CREATE TABLE `shop_goods_file`  (
  `shop_goods_file_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城商品文件表ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '产品表ID',
  `file_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_file_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`shop_goods_file_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_file_id`(`shop_goods_file_id`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_goods_file_time`(`shop_goods_file_time`) USING BTREE,
  INDEX `file_id`(`file_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城商品文件表。包括文档、视频等文件数据' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_group
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_group`;
CREATE TABLE `shop_goods_group`  (
  `shop_goods_group_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品ID',
  `shop_goods_sku_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品规格ID',
  `shop_goods_group_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `shop_goods_group_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `shop_goods_group_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shop_goods_group_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团购价',
  `shop_goods_group_people` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团购人数',
  `shop_goods_group_people_now` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团购当前人数',
  `shop_goods_group_start_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间戳',
  `shop_goods_group_end_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间戳',
  `shop_goods_group_success` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '拼团成功：0否，1是',
  `shop_goods_group_success_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '拼团成功时间戳',
  `shop_goods_group_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态：0已结束，1进行中，2未开始',
  `shop_goods_group_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `shop_goods_group_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `shop_order_group_delete` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除：0否，1是',
  `shop_order_group_delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  `shop_order_group_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收：0否，1是',
  `shop_order_group_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `shop_order_group_pay` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付：0否，1是',
  `shop_order_group_pay_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付时间',
  PRIMARY KEY (`shop_goods_group_id`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `shop_goods_sku_id`(`shop_goods_sku_id`) USING BTREE,
  INDEX `shop_goods_group_state`(`shop_goods_group_state`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '拼团商品' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_image
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_image`;
CREATE TABLE `shop_goods_image`  (
  `shop_goods_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城商品图片表ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品表ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_image_main` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否主图。0否，1是',
  `shop_goods_image_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`shop_goods_image_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_image_id`(`shop_goods_image_id`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_goods_image_time`(`shop_goods_image_time`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `shop_goods_image_main`(`shop_goods_image_main`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城商品图片表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_region
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_region`;
CREATE TABLE `shop_goods_region`  (
  `shop_goods_region_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品销售地区ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品表ID',
  `shop_goods_sku_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品规格ID，预留字段',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_region_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `shop_goods_region_scope` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销售范围。0表示未知，1省级售卖，2市级售卖，3区级售卖',
  `shop_goods_region_province` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '省',
  `shop_goods_region_city` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '市',
  `shop_goods_region_district` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '区',
  `shop_goods_region_details` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `shop_goods_region_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `shop_goods_region_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0封禁，1开启',
  `shop_goods_region_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shop_goods_region_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `shop_goods_region_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  UNIQUE INDEX `shop_goods_region_id`(`shop_goods_region_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_region`(`shop_goods_id`, `shop_goods_region_province`, `shop_goods_region_city`, `shop_goods_region_district`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `shop_goods_sku_id`(`shop_goods_sku_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_goods_region_scope`(`shop_goods_region_scope`) USING BTREE,
  INDEX `shop_goods_region_province`(`shop_goods_region_province`) USING BTREE,
  INDEX `shop_goods_region_city`(`shop_goods_region_city`) USING BTREE,
  INDEX `shop_goods_region_district`(`shop_goods_region_district`) USING BTREE,
  INDEX `shop_goods_region_state`(`shop_goods_region_state`) USING BTREE,
  INDEX `shop_goods_region_update_time`(`shop_goods_region_update_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品售卖地区表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_sku
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_sku`;
CREATE TABLE `shop_goods_sku`  (
  `shop_goods_sku_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品规格ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '产品表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片表ID',
  `shop_goods_spu_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品属性ID，是多个。数据存在时，首尾必须是英文逗号，多个并且是英文逗号分隔。如：,id1,id2,',
  `shop_goods_sku_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `shop_goods_sku_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `shop_goods_sku_stock` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '库存',
  `shop_goods_sku_market_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '市场价，积分或者人民币(分)',
  `shop_goods_sku_cost_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '成本价，积分或者人民币(分)。只能管理员可见',
  `shop_goods_sku_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '售卖积分或者人民币(分)',
  `shop_goods_sku_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `shop_goods_sku_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `shop_goods_sku_additional_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `shop_goods_sku_additional_credit` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`shop_goods_sku_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_sku_id`(`shop_goods_sku_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `shop_goods_sku_name`(`shop_goods_sku_name`) USING BTREE,
  INDEX `shop_goods_sku_insert_time`(`shop_goods_sku_insert_time`) USING BTREE,
  INDEX `shop_goods_sku_update_time`(`shop_goods_sku_update_time`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `shop_goods_sku_price`(`shop_goods_sku_price`) USING BTREE,
  INDEX `shop_goods_sku_market_price`(`shop_goods_sku_market_price`) USING BTREE,
  INDEX `shop_goods_sku_cost_price`(`shop_goods_sku_cost_price`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城商品规格表（Stock Keeping Unit，SKU对应多个SPU）' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_spu
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_spu`;
CREATE TABLE `shop_goods_spu`  (
  `shop_goods_spu_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品属性ID',
  `shop_goods_spu_parent_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '产品表ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_spu_required` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否必选。0不是，1是必选',
  `shop_goods_spu_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `shop_goods_spu_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `shop_goods_spu_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shop_goods_spu_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `shop_goods_spu_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`shop_goods_spu_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_spu_id`(`shop_goods_spu_id`) USING BTREE,
  INDEX `shop_goods_spu_parent_id`(`shop_goods_spu_parent_id`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_goods_spu_name`(`shop_goods_spu_name`(191)) USING BTREE,
  INDEX `shop_goods_spu_sort`(`shop_goods_spu_sort`) USING BTREE,
  INDEX `shop_goods_spu_insert_time`(`shop_goods_spu_insert_time`) USING BTREE,
  INDEX `shop_goods_spu_update_time`(`shop_goods_spu_update_time`) USING BTREE,
  INDEX `shop_goods_spu_required`(`shop_goods_spu_required`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城商品属性表（Standard Product Unit）' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_type
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_type`;
CREATE TABLE `shop_goods_type`  (
  `shop_goods_type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品分类ID',
  `shop_goods_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品ID',
  `type_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_type_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`shop_goods_type_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_type_id`(`shop_goods_type_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_type`(`shop_goods_id`, `type_id`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_goods_time`(`shop_goods_type_time`) USING BTREE,
  INDEX `type_id`(`type_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城商品分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_goods_when
-- ----------------------------
DROP TABLE IF EXISTS `shop_goods_when`;
CREATE TABLE `shop_goods_when`  (
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城商品ID',
  `shop_goods_when_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `shop_goods_when_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `shop_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_goods_when_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0已结束，1销售时间段，2还没有开始',
  `shop_goods_when_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shop_goods_when_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `shop_goods_when_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `shop_goods_when_start_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始售卖时间',
  `shop_goods_when_end_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束售卖时间',
  PRIMARY KEY (`shop_goods_id`) USING BTREE,
  UNIQUE INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `shop_goods_when_name`(`shop_goods_when_name`(191)) USING BTREE,
  INDEX `shop_id`(`shop_id`) USING BTREE,
  INDEX `shop_goods_when_sort`(`shop_goods_when_sort`) USING BTREE,
  INDEX `shop_goods_when_insert_time`(`shop_goods_when_insert_time`) USING BTREE,
  INDEX `shop_goods_when_update_time`(`shop_goods_when_update_time`) USING BTREE,
  INDEX `shop_goods_when_start_time`(`shop_goods_when_start_time`) USING BTREE,
  INDEX `shop_goods_when_end_time`(`shop_goods_when_end_time`) USING BTREE,
  INDEX `shop_goods_when_state`(`shop_goods_when_state`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城商品限时购表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_order
-- ----------------------------
DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE `shop_order`  (
  `shop_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城订单ID',
  `shop_order_parent_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父ID，为空则是顶级订单，顶级订单是系统结构非展示',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '购买用户，用户ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID',
  `shop_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺ID/商家ID。为空则自营',
  `shop_order_index` tinyint(1) NOT NULL DEFAULT 0 COMMENT '订单类型：0普通商品订单，1会员商品订单',
  `shop_order_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '订单状态。0取消订单，1确认订单',
  `shop_order_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `shop_order_buyer_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '买家备注、买家留言',
  `shop_order_seller_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '卖家备注、卖家留言',
  `shop_order_admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '管理员备注、留言，管理员自己看的',
  `shop_order_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单的应付金额，单位人民币分。应付金额 = 商品总价 - 折扣+运费',
  `shop_order_credit` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单的应付积分，单位人民币分。shop_order_credit = shop_order_goods_price商品总价(积分类型) - shop_order_discount_credit 积分折扣+运费(积分类型)',
  `shop_order_goods_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单的商品合计费用，积分或者人民币(分)。shop_order_property = 0 该字段就是存人民币，shop_order_property = 1 该字段就是存积分 ',
  `shop_order_goods_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单的商品合计费用，人民币(分)',
  `shop_order_goods_credit` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单的商品合计费用，积分数量',
  `shop_order_discount_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单的总折扣金额，单位人民币分',
  `shop_order_discount_credit` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单的总折扣积分',
  `shop_order_pay_parent` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是合并支付、是否是父级订单支付。0不是，1是。',
  `shop_order_pay_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)',
  `shop_order_pay_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '全部支付完成时间',
  `shop_order_pay_money_method` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '人民币支付方式',
  `shop_order_pay_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付费用，单位人民币分',
  `shop_order_pay_money_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '人民币支付的支付时间',
  `shop_order_pay_money_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '人民币支付状态。0未支付；1支付成功;',
  `shop_order_pay_money_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '人民币支付的资金订单id，当支付成功后产生的订单数据',
  `shop_order_pay_credit_method` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '积分的支付方式',
  `shop_order_pay_credit` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付的积分',
  `shop_order_pay_credit_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分支付的支付时间',
  `shop_order_pay_credit_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分支付状态。0未支付；1支付成功;',
  `shop_order_pay_credit_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '积分支付的资金订单id，当支付成功后产生的订单数据',
  `shipping_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配送ID',
  `shop_order_property` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单类型。0普通商品订单，1积分商品订单',
  `shop_order_shipping_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是合并配送、是否是父级订单配送。0不是，1是。',
  `shop_order_shipping_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员发货的物流ID',
  `shop_order_shipping_sign` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '管理员发货的物流推送英文标识，如：yuantong',
  `shop_order_shipping_property` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '运费类型，0人民币分，1积分',
  `shop_order_shipping_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '运费，积分或者人民币(分)，根据shop_order_shipping_property判断',
  `shop_order_shipping_no` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '物流运单号',
  `shop_order_shipping_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0未发货，等待发货；1确认收货; 2已发货，运送中',
  `shop_order_shipping_send_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发货时间，时间戳，秒',
  `shop_order_shipping_take_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '确认收货、拿货时间，时间戳，秒',
  `shop_order_comment_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论状态。0未评论，1已评论',
  `shop_order_close_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单关闭时间，订单状态未0时的操作时间',
  `shop_order_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单确认、生成时间',
  `shop_order_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `shop_order_delete_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户删除状态。0未删，1已删',
  `shop_order_delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户删除时间',
  `user_address_consignee` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的名字',
  `user_address_tel` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的电话',
  `user_address_phone` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的手机号',
  `user_address_country` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '中国' COMMENT '收货人的国家',
  `user_address_province` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的省份',
  `user_address_city` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人城市',
  `user_address_district` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的地区',
  `user_address_details` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的详细地址',
  `user_address_zipcode` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的邮编',
  `user_address_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的电子邮件',
  `shop_order_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `shop_order_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  PRIMARY KEY (`shop_order_id`) USING BTREE,
  UNIQUE INDEX `shop_order_id`(`shop_order_id`) USING BTREE,
  INDEX `shop_order_parent_id`(`shop_order_parent_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_order_state`(`shop_order_state`) USING BTREE,
  INDEX `shop_order_discount_money`(`shop_order_discount_money`) USING BTREE,
  INDEX `shop_order_pay_state`(`shop_order_pay_state`) USING BTREE,
  INDEX `shop_order_pay_time`(`shop_order_pay_time`) USING BTREE,
  INDEX `shipping_id`(`shipping_id`) USING BTREE,
  INDEX `shop_order_shipping_money`(`shop_order_shipping_property`) USING BTREE,
  INDEX `shop_order_shipping_state`(`shop_order_shipping_state`) USING BTREE,
  INDEX `shop_order_shipping_send_time`(`shop_order_shipping_send_time`) USING BTREE,
  INDEX `shop_order_shipping_take_time`(`shop_order_shipping_take_time`) USING BTREE,
  INDEX `shop_order_comment_state`(`shop_order_comment_state`) USING BTREE,
  INDEX `shop_order_close_time`(`shop_order_close_time`) USING BTREE,
  INDEX `shop_order_insert_time`(`shop_order_insert_time`) USING BTREE,
  INDEX `user_address_consignee`(`user_address_consignee`) USING BTREE,
  INDEX `user_address_phone`(`user_address_phone`) USING BTREE,
  INDEX `user_address_country`(`user_address_country`) USING BTREE,
  INDEX `user_address_province`(`user_address_province`) USING BTREE,
  INDEX `user_address_city`(`user_address_city`) USING BTREE,
  INDEX `user_address_district`(`user_address_district`) USING BTREE,
  INDEX `shop_id`(`shop_id`) USING BTREE,
  INDEX `shop_order_trash_time`(`shop_order_trash_time`) USING BTREE,
  INDEX `shop_order_trash`(`shop_order_trash`) USING BTREE,
  INDEX `shop_order_delete_state`(`shop_order_delete_state`) USING BTREE,
  INDEX `shop_order_delete_time`(`shop_order_delete_time`) USING BTREE,
  INDEX `shop_order_pay_parent`(`shop_order_pay_parent`) USING BTREE,
  INDEX `shop_order_update_time`(`shop_order_update_time`) USING BTREE,
  INDEX `shop_order_money`(`shop_order_money`) USING BTREE,
  INDEX `shop_order_pay_credit`(`shop_order_pay_credit`) USING BTREE,
  INDEX `shop_order_property`(`shop_order_property`) USING BTREE,
  INDEX `shop_order_pay_credit_method`(`shop_order_pay_credit_method`) USING BTREE,
  INDEX `shop_order_pay_money_method`(`shop_order_pay_money_method`) USING BTREE,
  INDEX `shop_order_discount_credit`(`shop_order_discount_credit`) USING BTREE,
  INDEX `shop_order_pay_money_order_id`(`shop_order_pay_money_order_id`) USING BTREE,
  INDEX `shop_order_pay_credit_order_id`(`shop_order_pay_credit_order_id`) USING BTREE,
  INDEX `shop_order_shipping_property`(`shop_order_shipping_property`) USING BTREE,
  INDEX `shop_order_shipping_parent`(`shop_order_shipping_parent`) USING BTREE,
  INDEX `shop_order_goods_price`(`shop_order_goods_price`) USING BTREE,
  INDEX `shop_order_goods_money`(`shop_order_goods_money`) USING BTREE,
  INDEX `shop_order_goods_credit`(`shop_order_goods_credit`) USING BTREE,
  INDEX `merchant_id`(`merchant_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城订单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `shop_order_goods`;
CREATE TABLE `shop_order_goods`  (
  `shop_order_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城订单商品表ID',
  `shop_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺ID/商家ID',
  `shop_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID',
  `shop_goods_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品ID',
  `shop_goods_sn` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品的货号',
  `shop_goods_sku_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品规格ID',
  `shop_goods_property` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品类型。0普通商品，1是积分商品',
  `shop_goods_index` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '索引标记。如 1 表示会员商品',
  `shop_order_goods_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `shop_order_goods_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品图片ID',
  `shop_order_goods_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品单价，积分或者人民币(分)',
  `shop_order_goods_number` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品数量',
  `shop_order_goods_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据。如：spu_name 商品属性名称',
  `shop_order_goods_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `recommend_user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '推荐用户ID',
  `recommend_money` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推荐奖金（钱包）',
  `recommend_credit` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '奖励积分。预留字段',
  `recommend_money_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资金订单ID，当推荐奖励成功后产生的订单数据',
  `recommend_credit_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资金订单ID，当推荐奖励积分成功后产生的订单数据（预留字段）',
  PRIMARY KEY (`shop_order_goods_id`) USING BTREE,
  UNIQUE INDEX `shop_order_goods_id`(`shop_order_goods_id`) USING BTREE,
  INDEX `shop_order_id`(`shop_order_id`) USING BTREE,
  INDEX `shop_goods_id`(`shop_goods_id`) USING BTREE,
  INDEX `shop_goods_sn`(`shop_goods_sn`) USING BTREE,
  INDEX `shop_goods_sku_id`(`shop_goods_sku_id`) USING BTREE,
  INDEX `shop_order_goods_name`(`shop_order_goods_name`) USING BTREE,
  INDEX `shop_order_goods_image_id`(`shop_order_goods_image_id`) USING BTREE,
  INDEX `shop_order_goods_price`(`shop_order_goods_price`) USING BTREE,
  INDEX `shop_order_goods_number`(`shop_order_goods_number`) USING BTREE,
  INDEX `shop_order_goods_time`(`shop_order_goods_time`) USING BTREE,
  INDEX `shop_goods_property`(`shop_goods_property`) USING BTREE,
  INDEX `recommend_user_id`(`recommend_user_id`) USING BTREE,
  INDEX `recommend_credit`(`recommend_credit`) USING BTREE,
  INDEX `recommend_money_order_id`(`recommend_money_order_id`) USING BTREE,
  INDEX `recommend_money`(`recommend_money`) USING BTREE,
  INDEX `recommend_credit_order_id`(`recommend_credit_order_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城订单商品表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_order_group
-- ----------------------------
DROP TABLE IF EXISTS `shop_order_group`;
CREATE TABLE `shop_order_group`  (
  `shop_order_group_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺ID',
  `order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID',
  `shop_order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城订单ID',
  `shop_goods_group_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '拼团商品ID',
  `shop_goods` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品信息',
  `shop_order_group_number` bigint(20) UNSIGNED NOT NULL DEFAULT 1 COMMENT '商品数量',
  `shop_order_group_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '拼团订单价格',
  `shop_order_group_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `shop_order_group_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态：0拼团失败，1拼团成功，2拼团中',
  `shop_order_group_pay` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付：0否，1是',
  `shop_order_group_pay_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付时间戳',
  `shop_order_group_pay_method` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '支付方式',
  `shop_order_group_delete` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除：0否，1是',
  `shop_order_group_delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间戳',
  `shop_order_group_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收：0否，1是',
  `shop_order_group_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间戳',
  `user_address_consignee` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的名字',
  `user_address_phone` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的手机号',
  `user_address_province` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的省份',
  `user_address_city` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人城市',
  `user_address_district` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的地区',
  `user_address_details` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的详细地址',
  `shop_order_group_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `shop_order_group_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `shop_order_group_refund_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款状态：0未退款，1已退款',
  `shop_order_group_refund_time` bigint(20) NOT NULL COMMENT '退款时间',
  `shop_order_group_refund_order` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资金订单ID',
  PRIMARY KEY (`shop_order_group_id`) USING BTREE,
  INDEX `shop_order_group_refund_state`(`shop_order_group_refund_state`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for shop_type
-- ----------------------------
DROP TABLE IF EXISTS `shop_type`;
CREATE TABLE `shop_type`  (
  `shop_type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类ID',
  `shop_type_parent_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `shop_type_logo_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类别图标，存的是图片表ID',
  `shop_type_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `shop_type_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `shop_type_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0隐藏，1正常',
  `shop_type_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `shop_type_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `shop_type_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`shop_type_id`) USING BTREE,
  UNIQUE INDEX `shop_type_id`(`shop_type_id`) USING BTREE,
  INDEX `shop_type_parent_id`(`shop_type_parent_id`) USING BTREE,
  INDEX `shop_type_name`(`shop_type_name`) USING BTREE,
  INDEX `shop_type_state`(`shop_type_state`) USING BTREE,
  INDEX `shop_type_sort`(`shop_type_sort`) USING BTREE,
  INDEX `shop_type_insert_time`(`shop_type_insert_time`) USING BTREE,
  INDEX `shop_type_update_time`(`shop_type_update_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `shop_type_logo_image_id`(`shop_type_logo_image_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商城分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for slideshow
-- ----------------------------
DROP TABLE IF EXISTS `slideshow`;
CREATE TABLE `slideshow`  (
  `slideshow_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '轮播图ID',
  `image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `slideshow_module` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所属模块，模块名称',
  `slideshow_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `slideshow_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `slideshow_label` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '页面标识。用于前端根据页面的标识显示不同的页面。',
  `slideshow_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '注释信息',
  `slideshow_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json配置',
  `slideshow_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `slideshow_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。1 显示，0 隐藏',
  `slideshow_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `slideshow_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  PRIMARY KEY (`slideshow_id`) USING BTREE,
  UNIQUE INDEX `slideshow_id`(`slideshow_id`) USING BTREE,
  INDEX `image_id`(`image_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `slideshow_name`(`slideshow_name`(191)) USING BTREE,
  INDEX `slideshow_state`(`slideshow_state`) USING BTREE,
  INDEX `slideshow_update_time`(`slideshow_update_time`) USING BTREE,
  INDEX `slideshow_insert_time`(`slideshow_insert_time`) USING BTREE,
  INDEX `slideshow_sort`(`slideshow_sort`) USING BTREE,
  INDEX `slideshow_module`(`slideshow_module`(191)) USING BTREE,
  INDEX `slideshow_label`(`slideshow_label`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '轮播图表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for type
-- ----------------------------
DROP TABLE IF EXISTS `type`;
CREATE TABLE `type`  (
  `type_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类ID',
  `type_parent_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父ID',
  `merchant_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商家ID：如果存在，则表示该分类为商家商品分类',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `type_module` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类模块，模块名称',
  `type_label` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签',
  `type_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '注释信息',
  `type_logo_image_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类别图标，存的是图片表ID',
  `type_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `type_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `type_merchant_usable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家是否可用：0假，1真',
  `type_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `type_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0隐藏，1显示',
  `type_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `type_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `type_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`type_id`) USING BTREE,
  UNIQUE INDEX `type_id`(`type_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `type_parent_id`(`type_parent_id`) USING BTREE,
  INDEX `type_name`(`type_name`) USING BTREE,
  INDEX `type_state`(`type_state`) USING BTREE,
  INDEX `type_sort`(`type_sort`) USING BTREE,
  INDEX `type_insert_time`(`type_insert_time`) USING BTREE,
  INDEX `type_update_time`(`type_update_time`) USING BTREE,
  INDEX `type_logo_image_id`(`type_logo_image_id`) USING BTREE,
  INDEX `type_module`(`type_module`(191)) USING BTREE,
  INDEX `type_label`(`type_label`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of type
-- ----------------------------
INSERT INTO `type` VALUES ('008d02096ddf29635566ff156940132842', 'ea657616591660166c4d6115694011873565', '', '1', 'shop_goods_type', '', '', 'eb05dc403dcc65e4152c4115694013282652', 'ccc', '', 0, '', 1, 0, 1569401328, 1569401328);
INSERT INTO `type` VALUES ('044437459b950475508570156714608174', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'd5191bcdd780c7f377ce3c15697228038437', '孕妈用品', '', 0, '', 1, 7, 1567146081, 1571277145);
INSERT INTO `type` VALUES ('04bc8bc2e3c32c34364cc015671564483359', '7eacaaa1558acbcaaea6a715671472321443', '', '1', 'cms_article_type', '', '', '', '默认', '', 0, '', 1, 0, 1567156448, 1567156448);
INSERT INTO `type` VALUES ('0596a2e959cde02ec2ad0a1569376316883', '6e94026433673f7438704515693762903549', '', '1', 'shop_goods_type', '', '', 'ca46bb2665e6664d14b22015693763167207', '第二栏测试1', '', 0, '', 0, 0, 1569376316, 1570691052);
INSERT INTO `type` VALUES ('05ee15622f7555f9267de715694013156781', 'ea657616591660166c4d6115694011873565', '', '1', 'shop_goods_type', '', '', '77b11993379e6316c7395e15694013155469', 'ces', '', 0, '', 1, 0, 1569401315, 1569401315);
INSERT INTO `type` VALUES ('0718ff1e2665a5719a63451569578317094', '8c1d609982b9aedb2ce2bb15671462225357', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'dafb7be0fa0bd138edf8a815697238739811', '纸尿裤', '', 0, '', 1, 1, 1569578317, 1569723967);
INSERT INTO `type` VALUES ('088ab2ab955ea7525aec9b15698243632681', '', '1', 'cac9c0a1cc15675687810455', 'merchant_goods_type', '', '', '', '商品分类', '', 0, '[{\"id\":\"bab033c71f30013ecb93b315650865623123\"}]', 0, 3, 1569824363, 1570432186);
INSERT INTO `type` VALUES ('0b814ab64354a1a7c666071569210669508', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '33b33508b9b8253b52b0a515697476173805', '母婴服务', '', 0, '', 1, 10, 1569210669, 1571277145);
INSERT INTO `type` VALUES ('0ee035c79eee90213f3e0e15693766765512', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'af7610a77a1355739160b015706911297656', '日常用品', '', 0, '', 0, 2, 1569376676, 1571277194);
INSERT INTO `type` VALUES ('111616661ad0cd159d21aa1569376544742', '6e94026433673f7438704515693762903549', '', '1', 'shop_goods_type', '', '', 'e4a91e199cb314cd175d3e15706910249729', '笔记本', '', 0, '', 1, 0, 1569376544, 1570691033);
INSERT INTO `type` VALUES ('11dac5c2cd6caca282673b15697232513976', '74cf776e6bb6ecf4f46b5615697231805336', '', '1', 'shop_goods_type', '', '', '552465b5df2c92992f129b15697232512336', '婴儿车', '', 0, '', 1, 1, 1569723251, 1569723327);
INSERT INTO `type` VALUES ('163374c66c6dcacbcaa6a115694012386733', 'ea657616591660166c4d6115694011873565', '', '1', 'shop_goods_type', '', '', '5868bbdd76bf1186a550fa15694012385227', '24', '', 0, '', 1, 0, 1569401238, 1569401238);
INSERT INTO `type` VALUES ('192b72e73bcbcd7822b3cd15706730478087', '', '', '1', 'shop_goods_type', '', '', '', '美容美颜', '', 0, '{}', 1, 0, 1570673047, 1570688711);
INSERT INTO `type` VALUES ('19bc4622d79b28dc5513d115671469509965', 'f020a2685ce6f65bdb39901567146470213', '', '1', 'shop_goods_type', '日常护理', '', 'e383585555565c5c58782515687734842278', '洗护护肤', '', 0, '', 1, 5, 1567146950, 1568944829);
INSERT INTO `type` VALUES ('1cf871e9e964d698dc91a11567146309334', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '08ed882fce586b6b6fd6cc15671463172114', '店铺分类', '', 0, '', 0, 5, 1567146309, 1568617999);
INSERT INTO `type` VALUES ('1d1838e2816a9b126fd76115697482525275', '044437459b950475508570156714608174', '', '1', 'shop_goods_type', '', '', '413a43c2aa5a1c3c32e63315697484435891', '孕妈营养', '', 0, '', 1, 0, 1569748252, 1569748443);
INSERT INTO `type` VALUES ('206b2f3c4a48b22738b5a515707000450651', '', 'c12d2ad2054c250af5395815706150385939', '950528d99315695484978768', 'shop_goods_type', '', '', '', 'ces', '', 0, '{}', 1, 0, 1570700045, 1570700045);
INSERT INTO `type` VALUES ('20e4a82e3f38424044b8e815705843736427', '044437459b950475508570156714608174', '', '1', 'shop_goods_type', '', '', '005fb5fe56bfb5b5ffcfff15705843733201', '卫生纸/卫生巾', '', 0, '', 1, 0, 1570584373, 1570589258);
INSERT INTO `type` VALUES ('234e707aa9f383ccf8882715693766272718', 'd9724bb48338af0aa4989715693753836367', '', '1', 'shop_goods_type', '', '', '9de9d86e1e03a8e344090015693766271412', '第一栏测试', '', 0, '', 1, 0, 1569376627, 1569376627);
INSERT INTO `type` VALUES ('26d4a224f25af2218bbd2f15705248251335', 'a8197a66d5ed8841926d0215697195380992', '', '1', 'shop_goods_type', '', '', '47cef8e24ffe8a7f4b57d115705248248194', '包被/睡袋', '', 0, '', 1, 0, 1570524825, 1570524825);
INSERT INTO `type` VALUES ('2a9b3833362195594978a31570694772401', 'd1b7cb6cbcd1d5bd60077215706908325805', '', '1', 'shop_goods_type', '', '', '', '子分类1', '', 1, '[]', 1, 0, 1570694772, 1570694772);
INSERT INTO `type` VALUES ('2abc4b2154ac2bb9a544c815717095099803', '7fa99a80ff87740897933b1571708809182', '4dab9b986344866888410315696386137782', 'cac9c0a1cc15675687810455', 'shop_goods_type', '', '', '', 'B', '', 0, '', 1, 0, 1571709509, 1571709509);
INSERT INTO `type` VALUES ('2b055a7ebae1f60b1e0ab215695784993157', 'ace6a7fae52fa85e68ae9e15671461390451', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'c083c0f2ec27ce8cefc92c15697212802601', '日常用品', '', 0, '', 1, 6, 1569578499, 1569721295);
INSERT INTO `type` VALUES ('2f9cb8094409b58e46c0031569744898901', '96694a99318441234d240115697445461126', '', '1', 'shop_goods_type', '', '', '73cf4d354380cd1c84f78115697448983902', '孕妇餐', '', 1, '{}', 1, 0, 1569744898, 1570692438);
INSERT INTO `type` VALUES ('2ff47ebe06748c43243cbc15704326557595', 'ec69a0b20b20b2e359af8515692933003721', '1', 'cac9c0a1cc15675687810455', 'merchant_goods_type', '', '', '', 'cesss', 'ce', 0, '{}', 1, 0, 1570432655, 1570432670);
INSERT INTO `type` VALUES ('31444bcd0b1c6cbe5cc16d15693754999655', 'd9724bb48338af0aa4989715693753836367', '', '1', 'shop_goods_type', 'vip礼包', '', 'f86728866ffedc886f27f615693754998197', '测试分类1', '', 0, '', 1, 0, 1569375499, 1569375499);
INSERT INTO `type` VALUES ('3196af42f0393e9962633715697449142528', '96694a99318441234d240115697445461126', '', '1', 'shop_goods_type', '', '', '0c36eb74d0ebb7704ea7cc15697449139726', '月子餐', '', 1, '[]', 1, 0, 1569744914, 1570692445);
INSERT INTO `type` VALUES ('35c972111ddf92bd323df215697233147348', '74cf776e6bb6ecf4f46b5615697231805336', '', '1', 'shop_goods_type', '', '', 'e94301ff73240ea5e0999f15697233144511', '安全座椅', '', 0, '', 1, 3, 1569723314, 1569723327);
INSERT INTO `type` VALUES ('3714099c409cb8794478cd15697479155103', '0b814ab64354a1a7c666071569210669508', '', '1', 'shop_goods_type', '', '', 'c4b983b253e423edb1229e1569747925513', '月嫂', '', 1, '{}', 1, 0, 1569747915, 1570700603);
INSERT INTO `type` VALUES ('37e7fef8062b3238f776231569376726632', '0ee035c79eee90213f3e0e15693766765512', '', '1', 'shop_goods_type', '', '', '06886846a4e414b4a8db0e15706912042315', '桌椅板凳', '', 0, '', 1, 0, 1569376726, 1570691204);
INSERT INTO `type` VALUES ('3b493ed17911b873d8d84715706938752175', '', 'c12d2ad2054c250af5395815706150385939', '950528d99315695484978768', 'shop_goods_type', '', '', '', '商品商场分类测试', '', 0, '[]', 1, 0, 1570693875, 1570701839);
INSERT INTO `type` VALUES ('3b4a26ec51a6544947189415695784401121', 'ace6a7fae52fa85e68ae9e15671461390451', '', '1', 'shop_goods_type', 'APP首页菜单', '', '638378b8ba7af3e6dde26315706089404268', '洗衣用品', '', 0, '', 1, 3, 1569578440, 1570608940);
INSERT INTO `type` VALUES ('3f31ee7b7e750eee8db8f715697198104569', 'a8197a66d5ed8841926d0215697195380992', '', '1', 'shop_goods_type', '', '', 'e8374094b0f4ff37c0e47e1569719810325', '配饰', '', 0, '', 1, 5, 1569719810, 1569719810);
INSERT INTO `type` VALUES ('3ff904ae4726290762276a15713848180778', '5db942bb6800b4bb9c6c6015713847950802', '4dab9b986344866888410315696386137782', '1', 'shop_goods_type', '', '', '08c84ec96ec9c80478e74d15713848178143', 'ffffffff', '', 0, '', 1, 0, 1571384818, 1571384818);
INSERT INTO `type` VALUES ('42527b8d775bf10b011e5d15695783603951', 'e9bb771fab1617c005b2a415671455767996', '', '1', 'shop_goods_type', 'APP首页菜单', '', '45801322b1933326be682615697222587104', '儿童餐具', '', 0, '', 1, 2, 1569578360, 1569722472);
INSERT INTO `type` VALUES ('44d06464c4d43d33b4683e15697217686685', 'a45a5c5eeabaf82a51cba915697216109134', '', '1', 'shop_goods_type', '', '', '3adc8c6ff61cc8cef11f1c15697217684892', 'DIY手工/绘画', '', 0, '', 1, 3, 1569721768, 1569721768);
INSERT INTO `type` VALUES ('44e8de048dc5dcdfdcee8f15697198436431', 'a8197a66d5ed8841926d0215697195380992', '', '1', 'shop_goods_type', '', '', 'aa8c67fc07ca07af77838115697198434407', '婴儿服饰', '', 0, '', 1, 6, 1569719843, 1569719843);
INSERT INTO `type` VALUES ('46c0cb7f211b28ba4e48b015686174237584', '1cf871e9e964d698dc91a11567146309334', '', '1', 'shop_goods_type', '', '', '', '123', '', 0, '', 0, 0, 1568617423, 1568617921);
INSERT INTO `type` VALUES ('48654fdd4d346099d0454d15697232859953', '74cf776e6bb6ecf4f46b5615697231805336', '', '1', 'shop_goods_type', '', '', '66c6d389967797bf60981015697232858009', '婴儿床', '', 0, '', 1, 2, 1569723285, 1569725354);
INSERT INTO `type` VALUES ('4f51f1343f1f3a34a1c1f715693765644174', '6e94026433673f7438704515693762903549', '', '1', 'shop_goods_type', '', '', 'd99d060f26f5c09b9000c015706909087904', '直尺', '', 0, '', 1, 0, 1569376564, 1570690908);
INSERT INTO `type` VALUES ('54464b3c46693fe377a36815697216435358', 'a45a5c5eeabaf82a51cba915697216109134', '', '1', 'shop_goods_type', '', '', 'ca44469879f7470374f7f315697216433257', '毛绒玩具', '', 0, '', 1, 1, 1569721643, 1569721643);
INSERT INTO `type` VALUES ('5839de398a838e9d99e36d15671469021336', 'f020a2685ce6f65bdb39901567146470213', '', '1', 'shop_goods_type', '宝宝尿裤', '', '4c866862d4f6ef52fa666f15671495681013', '宝宝尿裤', '', 0, '', 1, 3, 1567146902, 1568773018);
INSERT INTO `type` VALUES ('59b1c1175559955855514f15695784886608', 'ace6a7fae52fa85e68ae9e15671461390451', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'a56ba929ae5e799895f50015697211352497', '驱蚊退烧', '', 0, '', 1, 5, 1569578488, 1569721295);
INSERT INTO `type` VALUES ('5db942bb6800b4bb9c6c6015713847950802', '', '4dab9b986344866888410315696386137782', '1', 'shop_goods_type', '', '', '2dc1d12775d17b7858511d15713847946121', 'gggggg', '', 0, '', 1, 0, 1571384795, 1571384795);
INSERT INTO `type` VALUES ('63ee15e53c616326cc223415717095032312', '7fa99a80ff87740897933b1571708809182', '4dab9b986344866888410315696386137782', 'cac9c0a1cc15675687810455', 'shop_goods_type', '', '', '', 'A', '', 0, '', 1, 0, 1571709503, 1571709503);
INSERT INTO `type` VALUES ('669faac90906d9cbabddba15694012101707', 'ea657616591660166c4d6115694011873565', '', '1', 'shop_goods_type', '', '', '197a96c1954eaa8e9a46a415694012100309', '123', '', 0, '', 1, 0, 1569401210, 1569401210);
INSERT INTO `type` VALUES ('6c81c4c4d62e22eccdb42915697229449751', '044437459b950475508570156714608174', '', '1', 'shop_goods_type', '', '', '96367176a7a60d3944aa9a1569722944709', '孕产用品', '', 0, '', 1, 0, 1569722944, 1569722944);
INSERT INTO `type` VALUES ('6e2b9e2b92afef1ab5b6461569376180225', 'd9724bb48338af0aa4989715693753836367', '', '1', 'shop_goods_type', 'VIP礼包', '', 'cc4be857eaa5bb8ec0057b15693761799426', '测试2', '', 0, '', 1, 0, 1569376180, 1569376180);
INSERT INTO `type` VALUES ('6e94026433673f7438704515693762903549', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '07733bd536c8d1a5c7118915706908501435', '文具', '', 0, '', 0, 1, 1569376290, 1571277194);
INSERT INTO `type` VALUES ('70ea07d608d6639907995615697205978373', '9edf8b4a7ed1f8922d816e1569720286971', '', '1', 'shop_goods_type', '', '', '0c89ba00ac57a00e09a00915697205975338', '辅食', '', 0, '', 1, 3, 1569720597, 1569720597);
INSERT INTO `type` VALUES ('74066b09cb0bbb9866687315671465916758', 'f020a2685ce6f65bdb39901567146470213', '', '1', 'shop_goods_type', '奶粉辅食', '', '4787c812b0fbfa19f2a37115687733343012', '奶粉辅食', '', 0, '', 1, 1, 1567146591, 1568773335);
INSERT INTO `type` VALUES ('7454edb4db0bbb59d68d4715706058422121', '', '4dab9b986344866888410315696386137782', '1', 'shop_goods_type', '', '', '', '感冒药', '', 0, '[]', 1, 1, 1570605842, 1570850961);
INSERT INTO `type` VALUES ('74cf776e6bb6ecf4f46b5615697231805336', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '01c48cceeed361cccff6cc15697231803434', '车床座椅', '', 0, '', 1, 8, 1569723180, 1571277145);
INSERT INTO `type` VALUES ('768606dff6c838c28d6d3c15717095178636', '7fa99a80ff87740897933b1571708809182', '4dab9b986344866888410315696386137782', 'cac9c0a1cc15675687810455', 'shop_goods_type', '', '', '', 'D', '', 0, '', 1, 0, 1571709517, 1571709517);
INSERT INTO `type` VALUES ('7761977a01a337e41f989015697592402774', '', '1', 'cac9c0a1cc15675687810455', 'merchant_goods_type', '', '', 'e3fc0b30fee59c2c6eac9915697592401325', '商家分类测试2', '', 0, '', 1, 1, 1569759240, 1570429554);
INSERT INTO `type` VALUES ('7eacaaa1558acbcaaea6a715671472321443', '', '', '1', 'cms_article_type', '', '', '0c1afdfdb0705b203c5dfd15671501101088', '护理讲堂', '', 0, '', 1, 0, 1567147232, 1567564909);
INSERT INTO `type` VALUES ('7fa99a80ff87740897933b1571708809182', '', '4dab9b986344866888410315696386137782', 'cac9c0a1cc15675687810455', 'shop_goods_type', '', '', 'e6399d534748a6b2d3839d15717088083072', '字母', '', 0, '', 1, 0, 1571708809, 1571708809);
INSERT INTO `type` VALUES ('84ab52773a73aa97318e2915697204706193', '9edf8b4a7ed1f8922d816e1569720286971', '', '1', 'shop_goods_type', '', '', '5187a36c350c2d62926d5615697204703008', '营养保健', '', 0, '', 1, 1, 1569720470, 1569720470);
INSERT INTO `type` VALUES ('86ab655634b8c64b88ab831569578432192', 'ace6a7fae52fa85e68ae9e15671461390451', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'ddd5194a154b70df875c7a15697207673614', '洗发沐浴', '', 0, '', 1, 1, 1569578432, 1569721295);
INSERT INTO `type` VALUES ('88326f52662e139ba14f2d15697223336762', 'e9bb771fab1617c005b2a415671455767996', '', '1', 'shop_goods_type', '', '', '112786f992ce7bb269e7fc15697223333923', '水杯/水壶', '', 0, '', 1, 3, 1569722333, 1569722472);
INSERT INTO `type` VALUES ('8848ad7020de0683e4046215671468044518', 'f020a2685ce6f65bdb39901567146470213', '', '1', 'shop_goods_type', '婴儿用品', '', 'a88cc08fcee36abb96db1d1567149531972', '婴儿用品', '', 0, '', 1, 2, 1567146804, 1568774514);
INSERT INTO `type` VALUES ('89b7960d10d7760990066f1569723954974', '8c1d609982b9aedb2ce2bb15671462225357', '', '1', 'shop_goods_type', '', '', 'a703c11229b43c46b22b5815697239547047', '隔尿用品', '', 0, '', 1, 3, 1569723954, 1569723967);
INSERT INTO `type` VALUES ('8ac06cc8008cfdc6f583f515697479461407', '0b814ab64354a1a7c666071569210669508', '', '1', 'shop_goods_type', '', '', '77ab6a77754a067e4e0e6b15697479458373', '家政清洁', '', 1, '{}', 1, 0, 1569747946, 1570700597);
INSERT INTO `type` VALUES ('8c1d609982b9aedb2ce2bb15671462225357', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '6c4646d61d64542f60070815697237669021', '宝宝尿裤', '', 0, '', 1, 5, 1567146222, 1571277145);
INSERT INTO `type` VALUES ('96694a99318441234d240115697445461126', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'af0d0d0f7b057deaa80ddd15697445747921', '营养配餐', '', 0, '', 1, 9, 1569744546, 1571277145);
INSERT INTO `type` VALUES ('9759abdd9f98b0480a8b7b15677383089329', '', '', '1', 'merchant_type', '', '', '', '商家分类', '', 0, '', 1, 0, 1567738308, 1567738308);
INSERT INTO `type` VALUES ('9990ba117be01961771d2d15697224526996', 'e9bb771fab1617c005b2a415671455767996', '', '1', 'shop_goods_type', '', '', '8060a81786c8842d08414415697224525179', '消毒用品', '', 0, '', 1, 4, 1569722452, 1569722472);
INSERT INTO `type` VALUES ('9ba1b533086aa230cac31815697197549685', 'a8197a66d5ed8841926d0215697195380992', '', '1', 'shop_goods_type', '', '', '6c468996ba44608c487e4615697197547709', '男童鞋', '', 0, '', 1, 4, 1569719754, 1569719754);
INSERT INTO `type` VALUES ('9edf8b4a7ed1f8922d816e1569720286971', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '6a33826b4a93a404d9d70415697202865411', '奶粉辅食', '', 0, '', 1, 2, 1569720286, 1571277145);
INSERT INTO `type` VALUES ('a14efa18ec65835a8152c215713825036547', '', '4dab9b986344866888410315696386137782', '1', 'shop_goods_type', '', '', '', '这是测试商家分类', '', 0, '', 1, 0, 1571382503, 1571382503);
INSERT INTO `type` VALUES ('a420bbe5441d5dbd51459f15706885216214', '10953f33583ae88b757a5315704346245705', '', '1', 'merchant_type', '', '', '', 'ces', '', 0, '', 1, 0, 1570688521, 1570688521);
INSERT INTO `type` VALUES ('a45a5c5eeabaf82a51cba915697216109134', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'b6f22df005585d27f680dc15697216105635', '儿童玩具', '', 0, '', 1, 4, 1569721610, 1571277145);
INSERT INTO `type` VALUES ('a4faf0f0aaf0f5ef46aede15671470109681', 'f020a2685ce6f65bdb39901567146470213', '', '1', 'shop_goods_type', '车床座椅', '', '879d451da88897a5b40f5915671497184161', '车床座椅', '', 0, '', 1, 6, 1567147010, 1568773140);
INSERT INTO `type` VALUES ('a54de5c4dc1ec7f4dd1cad15671473946173', '', '', '1', 'cms_article_type', '', '', '78278d877777c7ac07f86d15671500903161', '通知消息', '', 0, '', 1, 0, 1567147394, 1567390346);
INSERT INTO `type` VALUES ('a7faf182c58ce1d2121df51569578461912', 'ace6a7fae52fa85e68ae9e15671461390451', '', '1', 'shop_goods_type', 'APP首页菜单', '', '781871875917773d31731315697210531285', '纸巾/湿巾', '', 0, '', 1, 4, 1569578461, 1569721295);
INSERT INTO `type` VALUES ('a8197a66d5ed8841926d0215697195380992', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'b44d77d5d98d8f420f2d6815697195376495', '儿童服装', '', 0, '', 1, 1, 1569719538, 1571277145);
INSERT INTO `type` VALUES ('aa33124a1dc445d411f12215671474759478', '', '', '1', 'cms_article_type', '', '', '340f0613610d144e564c7015671501001577', '新闻动态', '', 0, '', 1, 2, 1567147475, 1567390346);
INSERT INTO `type` VALUES ('ace6a7fae52fa85e68ae9e15671461390451', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '27732774370533a7df633a15697207348699', '生活用品', '', 0, '', 1, 3, 1567146139, 1571277145);
INSERT INTO `type` VALUES ('b15ab0adbb8ccd5d8dad5b15695784102544', 'ace6a7fae52fa85e68ae9e15671461390451', '', '1', 'shop_goods_type', 'APP首页菜单', '', 'e3c089a22e17618a0e092315697208568502', '宝宝护肤', '', 0, '', 1, 2, 1569578410, 1569721295);
INSERT INTO `type` VALUES ('b212058375bdb7c8be65b01569376694482', '0ee035c79eee90213f3e0e15693766765512', '', '1', 'shop_goods_type', '', '', 'a35c3764d092c0ee3832d215693766942244', '第三栏测试1', '', 0, '', 0, 0, 1569376694, 1570691529);
INSERT INTO `type` VALUES ('b4441d92ed4f95c1e8401815693765806109', '6e94026433673f7438704515693762903549', '', '1', 'shop_goods_type', '', '', '5862e452692e213614dcbb15706908827563', '笔', '', 0, '', 1, 0, 1569376580, 1570690882);
INSERT INTO `type` VALUES ('b6f6088384fe977b6084e115713827381758', '', '4dab9b986344866888410315696386137782', '1', 'shop_goods_type', '', '', '', '测试234', '', 0, '', 1, 0, 1571382738, 1571382738);
INSERT INTO `type` VALUES ('b7e37eefa6e41f7a4fb74215694012670259', 'ea657616591660166c4d6115694011873565', '', '1', 'shop_goods_type', '', '', '9f8996c968a979cc9a849215694012668811', '89', '', 0, '', 1, 0, 1569401267, 1569401267);
INSERT INTO `type` VALUES ('b8196cf1f6821c6e1a8e4115697228757347', '044437459b950475508570156714608174', '', '1', 'shop_goods_type', '', '', '94b1d9d367ede5d93cd20f15697228755602', '孕妈服饰', '', 0, '', 1, 0, 1569722875, 1569722875);
INSERT INTO `type` VALUES ('b9ae84ba89009bce49e4e415697229908002', '044437459b950475508570156714608174', '', '1', 'shop_goods_type', '', '', 'a76a6c1e155a86b00e240515697229906493', '外出用品', '', 0, '', 1, 0, 1569722990, 1569722990);
INSERT INTO `type` VALUES ('bab1687a723b2928bf706b15671469304456', 'f020a2685ce6f65bdb39901567146470213', '', '1', 'shop_goods_type', '宝妈护理', '', '6363e10e52344ff96f4f3415671496266928', '孕妈用品', '', 0, '', 1, 4, 1567146930, 1568773098);
INSERT INTO `type` VALUES ('bd72df57d37588962d9bff15697196307493', 'a8197a66d5ed8841926d0215697195380992', '', '1', 'shop_goods_type', '', '', '34c640ce651efaebcf4e8c15697196302685', '女童装', '', 0, '', 1, 1, 1569719630, 1569719630);
INSERT INTO `type` VALUES ('be51df87ab48dbd5587d6815697476369765', '0b814ab64354a1a7c666071569210669508', '', '1', 'shop_goods_type', '', '', '8fff058a28e545d6b4a54115697476367189', '育儿嫂', '', 1, '{}', 1, 0, 1569747636, 1570700588);
INSERT INTO `type` VALUES ('bea04784990a4570092e8e15697508221798', '044437459b950475508570156714608174', '', '1', 'shop_goods_type', '', '', 'dfe0dac7645e58ee6a16de15697508218083', '清洁护肤', '', 0, '', 1, 0, 1569750822, 1569750822);
INSERT INTO `type` VALUES ('c224514f122440cdf2409d15697197289358', 'a8197a66d5ed8841926d0215697195380992', '', '1', 'shop_goods_type', '', '', '93ee9d9961c199ee9bc19115697197287494', '女童鞋', '', 0, '', 1, 3, 1569719728, 1569719728);
INSERT INTO `type` VALUES ('c385cc275758052934e3cc15697252354432', '74cf776e6bb6ecf4f46b5615697231805336', '', '1', 'shop_goods_type', '', '', '227826afb2f02d2e20727c15697252353008', '婴儿桌椅', '', 0, '', 1, 4, 1569725235, 1569725235);
INSERT INTO `type` VALUES ('cad531447c061f37342cfd15693136632992', 'f020a2685ce6f65bdb39901567146470213', '', '1', 'shop_goods_type', 'Selected', '', '', '商品精选', '', 0, '', 1, 0, 1569313663, 1569313663);
INSERT INTO `type` VALUES ('ccb5e1a36eb5b19598225315697216818883', 'a45a5c5eeabaf82a51cba915697216109134', '', '1', 'shop_goods_type', '', '', 'af906e4fc9a6a4f86a2c6b15697216816163', '婴幼儿玩具', '', 0, '', 1, 2, 1569721681, 1569721681);
INSERT INTO `type` VALUES ('cebbfef9891e11e6216c7015693767106726', '0ee035c79eee90213f3e0e15693766765512', '', '1', 'shop_goods_type', '', '', '040fd90fffb0441b4c1aa215693767104675', '第三栏测试2', '', 0, '', 0, 0, 1569376710, 1570691529);
INSERT INTO `type` VALUES ('d12ad12cd2ad4bcad2170c15697592118109', 'ec69a0b20b20b2e359af8515692933003721', '1', 'cac9c0a1cc15675687810455', 'merchant_goods_type', '', '', 'f607c70f8e75bb6553d63e15697592113068', 'cs', '', 0, '', 1, 1, 1569759211, 1570429951);
INSERT INTO `type` VALUES ('d1b7cb6cbcd1d5bd60077215706908325805', '', '', '1', 'shop_goods_type', '', '', '', '测试商家后台用，勿动', '', 1, '{}', 1, 0, 1570690832, 1570695110);
INSERT INTO `type` VALUES ('d226afcb6602910b50e1661569721807081', 'a45a5c5eeabaf82a51cba915697216109134', '', '1', 'shop_goods_type', '', '', '7510c77a692990ead671c61569721806812', '遥控/模型', '', 0, '', 1, 4, 1569721807, 1569721837);
INSERT INTO `type` VALUES ('d8011342491032bdbb2bdd15712129153012', '', '4dab9b986344866888410315696386137782', '1', 'shop_goods_type', '', '', '', '测试233333', '', 0, '', 1, 0, 1571212915, 1571212915);
INSERT INTO `type` VALUES ('d9724bb48338af0aa4989715693753836367', '', '', '1', 'shop_goods_type', '首页活动', '', '4a34ed6bb4d5445e9324591569375437506', 'VIP礼包', '', 0, '', 0, 0, 1569375383, 1570691078);
INSERT INTO `type` VALUES ('d9d2deee9c93bb743e527e15694012549255', 'ea657616591660166c4d6115694011873565', '', '1', 'shop_goods_type', '', '', 'ff7777298433f9dff83d1f15694012547907', '78', '', 0, '', 1, 0, 1569401254, 1569401254);
INSERT INTO `type` VALUES ('dab688a886873089bb580315717095136454', '7fa99a80ff87740897933b1571708809182', '4dab9b986344866888410315696386137782', 'cac9c0a1cc15675687810455', 'shop_goods_type', '', '', '', 'C', '', 0, '', 1, 0, 1571709513, 1571709513);
INSERT INTO `type` VALUES ('dbdf8103f931cf0803d71115706088739306', 'ace6a7fae52fa85e68ae9e15671461390451', '', '1', 'shop_goods_type', '', '', 'e30a0cf9ca8bbefc49a69e15706088734208', '毛巾/浴巾', '', 0, '', 1, 0, 1570608873, 1570608873);
INSERT INTO `type` VALUES ('dde87735e83380579b5cdb15697206394021', '9edf8b4a7ed1f8922d816e1569720286971', '', '1', 'shop_goods_type', '', '', '19d4fc73ac6e4962b1c7cc15697206391719', '奶粉', '', 0, '', 1, 4, 1569720639, 1569720649);
INSERT INTO `type` VALUES ('e0df4d64ab149f4acbb0bc1569744971409', '96694a99318441234d240115697445461126', '', '1', 'shop_goods_type', '', '', '6b958bff3e05b5be65a73d15697449711453', '营养餐', '', 1, '{}', 1, 0, 1569744971, 1570692456);
INSERT INTO `type` VALUES ('e2d970c4dd477f2de454cb15695783525257', '8c1d609982b9aedb2ce2bb15671462225357', '', '1', 'shop_goods_type', 'APP首页菜单', '', '3aadd6762faa5a0de1aec41569723911654', '拉拉裤', '', 0, '', 1, 2, 1569578352, 1569723967);
INSERT INTO `type` VALUES ('e9bb771fab1617c005b2a415671455767996', '', '', '1', 'shop_goods_type', 'APP首页菜单', '', '72bbbc855f5ff7c33bf6b115697221817119', '喂养用品', '', 0, '', 1, 6, 1567145576, 1571277145);
INSERT INTO `type` VALUES ('ea657616591660166c4d6115694011873565', '', '', '1', 'shop_goods_type', '首页活动', '', '1a11116d155365613902eb1569401186776', 'VIP礼包4', '', 0, '', 0, 3, 1569401187, 1570691078);
INSERT INTO `type` VALUES ('ea942f08fff14414e01f9b15677366434599', 'aa33124a1dc445d411f12215671474759478', '', '1', 'cms_article_type', '', '', '', '默认', '', 0, '', 1, 0, 1567736643, 1567736643);
INSERT INTO `type` VALUES ('ec69a0b20b20b2e359af8515692933003721', '', '1', '1', 'merchant_goods_type', '商家商品分类', '测试', '5185c966ae288f96b9649815698258354572', '商家商品分类测试1', '分类与商家与商品关联', 0, '{}', 1, 4, 1569293300, 1570433038);
INSERT INTO `type` VALUES ('ee4aae5594a56ea45c9eef15677383296335', '9759abdd9f98b0480a8b7b15677383089329', '', '1', 'merchant_type', '', '', '', '自营商家', '', 0, '', 1, 0, 1567738329, 1567738329);
INSERT INTO `type` VALUES ('ee8e814e649359f3f1d43a15697205130446', '9edf8b4a7ed1f8922d816e1569720286971', '', '1', 'shop_goods_type', '', '', '01330b7e9377b78ce744b015697205128084', '零食', '', 0, '', 1, 2, 1569720513, 1569720513);
INSERT INTO `type` VALUES ('ef32d4f6df0e61126d2ffb15693767511576', '0ee035c79eee90213f3e0e15693766765512', '', '1', 'shop_goods_type', '', '', '31a5f3a7a5e071d557011015706911691203', '杯子', '', 0, '', 1, 0, 1569376751, 1570691169);
INSERT INTO `type` VALUES ('f020a2685ce6f65bdb39901567146470213', '', '', '1', 'shop_goods_type', 'APP首页专区', '', '', 'APP首页专区', '', 0, '', 1, 0, 1567146470, 1567390370);
INSERT INTO `type` VALUES ('f06a5b666ab3506620faad15695707577911', 'e9bb771fab1617c005b2a415671455767996', '', '1', 'shop_goods_type', 'APP首页菜单', '', '17b088010d6db8c9dd06cc15697222248718', '奶嘴/奶瓶', '', 0, '', 1, 1, 1569570757, 1569722472);
INSERT INTO `type` VALUES ('f19f198816d02d048860a015697450353038', '96694a99318441234d240115697445461126', '', '1', 'shop_goods_type', '', '', '6c28f29f58f05bfbc533f815697450350937', '婴儿餐', '', 1, '[]', 1, 0, 1569745035, 1570692464);
INSERT INTO `type` VALUES ('f49fd7bd747f4e79b9154015693766123489', 'd9724bb48338af0aa4989715693753836367', '', '1', 'shop_goods_type', '', '', 'f85454a4122928a6b2820415693766122107', '第一栏测试', '', 0, '', 1, 0, 1569376612, 1569376612);
INSERT INTO `type` VALUES ('fb177854db3edfe0726fbe15694013441298', 'ea657616591660166c4d6115694011873565', '', '1', 'shop_goods_type', '', '', 'c78845839728338125b948156940134398', 'ccccc', '', 0, '', 1, 0, 1569401344, 1569401344);
INSERT INTO `type` VALUES ('febced1e0cac8eafe22a8d15697196581488', 'a8197a66d5ed8841926d0215697195380992', '', '1', 'shop_goods_type', '', '', '2d07db6136ac8622d4af2215697196579173', '男童装', '', 0, '', 1, 2, 1569719658, 1569719658);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_parent_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父ID，即 ：user表的 user_id',
  `user_logo_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像图片ID',
  `user_left_password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '左密码。即加密的时候，与用户id混合，放在左边。算法：md5(用户密码+用户ID)',
  `user_right_password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '右密码。即加密的时候，与用户id混合，放在右边。算法：md5(用户ID+用户密码)',
  `user_nickname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_compellation` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `user_sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别。0未知，1男，2女',
  `user_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0封禁，1正常',
  `user_wechat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户微信号',
  `user_qq` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户的QQ号',
  `user_email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户的邮箱',
  `user_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `user_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `user_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `user_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资料更新时间',
  `user_register_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册时间',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_sex`(`user_sex`) USING BTREE,
  INDEX `user_qq`(`user_qq`(191)) USING BTREE,
  INDEX `user_email`(`user_email`(191)) USING BTREE,
  INDEX `user_register_time`(`user_register_time`) USING BTREE,
  INDEX `user_update_time`(`user_update_time`) USING BTREE,
  INDEX `user_nickname`(`user_nickname`(191)) USING BTREE,
  INDEX `user_parent_id`(`user_parent_id`) USING BTREE,
  INDEX `user_compellation`(`user_compellation`(191)) USING BTREE,
  INDEX `user_state`(`user_state`) USING BTREE,
  INDEX `user_wechat`(`user_wechat`(191)) USING BTREE,
  INDEX `user_trash`(`user_trash`) USING BTREE,
  INDEX `user_trash_time`(`user_trash_time`) USING BTREE,
  INDEX `user_logo_image_id`(`user_logo_image_id`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '10a10e7a0915548686767751', '8c50a755505f88c2a758a815698235730001', 'feb8dc0697a2e0a947c6e20dc4ec3ebc', '88312213c3492c4cd89d297f16cb0fc4', '王小帅', '小王', 1, 1, 'weixin_id', '12345678901', '123456', '{\"pay_password\":{\"left\":\"5aff69de3c902906c52525436d8abc00\",\"right\":\"23dc1a6936f96fab2e2fc8698b081a0a\",\"error_time\":[1554713805,1554713813,1554714155]}}', 0, 0, 1569823573, 1542265548);

-- ----------------------------
-- Table structure for user_address
-- ----------------------------
DROP TABLE IF EXISTS `user_address`;
CREATE TABLE `user_address`  (
  `user_address_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户地址表ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_address_consignee` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的名字',
  `user_address_tel` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的电话',
  `user_address_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的手机号',
  `user_address_country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '中国' COMMENT '收货人的国家',
  `user_address_province` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的省份',
  `user_address_city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人城市',
  `user_address_district` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的地区',
  `user_address_details` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的详细地址',
  `user_address_longitude` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '维度',
  `user_address_latitude` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '经度',
  `user_address_zipcode` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的邮编',
  `user_address_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收货人的电子邮件',
  `user_address_default` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1默认收货地址，0非默认',
  `user_address_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `user_address_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'JSON数据',
  `user_address_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `user_address_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`user_address_id`) USING BTREE,
  UNIQUE INDEX `user_address_id`(`user_address_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_address_consignee`(`user_address_consignee`) USING BTREE,
  INDEX `user_address_phone`(`user_address_phone`) USING BTREE,
  INDEX `user_address_country`(`user_address_country`) USING BTREE,
  INDEX `user_address_province`(`user_address_province`) USING BTREE,
  INDEX `user_address_city`(`user_address_city`) USING BTREE,
  INDEX `user_address_district`(`user_address_district`) USING BTREE,
  INDEX `user_address_default`(`user_address_default`) USING BTREE,
  INDEX `user_address_insert_time`(`user_address_insert_time`) USING BTREE,
  INDEX `user_address_update_time`(`user_address_update_time`) USING BTREE,
  INDEX `user_address_sort`(`user_address_sort`) USING BTREE,
  INDEX `user_address_email`(`user_address_email`) USING BTREE,
  INDEX `user_address_tel`(`user_address_tel`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户地址表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_collection
-- ----------------------------
DROP TABLE IF EXISTS `user_collection`;
CREATE TABLE `user_collection`  (
  `user_collection_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城收藏ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_collection_label` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收藏标签',
  `user_collection_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '注释信息',
  `user_collection_module` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收藏模块。如表名称，保存不同模块的收藏数据',
  `user_collection_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '不同收藏模块的主键',
  `user_collection_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '所对应收藏模块的json数据',
  `user_collection_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `user_collection_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `user_collection_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`user_collection_id`) USING BTREE,
  UNIQUE INDEX `user_collection_id`(`user_collection_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_collection_label`(`user_collection_label`(191)) USING BTREE,
  INDEX `user_collection_update_time`(`user_collection_update_time`) USING BTREE,
  INDEX `user_collection_insert_time`(`user_collection_insert_time`) USING BTREE,
  INDEX `user_collection_sort`(`user_collection_sort`) USING BTREE,
  INDEX `user_collection_key`(`user_collection_key`) USING BTREE,
  INDEX `user_collection_module`(`user_collection_module`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户收藏表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_comment
-- ----------------------------
DROP TABLE IF EXISTS `user_comment`;
CREATE TABLE `user_comment`  (
  `user_comment_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户评论ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_comment_root_id` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所属顶级评论id，所在主留言下的那个人的评论id',
  `user_comment_parent_id` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所属父级评论id，回复的那个人的评论id',
  `user_comment_module` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '评论模块',
  `user_comment_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '评论对象id',
  `user_comment_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '评论内容',
  `user_comment_ip` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'ip地址',
  `user_comment_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '评论状态。0 未通过审核，1通过审核，2审核中',
  `user_comment_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `user_comment_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `user_comment_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`user_comment_id`) USING BTREE,
  UNIQUE INDEX `user_comment_id`(`user_comment_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_comment_root_id`(`user_comment_root_id`) USING BTREE,
  INDEX `user_comment_parent_id`(`user_comment_parent_id`) USING BTREE,
  INDEX `user_comment_module`(`user_comment_module`(191)) USING BTREE,
  INDEX `user_comment_key`(`user_comment_key`) USING BTREE,
  INDEX `user_comment_ip`(`user_comment_ip`(191)) USING BTREE,
  INDEX `user_comment_state`(`user_comment_state`) USING BTREE,
  INDEX `user_comment_update_time`(`user_comment_update_time`) USING BTREE,
  INDEX `user_comment_insert_time`(`user_comment_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户评论表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_coupon
-- ----------------------------
DROP TABLE IF EXISTS `user_coupon`;
CREATE TABLE `user_coupon`  (
  `user_coupon_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '主键ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `coupon_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '优惠券ID',
  `user_coupon_source` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '优惠券来源标识',
  `user_coupon_sign` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备用标识，比如保存一些特殊的主键，一些防止重复的标签ID等',
  `user_coupon_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '优惠券原始信息（获取时，优惠券信息）',
  `user_coupon_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0失效或已使用，1可使用',
  `user_coupon_number` bigint(20) UNSIGNED NOT NULL DEFAULT 1 COMMENT '可使用的次数。0表示无限制',
  `user_coupon_use_number` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '已经使用次数',
  `user_coupon_use_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后的使用时间',
  `user_coupon_expire_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '过期时间',
  `user_coupon_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `user_coupon_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`user_coupon_id`) USING BTREE,
  UNIQUE INDEX `user_coupon_id`(`user_coupon_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `coupon_id`(`coupon_id`) USING BTREE,
  INDEX `user_coupon_use_time`(`user_coupon_use_time`) USING BTREE,
  INDEX `user_coupon_expire_time`(`user_coupon_expire_time`) USING BTREE,
  INDEX `user_coupon_insert_time`(`user_coupon_insert_time`) USING BTREE,
  INDEX `user_coupon_update_time`(`user_coupon_update_time`) USING BTREE,
  INDEX `user_coupon_number`(`user_coupon_number`) USING BTREE,
  INDEX `user_coupon_use_number`(`user_coupon_use_number`) USING BTREE,
  INDEX `user_coupon_state`(`user_coupon_state`) USING BTREE,
  INDEX `user_coupon_source`(`user_coupon_source`) USING BTREE,
  INDEX `user_coupon_sign`(`user_coupon_sign`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户，优惠券' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_credit
-- ----------------------------
DROP TABLE IF EXISTS `user_credit`;
CREATE TABLE `user_credit`  (
  `user_credit_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户积分ID',
  `user_credit_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_credit_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `user_credit_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，个。支出等于0，而该值不为0，表示该条数据为收入操作',
  `user_credit_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，个。收入等于0，而该值不为0，表示该条数据为支出操作',
  `user_credit_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩积分。当前最新所剩积分。单位，个',
  `user_credit_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_credit_id`) USING BTREE,
  UNIQUE INDEX `user_credit_id`(`user_credit_id`) USING BTREE,
  UNIQUE INDEX `user_id_time`(`user_id`, `user_credit_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_credit_type`(`user_credit_type`) USING BTREE,
  INDEX `user_credit_plus`(`user_credit_plus`) USING BTREE,
  INDEX `user_credit_minus`(`user_credit_minus`) USING BTREE,
  INDEX `user_credit_time`(`user_credit_time`) USING BTREE,
  INDEX `user_credit_join_id`(`user_credit_join_id`) USING BTREE,
  INDEX `user_credit_value`(`user_credit_value`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户积分表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_evaluate
-- ----------------------------
DROP TABLE IF EXISTS `user_evaluate`;
CREATE TABLE `user_evaluate`  (
  `user_evaluate_id` varchar(150) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL DEFAULT '' COMMENT '主键ID',
  `user_id` varchar(150) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_evaluate_module` varchar(150) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL DEFAULT '' COMMENT '模块',
  `user_evaluate_key` varchar(150) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL DEFAULT '' COMMENT '模块主键ID',
  `user_evaluate_score` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评分',
  `user_evaluate_value` longtext CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL COMMENT '评价',
  `user_evaluate_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `user_evaluate_json` longtext CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL COMMENT '个性化json数据',
  `user_evaluate_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插入时间',
  `user_evaluate_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`user_evaluate_id`) USING BTREE,
  INDEX `user_evaluate_module`(`user_evaluate_module`) USING BTREE,
  INDEX `user_evaluate_key`(`user_evaluate_key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = gbk COLLATE = gbk_chinese_ci COMMENT = '用户，评价' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_follow
-- ----------------------------
DROP TABLE IF EXISTS `user_follow`;
CREATE TABLE `user_follow`  (
  `user_follow_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商城收藏ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_follow_label` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收藏标签',
  `user_follow_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '注释信息',
  `user_follow_module` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收藏模块。如表名称，保存不同模块的收藏数据',
  `user_follow_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '不同收藏模块的主键',
  `user_follow_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '所对应收藏模块的json数据',
  `user_follow_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `user_follow_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `user_follow_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`user_follow_id`) USING BTREE,
  UNIQUE INDEX `user_follow_id`(`user_follow_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_follow_label`(`user_follow_label`(191)) USING BTREE,
  INDEX `user_follow_update_time`(`user_follow_update_time`) USING BTREE,
  INDEX `user_follow_insert_time`(`user_follow_insert_time`) USING BTREE,
  INDEX `user_follow_sort`(`user_follow_sort`) USING BTREE,
  INDEX `user_follow_key`(`user_follow_key`) USING BTREE,
  INDEX `user_follow_module`(`user_follow_module`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户关注表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_identity
-- ----------------------------
DROP TABLE IF EXISTS `user_identity`;
CREATE TABLE `user_identity`  (
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_identity_real_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `user_identity_card_number` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证号码',
  `user_identity_card_country` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '中国' COMMENT '身份证国家',
  `user_identity_card_province` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证省份',
  `user_identity_card_city` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证城市',
  `user_identity_card_district` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证区县',
  `user_identity_card_address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证住址信息',
  `user_identity_front_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证正面。图片ID',
  `user_identity_back_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证背面。图片ID',
  `user_identity_waist_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证半面。图片ID',
  `user_identity_other_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '其他图片。图片ID',
  `user_identity_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '审核状态。0未通过审核，1通过审核，2待审核',
  `user_identity_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `user_identity_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `user_identity_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资料更新时间,时间戳，秒',
  `user_identity_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_identity_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间,时间戳，秒',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_identity_real_name`(`user_identity_real_name`(191)) USING BTREE,
  INDEX `user_identity_card_number`(`user_identity_card_number`(191)) USING BTREE,
  INDEX `user_identity_front_image_id`(`user_identity_front_image_id`(191)) USING BTREE,
  INDEX `user_identity_back_image_id`(`user_identity_back_image_id`(191)) USING BTREE,
  INDEX `user_identity_waist_image_id`(`user_identity_waist_image_id`(191)) USING BTREE,
  INDEX `user_identity_state`(`user_identity_state`) USING BTREE,
  INDEX `user_identity_trash`(`user_identity_trash`) USING BTREE,
  INDEX `user_identity_trash_time`(`user_identity_trash_time`) USING BTREE,
  INDEX `user_identity_update_time`(`user_identity_update_time`) USING BTREE,
  INDEX `user_identity_insert_time`(`user_identity_insert_time`) USING BTREE,
  INDEX `user_identity_other_image_id`(`user_identity_other_image_id`(191)) USING BTREE,
  INDEX `user_identity_card_country`(`user_identity_card_country`(191)) USING BTREE,
  INDEX `user_identity_card_province`(`user_identity_card_province`(191)) USING BTREE,
  INDEX `user_identity_card_city`(`user_identity_card_city`(191)) USING BTREE,
  INDEX `user_identity_card_district`(`user_identity_card_district`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户实名认证表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_log
-- ----------------------------
DROP TABLE IF EXISTS `user_log`;
CREATE TABLE `user_log`  (
  `user_log_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '唯一ID、主键',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `session_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '会话id',
  `user_log_ip` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `user_log_method` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录方式',
  `user_log_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '{\"session\":\"保存整个会话信息\",\"account\":\"保存登录时用的账号数据\"}。比如第三方登录，则保存{\"用户授权表\":{编号:xxxx}}。或者邮箱登录：{\"用户电邮表\":{\"账号\":{xxxx}}}。该数据结构是一个json数据。',
  `user_log_in_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户登录时间(时间戳，秒)',
  `user_log_out_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户退出(注销)时间(时间戳，秒)、保存的是session失效时间，每次session更新失效时间这个参数也要更新',
  PRIMARY KEY (`user_log_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户登录、退出日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_luck_draw
-- ----------------------------
DROP TABLE IF EXISTS `user_luck_draw`;
CREATE TABLE `user_luck_draw`  (
  `user_luck_draw_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `user_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_coupon_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '抽奖--关联user_coupon_id',
  `express_order_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID',
  `user_luck_draw_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '抽奖记录详细信息',
  `user_luck_draw_state` tinyint(1) NOT NULL DEFAULT 0 COMMENT '中奖记录  0=》虚假   1=》真实',
  `user_luck_draw_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '抽奖记录是否删除  0=》否   1=》是   ',
  `user_luck_draw_insert_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '抽奖记录  插入时间',
  `user_luck_draw_update_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '抽奖记录   更新时间',
  PRIMARY KEY (`user_luck_draw_id`) USING BTREE,
  UNIQUE INDEX `user_luck_draw_id`(`user_luck_draw_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户抽奖记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_money
-- ----------------------------
DROP TABLE IF EXISTS `user_money`;
CREATE TABLE `user_money`  (
  `user_money_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户钱包余额ID',
  `user_money_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_money_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `user_money_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，元。支出等于0，而该值不为0，表示该条数据为收入操作',
  `user_money_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，元。收入等于0，而该值不为0，表示该条数据为支出操作',
  `user_money_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩余额。当前最新所剩余额。单位，分',
  `user_money_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_money_id`) USING BTREE,
  UNIQUE INDEX `user_money_id`(`user_money_id`) USING BTREE,
  UNIQUE INDEX `user_id_time`(`user_id`, `user_money_time`) USING BTREE,
  INDEX `user_money_join_id`(`user_money_join_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_money_type`(`user_money_type`) USING BTREE,
  INDEX `user_money_plus`(`user_money_plus`) USING BTREE,
  INDEX `user_money_minus`(`user_money_minus`) USING BTREE,
  INDEX `user_money_time`(`user_money_time`) USING BTREE,
  INDEX `user_money_value`(`user_money_value`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户钱包表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_money_annuity
-- ----------------------------
DROP TABLE IF EXISTS `user_money_annuity`;
CREATE TABLE `user_money_annuity`  (
  `user_money_annuity_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户共享金余额ID',
  `user_money_annuity_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_money_annuity_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `user_money_annuity_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，元。支出等于0，而该值不为0，表示该条数据为收入操作',
  `user_money_annuity_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，元。收入等于0，而该值不为0，表示该条数据为支出操作',
  `user_money_annuity_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩余额。当前最新所剩余额。单位，分',
  `user_money_annuity_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_money_annuity_id`) USING BTREE,
  UNIQUE INDEX `user_money_annuity_id`(`user_money_annuity_id`) USING BTREE,
  UNIQUE INDEX `user_id_time`(`user_id`, `user_money_annuity_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_money_annuity_join_id`(`user_money_annuity_join_id`) USING BTREE,
  INDEX `user_money_annuity_type`(`user_money_annuity_type`) USING BTREE,
  INDEX `user_money_annuity_plus`(`user_money_annuity_plus`) USING BTREE,
  INDEX `user_money_annuity_minus`(`user_money_annuity_minus`) USING BTREE,
  INDEX `user_money_annuity_value`(`user_money_annuity_value`) USING BTREE,
  INDEX `user_money_annuity_time`(`user_money_annuity_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户养老金表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_money_earning
-- ----------------------------
DROP TABLE IF EXISTS `user_money_earning`;
CREATE TABLE `user_money_earning`  (
  `user_money_earning_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户共享金余额ID',
  `user_money_earning_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_money_earning_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `user_money_earning_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，元。支出等于0，而该值不为0，表示该条数据为收入操作',
  `user_money_earning_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，元。收入等于0，而该值不为0，表示该条数据为支出操作',
  `user_money_earning_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩余额。当前最新所剩余额。单位，分',
  `user_money_earning_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_money_earning_id`) USING BTREE,
  UNIQUE INDEX `user_money_earning_id`(`user_money_earning_id`) USING BTREE,
  UNIQUE INDEX `user_id_time`(`user_id`, `user_money_earning_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_money_earning_join_id`(`user_money_earning_join_id`) USING BTREE,
  INDEX `user_money_earning_type`(`user_money_earning_type`) USING BTREE,
  INDEX `user_money_earning_plus`(`user_money_earning_plus`) USING BTREE,
  INDEX `user_money_earning_minus`(`user_money_earning_minus`) USING BTREE,
  INDEX `user_money_earning_value`(`user_money_earning_value`) USING BTREE,
  INDEX `user_money_earning_time`(`user_money_earning_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户赠送收益表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_money_help
-- ----------------------------
DROP TABLE IF EXISTS `user_money_help`;
CREATE TABLE `user_money_help`  (
  `user_money_help_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'ID',
  `user_money_help_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_money_help_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `user_money_help_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，元。支出等于0，而该值不为0，表示该条数据为收入操作',
  `user_money_help_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，元。收入等于0，而该值不为0，表示该条数据为支出操作',
  `user_money_help_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩余额。当前最新所剩余额。单位，分',
  `user_money_help_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_money_help_id`) USING BTREE,
  UNIQUE INDEX `user_money_help_id`(`user_money_help_id`) USING BTREE,
  UNIQUE INDEX `user_id_time`(`user_id`, `user_money_help_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_money_help_join_id`(`user_money_help_join_id`) USING BTREE,
  INDEX `user_money_help_type`(`user_money_help_type`) USING BTREE,
  INDEX `user_money_help_plus`(`user_money_help_plus`) USING BTREE,
  INDEX `user_money_help_minus`(`user_money_help_minus`) USING BTREE,
  INDEX `user_money_help_value`(`user_money_help_value`) USING BTREE,
  INDEX `user_money_help_time`(`user_money_help_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户扶贫资' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_money_service
-- ----------------------------
DROP TABLE IF EXISTS `user_money_service`;
CREATE TABLE `user_money_service`  (
  `user_money_service_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户共享金余额ID',
  `user_money_service_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_money_service_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `user_money_service_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，元。支出等于0，而该值不为0，表示该条数据为收入操作',
  `user_money_service_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，元。收入等于0，而该值不为0，表示该条数据为支出操作',
  `user_money_service_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩余额。当前最新所剩余额。单位，分',
  `user_money_service_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_money_service_id`) USING BTREE,
  UNIQUE INDEX `user_money_service_id`(`user_money_service_id`) USING BTREE,
  UNIQUE INDEX `user_id_time`(`user_id`, `user_money_service_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_money_service_join_id`(`user_money_service_join_id`) USING BTREE,
  INDEX `user_money_service_type`(`user_money_service_type`) USING BTREE,
  INDEX `user_money_service_plus`(`user_money_service_plus`) USING BTREE,
  INDEX `user_money_service_minus`(`user_money_service_minus`) USING BTREE,
  INDEX `user_money_service_value`(`user_money_service_value`) USING BTREE,
  INDEX `user_money_service_time`(`user_money_service_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户服务费表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_money_share
-- ----------------------------
DROP TABLE IF EXISTS `user_money_share`;
CREATE TABLE `user_money_share`  (
  `user_money_share_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户共享金余额ID',
  `user_money_share_join_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所连接的上一个积分ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_money_share_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易类型。充值、转账、红包、购物、退款，还有管理员后台的操作：人工收入、人工支出',
  `user_money_share_plus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收入。单位，元。支出等于0，而该值不为0，表示该条数据为收入操作',
  `user_money_share_minus` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支出。单位，元。收入等于0，而该值不为0，表示该条数据为支出操作',
  `user_money_share_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所剩余额。当前最新所剩余额。单位，分',
  `user_money_share_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_money_share_id`) USING BTREE,
  UNIQUE INDEX `user_money_share_id`(`user_money_share_id`) USING BTREE,
  UNIQUE INDEX `user_id_time`(`user_id`, `user_money_share_time`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_money_share_join_id`(`user_money_share_join_id`) USING BTREE,
  INDEX `user_money_share_type`(`user_money_share_type`) USING BTREE,
  INDEX `user_money_share_plus`(`user_money_share_plus`) USING BTREE,
  INDEX `user_money_share_minus`(`user_money_share_minus`) USING BTREE,
  INDEX `user_money_share_value`(`user_money_share_value`) USING BTREE,
  INDEX `user_money_share_time`(`user_money_share_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户消费共享金表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_oauth
-- ----------------------------
DROP TABLE IF EXISTS `user_oauth`;
CREATE TABLE `user_oauth`  (
  `user_oauth_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户资源授权ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_oauth_platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '授权平台。\"github\"、\"weibo\"、\"qq\"、\"weixin\"',
  `user_oauth_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '第三方平台提供的用户信息唯一标识',
  `user_oauth_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '所获取的json数据，每次第三方登陆都要更新',
  `user_oauth_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间(时间戳，秒)，每次登录都要更新“值”',
  `user_oauth_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定时间(时间戳，秒)',
  `user_oauth_wx_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信授权登录用户openID',
  PRIMARY KEY (`user_oauth_id`) USING BTREE,
  UNIQUE INDEX `user_oauth_id`(`user_oauth_id`) USING BTREE,
  UNIQUE INDEX `platform_key`(`user_oauth_platform`, `user_oauth_key`) USING BTREE,
  UNIQUE INDEX `user_platform`(`user_id`, `user_oauth_platform`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_oauth_update_time`(`user_oauth_update_time`) USING BTREE,
  INDEX `user_oauth_insert_time`(`user_oauth_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户资源授权表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_phone
-- ----------------------------
DROP TABLE IF EXISTS `user_phone`;
CREATE TABLE `user_phone`  (
  `user_phone_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户手机号',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_phone_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '手机类型。0联系手机号，1登录手机号',
  `user_phone_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态。0未认证，1已认证。注意，user_phone_type为1，那么该参数必须是为1',
  `user_phone_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'JSON数据',
  `user_phone_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `user_phone_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `user_phone_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`user_phone_id`) USING BTREE,
  UNIQUE INDEX `user_phone_id`(`user_phone_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_phone_sort`(`user_phone_sort`) USING BTREE,
  INDEX `user_phone_type`(`user_phone_type`) USING BTREE,
  INDEX `user_phone_state`(`user_phone_state`) USING BTREE,
  INDEX `user_phone_insert_time`(`user_phone_insert_time`) USING BTREE,
  INDEX `user_phone_update_time`(`user_phone_update_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户手机表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_phone
-- ----------------------------
INSERT INTO `user_phone` VALUES ('11111111111', '1', 1, 1, '[]', 0, 1542265548, 1548400009);

-- ----------------------------
-- Table structure for user_withdraw
-- ----------------------------
DROP TABLE IF EXISTS `user_withdraw`;
CREATE TABLE `user_withdraw`  (
  `user_withdraw_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '提现ID',
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `order_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单ID，产生交易的订单而关联的订单',
  `user_withdraw_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '公开备注、说明，产生这条数据的方提供信息',
  `user_withdraw_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '提现类型。如“merchant_money”即商家钱包',
  `user_withdraw_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '提现方式。如：微信支付“weixinpay”、支付宝支付\"alipay\"',
  `user_withdraw_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提现金额/数量',
  `user_withdraw_rmb` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提现金额，人民币分',
  `user_withdraw_admin` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员的用户ID',
  `user_withdraw_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '状态。0审核不通过，1提现成功，2审核中',
  `user_withdraw_pass_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间。时间戳，秒',
  `user_withdraw_fail_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核不通过时间。时间戳，秒',
  `user_withdraw_fail_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '审核不通过原因，由管理员填写',
  `user_withdraw_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间。时间戳，秒',
  PRIMARY KEY (`user_withdraw_id`) USING BTREE,
  UNIQUE INDEX `merchant_withdraw_id`(`user_withdraw_id`) USING BTREE,
  INDEX `merchant_withdraw_type`(`user_withdraw_type`) USING BTREE,
  INDEX `merchant_withdraw_method`(`user_withdraw_method`) USING BTREE,
  INDEX `merchant_withdraw_value`(`user_withdraw_value`) USING BTREE,
  INDEX `merchant_withdraw_admin`(`user_withdraw_admin`) USING BTREE,
  INDEX `merchant_withdraw_state`(`user_withdraw_state`) USING BTREE,
  INDEX `merchant_withdraw_pass_time`(`user_withdraw_pass_time`) USING BTREE,
  INDEX `merchant_withdraw_fail_time`(`user_withdraw_fail_time`) USING BTREE,
  INDEX `merchant_withdraw_insert_time`(`user_withdraw_insert_time`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `merchant_withdraw_rmb`(`user_withdraw_rmb`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商家提现表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
