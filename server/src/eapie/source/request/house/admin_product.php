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



namespace eapie\source\request\house;
use eapie\main;
use eapie\error;
class admin_product extends \eapie\source\request\house {


	//楼盘项目
	
	
	
	
		
	/**
	 * 编辑
	 * 
	 * HOUSEADMINPRODUCTEDIT
	 * {"class":"house/admin_product","method":"api_edit"}
	 * 
	 * @param	array		$data
	 * @return	string
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_EDIT);
		
        //检测输入
        object(parent::ERROR)->check($data, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
        if( isset($data['wechat_group_id']) )
        object(parent::ERROR)->check($data, 'wechat_group_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
		
		 //查询旧数据
        $original = object(parent::TABLE_HOUSE_PRODUCT)->find($data['house_product_id']);
        if (empty($original))
        throw new error('数据不存在');
		
		//白名单
        $whitelist = array(
        	'wechat_group_id',
        );
        $update_data = cmd(array($data, $whitelist), 'arr whitelist');
		//过滤不需要更新的数据
		if( !empty($update_data) ){
			foreach($update_data as $k => &$v) {
	            if(isset($original[$k]) && $original[$k] == $v){
	            	unset($update_data[$k]);
	            }
	        }
		}
        
        if ( empty($update_data) ) throw new error('没有需要更新的数据');
		
		//更新时间
        $update_data['house_product_update_time'] = time();
		//更新数据，记录日志
        if (object(parent::TABLE_HOUSE_PRODUCT)->update(array(array('house_product_id=[+]', $data['house_product_id'])), $update_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
            return $data['house_product_id'];
        } else {
            throw new error('操作失败');
        }
		
		
	}
	
	
	
	
	
	
	
	
	

    /**
     * 审核状态
     *
     * HOUSEADMINPRODUCTSTATE
     * $data = array(
     *	house_product_id [str] [必填] [楼盘项目ID]
     * )
     * {"class":"house/admin_product","method":"api_state"}
	 * 
	 * @param	array	$data
     * @return string
     */
    public function api_state($data = array()){
        //检测权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_STATE);

        //检测输入
        object(parent::ERROR)->check($data, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
        if( isset($data['house_product_state']) )
        object(parent::ERROR)->check($data, 'house_product_state', parent::TABLE_HOUSE_PRODUCT, array('args'));
		
		if( !in_array($data['house_product_state'], array("0", "1")) ){
			throw new error('审核状态值不合法');
		}
		
        //查询旧数据
        $original = object(parent::TABLE_HOUSE_PRODUCT)->find($data['house_product_id']);
        if (empty($original))
        throw new error('数据不存在');
		
		if( $original['house_product_state'] != 2 ){
			throw new error('非等待审核产品');
		}
		
        //白名单
        $whitelist = array(
            'house_product_state', 
        );
        $update_data = cmd(array($data, $whitelist), 'arr whitelist');

        //过滤不需要更新的数据
        foreach ($update_data as $key => &$val) {
            if( isset($original[$key]) && $original[$key] == $val ){
            	unset($update_data[$key]);
            }
        }

        if (empty($update_data))
        throw new error('没有需要更新的数据');
		
        //更新时间
        $update_data['house_product_update_time'] = time();
        $update_where = array(array('house_product_id=[+]', $data['house_product_id']));
		
        //更新数据
        if( object(parent::TABLE_HOUSE_PRODUCT)->update($update_where, $update_data) ){
        	
        	//审核通过送用户钱包
        	if( $update_data['house_product_state'] == 1 ){
				$this->_pass_reward_money($original['user_id'], $data['house_product_id'], $original['house_product_name']);
        	}
			
            //记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
            return $data['house_product_id'];
        } else {
            throw new error('操作失败');
        }
		
		
    }


	/**
	 * 审核送用户钱包
	 * 
	 * @param	string		$user_id				用户ID
	 * @param	string		$house_product_id		产品ID
	 * @param	string		$house_product_name		项目名称
	 * @return	bool
	 */
	private function _pass_reward_money( $user_id, $house_product_id, $house_product_name ){
		
		$find_reward = object(parent::TABLE_ORDER)->find_where(array(
			array('order_sign=[+]', $house_product_id),
			array('[and] order_type=[+]', parent::TRANSACTION_TYPE_HOUSE_PRODUCT_AWARD_MONEY),
			array('[and] order_state=1'),
			array('[and] order_pay_state=1')
		));
		if( !empty($find_reward) ){
			return false;//已经奖励
		}
		
		//查询配置信息
        $data_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_product_add_reward_user_money'), true);
        if( empty($data_config['state']) ){
        	return false;
        }
            
		//是否不随机
        if (empty($data_config['random_state'])) {
        	if (empty($data_config['user_money']) || !is_numeric($data_config['user_money']))
        		return false;

        	$money = $data_config['user_money'];
			$comment = '发布楼盘产品“'.$house_product_name.'”审核通过，奖励用户钱包';
        }else{
        	if (empty($data_config['random_min_user_money']) || !is_numeric($data_config['random_min_user_money']))
	            return false;

	        if (empty($data_config['random_max_user_money']) || 
	        !is_numeric($data_config['random_max_user_money']) || 
	        $data_config['random_max_user_money'] < $data_config['random_min_user_money'])
	            return false;

	        $money = mt_rand($data_config['random_min_user_money'], $data_config['random_max_user_money']);
			$comment = '发布楼盘产品“'.$house_product_name.'”审核通过，随机奖励用户钱包[￥'.($data_config['random_min_user_money']/100).'~￥'.($data_config['random_max_user_money']/100).']';
        }
		
		//订单信息
        $order = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_type' => parent::TRANSACTION_TYPE_HOUSE_PRODUCT_AWARD_MONEY,
            'order_comment' => $comment,
            'order_action_user_id' => $_SESSION['user_id'],
            'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
            'order_plus_account_id' => $user_id,
            'order_plus_value' => $money,
            'order_plus_transaction_id' => '',
            'order_sign' => $house_product_id,
            'order_json' => cmd(array($data_config), 'json encode'),
            'order_state' => 1,
            'order_pay_state' => 1,
            'order_pay_time' => time(),
            'order_insert_time' => time(),
        );

        //奖励金钱
        return object(parent::TABLE_USER_MONEY)->reward_money($order);
	}





