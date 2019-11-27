<?php
/** ---- eapie ----
 * 优狐积木框架，让开发就像组装积木一样简单！
 * 解放千千万万程序员！这只是1.0版本，后续版本势如破竹！
 * 
 * QQ群：523668865
 * 开源地址 https://gitee.com/lxh888/openshop
 * 官网 http://eonfox.com/
 * 后端框架文档 http://cao.php.eonfox.com
 * 
 * 作者：绵阳市优狐网络科技有限公司
 * 电话/微信：18981181942
 * QQ：294520544
 */



namespace eapie\source\initialize;

/**
 * 数据表
 */
class table extends request
{   
    
    //application 应用下的表
    const   TABLE_CONFIG            =   'eapie\source\table\application\config';
    const   TABLE_IMAGE             =   'eapie\source\table\application\image';
    const   TABLE_FILE              =   'eapie\source\table\application\file';
    const   TABLE_NAVIGATION        =   'eapie\source\table\application\navigation';
    const   TABLE_LOCK              =   'eapie\source\table\application\lock';
    const   TABLE_ORDER             =   'eapie\source\table\application\order';
    const   TABLE_EVENT             =   'eapie\source\table\application\event';
    const   TABLE_SLIDESHOW         =   'eapie\source\table\application\slideshow';
    const   TABLE_TYPE              =   'eapie\source\table\application\type';
    //const TABLE_MESSAGE           =   'eapie\source\table\application\message';
    const   TABLE_SHIPPING          =   'eapie\source\table\application\shipping';
    const   TABLE_COUPON            =   'eapie\source\table\application\coupon';
    
    
    
    //上面是应用没有的表，下面是应用共有的表
    const   TABLE_USER              =   'eapie\source\table\user\user';
    const   TABLE_USER_LOG          =   'eapie\source\table\user\user_log';
    const   TABLE_USER_PHONE        =   'eapie\source\table\user\user_phone';
    const   TABLE_USER_AUTHENT      =   'eapie\source\table\user\user_authent';
    const   TABLE_USER_IDENTITY     =   'eapie\source\table\user\user_identity';
    const   TABLE_USER_COLLECTION   =   'eapie\source\table\user\user_collection';
    const   TABLE_USER_FOLLOW       =   'eapie\source\table\user\user_follow';
    const   TABLE_USER_COMMENT      =   'eapie\source\table\user\user_comment';
    
    const   TABLE_USER_ADDRESS      =   'eapie\source\table\user\user_address';//用户地址
    
    const   TABLE_USER_OAUTH        =   'eapie\source\table\user\user_oauth';
    const   TABLE_USER_CREDIT       =   'eapie\source\table\user\user_credit';
    const   TABLE_USER_MONEY        =   'eapie\source\table\user\user_money';
    const   TABLE_USER_MONEY_ANNUITY        =   'eapie\source\table\user\user_money_annuity';
    const   TABLE_USER_MONEY_EARNING        =   'eapie\source\table\user\user_money_earning';
    const   TABLE_USER_MONEY_HELP           =   'eapie\source\table\user\user_money_help';
    const   TABLE_USER_MONEY_SERVICE        =   'eapie\source\table\user\user_money_service';
    const   TABLE_USER_MONEY_SHARE          =   'eapie\source\table\user\user_money_share';
    const   TABLE_USER_EVALUATE             =   'eapie\source\table\user\user_evaluate';
    
    const   TABLE_USER_COUPON       =   'eapie\source\table\user\user_coupon';
    const   TABLE_USER_LUCK_DRAW    =   'eapie\source\table\user\user_luck_draw';
    const   TABLE_USER_WITHDRAW     =   'eapie\source\table\user\user_withdraw';
    const   TABLE_USER_RECOMMEND    =   'eapie\source\table\user\user_recommend';

    const   TABLE_USER_OFFICER      =   'eapie\source\table\user\user_officer';
    
    
    
    
    //后台
    const   TABLE_ADMIN                     =   'eapie\source\table\admin\admin';
    const   TABLE_ADMIN_LOG                 =   'eapie\source\table\admin\admin_log';
    const   TABLE_ADMIN_AUTHORITY           =   'eapie\source\table\admin\admin_authority';
    const   TABLE_ADMIN_NAVIGATION          =   'eapie\source\table\admin\admin_navigation';
    const   TABLE_ADMIN_USER                =   'eapie\source\table\admin\admin_user';
    
    
    
    /*软件商城*/
    const   TABLE_SOFTSTORE_ORDER                   =   'eapie\source\table\softstore\ss_order';
    const   TABLE_SOFTSTORE_ORDER_PRODUCT           =   'eapie\source\table\softstore\ss_order_product';
    const   TABLE_SOFTSTORE_TYPE                    =   'eapie\source\table\softstore\ss_type';
    const   TABLE_SOFTSTORE_RECOMMEND               =   'eapie\source\table\softstore\ss_recommend';
    const   TABLE_SOFTSTORE_PRODUCT                 =   'eapie\source\table\softstore\ss_product';
    const   TABLE_SOFTSTORE_PRODUCT_TYPE            =   'eapie\source\table\softstore\ss_product_type';
    const   TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE       =   'eapie\source\table\softstore\ss_product_attr';
    const   TABLE_SOFTSTORE_PRODUCT_FILE            =   'eapie\source\table\softstore\ss_product_file';
    const   TABLE_SOFTSTORE_PRODUCT_IMAGE           =   'eapie\source\table\softstore\ss_product_image';
    
    
    
