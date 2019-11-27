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
class admin_identity extends \eapie\source\request\user {



	/**
     * 清理所有用户有效期外的认证状态
     * 
	 * USERADMINIDENTITYSTATECLEAR
     * {"class":"user/admin_identity","method":"api_state_clear"}
	 * 
     * @param   array   $input
     * @return  string  [用户ID]
     */
    public function api_state_clear($input = array()){
		object(parent::REQUEST_ADMIN)->check();
		return object(parent::TABLE_USER_IDENTITY)->update_state_clear();
    }
	



		
	/**
     * 编辑用户实名认证的权限检测
     *	
     * USERADMINIDENTITYEDITCHECK
     * {"class":"user/admin_identity","method":"api_edit_check"}
     *
     * @param  [arr] $input [请求参数]
     * @return [arr]
     */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_EDIT);
		return true;
	}
	
	



    /**
     * 编辑
     * 
     * $input = array (
     * 	"user_identity_state" [int] [可选] [状态。0未通过审核，1通过审核，2待审核]
     * )
	 * 
	 * USERADMINIDENTITYEDIT
     * {"class":"user/admin_identity","method":"api_list"}
	 * 
     * @param   array   $input
     * @return  string  [用户ID]
     */
    public function api_edit($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_IDENTITY_EDIT);
		
        //数据检测
        object(parent::ERROR)->check($input, 'user_id', parent::TABLE_USER, array('args'));
        object(parent::ERROR)->check($input, 'user_identity_state', parent::TABLE_USER_IDENTITY, array('args'));
		
        //查询原始数据
        $original = object(parent::TABLE_USER_IDENTITY)->find($input['user_id']);
        if (empty($original)) throw new error('用户ID有误，数据不存在');
		
        //白名单
        $whitelist = array(
            'user_identity_state',
        );
        $update_data = cmd(array($input, $whitelist), 'arr whitelist');
		
        //过滤不需要更新的数据
        foreach ($update_data as $k => $v) {
            if (isset($original[$k]) && $original[$k] == $v){
            	unset($update_data[$k]);
            }
        }
		
        if (empty($update_data))
        throw new error('没有需要更新的数据');

		if( isset($update_data['user_identity_state']) && 
		!in_array($original['user_identity_state'], array("0", "1", "2")) ){
			throw new error('该数据正在编辑中，无法审核状态');
		}
		
        //更新时间
        $update_data['user_identity_update_time'] = time();
        if (object(parent::TABLE_USER_IDENTITY)->update(array(array('user_id=[+]', $input['user_id'])), $update_data)) {
            //插入操作日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
            return $input['user_id'];
        } else {
            throw new error('操作失败');
        }
    }







    /**
     * 获取数据列表
     *
     * USERADMINIDENTITYLIST
     * {"class":"user/admin_identity","method":"api_list"}
	 * 
	 * @param	array	$data
     * @return 	array
     */
    public function api_list($data = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_IDENTITY_READ);
		
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );
        
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'user_parent_id_desc' => array('user_parent_id', true),
            'user_parent_id_asc' => array('user_parent_id', false),
            'user_nickname_desc' => array('user_nickname', true),
            'user_nickname_asc' => array('user_nickname', false),
            'user_phone_verify_list_desc' => array('user_phone_verify_list', true),
            'user_phone_verify_list_asc' => array('user_phone_verify_list', false),
			
			'real_name_desc' => array('user_identity_real_name', true),
            'real_name_asc' => array('user_identity_real_name', false),
			'card_number_desc' => array('user_identity_card_number', true),
            'card_number_asc' => array('user_identity_card_number', false),
            'card_address_desc' => array('user_identity_card_address', true),
            'card_address_asc' => array('user_identity_card_address', false),
            
            'insert_time_desc' => array('user_identity_insert_time', true),
            'insert_time_asc' => array('user_identity_insert_time', false),
            'update_time_desc' => array('user_identity_update_time', true),
            'update_time_asc' => array('user_identity_update_time', false),
        ));
        //避免排序重复
        $config['orderby'][] = array('user_id', false);

        if (!empty($data['search'])) {
        	if( isset($data['search']['user_identity_state']) && 
			(is_string($data['search']['user_identity_state']) || is_numeric($data['search']['user_identity_state'])) &&
			in_array($data['search']['user_identity_state'], array("0", "1", "2", "3")) ){
				$config["where"][] = array('[and] ui.user_identity_state=[+]', $data['search']['user_identity_state']);
				}
			
			if (isset($data['search']['card_number']) && is_string($data['search']['card_number'])) {
                $config['where'][] = array('[and] ui.user_identity_card_number=[+]', $data['search']['card_number']);
            }
			if (isset($data['search']['card_address']) && is_string($data['search']['card_address'])) {
                $config['where'][] = array('[and] ui.user_identity_card_address LIKE "%[-]%"', $data['search']['card_address']);
            }
			if (isset($data['search']['real_name']) && is_string($data['search']['real_name'])) {
                $config['where'][] = array('[and] ui.user_identity_real_name LIKE "%[-]%"', $data['search']['real_name']);
            }
			
			
        	if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
            }
			
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
            if (isset($data['search']['user_parent_id']) && is_string($data['search']['user_parent_id'])) {
                $config['where'][] = array('[and] u.user_parent_id=[+]', $data['search']['user_parent_id']);
            }

			if (isset($data['search']['user_parent_phone']) && is_string($data['search']['user_parent_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_parent_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "-";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_parent_id=[+]', $user_id);
            }
			
        }
		
		
        return object(parent::TABLE_USER_IDENTITY)->select_page($config);
    }
	
	
	
	
    /**
     * 获取一条数据
     * 
     * $input = array(
     *  user_id     [str] [必填] [用户ID]
     * )
	 * 
     * USERADMINIDENTITYGET
	 * {"class":"user/admin_identity","method":"api_get"}
	 * 
     * @return  array
     */
    public function api_get($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_IDENTITY_READ);
        //校验数据
        object(parent::ERROR)->check($input, 'user_id', parent::TABLE_USER, array('args'));
        //查询数据
        $data = object(parent::TABLE_USER_IDENTITY)->find($input['user_id']);
        if( empty($data) ){
        	throw new error('数据不存在');
        }

        return $data;
    }











}