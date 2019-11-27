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



namespace eapie\source\table\application;
use eapie\main;
class config extends main {
	
	
	/*配置表*/
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
	
	
	
	/**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
    	'config_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少配置ID参数"),
					'echo'=>array("配置ID数据类型不合法"),
					'!null'=>array("配置ID不能为空"),
					),
			//参数检测
			'format'=>array(
					'echo'=>array("配置ID的数据类型不合法"),
					),		
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_CONFIG, 'find_exists_id'), "配置ID有误，数据不存在",) 
			),
		),
	
		'page_size' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("在分页条数配置中，缺少分页条数值参数"),
					'echo'=>array("在分页条数配置中，分页条数值的类型不合法"),
					'!null'=>array("在分页条数配置中，分页条数值不能为空"),
					'match'=>array('/^[0-9]{0,}$/', "在分页条数配置中，分页条数值必须是整数"),
					),
		),
	
	
	
		"weixin_mp_access[id]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("微信公众号、H5网页微信授权配置中，缺少appid参数"),
                    'echo'=>array("微信公众号、H5网页微信授权配置中，appid的数据类型不合法"),
                    '!null'=>array("微信公众号、H5网页微信授权配置中，appid不能为空"),
                    ),
		),
	
		"weixin_mp_access[secret]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("微信公众号、H5网页微信授权配置中，缺少appsecret参数"),
                    'echo'=>array("微信公众号、H5网页微信授权配置中，appsecret的数据类型不合法"),
                    '!null'=>array("微信公众号、H5网页微信授权配置中，appsecret不能为空"),
                    ),
		),
		
		
		"weixin_applet_access[id]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("微信小程序配置中，缺少appid参数"),
                    'echo'=>array("微信小程序配置中，appid的数据类型不合法"),
                    '!null'=>array("微信小程序配置中，appid不能为空"),
                    ),
		),
	
		"weixin_applet_access[secret]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("微信小程序配置中，缺少appsecret参数"),
                    'echo'=>array("微信小程序配置中，appsecret的数据类型不合法"),
                    '!null'=>array("微信小程序配置中，appsecret不能为空"),
                    ),
		),
		
		
		
		"weixin_app_access[id]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("微信APP应用配置中，缺少appid参数"),
                    'echo'=>array("微信APP应用配置中，appid的数据类型不合法"),
                    '!null'=>array("微信APP应用配置中，appid不能为空"),
                    ),
		),
	
		"weixin_app_access[secret]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("微信APP应用配置中，缺少appsecret参数"),
                    'echo'=>array("微信APP应用配置中，appsecret的数据类型不合法"),
                    '!null'=>array("微信APP应用配置中，appsecret不能为空"),
                    ),
		),
	
	
	
	
		"app_android_version[name]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("安卓APP版本配置中，缺少版本名称参数"),
                    'echo'=>array("安卓APP版本配置中，版本名称的数据类型不合法"),
                    '!null'=>array("安卓APP版本配置中，版本名称不能为空"),
                    ),
		),
	
		"app_android_version[info]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("安卓APP版本配置中，缺少版本描述参数"),
                    'echo'=>array("安卓APP版本配置中，版本描述的数据类型不合法"),
                    '!null'=>array("安卓APP版本配置中，版本描述不能为空"),
                    ),
		),
		
		"app_android_version[number]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("安卓APP版本配置中，缺少版本号参数"),
                    'echo'=>array("安卓APP版本配置中，版本号的数据类型不合法"),
                    '!null'=>array("安卓APP版本配置中，版本号不能为空"),
                    ),
		),
	
		"app_android_version[download]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("安卓APP版本配置中，缺少下载地址参数"),
                    'echo'=>array("安卓APP版本配置中，下载地址的数据类型不合法"),
                    '!null'=>array("安卓APP版本配置中，下载地址不能为空"),
                    ),
		),
		
		"app_android_version[required]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("安卓APP版本配置中，缺少强制更新状态值参数"),
                    'echo'=>array("安卓APP版本配置中，强制更新状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '安卓APP版本配置中，强制更新状态值必须是0或1'),
                    ),
		),
		
		
		
	
	
		"rmb_withdraw_merchant_money[min_merchant_money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("商家钱包提现配置中，缺少最小提现金额参数"),
                    'echo'=>array("商家钱包提现配置中，最小提现金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "商家钱包提现配置中，最小提现金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "商家钱包提现配置中，最小提现金额不能小于0"),
                    ),
		),
		"rmb_withdraw_merchant_money[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("商家钱包提现配置中，缺少状态值参数"),
                    'echo'=>array("商家钱包提现配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '商家钱包提现配置中，状态值必须是0或1'),
                    ),
        ),
	
	
	
	
		"recommend_reward_user_money[money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户推荐奖励钱包配置中，缺少固定金额参数"),
                    'echo'=>array("用户推荐奖励钱包配置中，固定金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户推荐奖励钱包配置中，固定金额不合理。注意必须是整数"),
                    ),
		),
		"recommend_reward_user_money[money_min]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户推荐奖励钱包配置中，缺少最小随机金额参数"),
                    'echo'=>array("用户推荐奖励钱包配置中，最小随机金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户推荐奖励钱包配置中，最小随机金额不合理。注意必须是整数"),
                    ),
		),
		"recommend_reward_user_money[money_max]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户推荐奖励钱包配置中，缺少最大随机金额参数"),
                    'echo'=>array("用户推荐奖励钱包配置中，最大随机金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户推荐奖励钱包配置中，最大随机金额不合理。注意必须是整数"),
                    ),
		),
		"recommend_reward_user_money[random]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户推荐奖励钱包配置中，缺少随机状态值参数"),
                    'echo'=>array("用户推荐奖励钱包配置中，随机状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户推荐奖励钱包配置中，随机状态值必须是0或1'),
                    ),
        ),
		"recommend_reward_user_money[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户推荐奖励钱包配置中，缺少状态值参数"),
                    'echo'=>array("用户推荐奖励钱包配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户推荐奖励钱包配置中，状态值必须是0或1'),
                    ),
        ),
		
	
	
	
	
	
		"house_product_add_reward_user_money[user_money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户发布楼盘产品审核通过奖励钱包配置中，缺少固定金额参数"),
                    'echo'=>array("用户发布楼盘产品审核通过奖励钱包配置中，固定金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户发布楼盘产品审核通过奖励钱包配置中，固定金额不合理。注意必须是整数"),
                    ),
		),
		"house_product_add_reward_user_money[random_min_user_money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户发布楼盘产品审核通过奖励钱包配置中，缺少最小随机金额参数"),
                    'echo'=>array("用户发布楼盘产品审核通过奖励钱包配置中，最小随机金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户发布楼盘产品审核通过奖励钱包配置中，最小随机金额不合理。注意必须是整数"),
                    ),
		),
		"house_product_add_reward_user_money[random_max_user_money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户发布楼盘产品审核通过奖励钱包配置中，缺少最大随机金额参数"),
                    'echo'=>array("用户发布楼盘产品审核通过奖励钱包配置中，最大随机金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户发布楼盘产品审核通过奖励钱包配置中，最大随机金额不合理。注意必须是整数"),
                    ),
		),
		"house_product_add_reward_user_money[random_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户发布楼盘产品审核通过奖励钱包配置中，缺少随机状态值参数"),
                    'echo'=>array("用户发布楼盘产品审核通过奖励钱包配置中，随机状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户发布楼盘产品审核通过奖励钱包配置中，随机状态值必须是0或1'),
                    ),
        ),
		"house_product_add_reward_user_money[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户发布楼盘产品审核通过奖励钱包配置中，缺少状态值参数"),
                    'echo'=>array("用户发布楼盘产品审核通过奖励钱包配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户发布楼盘产品审核通过奖励钱包配置中，状态值必须是0或1'),
                    ),
        ),
		
	
	
	
	
	
	
		
		"rmb_withdraw_user_money[min_user_money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户钱包提现配置中，缺少最小提现的赠送收益金额参数"),
                    'echo'=>array("用户钱包提现配置中，最小提现的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户的赠送收益金额提现配置中，最小提现的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额提现配置中，最小提现的赠送收益金额不能小于0"),
                    ),
		),
		"rmb_withdraw_user_money[max_user_money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户钱包提现配置中，缺少最大提现的赠送收益金额参数"),
                    'echo'=>array("用户钱包提现配置中，最大提现的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{1,}$/', "用户的赠送收益金额提现配置中，最大提现的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额提现配置中，最大提现的赠送收益金额不能小于0"),
                    ),
		),
		"rmb_withdraw_user_money[ratio_service_money]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户钱包提现配置中，缺少收取的用户服务费比值参数"),
                    'echo'=>array("用户钱包提现配置中，收取的用户服务费比值的数据类型不合法"),
                    'match'=>array('/^(0\.[0-9]{1,}|0)$/', "用户钱包提现配置中，收取的用户服务费比值不合理。注意必须是小于1的小数或者为0"),
                    ),
		),
		"rmb_withdraw_user_money[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户钱包提现配置中，缺少运算法则参数"),
                    'echo'=>array("用户钱包提现配置中，运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "用户钱包提现配置中，运算法则异常"),
                    ),
        ),
        "rmb_withdraw_user_money[pay_password_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户钱包提现配置中，缺少支付密码状态值参数"),
                    'echo'=>array("用户钱包提现配置中，支付密码状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户钱包提现配置中，支付密码状态值必须是0或1'),
                    ),
        ),
        "rmb_withdraw_user_money[user_identity_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户钱包提现配置中，缺少实名认证状态值参数"),
                    'echo'=>array("用户钱包提现配置中，实名认证状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户钱包提现配置中，实名认证状态值必须是0或1'),
                    ),
        ),
		"rmb_withdraw_user_money[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户钱包提现配置中，缺少状态值参数"),
                    'echo'=>array("用户钱包提现配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户钱包提现配置中，状态值必须是0或1'),
                    ),
        ),
		
	
	
	
	
	
	
		"rmb_withdraw_user_money_earning[min_user_money_earning]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额提现配置中，缺少最小提现的赠送收益金额参数"),
                    'echo'=>array("用户的赠送收益金额提现配置中，最小提现的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户的赠送收益金额提现配置中，最小提现的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额提现配置中，最小提现的赠送收益金额不能小于0"),
                    ),
		),
		"rmb_withdraw_user_money_earning[max_user_money_earning]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额提现配置中，缺少最大提现的赠送收益金额参数"),
                    'echo'=>array("用户的赠送收益金额提现配置中，最大提现的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户的赠送收益金额提现配置中，最大提现的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额提现配置中，最大提现的赠送收益金额不能小于0"),
                    ),
		),
		"rmb_withdraw_user_money_earning[ratio_user_money_service]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额提现配置中，缺少收取的用户服务费比值参数"),
                    'echo'=>array("用户的赠送收益金额提现配置中，收取的用户服务费比值的数据类型不合法"),
                    'match'=>array('/^(0\.[0-9]{1,}|0)$/', "用户的赠送收益金额提现配置中，收取的用户服务费比值不合理。注意必须是小于1的小数或者为0"),
                    ),
		),
		"rmb_withdraw_user_money_earning[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额提现配置中，缺少运算法则参数"),
                    'echo'=>array("用户的赠送收益金额提现配置中，运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "用户的赠送收益金额提现配置中，运算法则异常"),
                    ),
        ),
        "rmb_withdraw_user_money_earning[pay_password_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额提现配置中，缺少支付密码状态值参数"),
                    'echo'=>array("用户的赠送收益金额提现配置中，支付密码状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额提现配置中，支付密码状态值必须是0或1'),
                    ),
        ),
        "rmb_withdraw_user_money_earning[user_identity_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额提现配置中，缺少实名认证状态值参数"),
                    'echo'=>array("用户的赠送收益金额提现配置中，实名认证状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额提现配置中，实名认证状态值必须是0或1'),
                    ),
        ),
		"rmb_withdraw_user_money_earning[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额提现配置中，缺少状态值参数"),
                    'echo'=>array("用户的赠送收益金额提现配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额提现配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		
		
		"user_money_earning_transfer_user_money[min_user_money_earning]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户钱包配置中，缺少最小转账的赠送收益金额参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户钱包配置中，最小转账的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户的赠送收益金额转账到用户钱包配置中，最小转账的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额转账到用户钱包配置中，最小转账的赠送收益金额不能小于0"),
                    ),
		),
		"user_money_earning_transfer_user_money[max_user_money_earning]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户钱包配置中，缺少最大转账的赠送收益金额参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户钱包配置中，最大转账的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户的赠送收益金额转账到用户钱包配置中，最大转账的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额转账到用户钱包配置中，最大转账的赠送收益金额不能小于0"),
                    ),
		),
		"user_money_earning_transfer_user_money[ratio_user_money_service]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户钱包配置中，缺少收取的用户服务费比值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户钱包配置中，收取的用户服务费比值的数据类型不合法"),
                    'match'=>array('/^(0\.[0-9]{1,}|0)$/', "用户的赠送收益金额转账到用户钱包配置中，收取的用户服务费比值不合理。注意必须是小于1的小数或者为0"),
                    ),
		),
		"user_money_earning_transfer_user_money[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户钱包配置中，缺少运算法则参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户钱包配置中，运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "用户的赠送收益金额转账到用户钱包配置中，运算法则异常"),
                    ),
        ),
        "user_money_earning_transfer_user_money[pay_password_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户钱包配置中，缺少支付密码状态值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户钱包配置中，支付密码状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额转账到用户钱包配置中，支付密码状态值必须是0或1'),
                    ),
        ),
        "user_money_earning_transfer_user_money[user_identity_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户钱包配置中，缺少实名认证状态值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户钱包配置中，实名认证状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额转账到用户钱包配置中，实名认证状态值必须是0或1'),
                    ),
        ),
		"user_money_earning_transfer_user_money[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户钱包配置中，缺少状态值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户钱包配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额转账到用户钱包配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		"user_money_earning_transfer_user_money_help[min_user_money_earning]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，缺少最小转账的赠送收益金额参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，最小转账的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户的赠送收益金额转账到用户扶贫账户配置中，最小转账的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额转账到用户扶贫账户配置中，最小转账的赠送收益金额不能小于0"),
                    ),
		),
		"user_money_earning_transfer_user_money_help[max_user_money_earning]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，缺少最大转账的赠送收益金额参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，最大转账的赠送收益金额的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户的赠送收益金额转账到用户扶贫账户配置中，最大转账的赠送收益金额必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户的赠送收益金额转账到用户扶贫账户配置中，最大转账的赠送收益金额不能小于0"),
                    ),
		),
		"user_money_earning_transfer_user_money_help[ratio_user_money_service]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，缺少收取的用户服务费比值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，收取的用户服务费比值的数据类型不合法"),
                    'match'=>array('/^(0\.[0-9]{1,}|0)$/', "用户的赠送收益金额转账到用户扶贫账户配置中，收取的用户服务费比值不合理。注意必须是小于1的小数或者为0"),
                    ),
		),
		"user_money_earning_transfer_user_money_help[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，缺少运算法则参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "用户的赠送收益金额转账到用户扶贫账户配置中，运算法则异常"),
                    ),
        ),
        "user_money_earning_transfer_user_money_help[pay_password_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，缺少支付密码状态值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，支付密码状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额转账到用户扶贫账户配置中，支付密码状态值必须是0或1'),
                    ),
        ),
        "user_money_earning_transfer_user_money_help[user_identity_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，缺少实名认证状态值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，实名认证状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额转账到用户扶贫账户配置中，实名认证状态值必须是0或1'),
                    ),
        ),
		"user_money_earning_transfer_user_money_help[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，缺少状态值参数"),
                    'echo'=>array("用户的赠送收益金额转账到用户扶贫账户配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户的赠送收益金额转账到用户扶贫账户配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		"daily_attendance_earn_user_credit[credit]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户每日签到赠送积分配置中，缺少积分参数"),
                    'echo'=>array("用户每日签到赠送积分配置中，积分的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户每日签到赠送积分配置中，积分必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户每日签到赠送积分配置中，积分不能小于0"),
                    ),
        ),
        "daily_attendance_earn_user_credit[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户每日签到赠送积分配置中，缺少状态值参数"),
                    'echo'=>array("用户每日签到赠送积分配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户每日签到赠送积分配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		
        "rmb_buy_merchant_credit[ratio_credit]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("人民币购买商家积分配置中，缺少积分比值参数"),
                    'echo'=>array("人民币购买商家积分配置中，积分比值的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "人民币购买商家积分配置中，积分比值的必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "人民币购买商家积分配置中，积分比值不能小于0"),
                    ),
        ),
        "rmb_buy_merchant_credit[ratio_rmb]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("人民币购买商家积分配置中，缺少人名币比值参数"),
                    'echo'=>array("人民币购买商家积分配置中，人名币比值的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "人民币购买商家积分配置中，人名币比值的必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "人民币购买商家积分配置中，人名币比值不能小于0"),
                    ),
        ),
        "rmb_buy_merchant_credit[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("人民币购买商家积分配置中，缺少运算法则参数"),
                    'echo'=>array("人民币购买商家积分配置中，运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "人民币购买商家积分配置中，运算法则输入有误"),
                    ),
        ),
        "rmb_buy_merchant_credit[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("人民币购买商家积分配置中，缺少状态值参数"),
                    'echo'=>array("人民币购买商家积分配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '人民币购买商家积分配置中，状态值必须是0或1'),
                    ),
        ),
		
        
		
		
		
		"rmb_consume_user_credit[ratio_credit]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费赠送积分配置中，缺少积分比值参数"),
                    'echo'=>array("用户消费赠送积分配置中，积分比值的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户消费赠送积分配置中，积分比值的必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户消费赠送积分配置中，积分比值不能小于0"),
                    ),
        ),
        "rmb_consume_user_credit[ratio_rmb]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费赠送积分配置中，缺少人名币比值参数"),
                    'echo'=>array("用户消费赠送积分配置中，人名币比值的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "用户消费赠送积分配置中，人名币比值的必须是整数"),
                    '!match'=>array('/^[0]{0,}$/', "用户消费赠送积分配置中，人名币比值不能小于0"),
                    ),
        ),
        "rmb_consume_user_credit[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费赠送积分配置中，缺少运算法则参数"),
                    'echo'=>array("用户消费赠送积分配置中，运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "用户消费赠送积分配置中，运算法则输入有误"),
                    ),
        ),
		"rmb_consume_user_credit[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费赠送积分配置中，缺少状态值参数"),
                    'echo'=>array("用户消费赠送积分配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户消费赠送积分配置中，状态值必须是0或1'),
                    ),
        ),
        
		
		
		
		"parent_recommend_user_credit[ratio_user_credit]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，缺少消费用户推荐人平台赠送积分比值参数"),
                    'echo'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，消费用户推荐人平台赠送积分比值的数据类型不合法"),
                    'match'=>array('/^([\d]+)(\.[\d]+)?$/', "消费用户推荐人与商家用户推荐人的平台积分奖励配置中，消费用户推荐人平台赠送积分比值有误。注意必须是整数或小数"),
                    ),
        ),
        "parent_recommend_user_credit[ratio_merchant_user_credit]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，缺少商家用户推荐人平台赠送积分比值参数"),
                    'echo'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，商家用户推荐人平台赠送积分比值的数据类型不合法"),
                    'match'=>array('/^([\d]+)(\.[\d]+)?$/', "消费用户推荐人与商家用户推荐人的平台积分奖励配置中，商家用户推荐人平台赠送积分比值有误。注意必须是整数或小数"),
                    ),
        ),
        "parent_recommend_user_credit[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，缺少运算法则参数"),
                    'echo'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "消费用户推荐人与商家用户推荐人的平台积分奖励配置中，运算法则输入有误"),
                    ),
        ),
		"parent_recommend_user_credit[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，缺少状态值参数"),
                    'echo'=>array("消费用户推荐人与商家用户推荐人的平台积分奖励配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '消费用户推荐人与商家用户推荐人的平台积分奖励配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		"user_credit_conversion_user_money_share[basic_conversion_ratio]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户积分兑换为共享金配置中，缺少转换率基数参数"),
                    'echo'=>array("用户积分兑换为共享金配置中，转换率基数的数据类型不合法"),
                    'match'=>array('/^([\d]+)(\.[\d]+)?$/', "用户积分兑换为共享金配置中，转换率基数有误。注意必须是整数或小数"),
                    ),
        ),
		"user_credit_conversion_user_money_share[min_conversion_ratio]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户积分兑换为共享金配置中，缺少最小转换率参数"),
                    'echo'=>array("用户积分兑换为共享金配置中，最小转换率的数据类型不合法"),
                    'match'=>array('/^([\d]+)(\.[\d]+)?$/', "用户积分兑换为共享金配置中，最小转换率有误。注意必须是整数或小数"),
                    ),
        ),
		"user_credit_conversion_user_money_share[max_conversion_ratio]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户积分兑换为共享金配置中，缺少最大转换率参数"),
                    'echo'=>array("用户积分兑换为共享金配置中，最大转换率的数据类型不合法"),
                    'match'=>array('/^([\d]+)(\.[\d]+)?$/', "用户积分兑换为共享金配置中，最大转换率有误。注意必须是整数或小数"),
                    ),
        ),
		"user_credit_conversion_user_money_share[precision_conversion_ratio]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户积分兑换为共享金配置中，缺少转换率精度参数"),
                    'echo'=>array("用户积分兑换为共享金配置中，转换率精度的数据类型不合法"),
                    'match'=>array('/^[\d]+$/', "用户积分兑换为共享金配置中，转换率精度有误。注意必须是整数"),
                    ),
        ),
        "user_credit_conversion_user_money_share[multiple_user_credit]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户积分兑换为共享金配置中，缺少积分倍数参数"),
                    'echo'=>array("用户积分兑换为共享金配置中，积分倍数的数据类型不合法"),
                    'match'=>array('/^[\d]+$/', "用户积分兑换为共享金配置中，积分倍数格式不合法"),
                    ),
        ),
		"user_credit_conversion_user_money_share[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户积分兑换为共享金配置中，缺少状态值参数"),
                    'echo'=>array("用户积分兑换为共享金配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户积分兑换为共享金配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		"user_identity[expire_time]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户实名认证配置中，缺少认证时间有效期参数"),
                    'echo'=>array("用户实名认证配置中，认证时间有效期的数据类型不合法"),
                    'match'=>array('/^[\d]+$/', "用户实名认证配置中，认证时间有效期的格式不合法"),
                    ),
        ),
		"user_identity[expire_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户实名认证配置中，缺少有效期状态值参数"),
                    'echo'=>array("用户实名认证配置中，有效期状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户实名认证配置中，有效期状态值必须是0或1'),
                    ),
        ),
		"user_identity[auto_state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户实名认证配置中，缺少自动认证状态值参数"),
                    'echo'=>array("用户实名认证配置中，自动认证状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户实名认证配置中，自动认证状态值必须是0或1'),
                    ),
        ),
		
		
		"user_money_share_conversion_annuity_earning_help[multiple_user_money_share]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，缺少消费共享金倍数参数"),
                    'echo'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金倍数的数据类型不合法"),
                    'match'=>array('/^[\d]+$/', "用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金倍数有误"),
                    ),
        ),
		"user_money_share_conversion_annuity_earning_help[ratio_user_money_help]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，缺少消费共享金转换到扶贫资金账户比例参数"),
                    'echo'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金转换到扶贫资金账户比例的数据类型不合法"),
                    'match'=>array('/^0(\.[\d]+)?$/', "用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金转换到扶贫资金账户比例有误。注意必须是小于1的小数"),
                    ),
        ),
		"user_money_share_conversion_annuity_earning_help[ratio_user_money_annuity]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，缺少消费共享金转换到养老金比例参数"),
                    'echo'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金转换到养老金比例的数据类型不合法"),
                    'match'=>array('/^0(\.[\d]+)?$/', "用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金转换到养老金比例有误。注意必须是小于1的小数"),
                    ),
        ),
		"user_money_share_conversion_annuity_earning_help[ratio_user_money_earning]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，缺少消费共享金转换到赠送收益账户比例参数"),
                    'echo'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金转换到赠送收益账户比例的数据类型不合法"),
                    'match'=>array('/^0(\.[\d]+)?$/', "用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，消费共享金转换到赠送收益账户比例有误。注意必须是小于1的小数"),
                    ),
        ),
		"user_money_share_conversion_annuity_earning_help[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，缺少状态值参数"),
                    'echo'=>array("用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		
		
		"alipay_access[id]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝支付配置中，缺少appId参数"),
                    'echo'=>array("支付宝支付配置中，appId的数据类型不合法"),
                    ),
        ),
        "alipay_access[rsa_private_key]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝支付配置中，缺少开发者私钥 rsaPrivateKey参数"),
                    'echo'=>array("支付宝支付配置中，开发者私钥 rsaPrivateKey的数据类型不合法"),
                    ),
        ),
		"alipay_access[alipayrsa_public_key]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝支付配置中，缺少支付宝公钥 alipayrsaPublicKey参数"),
                    'echo'=>array("支付宝支付配置中，支付宝公钥 alipayrsaPublicKey的数据类型不合法"),
                    ),
        ),
		"alipay_access[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝支付配置中，缺少状态值参数"),
                    'echo'=>array("支付宝支付配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '支付宝支付配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		"alipay_withdraw_access[id]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝提现配置中，缺少appId参数"),
                    'echo'=>array("支付宝提现配置中，appId的数据类型不合法"),
                    ),
        ),
        "alipay_withdraw_access[rsa_private_key]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝提现配置中，缺少开发者私钥 rsaPrivateKey参数"),
                    'echo'=>array("支付宝提现配置中，开发者私钥 rsaPrivateKey的数据类型不合法"),
                    ),
        ),
		"alipay_withdraw_access[alipayrsa_public_key]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝提现配置中，缺少支付宝公钥 alipayrsaPublicKey参数"),
                    'echo'=>array("支付宝提现配置中，支付宝公钥 alipayrsaPublicKey的数据类型不合法"),
                    ),
        ),
		"alipay_withdraw_access[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("支付宝提现配置中，缺少状态值参数"),
                    'echo'=>array("支付宝提现配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '支付宝提现配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		
		
		
		"weixin_pay_access[mch_id]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("微信支付商户配置中，缺少商户号 mch_id参数"),
                    'echo'=>array("微信支付商户配置中，商户号 mch_id的数据类型不合法"),
                    ),
        ),
        "weixin_pay_access[pay_key]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("微信支付商户配置中，缺少支付密匙 pay_key参数"),
                    'echo'=>array("微信支付商户配置中，支付密匙 pay_key的数据类型不合法"),
                    ),
        ),
		"weixin_pay_access[spbill_create_ip]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("微信支付商户配置中，缺少终端IP spbill_create_ip参数"),
                    'echo'=>array("微信支付商户配置中，终端IP spbill_create_ip的数据类型不合法"),
                    ),
        ),
        "weixin_pay_access[ssl_cert]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("微信支付商户配置中，缺少证书cert参数"),
                    'echo'=>array("微信支付商户配置中，证书cert的数据类型不合法"),
                    ),
        ),
		"weixin_pay_access[ssl_key]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("微信支付商户配置中，缺少证书key参数"),
                    'echo'=>array("微信支付商户配置中，证书key的数据类型不合法"),
                    ),
        ),
		"weixin_pay_access[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("微信支付商户配置中，缺少状态值参数"),
                    'echo'=>array("微信支付商户配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '微信支付商户配置中，状态值必须是0或1'),
                    ),
        ),
		
		
		"shop_order_user_comment[check]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("购物商城订单评论配置中，缺少评论审核状态值参数"),
                    'echo'=>array("购物商城订单评论配置中，评论审核状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '购物商城订单评论配置中，评论审核状态值必须是0或1'),
                    ),
        ),
		"shop_order_user_comment[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("购物商城订单评论配置中，缺少状态值参数"),
                    'echo'=>array("购物商城订单评论配置中，状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '购物商城订单评论配置中，状态值必须是0或1'),
                    ),
        ),
		
		
	);
	
	
	
	
	
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$config_id
	 * @return	array
	 */
	public function find($config_id = ''){
		if( empty($config_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config_id), function($config_id){
			return db(parent::DB_APPLICATION_ID)
			->table('config')
			->where(array('config_id=[+]', (string)$config_id))
			->find();
		});
		
	}	
	
	
	
	
		
