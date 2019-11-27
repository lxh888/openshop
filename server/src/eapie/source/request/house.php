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



namespace eapie\source\request;
use eapie\main;
use eapie\error;
class house extends main {
	
	
	//楼盘
	
	
    //权限码——楼盘项目
    const AUTHORITY_PRODUCT_ADD       = 'house_product_add';
    const AUTHORITY_PRODUCT_REMOVE    = 'house_product_remove';
    const AUTHORITY_PRODUCT_EDIT      = 'house_product_edit';
    const AUTHORITY_PRODUCT_READ      = 'house_product_read';
	const AUTHORITY_PRODUCT_DETAILS   = 'house_product_details';
	
	const AUTHORITY_PRODUCT_STATE     = 'house_product_state';
	
	const AUTHORITY_PRODUCT_TRASH = "house_product_trash";//逻辑删除，丢进回收站
	const AUTHORITY_PRODUCT_TRASH_READ = "house_product_trash_read";//回收站读取权限
	const AUTHORITY_PRODUCT_TRASH_EDIT = "house_product_trash_edit";//回收站编辑
	const AUTHORITY_PRODUCT_TRASH_RESTORE = "house_product_trash_restore";//回收站还原
	
	
    const AUTHORITY_PRODUCT_TOP_EDIT  = 'house_product_top_edit';
    const AUTHORITY_PRODUCT_TOP_READ  = 'house_product_top_read';
	
	
	
	const AUTHORITY_CONFIG_READ 	= 'house_config_read';
	const AUTHORITY_CONFIG_EDIT 	= 'house_config_edit';


    /**
     * 检测当前用户是否合法经纪人
     * @param array $input 请求参数
     * @return void
     */
    protected function _check_agent_state_($input = array())
    {
        //是否登录
        object(parent::REQUEST_USER)->check();

        // 查询用户信息
        $user = object(parent::TABLE_USER)->find($_SESSION['user_id']);
        if (empty($user['user_name']) || empty($user['user_company']))
            throw new error('请完善个人信息');

        // 查询实名认证信息
        // $data = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        // if (empty($data))
        //     throw new error('未实名认证');

        // if ($data['user_identity_state'] == 0)
        //     throw new error('实名认证审核失败');
            
        // if ($data['user_identity_state'] == 0)
        //     throw new error('实名认证审核中');

        // 查询条件
        // $call_where = array();
        // $call_where[] = array('merchant_user_state=1');

        // //是否指定商家
        // if (isset($input['merchant_id']) && is_string($input['merchant_id'])) {
        //     $call_where[] = array('[and] merchant_id=[+]', $input['merchant_id']);
        // }
        // $call_where[] = array('[and] user_id=[+]', $_SESSION['user_id']);

        // $data = object(parent::TABLE_MERCHANT_USER)->find_where($call_where);
        // if ($data) {
        //     $_SESSION['user']['merchant_id'] = $data['merchant_id'];
        //     $_SESSION['user']['merchant_user_id'] = $data['merchant_user_id'];
        // } else {
        //     throw new error('未认证');
        // }
    }


}