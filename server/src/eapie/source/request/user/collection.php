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
class collection extends \eapie\source\request\user {
	
	
	
	/**
	 * 当前登录用户的收藏列表
	 * 
	 * USERCOLLECTIONSELFLIST
	 * {"class":"user/collection","method":"api_self_list"}
	 * 
	 * @param	array	$data
	 * {"module":"模块名称"}
	 * @return array
	 */
	public function api_self_list( $data = array() ){
		// 检测登录
		object(parent::REQUEST_USER)->check();
		
		// 用户ID
		$user_id = $_SESSION['user_id'];
		

		$module_table_list = object(parent::TABLE_USER_COLLECTION)->get_module_table();
		if( !isset($module_table_list[$data['module']]) )
			throw new error('模块不存在');

		$result = array();

		$config['where'] = array(
			array('user_collection_module = [+]' , $data['module']),
			array('user_id=[+]',$user_id)
		);

		$config['select'] = array(
			'user_collection_key'
		);

		// $module => shop_goods
		if( $data['module'] == parent::MODULE_SHOP_GOODS ){
			$collection = object(parent::TABLE_USER_COLLECTION)->select($config);
			foreach ($collection as $key => $value) {
				$cache = object(parent::TABLE_SHOP_GOODS)->get_additional_data(array(array('shop_goods_id' => $value['user_collection_key'])));
				$result[] = $cache[0];
				unset($cache);
			}
		}

		// $module => merchant
		if( $data['module'] == parent::MODULE_MERCHANT ){
			$collection = object(parent::TABLE_USER_COLLECTION)->select($config);
			foreach ($collection as $key => $value) {
				$result[] = object(parent::TABLE_MERCHANT)->find($value['user_collection_key']);
			}
		}

		return $result;
	}


	/**
	 * 当前登录用户是否收藏
	 * 
	 * USERCOLLECTIONSELFISCOLLECTION
	 * {"class":"user/collection","method":"api_self_is_collection"}
	 * 
	 * @param	array	$data
	 * {"key":"ID主键","module":"模块"}
	 * @return array
	 */
	public function api_self_is_collection($data){
		$exist_data = object(parent::TABLE_USER_COLLECTION)->find_module_key_user($data['module'], $data['key'], $_SESSION['user_id']);

		if( !empty($exist_data) && isset($exist_data['user_collection_id']) ){
			return $exist_data['user_collection_id'];
		} else {
			return false;
		}
	}
	