    /*商城*/
    const   TABLE_SHOP                              =   'eapie\source\table\shop\shop';
    const   TABLE_SHOP_CART                         =   'eapie\source\table\shop\shop_cart';
    const   TABLE_SHOP_TYPE                         =   'eapie\source\table\shop\shop_type';
    const   TABLE_SHOP_GOODS                        =   'eapie\source\table\shop\shop_goods';
    const   TABLE_SHOP_GOODS_TYPE                   =   'eapie\source\table\shop\shop_goods_type';
    const   TABLE_SHOP_GOODS_SPU                    =   'eapie\source\table\shop\shop_goods_spu';
    const   TABLE_SHOP_GOODS_SKU                    =   'eapie\source\table\shop\shop_goods_sku';
    const   TABLE_SHOP_GOODS_FILE                   =   'eapie\source\table\shop\shop_goods_file';
    const   TABLE_SHOP_GOODS_IMAGE                  =   'eapie\source\table\shop\shop_goods_image';
    const   TABLE_SHOP_GOODS_WHEN                   =   'eapie\source\table\shop\shop_goods_when';
    const   TABLE_SHOP_GOODS_GROUP                  =   'eapie\source\table\shop\shop_goods_group';
    const   TABLE_SHOP_GOODS_REGION                 =   'eapie\source\table\shop\shop_goods_region';
    const   TABLE_SHOP_GOODS_STOCK_LOG              =   'eapie\source\table\shop\shop_goods_stock_log';
    
    const   TABLE_SHOP_ORDER                        =   'eapie\source\table\shop\shop_order';
    const   TABLE_SHOP_ORDER_GOODS                  =   'eapie\source\table\shop\shop_order_goods';
    const   TABLE_SHOP_GROUP_GOODS                  =   'eapie\source\table\shop\shop_group_goods';
    const   TABLE_SHOP_GROUP_ORDER                  =   'eapie\source\table\shop\shop_group_order';
    const   TABLE_SHOP_ORDER_WRITE_OFF              =   'eapie\source\table\shop\shop_order_write_off';
    const   TABLE_SHOP_ORDER_GROUP                  =   'eapie\source\table\shop\shop_order_group';
    
    
    
    /*内容管理系统*/
    const   TABLE_CMS_ARTICLE                       =   'eapie\source\table\cms\cms_article';
    const   TABLE_CMS_ARTICLE_TYPE                  =   'eapie\source\table\cms\cms_article_type';
    const   TABLE_CMS_ARTICLE_IMAGE                 =   'eapie\source\table\cms\cms_article_image';
    
    
    
    /*品牌*/
    const   TABLE_BRAND                             =   'eapie\source\table\brand\brand';
    const   TABLE_BRAND_TYPE                        =   'eapie\source\table\brand\brand_type';
    
    
    
    
    /*商家*/
    const   TABLE_MERCHANT                          =   'eapie\source\table\merchant\merchant';
    const   TABLE_MERCHANT_TYPE                     =   'eapie\source\table\merchant\merchant_type';
    const   TABLE_MERCHANT_USER                     =   'eapie\source\table\merchant\merchant_user';
    const   TABLE_MERCHANT_CREDIT                   =   'eapie\source\table\merchant\merchant_credit';
    const   TABLE_MERCHANT_MONEY                    =   'eapie\source\table\merchant\merchant_money';
    const   TABLE_MERCHANT_IMAGE                    =   'eapie\source\table\merchant\merchant_image';
    const   TABLE_MERCHANT_WITHDRAW                 =   'eapie\source\table\merchant\merchant_withdraw';
    const   TABLE_MERCHANT_TALLY                    =   'eapie\source\table\merchant\merchant_tally';
    const   TABLE_MERCHANT_CASHIER                  =   'eapie\source\table\merchant\merchant_cashier';
    const   TABLE_MERCHANT_GOODS_TYPE               =   'eapie\source\table\merchant\merchant_goods_type';            
    
    
    /*商家楼盘*/
    const   TABLE_HOUSE_CLIENT                      =   'eapie\source\table\house\house_client';
    const   TABLE_HOUSE_FILING                      =   'eapie\source\table\house\house_filing';
    const   TABLE_HOUSE_ORDER                       =   'eapie\source\table\house\house_order';
    const   TABLE_HOUSE_ORDER_IMAGE                 =   'eapie\source\table\house\house_order_image';
    const   TABLE_HOUSE_PRODUCT                     =   'eapie\source\table\house\house_product';
    const   TABLE_HOUSE_PRODUCT_IMAGE               =   'eapie\source\table\house\house_product_image';
    
    const   TABLE_HOUSE_PRODUCT_TOP                 =   'eapie\source\table\house\house_product_top';
    const   TABLE_HOUSE_TOP_OPTION                  =   'eapie\source\table\house\house_top_option';
    const   TABLE_HOUSE_TOP_ORDER                   =   'eapie\source\table\house\house_top_order';
    
    
    /*应用软件*/
    const   TABLE_APP                               =   'eapie\source\table\app\app';
    
    
    /*代理系统*/
    const   TABLE_AGENT_USER                        =   'eapie\source\table\agent\agent_user';
    const   TABLE_AGENT_REGION                      =   'eapie\source\table\agent\agent_region';
    const   TABLE_AGENT_USER_CREDIT_AWARD_LOG       =   'eapie\source\table\agent\agent_user_credit_award_log';
    
    
    /*快递系统*/
    const   TABLE_EXPRESS_RIDER                     =   'eapie\source\table\express\express_rider';
    const   TABLE_EXPRESS_ORDER                     =   'eapie\source\table\express\express_order';
    
    
    
    
    
    
    
    
    
    
}