	/**
	 * 插入新数据
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	bool
	 */
	public function insert($data = array(), $call_data = array()){
		if( empty($data) && empty($call_data) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('config')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
	
	
	
	/**
	 * 更新数据
	 * 
	 * @param	array		$where
	 * @param	array		$data
	 * @param	array		$call_data
	 * @return	bool
	 */
	public function update($where = array(), $data = array(), $call_data = array()){
		if( empty($where) || (empty($data) && empty($call_data)) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('config')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
	
	/**
	 * 更新数据
	 * 
	 * @param	string		$config_id
	 * @param	string		$config_value
	 * @return	bool
	 */
	public function update_value($config_id = '', $config_value = ''){
		if( !isset($config_id) || !isset($config_value) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('config')
		->where(array('config_id=[+]', (string)$config_id))
		->update(array(
			'config_value' => $config_value,
			'config_update_time' => time()
		));
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
	
	
		
	/**
	 * 获取多条数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 *  'select' => array(),//查询的字段，可以是数组和字符串
	 * );
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function select($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			return db(parent::DB_APPLICATION_ID)
			->table('config')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
	}		
	
		
		
		
		
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$config_id
	 * @return	bool
	 */
	public function find_exists_id($config_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config_id), function($config_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('config')
			->where(array('config_id=[+]', $config_id))
			->find('config_id');
		});
	}
	
			
		
	
	
		
	/**
	 * 处理配置数据
	 * 
	 * $value['config_type']
	 * 值类型：为空或者"string"表示字符串，"integer"和"state"表示整数，"float"表示浮点数，"json"、"serialize"解码。自动转换数据。
	 *  $data['config_id']		可以是数组，也可以是字符串。如果为空，那么则获取所有的数据
	 * 
	 * @param	string		$data			配置id如果为空，则是获取所有的配置
	 * @param	bool		$is_value		默认false，返回值包括名称、描述。为true直接返回值
	 * @return	array
	 */
	public function data( $config_data = array(), $is_value = false){
		$data = array();
		if(!isset($config_data['config_id']) ||
		!isset($config_data['config_value']) ||
		!isset($config_data['config_type']) ){
			return false;
		}
		
		$data = $config_data;
		$data['config_value'] = NULL;
		
		$config_data['config_type'] = trim($config_data['config_type']);
		if($config_data['config_type'] == 'float'){
			$data['config_value'] = (float)$config_data['config_value'];
		}else
		if(in_array($config_data['config_type'], array('integer', 'state'))){
			$data['config_value'] = (integer)$config_data['config_value'];
		}else
		if($config_data['config_type'] == 'json'){
			$data['config_value'] = cmd(array($config_data['config_value']), 'json decode');
		}else
		if($config_data['config_type'] == 'serialize'){
			$data['config_value'] = unserialize($config_data['config_value']);
		}else{
			$data['config_value'] = $config_data['config_value'];
		}
		
		if( empty($is_value) ){
			return $data;//返回值包括名称、描述
		}else{
			return $data['config_value']; //只返回值
		}
		
	}
	


		
	
	
	
	
	
	
	
	
	
	
	
	
}
?>