	/**
	 * 当前登录用户的收藏获取
	 * 收藏ID 或者 模块标签与其主键  任一组
	 * 
	 * $data = array(
	 * 	"id" => 收藏ID
	 *  "module" => 模块标签
	 *  "key" => 该模块的主键
	 * )
	 * 
	 * USERCOLLECTIONSELFGET
	 * {"class":"user/collection","method":"api_self_get"}
	 * 
	 * [{"id":"检索|收藏ID","module":"检索|模块标签","key":"检索|该模块的主键"}]
	 * @param	array	$data
	 * @return	string
	 */
	public function api_self_get( $data = array() ){
		object(parent::REQUEST_USER)->check();
		if( isset($data["id"]) ){
			object(parent::ERROR)->check($data, 'id', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_id");
			$get_data = object(parent::TABLE_USER_COLLECTION)->find_id_user($data["id"], $_SESSION['user_id'], array(
				"user_collection_id as `id`",
				"user_id",
				"user_collection_label as `label`",
				"user_collection_comment as `comment`",
				"user_collection_module as `module`",
				"user_collection_key as `key`",
				"user_collection_value as `value`",
				"user_collection_sort as `sort`",
				"user_collection_update_time as `update_time`",
				"user_collection_insert_time as `insert_time`",
			));
		}else{
			object(parent::ERROR)->check($data, 'module', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_module");
			object(parent::ERROR)->check($data, 'key', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_key");
			$get_data = object(parent::TABLE_USER_COLLECTION)->find_module_key_user($data['module'], $data['key'], $_SESSION['user_id'], array(
				"user_collection_id as `id`",
				"user_id",
				"user_collection_label as `label`",
				"user_collection_comment as `comment`",
				"user_collection_module as `module`",
				"user_collection_key as `key`",
				"user_collection_value as `value`",
				"user_collection_sort as `sort`",
				"user_collection_update_time as `update_time`",
				"user_collection_insert_time as `insert_time`",
			));
		}
		if( empty($get_data) ){
			throw new error('没有收藏数据');
		}
		
		return $get_data;
	}
	
	
	
	
	
	
	/**
	 * 当前登录用户的收藏添加
	 * 
	 * $data = array(
	 * 	"module" => 模块标签
	 *  "key" => 该模块的主键
	 *  "label" => 收藏标签
	 *  "comment" => 注释
	 * )
	 * 
	 * USERCOLLECTIONSELFADD
	 * {"class":"user/collection","method":"api_self_add"}
	 * 
	 * [{"module":"模块标签","key":"该模块的主键","label":"收藏标签","comment":"注释","sort":"排序"}]
	 * @param	array	$data
	 * @return  string
	 */
	public function api_self_add( $data = array() ){
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check($data, 'module', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_module");
		object(parent::ERROR)->check($data, 'key', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_key");
		object(parent::ERROR)->check($data, 'label', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_label");
		object(parent::ERROR)->check($data, 'comment', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_comment");
		object(parent::ERROR)->check($data, 'sort', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_sort");
		
		//检测模块的主键是否合法
		$user_collection_value = object(parent::TABLE_USER_COLLECTION)->get_module_key($data['module'], $data['key']);
		if( empty($user_collection_value) ){
			throw new error('模块的主键有误，数据不存在');
		}
		
		$exist_data = object(parent::TABLE_USER_COLLECTION)->find_module_key_user($data['module'], $data['key'], $_SESSION['user_id']);
		if( !empty($exist_data) ){
			throw new error('已经收藏');
		}
		
        $insert_data = array(
        	"user_collection_id" => object(parent::TABLE_USER_COLLECTION)->get_unique_id(),
        	"user_id" => $_SESSION['user_id'],
			"user_collection_module" => $data["module"],
			"user_collection_key" => $data["key"],
			"user_collection_value" => $user_collection_value,
			"user_collection_update_time" => time(),
			"user_collection_insert_time" => time(),
		);
		
		if( isset($data['label']) ) $insert_data['user_collection_label'] = $data['label'];
		if( isset($data['comment']) ) $insert_data['user_collection_comment'] = $data['comment'];
		if( isset($data['sort']) ) $insert_data['user_collection_sort'] = $data['sort'];
		
		if( object(parent::TABLE_USER_COLLECTION)->insert($insert_data) ){
			return $insert_data['user_collection_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 当前登录用户的收藏编辑
	 * 收藏ID 或者 模块标签与其主键  任一组
	 * 
	 * $data = array(
	 * 	"id" => 收藏ID
	 *  "module" => 模块标签
	 *  "key" => 该模块的主键 
	 * 
	 *  "label" => 收藏标签
	 *  "comment" => 注释
	 * )
	 * 
	 * USERCOLLECTIONSELFEDIT
	 * {"class":"user/collection","method":"api_self_edit"}
	 * 
	 * [{"id":"检索|收藏ID","module":"检索|模块标签","key":"检索|该模块的主键","label":"编辑项|收藏标签","comment":"编辑项|注释","sort":"编辑项|排序"}]
	 * @param	array	$data
	 * @return	string
	 */
	public function api_self_edit( $data = array() ){
		object(parent::REQUEST_USER)->check();
		
		
		
		
		
		
	}
	
	
	
	
	
	/**
	 * 当前登录用户的收藏删除
	 * 收藏ID 或者 模块标签与其主键  任一组
	 * 
	 * $data = array(
	 * 	"id" => 收藏ID
	 * 	"module" => 模块标签
	 *  "key" => 该模块的主键
	 * );
	 * 
	 * USERCOLLECTIONSELFREMOVE
	 * {"class":"user/collection","method":"api_self_remove"}
	 * 
	 * [{"id":"检索|收藏ID","module":"检索|模块标签","key":"检索|该模块的主键"}]
	 * @param	array	$data
	 * @return	string
	 */
	public function api_self_remove( $data = array() ){
		object(parent::REQUEST_USER)->check();
		if( isset($data["id"]) ){
			object(parent::ERROR)->check($data, 'id', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_id");
			$bool = object(parent::TABLE_USER_COLLECTION)->delete(array(
				array("user_collection_id=[+]", $data['id']),
				array("[and] user_id=[+]", $_SESSION['user_id'])
			));
		}else{
			object(parent::ERROR)->check($data, 'module', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_module");
			object(parent::ERROR)->check($data, 'key', parent::TABLE_USER_COLLECTION, array('args'), "user_collection_key");
			$bool = object(parent::TABLE_USER_COLLECTION)->delete(array(
				array("user_collection_module=[+]", $data['module']),
				array("[and] user_collection_key=[+]", $data['key']),
				array("[and] user_id=[+]", $_SESSION['user_id'])
			));
		}
		
		if( empty($bool) ){
			throw new error("操作失败");
		}else{
			return true;
		}
		
		
	}


	/**
     * 查询收藏数量
     *
     * api: USERCOLLECTIONSELFCOUNT
     * req: {
     *  module  [str] [可选] [收藏的模块]
     * }
     * 
     * @return integer
     */
    public function api_self_count($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $where = array();
        $where[] = array('user_id = [+]', $_SESSION['user_id']);

        //是否指定某模块
        if (isset($input['module'])) {
            object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_COLLECTION, array('args'), 'user_collection_module');
            $where[] = array('[and] user_collection_module = [+]', $input['module']);
        }

        return object(parent::TABLE_USER_COLLECTION)->get_count($where);
    }
	
	
	
	
}
?>