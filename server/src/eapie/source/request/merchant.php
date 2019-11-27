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
class merchant extends main 
{

    //权限码
    const AUTHORITY_MERCHANT_READ   = 'merchant_read';
    const AUTHORITY_MERCHANT_ADD    = 'merchant_add';
    const AUTHORITY_MERCHANT_EDIT   = 'merchant_edit';
    const AUTHORITY_MERCHANT_REMOVE = 'merchant_remove';

    const AUTHORITY_MERCHANT_USER_READ   = 'merchant_user_read';
    const AUTHORITY_MERCHANT_USER_ADD    = 'merchant_user_add';
    const AUTHORITY_MERCHANT_USER_EDIT   = 'merchant_user_edit';
    const AUTHORITY_MERCHANT_USER_REMOVE = 'merchant_user_remove';


	const AUTHORITY_IMAGE_UPLOAD 	= "merchant_image_upload";//商家展示图片上传
	const AUTHORITY_IMAGE_REMOVE 	= "merchant_image_remove";//商家展示图片删除
	const AUTHORITY_IMAGE_EDIT 		= "merchant_image_edit";//商家展示图片编辑


	const AUTHORITY_CREDIT_READ   = 'merchant_credit_read';
    const AUTHORITY_CREDIT_EDIT   = 'merchant_credit_edit';
	
	const AUTHORITY_MONEY_READ   = 'merchant_money_read';
    const AUTHORITY_MONEY_EDIT   = 'merchant_money_edit';

	const AUTHORITY_WITHDRAW_READ   	= 'merchant_withdraw_read';
	const AUTHORITY_WITHDRAW_STATE   	= 'merchant_withdraw_state';
	
	const AUTHORITY_TALLY_READ   	= 'merchant_tally_read';//商家记账

	const AUTHORITY_CONFIG_READ = 'merchant_config_read';
    const AUTHORITY_CONFIG_EDIT = 'merchant_config_edit';

    
    const AUTHORITY_CASHIER_LIST = 'merchant_cashier_list';// 员工列表
	const AUTHORITY_CASHIER_STATE = 'merchant_cashier_state';// 审核员工
	const AUTHORITY_CASHIER_REMOVE = 'merchant_cashier_remove';// 员工删除
	
	
	
	


    /**
     * 检测当前用户是否合法商家
     * @param  array  $input [HTTP请求参数]
     * @return [str] [商家ID]
     */
    protected function check_role($input = array())
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //是否指定商家ID
        $mch_id = null;
        if (isset($input['merchant_id'])) {
            object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
            $mch_id = $input['merchant_id'];
        } else {
            $mch_ids = object(parent::TABLE_MERCHANT_USER)->get_mch_ids($user_id);
            if (empty($mch_ids))
                throw new error('不是商家用户');
            else
                $mch_id = $mch_ids[0];
        }

        //判断权限
        if (object(parent::TABLE_MERCHANT_USER)->check_exist($user_id, $mch_id, true))
            return $mch_id;
        else
            throw new error('权限不足');
    }




	/**
     * 检查当前登录用户的商家权限
     * 是否已登陆、当前用户是否是该商家用户
	 * 主要用于商家后台
     * 
	 * @param   bool    $return_bool    是否返回布尔值
     * @param   bool    $return_bool    是否返回布尔值
     * @return  mixed
     */
    public function check($merchant_id, $return_bool = false){
    	if( empty($return_bool) ){
            //检查是否已初始化
            object(parent::REQUEST_USER)->check();
        }else{
            $bool = object(parent::REQUEST_USER)->check(true);
            if( empty($bool) ){
                return false;
            }
        }
		
		//查询商家信息
        $merchant = object(parent::TABLE_MERCHANT)->find($merchant_id);
        if( empty($merchant) ){
        	if( empty($return_bool) ){
        		throw new error('商家不存在');
			}else{
				return false;
			}
        }
        if( $merchant['merchant_state'] != 1 ){
        	if( empty($return_bool) ){
        		throw new error('商家未认证');
			}else{
				return false;
			}
        }
		
		//查询商家用户信息
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where(array(
            array('merchant_id = [+]', $merchant_id),
            array('[and] user_id = [+]', $_SESSION['user_id'])
        ));
		if( empty($merchant_user) ){
			if( empty($return_bool) ){
        		throw new error('不是商家用户');
			}else{
				return false;
			}
		}
        if( $merchant_user['merchant_user_state'] != 1 ){
        	if( empty($return_bool) ){
        		throw new error('商家用户未认证');
			}else{
				return false;
			}
        }
		
		$_SESSION['merchant'] = $merchant;
		$_SESSION['merchant'] = array_merge($_SESSION['merchant'], $merchant_user);
		
		return true;
    }
	
	

    /**
     * 检测商家用户状态
     * 
     * @param  string $merchant_id [商家ID，可选]
     * @return string
     */
    protected function _check_merchant_user_state_($merchant_id = '')
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //是否指定商家
        if (!empty($merchant_id)) {
            $mch_ids = object(parent::TABLE_MERCHANT_USER)->get_mch_ids($user_id);
            if (empty($mch_ids)) {
                throw new error('不是商家用户');
            } else {
                $merchant_id = $mch_ids[0];
            }
        }

        //查询商家信息
        $merchant = object(parent::TABLE_MERCHANT)->find($merchant_id);
        if (empty($merchant))
            throw new error('商家不存在');
        if ($merchant['merchant_state'] != 1)
            throw new error('商家未认证');

        //查询商家用户信息
        $merchant_user = object(parent::TABLE_MERCHANT_USER)->find_where(array(
            array('merchant_id = [+]', $merchant_id),
            array('[and] user_id = [+]', $_SESSION['user_id'])
        ));
        if (empty($merchant_user))
            throw new error('不是商家用户');
        if ($merchant_user['merchant_user_state'] != 1)
            throw new error('商家用户未认证');

        return $merchant_id;
    }


}