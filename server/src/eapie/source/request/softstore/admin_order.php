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



namespace eapie\source\request\softstore;
use eapie\main;
use eapie\error;
class admin_order extends \eapie\source\request\softstore {
	
	
	
	/**
	 * 逻辑回收订单
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_TRASH);
		object(parent::ERROR)->check($data, 'ss_order_id', parent::TABLE_SOFTSTORE_ORDER, array('args'));
		
		//获取旧数据
		$ss_product_data = object(parent::TABLE_SOFTSTORE_ORDER)->find($data['ss_order_id']);
		if( empty($ss_product_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($ss_product_data["ss_order_trash"]) ){
			throw new error("该订单已经在回收站");
		}
		
		//更新回收状态
		$update_data["ss_order_trash"] = 1;
		$update_data['ss_order_trash_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_ORDER)->update( array(array('ss_order_id=[+]', (string)$data['ss_order_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_order_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	/**
	 * 确认订单已经联系
	 * $data = arrray(
	 *	ss_order_id 订单ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_contact( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_CONTACT);
		object(parent::ERROR)->check($data, 'ss_order_id', parent::TABLE_SOFTSTORE_ORDER, array('args'));
		object(parent::ERROR)->check($data, 'ss_order_contact_notes', parent::TABLE_SOFTSTORE_ORDER, array('args'));
		object(parent::ERROR)->check($data, 'ss_order_contact_state', parent::TABLE_SOFTSTORE_ORDER, array('args'));
		
		//获取旧数据
		$ss_order_data = object(parent::TABLE_SOFTSTORE_ORDER)->find($data['ss_order_id']);
		if( empty($ss_order_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_order_contact_notes', 
			'ss_order_contact_state', 
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($ss_order_data[$key]) ){
				if($ss_order_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['ss_order_contact_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_ORDER)->update( array(array('ss_order_id=[+]', (string)$data['ss_order_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_order_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 获取数据列表
	 * $data = arrray(
	 *	ss_order_id 订单ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_detail($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_DETAIL_READ);
		object(parent::ERROR)->check($data, 'ss_order_id', parent::TABLE_SOFTSTORE_ORDER, array('args'));
		
		return object(parent::TABLE_SOFTSTORE_ORDER)->find_detail($data["ss_order_id"]);
	}
	
	
	
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
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			'name_desc' => array('ss_order_name', true),
			'name_asc' => array('ss_order_name', false),
			
			'contact_state_desc' => array('ss_order_contact_state', true),
			'contact_state_asc' => array('ss_order_contact_state', false),
			'time_desc' => array('ss_order_time', true),
			'time_asc' => array('ss_order_time', false),
			'contact_time_desc' => array('ss_order_contact_time', true),
			'contact_time_asc' => array('ss_order_contact_time', false),
			
			'order_id_desc' => array('ss_order_id', true),
			'order_id_asc' => array('ss_order_id', false),
			
			'total_money_desc' => array('ss_order_total_money', true),
			'total_money_asc' => array('ss_order_total_money', false),
			
			'trash_time_desc' => array('ss_order_trash_time', true),
			'trash_time_asc' => array('ss_order_trash_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_order_id', false);
		
		
		$config["where"][] = array('[and] so.ss_order_trash=0');
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] so.ss_order_id=[+]', $data['search']['id']);
			}
			
			if( isset($data['search']['contact_state']) && 
			(is_string($data['search']['contact_state']) || is_numeric($data['search']['contact_state'])) &&
			in_array($data['search']['contact_state'], array("0", "1")) ){
				$config["where"][] = array('[and] so.ss_order_contact_state=[+]', $data['search']['contact_state']);
				}
			
		}
		
		return object(parent::TABLE_SOFTSTORE_ORDER)->select_page($config);
	}
	
	
	
	
	
		
	/**
	 * 获取回收数据列表
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
	 * @param	array	$data
	 * @return	array
	 */
	public function api_trash_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_TRASH_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			'name_desc' => array('ss_order_name', true),
			'name_asc' => array('ss_order_name', false),
			
			'time_desc' => array('ss_order_time', true),
			'time_asc' => array('ss_order_time', false),
			
			'order_id_desc' => array('ss_order_id', true),
			'order_id_asc' => array('ss_order_id', false),
			
			'total_money_desc' => array('ss_order_total_money', true),
			'total_money_asc' => array('ss_order_total_money', false),
			
			'trash_time_desc' => array('ss_order_trash_time', true),
			'trash_time_asc' => array('ss_order_trash_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_order_id', false);
		
		
		$config["where"][] = array('[and] so.ss_order_trash=1');
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] so.ss_order_id=[+]', $data['search']['id']);
			}
			
		}
		
		return object(parent::TABLE_SOFTSTORE_ORDER)->select_page($config);
	}
	
	
	
	
	
	
}
?>