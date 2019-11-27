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



namespace eapie\source\request\express;
use eapie\main;
use eapie\error;
class admin_rider extends \eapie\source\request\express {
	
	
	
	
	/**
	 * 获取数据列表
	 * 需要判断浏览权限
	 * 
	 * $request = array(
	 * 	'search' => array(),//搜索、筛选
	 * 	'sort' => array(),//排序
	 *  'size' => 0,//每页的条数
	 * 	'page' => 0, //当前页数，如果是等于 all 那么则查询所有
	 *  'start' => 0, //开始的位置，如果存在，则page无效
	 * );
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 *  'page_count' => //总页数
	 * 	'data' => //数据
	 * );
	 * 
	 * EXPRESSADMINRIDERLIST
	 * {"class":"express/admin_rider","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_RIDER_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('express_rider_name', true),
			'name_asc' => array('express_rider_name', false),
			'state_desc' => array('express_rider_state', true),
			'state_asc' =>  array('express_rider_state', false),
			
			'insert_time_desc' => array('express_rider_insert_time', true),
			'insert_time_asc' => array('express_rider_insert_time', false),
			'update_time_desc' => array('express_rider_update_time', true),
			'update_time_asc' => array('express_rider_update_time', false),
			
			'on_off_desc' => array('express_rider_on_off', true),
			'on_off_asc' => array('express_rider_on_off', false),
			
			'phone_desc' => array('express_rider_phone', true),
			'phone_asc' => array('express_rider_phone', false),
			
			'province_desc' => array('express_rider_province', true),
			'province_asc' => array('express_rider_province', false),
			'city_desc' => array('express_rider_city', true),
			'city_asc' => array('express_rider_city', false),
			'district_desc' => array('express_rider_district', true),
			'district_asc' => array('express_rider_district', false),
			
		));
		
		//避免排序重复
		$config["orderby"][] = array('user_id', false);
		
		
		if(!empty($data['search'])){
			
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] er.express_rider_state=[+]', $data['search']['state']);
				}
			
			if( isset($data['search']['express_rider_on_off']) && 
			(is_string($data['search']['express_rider_on_off']) || is_numeric($data['search']['express_rider_on_off'])) &&
			in_array($data['search']['express_rider_on_off'], array("0", "1")) ){
				$config["where"][] = array('[and] er.express_rider_on_off=[+]', $data['search']['express_rider_on_off']);
				}
			
			if( isset($data['search']['user_id']) && is_string($data['search']['user_id']) ){
				$config["where"][] = array('[and] er.user_id=[+]', $data['search']['user_id']);
			}
			if( isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname']) ){
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			if( isset($data['search']['user_phone']) && is_string($data['search']['user_phone']) ){
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] er.user_id=[+]', $user_id);
            }
			
			
			if( isset($data['search']['express_rider_name']) && is_string($data['search']['express_rider_name']) ){
                $config['where'][] = array('[and] er.express_rider_name LIKE "%[-]%"', $data['search']['express_rider_name']);
            }
			if( isset($data['search']['express_rider_phone']) && 
			(is_string($data['search']['express_rider_phone']) || is_numeric($data['search']['express_rider_phone']) ) ){
				$config["where"][] = array('[and] er.express_rider_phone=[+]', $data['search']['express_rider_phone']);
			}
			
			if( isset($data['search']['express_rider_province']) && is_string($data['search']['express_rider_province'])) {
                $config['where'][] = array('[and] er.express_rider_province LIKE "%[-]%"', $data['search']['express_rider_province']);
            }
			if( isset($data['search']['express_rider_city']) && is_string($data['search']['express_rider_city']) ){
				$config['where'][] = array('[and] er.express_rider_city LIKE "%[-]%"', $data['search']['express_rider_city']);
			}
			if( isset($data['search']['express_rider_district']) && is_string($data['search']['express_rider_district']) ){
				$config['where'][] = array('[and] er.express_rider_district LIKE "%[-]%"', $data['search']['express_rider_district']);
			}
			
		}
		
		
		return object(parent::TABLE_EXPRESS_RIDER)->select_page($config);
	}
	
	
	
	
			
	/**
	 * 删除骑手
	 * 
	 * EXPRESSADMINRIDERREMOVE
	 * {"class":"express/admin_rider","method":"api_remove"}
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_RIDER_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_EXPRESS_RIDER)->find($data['user_id']);
        if (empty($original)) throw new error('数据不存在');
		
		if( object(parent::TABLE_EXPRESS_RIDER)->remove($data['user_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
			return $data['user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	
	
	/**
     * 添加骑手
     * 
     * EXPRESSADMINRIDERADD
	 * {"class":"express/admin_rider","method":"api_add"}
     * 
     * @param  [arr]  $input [请求参数]
     * @return bool
     */
    public function api_add($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_RIDER_ADD);
		