    /**
     * 查询列表
     *
     * HOUSEADMINPRODUCTLIST
     * 
	 * @param	array	$input
     * @return array
     */
    public function api_list($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_READ);

        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        $config['orderby'] = object(parent::REQUEST)->orderby($input, array(
        	'user_nickname_desc' => array('u.user_nickname', true),
            'user_nickname_asc' => array('u.user_nickname', false),
            
            'name_desc' => array('house_product_name', true),
            'name_asc' => array('house_product_name', false),
            'state_desc' => array('house_product_state', true),
            'state_asc' =>  array('house_product_state', false),
            'insert_time_desc' => array('house_product_insert_time', true),
            'insert_time_asc' => array('house_product_insert_time', false),
            'update_time_desc' => array('house_product_update_time', true),
            'update_time_asc' => array('house_product_update_time', false),
            'sort_desc' => array('house_product_sort', true),
            'sort_asc' => array('house_product_sort', false),
        ));

        //避免排序重复
        $config['orderby'][] = array('hp.house_product_id', false);


		$config["where"][] = array('[and] hp.house_product_trash=0');

        //筛选——楼盘产品名称
        if (isset($input['search']['house_product_name']) && is_string($input['search']['house_product_name'])) {
            $config['where'][] = array('[and] hp.house_product_name LIKE "%[-]%"', $input['search']['house_product_name']);
        }
        //筛选——楼盘产品ID
        if (isset($input['search']['house_product_id']) && is_string($input['search']['house_product_id'])) {
            $config['where'][] = array('[and] hp.house_product_id=[+]', $input['search']['house_product_id']);
        }
		
