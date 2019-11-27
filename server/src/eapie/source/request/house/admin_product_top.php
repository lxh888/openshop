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
class admin_product_top extends \eapie\source\request\house {
	
	
	/*产品置顶*/
	
	
	
		
	/**
	 * 获取数据列表
	 * 
	 * HOUSEADMINPRODUCTTOPLIST
	 * {"class":"house/admin_product_top","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TOP_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_nickname_desc' => array('u.user_nickname', true),
            'user_nickname_asc' => array('u.user_nickname', false),
            
			'house_product_name_desc' => array('house_product_name', true),
			'house_product_name_asc' => array('house_product_name', false),
			
			'house_product_state_desc' => array('house_product_state', true),
			'house_product_state_asc' => array('house_product_state', false),
			
			'insert_time_desc' => array('house_product_top_insert_time', true),
			'insert_time_asc' => array('house_product_top_insert_time', false),
			'update_time_desc' => array('house_product_top_update_time', true),
			'update_time_asc' => array('house_product_top_update_time', false),
			
			'sort_desc' => array('house_product_top_sort', true),
			'sort_asc' => array('house_product_top_sort', false),
			
			'start_time_desc' => array('house_product_top_start_time', true),
			'start_time_asc' => array('house_product_top_start_time', false),
			'end_time_desc' => array('house_product_top_end_time', true),
			'end_time_asc' => array('house_product_top_end_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('hpt.house_product_id', false);
		
		$config["where"][] = array('[and] hp.house_product_trash=0');
		if(!empty($data['search'])){
			if( isset($data['search']['house_product_id']) && is_string($data['search']['house_product_id']) ){
				$config["where"][] = array('[and] hpt.house_product_id=[+]', $data['search']['house_product_id']);
			}
			
			if (isset($data['search']['house_product_name']) && is_string($data['search']['house_product_name'])) {
                $config['where'][] = array('[and] hp.house_product_name LIKE "%[-]%"', $data['search']['house_product_name']);
            }
			
			if( isset($data['search']['house_product_state']) && 
			(is_string($data['search']['house_product_state']) || is_numeric($data['search']['house_product_state'])) &&
			in_array($data['search']['house_product_state'], array("0", "1", "2", "3", "4")) ){
				$config["where"][] = array('[and] hp.house_product_state=[+]', $data['search']['house_product_state']);
				}
			
		}
		
		return object(parent::TABLE_HOUSE_PRODUCT_TOP)->select_page($config);
	}
	
	
	
		
	
	/**
	 * 检查编辑的权限
	 * 
	 * HOUSEADMINPRODUCTTOPEDITCHECK
	 * {"class":"house/admin_product_top","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TOP_EDIT);
		return true;
	}
	
	
	
		
	
	/**
	 * 编辑
	 * 
	 * HOUSEADMINPRODUCTTOPEDIT
	 * {"class":"house/admin_product_top","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_edit( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TOP_EDIT);
		//校验数据
        object(parent::ERROR)->check($data, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
		object(parent::ERROR)->check($data, 'house_product_top_sort', parent::TABLE_HOUSE_PRODUCT_TOP, array('args'));
		
		//查询旧数据
        $old_data = object(parent::TABLE_HOUSE_PRODUCT_TOP)->find($data['house_product_id']);
        if( empty($old_data) ){
        	throw new error('数据不存在');
        }
		
		//白名单
		$whitelist = array(
			'house_product_top_sort', 
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
		$update_data['house_product_top_update_time'] = time();
		if( object(parent::TABLE_HOUSE_PRODUCT_TOP)->update( array(array('house_product_id=[+]', (string)$data['house_product_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['house_product_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>