        //校验数据
        object(parent::ERROR)->check($input, 'express_rider_name', parent::TABLE_EXPRESS_RIDER, array('args'));
        object(parent::ERROR)->check($input, 'express_rider_info', parent::TABLE_EXPRESS_RIDER, array('args'));
		object(parent::ERROR)->check($input, 'express_rider_phone', parent::TABLE_EXPRESS_RIDER, array('args'));
		object(parent::ERROR)->check($input, 'express_rider_province', parent::TABLE_EXPRESS_RIDER, array('args'));
		object(parent::ERROR)->check($input, 'express_rider_city', parent::TABLE_EXPRESS_RIDER, array('args'));
		object(parent::ERROR)->check($input, 'express_rider_district', parent::TABLE_EXPRESS_RIDER, array('args'));
		object(parent::ERROR)->check($input, 'express_rider_on_off', parent::TABLE_EXPRESS_RIDER, array('args'));
        object(parent::ERROR)->check($input, 'express_rider_state', parent::TABLE_EXPRESS_RIDER, array('args'));
		
        //查询用户ID
        if( empty($input['user']) ) throw new error('请填写用户ID或用户手机号');
        $user_data = object(parent::TABLE_USER)->find_id_or_phone($input['user']);
        if( empty($user_data['user_id']) ){
        	throw new error('用户不存在');
        }else{
        	//判断是否已存在
        	object(parent::ERROR)->check($user_data, 'user_id', parent::TABLE_EXPRESS_RIDER, array('exists'));
			$user_id = $user_data['user_id'];
        }
        
        //白名单
        $whitelist = array(
            'express_rider_name',
            'express_rider_info',
            'express_rider_phone',
            'express_rider_province',
            'express_rider_city',
            'express_rider_district',
            'express_rider_on_off',
            'express_rider_state',
        );
        $insert_data = cmd(array($input, $whitelist), 'arr whitelist');

        //格式化数据
        $insert_data['user_id'] = $user_id;
        $insert_data['express_rider_insert_time'] = time();
        $insert_data['express_rider_update_time'] = time();

        //插入数据，记录日志
        if (object(parent::TABLE_EXPRESS_RIDER)->insert($insert_data)) {
        	
            object(parent::TABLE_ADMIN_LOG)->insert($input, $insert_data);
            return $insert_data['user_id'];
			
        } else {
            throw new error('添加失败');
        }
		
    }
	
	
	
	
			
	/**
	 * 编辑快递骑手
	 * 
	 * EXPRESSADMINRIDEREDIT
	 * {"class":"express/admin_rider","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_RIDER_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		if( isset($data['express_rider_name']) )
		object(parent::ERROR)->check($data, 'express_rider_name', parent::TABLE_EXPRESS_RIDER, array('args'));
		if( isset($data['express_rider_info']) )
        object(parent::ERROR)->check($data, 'express_rider_info', parent::TABLE_EXPRESS_RIDER, array('args'));
		if( isset($data['express_rider_phone']) )
		object(parent::ERROR)->check($data, 'express_rider_phone', parent::TABLE_EXPRESS_RIDER, array('args'));
		if( isset($data['express_rider_province']) )
		object(parent::ERROR)->check($data, 'express_rider_province', parent::TABLE_EXPRESS_RIDER, array('args'));
		if( isset($data['express_rider_city']) )
		object(parent::ERROR)->check($data, 'express_rider_city', parent::TABLE_EXPRESS_RIDER, array('args'));
		if( isset($data['express_rider_district']) )
		object(parent::ERROR)->check($data, 'express_rider_district', parent::TABLE_EXPRESS_RIDER, array('args'));
		if( isset($data['express_rider_on_off']) )
		object(parent::ERROR)->check($data, 'express_rider_on_off', parent::TABLE_EXPRESS_RIDER, array('args'));
		if( isset($data['express_rider_state']) )
        object(parent::ERROR)->check($data, 'express_rider_state', parent::TABLE_EXPRESS_RIDER, array('args'));
		
		//获取旧数据
		$original = object(parent::TABLE_EXPRESS_RIDER)->find($data['user_id']);
		if( empty($original) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'express_rider_name',
            'express_rider_info',
            'express_rider_phone',
            'express_rider_province',
            'express_rider_city',
            'express_rider_district',
            'express_rider_on_off',
            'express_rider_state',
		);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($original[$key]) ){
				if($original[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['express_rider_update_time'] = time();
		if( object(parent::TABLE_EXPRESS_RIDER)->update( array(array('user_id=[+]', (string)$data['user_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	
	
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	user_id 用户ID
	 * )
	 * 
	 * EXPRESSADMINRIDERGET
	 * {"class":"express/admin_rider","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_RIDER_READ);
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		
		$get_data = object(parent::TABLE_EXPRESS_RIDER)->find($data['user_id']);
		if( empty($get_data) ){
			throw new error("快递骑手不存在");
		}
		
		return $get_data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>