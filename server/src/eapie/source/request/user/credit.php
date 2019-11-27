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



namespace eapie\source\request\user;

use eapie\main;
use eapie\error;

//用户积分
class credit extends \eapie\source\request\user
{

	/**
     * 查询总额
     *
     * api: [旧] USERSELFCREDITTOTAL
     * api: USERCREDITSELFTOTAL
     * req: null
     *
     * @return int
     */
    public function api_self_total()
    {
    	object(parent::REQUEST_USER)->check();
        $credit = object(parent::TABLE_USER_CREDIT)->find_now_data($_SESSION['user_id']);
        return empty($credit['user_credit_value']) ? 0 : $credit['user_credit_value'];
    }




	/**
	 * 获取当前所有用户的及时消费积分之和
	 * 
	 * USERCREDITTOTAL
	 * {"class":"user/credit","method":"api_total"}
	 * 
	 * @param	void
	 * @return	int
	 */
	public function api_total(){
		//检查是否已初始化
        object(parent::REQUEST_SESSION)->check();
		return object(parent::TABLE_USER_CREDIT)->find_now_all_sum();
	}






}