		if (isset($input['search']['user_id']) && is_string($input['search']['user_id'])) {
            $config['where'][] = array('[and] u.user_id=[+]', $input['search']['user_id']);
        }
		if (isset($input['search']['user_nickname']) && is_string($input['search']['user_nickname'])) {
            $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $input['search']['user_nickname']);
        }

		if (isset($input['search']['user_phone']) && is_string($input['search']['user_phone'])) {
			$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($input['search']['user_phone'], array("u.user_id"));
    		if( empty($user_data['user_id']) ){
    			$user_id = "";
    		}else{
    			$user_id = $user_data['user_id'];
    		}
            $config['where'][] = array('[and] u.user_id=[+]', $user_id);
        }
			
		
		//订单状态
		if( isset($input['search']['state']) && 
		(is_string($input['search']['state']) || is_numeric($input['search']['state'])) &&
		in_array($input['search']['state'], array("0", "1", "2", "3", "4")) ){
			$config["where"][] = array('[and] hp.house_product_state=[+]', $input['search']['state']);
			}
		
        return object(parent::TABLE_HOUSE_PRODUCT)->select_page($config);
    }


    /**
     * 查询产品详情
     * 
     * HOUSEADMINPRODUCTDETAILS
	 * {"class":"house/admin_product","method":"api_details"}
     * $input = array(
     *  house_product_id 楼盘产品ID
     * )
     * 
	 * @param	array	$input		参数
     * @return  array
     */
    public function api_details($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_DETAILS);
        //检测输入
        object(parent::ERROR)->check($input, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
        //查询数据
        $get_data = object(parent::TABLE_HOUSE_PRODUCT)->find($input['house_product_id']);
        if( empty($get_data) ){
        	throw new error('数据不存在');
        }
            
		if( !empty($get_data) ){
			$data = array($get_data);
			$data = object(parent::TABLE_HOUSE_PRODUCT)->get_additional_data($data);
			$get_data = $data[0];
		}
		
        return $get_data;
    }





		
		
	/**
	 * 逻辑回收产品
	 * 
	 * HOUSEADMINPRODUCTTRASH
	 * {"class":"house/admin_product","method":"api_trash"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TRASH);
		object(parent::ERROR)->check($data, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
		
		//查询旧数据
        $old_data = object(parent::TABLE_HOUSE_PRODUCT)->find($data['house_product_id']);
        if( empty($old_data) ){
        	throw new error('数据不存在');
        }
		
		if( !empty($old_data["house_product_trash"]) ){
			throw new error("该产品已经在回收站");
		}
		
		//更新回收状态
		$update_data["house_product_trash"] = 1;
		$update_data['house_product_trash_time'] = time();
		if( object(parent::TABLE_HOUSE_PRODUCT)->update( array(array('house_product_id=[+]', (string)$data['house_product_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['house_product_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	

	
		
			
	/**
	 * 还原回收产品
	 * 
	 * HOUSEADMINPRODUCTTRASHRESTORE
	 * {"class":"house/admin_product","method":"api_trash_restore"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash_restore($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TRASH_RESTORE);
		object(parent::ERROR)->check($data, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
		
		//查询旧数据
        $old_data = object(parent::TABLE_HOUSE_PRODUCT)->find($data['house_product_id']);
        if( empty($old_data) ){
        	throw new error('数据不存在');
        }
		
		if( empty($old_data["house_product_trash"]) ){
			throw new error("该商品不在回收站");
		}
		
		//更新回收状态
		$update_data["house_product_trash"] = 0;
		$update_data["house_product_trash_time"] = time();
		if( object(parent::TABLE_HOUSE_PRODUCT)->update( array(array('house_product_id=[+]', (string)$data['house_product_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['house_product_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	

		
		
	/**
	 * 获取回收产品的数据列表
	 * 
	 * HOUSEADMINPRODUCTTRASHLIST
	 * {"class":"house/admin_product","method":"api_trash_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_trash_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TRASH_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('house_product_id', true),
			'id_asc' => array('house_product_id', false),
			'name_desc' => array('house_product_name', true),
            'name_asc' => array('house_product_name', false),
            'state_desc' => array('house_product_state', true),
            'state_asc' =>  array('house_product_state', false),
			
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'trash_time_desc' => array('house_product_trash_time', true),
			'trash_time_asc' => array('house_product_trash_time', false),
			
			'update_time_desc' => array('house_product_update_time', true),
			'update_time_asc' => array('house_product_update_time', false),
			
			'insert_time_desc' => array('house_product_insert_time', true),
			'insert_time_asc' => array('house_product_insert_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('house_product_id', false);
		
		$config["where"][] = array('[and] hp.house_product_trash=1');
		if(!empty($data['search'])){
			if( isset($data['search']['house_product_id']) && is_string($data['search']['house_product_id']) ){
				$config["where"][] = array('[and] hp.house_product_id=[+]', $data['search']['house_product_id']);
				}
		}
		
		return object(parent::TABLE_HOUSE_PRODUCT)->select_page($config);
	}
	
	
	
	








}