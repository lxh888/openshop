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



namespace eapie\source\request\merchant;
use eapie\main;
use eapie\error;
class admin_tally extends \eapie\source\request\merchant {
	
	
	
	
	/**
     * 商家线下记账数据列表
     *
     * MERCHANTADMINTALLYLIST
     * {"class":"merchant/admin_tally","method":"api_list"}
	 * 
     * @param  array	$data 	请求参数
     * @return array
     */
    public function api_list($data = array()) {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_READ);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );

        //排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'merchant_id_desc' => array('merchant_id', true),
            'merchant_id_asc' => array('merchant_id', false),
            'merchant_name_desc' => array('merchant_name', true),
            'merchant_name_asc' => array('merchant_name', false),
            
			'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            
			'client_phone_desc' => array('merchant_tally_client_phone', true),
            'client_phone_asc' => array('merchant_tally_client_phone', false),
			
            'insert_time_desc' => array('merchant_tally_insert_time', true),
            'insert_time_asc' => array('merchant_tally_insert_time', false),
            'update_time_desc' => array('merchant_tally_update_time', true),
            'update_time_asc' => array('merchant_tally_update_time', false),
        ));
		
        //避免排序重复
        $config["orderby"][] = array('merchant_tally_id', false);

        //搜索
        if (!empty($data['search'])) {
        	if (isset($data['search']['client_phone']) && is_string($data['search']['client_phone'])) {
                $config['where'][] = array('[and] mt.merchant_tally_client_phone=[+]', $data['search']['client_phone']);
            }
			
        	if (isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id'])) {
                $config['where'][] = array('[and] m.merchant_id=[+]', $data['search']['merchant_id']);
            }
        	if (isset($data['search']['merchant_name']) && is_string($data['search']['merchant_name'])) {
                $config['where'][] = array('[and] m.merchant_name LIKE "%[-]%"', $data['search']['merchant_name']);
            }
			
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] mt.user_id=[+]', $data['search']['user_id']);
            }
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] mt.user_id=[+]', $user_id);
            }
			
        }

        //查询数据
        return object(parent::TABLE_MERCHANT_TALLY)->select_page($config);
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>