/*
 Navicat Premium Data Transfer

 Source Server         : 宝塔-本地数据库
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : db_administrator

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 25/10/2019 15:30:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for administrator_admin
-- ----------------------------
DROP TABLE IF EXISTS `administrator_admin`;
CREATE TABLE `administrator_admin`  (
  `admin_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理角色id',
  `admin_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理角色名称',
  `admin_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '介绍',
  `authority_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限表id，多个权限以英文逗号隔开。并且前后都要加逗号。如： ,admin_authority_id,admin_authority_id,admin_authority_id,',
  `admin_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序。升序排在前面，并且等于0的排在最后面。',
  `admin_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '管理员状态。0 封禁|1 正常',
  `admin_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建、更新时间(时间戳，秒)',
  `admin_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建、更新时间(时间戳，秒)',
  PRIMARY KEY (`admin_id`) USING BTREE,
  UNIQUE INDEX `admin_id`(`admin_id`) USING BTREE,
  INDEX `admin_name`(`admin_name`(191)) USING BTREE,
  INDEX `admin_sort`(`admin_sort`) USING BTREE,
  INDEX `admin_state`(`admin_state`) USING BTREE,
  INDEX `admin_update_time`(`admin_update_time`) USING BTREE,
  INDEX `admin_insert_time`(`admin_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of administrator_admin
-- ----------------------------
INSERT INTO `administrator_admin` VALUES ('admin_1', '超级管理员', '后台管理员的测试角色', ',user,admin,admin_add,admin_read,admin_user_add,admin_user_read,admin_log_read,admin_edit,admin_remove,user_read,user_edit,user_phone_add,user_phone_edit,user_phone_remove,application,application_cache_clear,application_cache_read,admin_user_edit,admin_user_remove,administrator_authority_add,administrator_authority_read,administrator_authority_edit,administrator_authority_remove,administrator_module_add,administrator_module_read,administrator_module_edit,administrator_module_remove,administrator,administrator_api_add,administrator_api_read,administrator_api_edit,administrator_api_remove,user_config_read,user_config_edit,administrator_program_error_read,administrator_program_error_remove,administrator_management_add,administrator_management_read,administrator_management_edit,administrator_management_remove,administrator_application_add,administrator_application_read,administrator_application_edit,administrator_application_remove,administrator_cache_read,administrator_cache_clear,user_phone_verify_code,user_add,administrator_markdown_read,', 1, 1, 1571881731, 1542353175);

-- ----------------------------
-- Table structure for administrator_admin_authority
-- ----------------------------
DROP TABLE IF EXISTS `administrator_admin_authority`;
CREATE TABLE `administrator_admin_authority`  (
  `admin_authority_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理权限ID',
  `admin_authority_parent_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '当前表父级id。为空则为权限组名称，否则为子权限。父级权限的父级id必须为空。',
  `admin_authority_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '权限名称',
  `admin_authority_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '描述',
  `admin_authority_sort` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序。升序排在前面，并且等于0的排在最后面。',
  `admin_authority_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间(时间戳，秒)',
  `admin_authority_insert_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳，秒)',
  PRIMARY KEY (`admin_authority_id`) USING BTREE,
  UNIQUE INDEX `admin_authority_id`(`admin_authority_id`) USING BTREE,
  INDEX `admin_authority_name`(`admin_authority_name`(191)) USING BTREE,
  INDEX `admin_authority_sort`(`admin_authority_sort`) USING BTREE,
  INDEX `admin_authority_parent_id`(`admin_authority_parent_id`) USING BTREE,
  INDEX `admin_authority_update_time`(`admin_authority_update_time`) USING BTREE,
  INDEX `admin_authority_insert_time`(`admin_authority_insert_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理权限表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of administrator_admin_authority
-- ----------------------------
INSERT INTO `administrator_admin_authority` VALUES ('admin', '', '管理系统', '', 2, 1546928597, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_add', 'admin', '添加管理角色', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_authority_add', 'admin', '添加管理权限', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_authority_edit', 'admin', '编辑管理权限', '对权限进行编辑', 0, 1546916318, 1546916318);
INSERT INTO `administrator_admin_authority` VALUES ('admin_authority_read', 'admin', '读取管理权限', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_authority_remove', 'admin', '删除管理权限', '', 0, 1546928185, 1546928142);
INSERT INTO `administrator_admin_authority` VALUES ('admin_edit', 'admin', '编辑管理角色', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_log_read', 'admin', '读取操作日志', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_read', 'admin', '读取管理角色', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_remove', 'admin', '删除管理角色', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_user_add', 'admin', '添加管理人员', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_user_edit', 'admin', '管理人员编辑', '', 0, 1551696498, 1551696498);
INSERT INTO `administrator_admin_authority` VALUES ('admin_user_read', 'admin', '读取管理人员', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('admin_user_remove', 'admin', '管理人员删除', '', 0, 1551696512, 1551696512);
INSERT INTO `administrator_admin_authority` VALUES ('api_list', 'system', '接口列表', '测试简介。啦啦啦', 2, 1546927731, 1546926301);
INSERT INTO `administrator_admin_authority` VALUES ('application', '', '应用管理', '', 0, 1547958898, 1547958898);
INSERT INTO `administrator_admin_authority` VALUES ('application_cache_clear', 'application', '清理缓存', '', 0, 1550128601, 1550128601);
INSERT INTO `administrator_admin_authority` VALUES ('application_cache_read', 'application', '缓存信息读取', '', 0, 1550132030, 1550132030);
INSERT INTO `administrator_admin_authority` VALUES ('application_config_pay_edit', 'application', '支付配置编辑', '', 0, 1550132356, 1550132356);
INSERT INTO `administrator_admin_authority` VALUES ('application_config_pay_read', 'application', '支付配置读取', '', 0, 1550132333, 1550132333);
INSERT INTO `administrator_admin_authority` VALUES ('application_slideshow_add', 'application', '轮播图添加', '', 0, 1550656395, 1550656395);
INSERT INTO `administrator_admin_authority` VALUES ('application_slideshow_edit', 'application', '轮播图编辑', '', 0, 1550656419, 1550656419);
INSERT INTO `administrator_admin_authority` VALUES ('application_slideshow_read', 'application', '轮播图读取', '', 0, 1550656406, 1550656406);
INSERT INTO `administrator_admin_authority` VALUES ('application_slideshow_remove', 'application', '轮播图删除', '', 0, 1550656431, 1550656431);
INSERT INTO `administrator_admin_authority` VALUES ('application_type_add', 'application', '分类添加', '', 0, 1551782349, 1551782349);
INSERT INTO `administrator_admin_authority` VALUES ('application_type_edit', 'application', '分类编辑', '', 0, 1551782371, 1551782371);
INSERT INTO `administrator_admin_authority` VALUES ('application_type_read', 'application', '读取分类列表', '', 0, 1551782335, 1551782335);
INSERT INTO `administrator_admin_authority` VALUES ('application_type_remove', 'application', '分类删除', '', 0, 1551782361, 1551782361);
INSERT INTO `administrator_admin_authority` VALUES ('cache_clear', 'system', '清理缓存', '清理redis缓存等其他数据缓存', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('cms', '', '内容管理系统', '', 0, 1549883147, 1549883147);
INSERT INTO `administrator_admin_authority` VALUES ('cms_article_add', 'cms', '添加文章', '', 0, 1549883186, 1549883186);
INSERT INTO `administrator_admin_authority` VALUES ('cms_article_edit', 'cms', '编辑文章', '', 0, 1549883234, 1549883234);
INSERT INTO `administrator_admin_authority` VALUES ('cms_article_read', 'cms', '读取文章', '', 0, 1549883252, 1549883252);
INSERT INTO `administrator_admin_authority` VALUES ('merchant', '', '商家系统', '', 0, 1548474102, 1548474102);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_add', 'merchant', '添加商家', '', 0, 1548643442, 1548483620);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_credit_edit', 'merchant', '商家积分编辑', '', 0, 1550456854, 1550456854);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_credit_read', 'merchant', '商家积分读取', '', 0, 1550456835, 1550456835);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_edit', 'merchant', '编辑商家', '', 0, 1548643454, 1548483645);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_image_edit', 'merchant', '商家展示图片编辑', '', 0, 1551670565, 1551670565);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_image_remove', 'merchant', '商家展示图片删除', '', 0, 1551670547, 1551670547);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_image_upload', 'merchant', '商家展示图片上传', '', 0, 1551670534, 1551670534);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_money_edit', 'merchant', '商家钱包编辑', '', 0, 1551420326, 1551420313);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_money_read', 'merchant', '商家钱包读取', '', 0, 1551420237, 1551420237);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_read', 'merchant', '读取商家', '', 0, 1548664210, 1548483675);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_remove', 'merchant', '删除商家', '', 0, 1548643499, 1548643499);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_user_add', 'merchant', '添加商家用户', '', 0, 1548664053, 1548664053);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_user_edit', 'merchant', '编辑商家用户', '', 0, 1548664153, 1548664153);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_user_read', 'merchant', '读取商家用户', '', 0, 1548727164, 1548727164);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_user_remove', 'merchant', '删除商家用户', '', 0, 1548664178, 1548664178);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_withdraw_read', 'merchant', '商家提现读取', '', 0, 1551234534, 1551234534);
INSERT INTO `administrator_admin_authority` VALUES ('merchant_withdraw_state', 'merchant', '商家提现审核', '', 0, 1551238974, 1551238974);
INSERT INTO `administrator_admin_authority` VALUES ('shop', '', '商城', '', 0, 1547542750, 1547542750);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_add', 'shop', '添加商品', '', 0, 1547867804, 1547867785);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_edit', 'shop', '编辑商品', '', 0, 1547867826, 1547867826);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_file_edit', 'shop', '编辑商品文件', '', 0, 1548063641, 1548063641);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_file_remove', 'shop', '删除商品文件', '', 0, 1548063626, 1548063626);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_file_upload', 'shop', '上传商品文件', '', 0, 1548063660, 1548063600);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_image_edit', 'shop', '编辑商品图片', '', 0, 1548050192, 1548050192);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_image_remove', 'shop', '删除商品图片', '', 0, 1548050161, 1548050161);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_image_upload', 'shop', '上传商品图片', '', 0, 1548050127, 1548050049);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_read', 'shop', '读取商品', '', 0, 1547867755, 1547867755);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_remove', 'shop', '删除商品', '', 0, 1547867874, 1547867874);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_sku_add', 'shop', '添加商品规格', '', 0, 1548002126, 1548002126);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_sku_edit', 'shop', '编辑商品规格', '', 0, 1548002142, 1548002142);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_sku_remove', 'shop', '删除商品规格', '', 0, 1548002164, 1548002164);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_spu_add', 'shop', '添加商品属性', '', 0, 1547977345, 1547977345);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_spu_edit', 'shop', '编辑商品属性', '', 0, 1547977387, 1547977387);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_spu_remove', 'shop', '删除商品属性', '', 0, 1547977401, 1547977401);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_trash', 'shop', '回收商品', '', 0, 1547867837, 1547867837);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_trash_read', 'shop', '读取回收商品', '', 0, 1547867854, 1547867854);
INSERT INTO `administrator_admin_authority` VALUES ('shop_goods_type_edit', 'shop', '编辑商品分类', '', 0, 1547962461, 1547962461);
INSERT INTO `administrator_admin_authority` VALUES ('shop_type_add', 'shop', '添加分类', '', 0, 1547796625, 1547796625);
INSERT INTO `administrator_admin_authority` VALUES ('shop_type_edit', 'shop', '编辑分类', '', 0, 1547796663, 1547796663);
INSERT INTO `administrator_admin_authority` VALUES ('shop_type_read', 'shop', '读取分类', '', 0, 1547796610, 1547796610);
INSERT INTO `administrator_admin_authority` VALUES ('shop_type_remove', 'shop', '删除分类', '', 0, 1547796641, 1547796641);
INSERT INTO `administrator_admin_authority` VALUES ('softstore', '', '软件商城', '', 4, 1546928597, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_order_contact', 'softstore', '联系处理订单', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_order_detail_read', 'softstore', '读取订单详细', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_order_read', 'softstore', '读取订单', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_order_trash', 'softstore', '回收订单', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_order_trash_read', 'softstore', '读取回收订单', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_add', 'softstore', '添加产品', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_attr_add', 'softstore', '添加产品属性', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_attr_edit', 'softstore', '编辑产品属性', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_attr_remove', 'softstore', '删除产品属性', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_edit', 'softstore', '编辑产品', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_file_edit', 'softstore', '编辑产品文件', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_file_remove', 'softstore', '删除产品文件', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_file_upload', 'softstore', '上传产品文件', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_image_edit', 'softstore', '编辑产品图片', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_image_remove', 'softstore', '删除产品图片', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_image_upload', 'softstore', '上传产品图片', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_read', 'softstore', '读取产品', '显示产品列表、读取单个产品信息', 0, 0, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_remove', 'softstore', '删除产品', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_trash', 'softstore', '回收产品', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_trash_read', 'softstore', '读取回收产品', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_product_type_edit', 'softstore', '编辑产品分类', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_type_add', 'softstore', '添加分类', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_type_edit', 'softstore', '编辑分类', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_type_read', 'softstore', '读取分类', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('softstore_type_remove', 'softstore', '删除分类', '', 0, 1542353175, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('system', '', '系统配置', '', 1, 1546928302, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('user', '', '用户系统', '', 3, 1546928597, 1542353175);
INSERT INTO `administrator_admin_authority` VALUES ('user_credit_edit', 'user', '用户积分编辑', '', 0, 1551349904, 1551349889);
INSERT INTO `administrator_admin_authority` VALUES ('user_credit_read', 'user', '用户积分读取', '', 0, 1551349920, 1551349795);
INSERT INTO `administrator_admin_authority` VALUES ('user_edit', 'user', '编辑用户', '', 0, 1547523964, 1547523964);
INSERT INTO `administrator_admin_authority` VALUES ('user_identity_edit', 'user', '编辑用户实名认证信息', '', 0, 1551941265, 1549852192);
INSERT INTO `administrator_admin_authority` VALUES ('user_identity_read', 'user', '读取用户实名认证信息', '', 0, 1551941286, 1549852222);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_annuity_edit', 'user', '用户养老资金编辑', '', 0, 1551426624, 1551426624);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_annuity_read', 'user', '用户养老资金读取', '', 0, 1551426609, 1551426609);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_earning_edit', 'user', '用户赠送收益编辑', '', 0, 1550566909, 1550566909);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_earning_read', 'user', '用户赠送收益读取', '', 0, 1550566883, 1550566883);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_edit', 'user', '用户钱包编辑', '', 0, 1551426073, 1551426073);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_help_edit', 'user', '扶贫资金编辑', '', 0, 1551426650, 1551426650);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_help_read', 'user', '扶贫资金读取', '', 0, 1551426639, 1551426639);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_read', 'user', '用户钱包读取', '', 0, 1551426063, 1551426063);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_service_read', 'user', '用户服务费读取', '', 0, 1551426669, 1551426669);
INSERT INTO `administrator_admin_authority` VALUES ('user_money_share_read', 'user', '消费共享金读取', '', 0, 1551426685, 1551426685);
INSERT INTO `administrator_admin_authority` VALUES ('user_phone_add', 'user', '添加用户认证手机', '', 0, 1547607697, 1547607697);
INSERT INTO `administrator_admin_authority` VALUES ('user_phone_edit', 'user', '编辑用户认证手机', '', 0, 1547607712, 1547607712);
INSERT INTO `administrator_admin_authority` VALUES ('user_phone_remove', 'user', '删除用户认证手机', '', 0, 1547607780, 1547607780);
INSERT INTO `administrator_admin_authority` VALUES ('user_read', 'user', '读取用户', '', 0, 1547523809, 1547523809);

-- ----------------------------
-- Table structure for administrator_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `administrator_admin_log`;
CREATE TABLE `administrator_admin_log`  (
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
-- Table structure for administrator_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `administrator_admin_user`;
CREATE TABLE `administrator_admin_user`  (
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
-- Records of administrator_admin_user
-- ----------------------------
INSERT INTO `administrator_admin_user` VALUES ('1', '', 'admin_1', '超级管理员', '{\"page_size\":\"20\"}', 1, 0, 1542353175, 1542353175);

-- ----------------------------
-- Table structure for administrator_config
-- ----------------------------
DROP TABLE IF EXISTS `administrator_config`;
CREATE TABLE `administrator_config`  (
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
-- Records of administrator_config
-- ----------------------------
INSERT INTO `administrator_config` VALUES ('admin_page_size', '10', 'integer', '后台分页的每页默认条数', '', 0, 1547434268, 1547434268);
INSERT INTO `administrator_config` VALUES ('page_size', '12', 'integer', '前台分页的每页默认条数', '', 0, 1547434268, 1547434268);

-- ----------------------------
-- Table structure for administrator_file
-- ----------------------------
DROP TABLE IF EXISTS `administrator_file`;
CREATE TABLE `administrator_file`  (
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
-- Table structure for administrator_image
-- ----------------------------
DROP TABLE IF EXISTS `administrator_image`;
CREATE TABLE `administrator_image`  (
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
-- Table structure for administrator_lock
-- ----------------------------
DROP TABLE IF EXISTS `administrator_lock`;
CREATE TABLE `administrator_lock`  (
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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '锁表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for administrator_user
-- ----------------------------
DROP TABLE IF EXISTS `administrator_user`;
CREATE TABLE `administrator_user`  (
  `user_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_parent_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父ID，即 ：user表的 user_id',
  `user_logo_image_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像图片ID',
  `user_left_password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '左密码。即加密的时候，与用户id混合，放在左边。算法：md5(用户密码+用户ID)',
  `user_right_password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '右密码。即加密的时候，与用户id混合，放在右边。算法：md5(用户ID+用户密码)',
  `user_nickname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_compellation` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `user_sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别。0未知，1男，2女',
  `user_wechat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户微信号',
  `user_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态。0封禁，1正常',
  `user_qq` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户的QQ号',
  `user_email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户的邮箱',
  `user_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json数据',
  `user_trash` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收。0正常；1已回收',
  `user_trash_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回收时间',
  `user_register_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册时间',
  `user_update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资料更新时间',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_sex`(`user_sex`) USING BTREE,
  INDEX `user_qq`(`user_qq`(191)) USING BTREE,
  INDEX `user_email`(`user_email`(191)) USING BTREE,
  INDEX `user_register_time`(`user_register_time`) USING BTREE,
  INDEX `user_update_time`(`user_update_time`) USING BTREE,
  INDEX `user_nickname`(`user_nickname`(191)) USING BTREE,
  INDEX `user_wechat`(`user_wechat`(191)) USING BTREE,
  INDEX `user_trash`(`user_trash`) USING BTREE,
  INDEX `user_trash_time`(`user_trash_time`) USING BTREE,
  INDEX `user_logo_image_id`(`user_logo_image_id`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of administrator_user
-- ----------------------------
INSERT INTO `administrator_user` VALUES ('1', '', '', 'feb8dc0697a2e0a947c6e20dc4ec3ebc', '88312213c3492c4cd89d297f16cb0fc4', '超级管理', '老王', 0, '', 1, '', '', '', 0, 0, 1542265548, 1571821664);

-- ----------------------------
-- Table structure for administrator_user_log
-- ----------------------------
DROP TABLE IF EXISTS `administrator_user_log`;
CREATE TABLE `administrator_user_log`  (
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
-- Table structure for administrator_user_phone
-- ----------------------------
DROP TABLE IF EXISTS `administrator_user_phone`;
CREATE TABLE `administrator_user_phone`  (
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
-- Records of administrator_user_phone
-- ----------------------------
INSERT INTO `administrator_user_phone` VALUES ('11111111111', '1', 1, 1, '[]', 0, 1542265548, 0);

SET FOREIGN_KEY_CHECKS = 1;
