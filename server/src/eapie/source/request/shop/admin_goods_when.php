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



namespace eapie\source\request\shop;
use eapie\main;
use eapie\error;
class admin_goods_when extends \eapie\source\request\shop {
	
	
	
	
	
	/**
	 * 添加限时商品
	 * 
	 * SHOPADMINGOODSWHENADD
	 * {"class":"shop/admin_goods_when","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_WHEN_ADD);
		
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS_WHEN, array('register_id'));
		object(parent::ERROR)->check($data, 'shop_goods_when_name', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_when_info', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_when_sort', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_when_start_time', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_when_end_time', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//白名单
		$whitelist = array(
			'shop_goods_id', 
			'shop_goods_when_name', 
			'shop_goods_when_info', 
			'shop_goods_when_sort',
			'shop_goods_when_start_time',
			'shop_goods_when_end_time',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		
		$insert_data['shop_goods_when_start_time'] = cmd(array($insert_data['shop_goods_when_start_time']), "time mktime");
		$insert_data['shop_goods_when_end_time'] = cmd(array($insert_data['shop_goods_when_end_time']), "time mktime");
		
		//创建时间
		$insert_data['shop_goods_when_insert_time'] = time();
		//更新时间
		$insert_data['shop_goods_when_update_time'] = time();
		//用户id
		$insert_data['user_id'] = $_SESSION['user_id'];
		if( object(parent::TABLE_SHOP_GOODS_WHEN)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['shop_goods_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	shop_goods_id 
	 * )
	 * 
	 * SHOPADMINGOODSWHENGET
	 * {"class":"shop/admin_goods_when","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_WHEN_READ);
		
		object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();
		
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		return object(parent::TABLE_SHOP_GOODS_WHEN)->find($data['shop_goods_id']);
	}
	
	
	
	/**
	 * 获取数据列表
	 * 
	 * SHOPADMINGOODSWHENLIST
	 * {"class":"shop/admin_goods_when","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_WHEN_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'shop_goods_name_desc' => array('shop_goods_name', true),
			'shop_goods_name_asc' => array('shop_goods_name', false),
			'shop_goods_state_desc' => array('shop_goods_state', true),
			'shop_goods_state_asc' => array('shop_goods_state', false),
			
			'name_desc' => array('shop_goods_when_name', true),
			'name_asc' => array('shop_goods_when_name', false),
			'state_desc' => array('shop_goods_when_state', true),
			'state_asc' => array('shop_goods_when_state', false),
			'insert_time_desc' => array('shop_goods_when_insert_time', true),
			'insert_time_asc' => array('shop_goods_when_insert_time', false),
			'update_time_desc' => array('shop_goods_when_update_time', true),
			'update_time_asc' => array('shop_goods_when_update_time', false),
			'sort_desc' => array('shop_goods_when_sort', true),
			'sort_asc' => array('shop_goods_when_sort', false),
			
			'start_time_desc' => array('shop_goods_when_start_time', true),
			'start_time_asc' => array('shop_goods_when_start_time', false),
			'end_time_desc' => array('shop_goods_when_end_time', true),
			'end_time_asc' => array('shop_goods_when_end_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('sgw.shop_goods_id', false);
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sgw.user_id=[+]', $_SESSION['user_id']);
		}
		
		$config["where"][] = array('[and] sg.shop_goods_trash=0');
		if(!empty($data['search'])){
			
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sg.shop_goods_id=[+]', $data['search']['shop_goods_id']);
			}
			
			if (isset($data['search']['shop_goods_name']) && is_string($data['search']['shop_goods_name'])) {
                $config['where'][] = array('[and] sg.shop_goods_name LIKE "%[-]%"', $data['search']['shop_goods_name']);
            }
			
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] sgw.shop_goods_when_state=[+]', $data['search']['state']);
				}
			
			if( isset($data['search']['shop_goods_state']) && 
			(is_string($data['search']['shop_goods_state']) || is_numeric($data['search']['shop_goods_state'])) &&
			in_array($data['search']['shop_goods_state'], array("0", "1", "2", "3")) ){
				$config["where"][] = array('[and] sg.shop_goods_state=[+]', $data['search']['shop_goods_state']);
				}
			
			
		}
		
		object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();
		return object(parent::TABLE_SHOP_GOODS_WHEN)->select_page($config);
	}
	
	
	
	
	
	/**
	 * 检查编辑的权限
	 * 
	 * SHOPADMINGOODSWHENEDITCHECK
	 * {"class":"shop/admin_goods_when","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_WHEN_EDIT);
		return true;
	}
	
	
	
	
	
	/**
	 * 编辑
	 * 
	 * SHOPADMINGOODSWHENEDIT
	 * {"class":"shop/admin_goods_when","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_WHEN_EDIT);
		//校验数据
        object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_when_name']) ) 
		object(parent::ERROR)->check($data, 'shop_goods_when_name', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		if( isset($data['shop_goods_when_info']) ) 
		object(parent::ERROR)->check($data, 'shop_goods_when_info', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		if( isset($data['shop_goods_when_sort']) ) 
		object(parent::ERROR)->check($data, 'shop_goods_when_sort', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		if( isset($data['shop_goods_when_start_time']) ) 
		object(parent::ERROR)->check($data, 'shop_goods_when_start_time', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		if( isset($data['shop_goods_when_end_time']) ) 
		object(parent::ERROR)->check($data, 'shop_goods_when_end_time', parent::TABLE_SHOP_GOODS_WHEN, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_SHOP_GOODS_WHEN)->find($data['shop_goods_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $old_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		
		if( isset($data['shop_goods_when_start_time']) )
		$data['shop_goods_when_start_time'] = cmd(array($data['shop_goods_when_start_time']), "time mktime");
		if( isset($data['shop_goods_when_end_time']) )
		$data['shop_goods_when_end_time'] = cmd(array($data['shop_goods_when_end_time']), "time mktime");
		
		//白名单
		$whitelist = array(
			'shop_goods_when_name', 
			'shop_goods_when_info', 
			'shop_goods_when_sort',
			'shop_goods_when_start_time',
			'shop_goods_when_end_time',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		if( !empty($update_data) ){
			foreach($update_data as $key => $value){
				if( isset($old_data[$key]) ){
					if($old_data[$key] == $value){
						unset($update_data[$key]);
					}
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['shop_goods_when_update_time'] = time();
		if( object(parent::TABLE_SHOP_GOODS_WHEN)->update( array(array('shop_goods_id=[+]', (string)$data['shop_goods_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	
		
	/**
	 * 删除
	 * 
	 * SHOPADMINGOODSWHENREMOVE
	 * {"class":"shop/admin_goods_when","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	string
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_WHEN_REMOVE);
		//校验数据
        object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
        //查询旧数据
        $original = object(parent::TABLE_SHOP_GOODS_WHEN)->find($data['shop_goods_id']);
        if (empty($original)) throw new error('数据不存在');
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $original['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
        //删除数据，记录日志
        if ( object(parent::TABLE_SHOP_GOODS_WHEN)->remove($original['shop_goods_id']) ) {
            object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
            return $data['shop_goods_id'];
        } else {
            throw new error('删除失败');
        }
		
	}
	
	
	
	
	
	
	
	
}
?>