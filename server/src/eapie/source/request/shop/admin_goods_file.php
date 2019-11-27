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
class admin_goods_file extends \eapie\source\request\shop {
	
	
		
	/**
	 * 检查编辑文件的权限
	 * 
	 * SHOPADMINGOODSFILEEDITCHECK
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_FILE_EDIT);
		return true;
	}
	
	
	
			
	/**
	 * 编辑文件信息
	 * 
	 * SHOPADMINGOODSFILEEDIT
	 * 
	 * @param	array	$data
	 * @return  bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_FILE_EDIT);
		
		//检查数据
		object(parent::ERROR)->check($data, 'shop_goods_file_id', parent::TABLE_SHOP_GOODS_FILE, array('args'));
		
		if( isset($data['file_name']) )
		object(parent::ERROR)->check($data, 'file_name', parent::TABLE_FILE, array('format', 'length'));
		if( isset($data['file_sort']) )
		object(parent::ERROR)->check($data, 'file_sort', parent::TABLE_FILE, array('args'));
		
		//获取旧数据
		$shop_goods_file_data = object(parent::TABLE_SHOP_GOODS_FILE)->find($data['shop_goods_file_id']);
		if( empty($shop_goods_file_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_file_data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//获取文件数据
		if( empty($shop_goods_file_data['file_id']) ){
			throw new error("文件ID为空");
		}
		$file_data = object(parent::TABLE_FILE)->find($shop_goods_file_data['file_id']);
		if( empty($file_data) ){
			throw new error("文件ID有误，数据不存在");
		}
		
		$update = array(
			"where" => array( array('file_id=[+]', (string)$file_data['file_id']) ),
			"data"	=> array()
		);
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'file_name', 
			'file_sort', 
			);
		$update["data"] = cmd(array($data, $whitelist), 'arr whitelist');
		if( !empty($update["data"]) ){
			foreach($update["data"] as $key => $value){
				if( isset($file_data[$key]) ){
					if($file_data[$key] == $value){
						unset($update["data"][$key]);
					}
				}
			}
		}
		
		
		if( empty($update["data"]) ){
			throw new error("没有需要更新的数据");
		}
		
		$update["data"]['file_update_time'] = time();
		//更新
		if( object(parent::TABLE_FILE)->update( $update["where"], $update["data"]) ){
				
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_file_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update);
			return $data['shop_goods_file_id'];
		}else{
			throw new error("操作失败");
		}
		
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
	 * SHOPADMINGOODSFILELIST
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'sort_desc' => array('file_sort', true),
			'sort_asc' => array('file_sort', false),
			'name_desc' => array('file_name', true),
			'name_asc' => array('file_name', false),
			'type_desc' => array('file_type', true),
			'type_asc' => array('file_type', false),
			'size_desc' => array('file_size', true),
			'size_asc' => array('file_size', false),
			'state_desc' => array('file_state', true),
			'state_asc' => array('file_state', false),
			'insert_time_desc' => array('file_insert_time', true),
			'insert_time_asc' => array('file_insert_time', false),
			'update_time_desc' => array('file_update_time', true),
			'update_time_asc' => array('file_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_file_id', false);
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sg.user_id=[+]', $_SESSION['user_id']);
		}
		
		if(!empty($data['search'])){
			//商品ID 筛选
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sgf.shop_goods_id=[+]', $data['search']['shop_goods_id']);
				}
		}
		
		return object(parent::TABLE_SHOP_GOODS_FILE)->select_page($config);
	}
	
	
	
	
		
		
	/**
	 * 获取 商品文件 上传的token
	 * $data = array(
	 * 	"shop_goods_id" 商品ID
	 * 	"file_name"	文件的原名字
	 *  "file_size"	文件的大小
	 *  "file_type"    文件的类型
	 *  "file_format"  文件的后缀
	 * )
	 * 
	 * 返回值：
	 * array(
	 * 	"qiniu_uptoken" 		七牛的上传token
	 * 	"file_id" 				文件ID
	 *  "shop_goods_file_id"	商品文件ID
	 * )
	 * 
	 * SHOPADMINGOODSFILEQINIUUPTOKEN
	 * 
	 * @param	array	$data
	 * @return array
	 */
	public function api_qiniu_uptoken($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_FILE_UPLOAD);
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'file_name', parent::TABLE_FILE, array('format', 'length'));
		object(parent::ERROR)->check($data, 'file_type', parent::TABLE_FILE, array('format'));
		object(parent::ERROR)->check($data, 'file_size', parent::TABLE_FILE, array('args', 'empty'));
		object(parent::ERROR)->check($data, 'file_format', parent::TABLE_FILE, array('args'));
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//获取配置
		$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
		if( empty($qiniu_config["bucket"]) ){
			throw new error("配置异常");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'file_name', 
			'file_type', 
			'file_size', 
			'file_format',
			);
		$file_insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		$file_insert_data['file_id'] = object(parent::TABLE_FILE)->get_unique_id();
		$file_insert_data['user_id'] = $_SESSION['user_id'];
		$file_insert_data['file_path'] = $qiniu_config["bucket"];//储存的空间
		$file_insert_data['file_state'] = 0;
		$file_insert_data['file_insert_time'] = time();
		$file_insert_data['file_update_time'] = time();
		//生成一个文件表数据
		if( !object(parent::TABLE_FILE)->insert($file_insert_data) ){
			throw new error("文件登记失败");
		}
		
		$insert_data = array(
			"shop_goods_file_id" => object(parent::TABLE_SHOP_GOODS_FILE)->get_unique_id(),
			"user_id" => $_SESSION['user_id'],
			"shop_goods_id" => $data["shop_goods_id"],
			"file_id" => $file_insert_data['file_id'],
			"shop_goods_file_time" => time(),
		);
		//生成一个文件表数据
		if( !object(parent::TABLE_SHOP_GOODS_FILE)->insert($insert_data) ){
			object(parent::TABLE_FILE)->remove($file_insert_data['file_id']);
			throw new error("商品文件登记失败");
		}
		
		
		//根据文件大小，设置有效时间
		$qiniu_config["expires"] = 7200;//两个小时
		$qiniu_config["policy"] = array(
			'returnBody' => '{"key":"$(key)","hash":"$(etag)","type":$(mimeType),"size":$(fsize),"name":$(fname),"bucket":"$(bucket)"}',
			//限定用户上传的文件类型。
			);
		$qiniu_uptoken = object(parent::PLUGIN_QINIU)->uptoken($qiniu_config);
		if( !empty($qiniu_uptoken["errno"]) ){
			//删除文件
			object(parent::TABLE_FILE)->remove($file_insert_data['file_id']);
			object(parent::TABLE_SHOP_GOODS_FILE)->remove($insert_data["shop_goods_file_id"]);
			throw new error($qiniu_uptoken["error"]);
		}
		
		//更新商品修改时间
		object(parent::TABLE_SHOP_GOODS)->update( 
			array( array('shop_goods_id=[+]', $data['shop_goods_id']) ), 
			array('shop_goods_update_time' => time() ) 
		);
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, array("shop_goods_file"=>$insert_data, "file"=>$file_insert_data));
		return array("qiniu_uptoken" => $qiniu_uptoken["data"], "file_id" => $file_insert_data["file_id"], "shop_goods_file_id" => $insert_data["shop_goods_file_id"]);
	}
	
	
	
	/**
	 * 更新上传 商品文件 的状态
	 * $data = array(
	 * 	"shop_goods_file_id" 商品文件ID
	 * 	"file_type"	文件的类型
	 *  "file_size"	文件的大小
	 *  "file_path"   储存空间
	 *  "file_hash"   hash值
	 * )
	 * 
	 * SHOPADMINGOODSFILEQINIUSTATE
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_qiniu_state($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_FILE_UPLOAD);
		object(parent::ERROR)->check($data, 'shop_goods_file_id', parent::TABLE_SHOP_GOODS_FILE, array('args'));
		object(parent::ERROR)->check($data, 'file_type', parent::TABLE_FILE, array('format'));
		object(parent::ERROR)->check($data, 'file_size', parent::TABLE_FILE, array('args'));
		object(parent::ERROR)->check($data, 'file_hash', parent::TABLE_FILE, array('args'));
		object(parent::ERROR)->check($data, 'file_path', parent::TABLE_FILE, array('args'));
		
		
		//获取旧数据
		$shop_goods_file_data = object(parent::TABLE_SHOP_GOODS_FILE)->find($data['shop_goods_file_id']);
		if( empty($shop_goods_file_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_file_data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//获取文件数据
		if( empty($shop_goods_file_data['file_id']) ){
			throw new error("文件ID为空");
		}
		
		$update = array(
			"where" => array( array('file_id=[+]', (string)$shop_goods_file_data['file_id']) ),
			"data"	=> array()
		);
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'file_type', 
			'file_size', 
			'file_hash',
			'file_path',
			);
		$update["data"] = cmd(array($data, $whitelist), 'arr whitelist');
		//上传情况
		$update["data"]['file_state'] = 1;
		$update["data"]['file_update_time'] = time();
		if( object(parent::TABLE_FILE)->update( $update['where'], 	$update["data"]) ){
				
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_file_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update);
			return $data['shop_goods_file_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
		
	/**
	 * 删除商品文件
	 * 
	 * SHOPADMINGOODSFILEQINIUREMOVE
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_qiniu_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_FILE_REMOVE);
		//检查数据
		object(parent::ERROR)->check($data, 'shop_goods_file_id', parent::TABLE_SHOP_GOODS_FILE, array('args'));
		
		//获取旧数据
		$shop_goods_file_data = object(parent::TABLE_SHOP_GOODS_FILE)->find($data['shop_goods_file_id']);
		if( empty($shop_goods_file_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_file_data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//获取图片数据
		if( !empty($shop_goods_file_data['file_id']) ){
			//获取配置
			$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
			if( empty($qiniu_config) ){
				throw new error("配置异常");
			}
			
			$qiniu_config["key"] = $shop_goods_file_data["file_id"];
			$qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($qiniu_config);
			if( !empty($qiniu_uptoken["errno"]) ){
				throw new error($qiniu_uptoken["error"]);
			}
			
			//删除图片登记
			object(parent::TABLE_FILE)->remove($shop_goods_file_data['file_id']);
		}
		
		
		if( object(parent::TABLE_SHOP_GOODS_FILE)->remove($data['shop_goods_file_id']) ){
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_file_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $shop_goods_file_data);
			return $data['shop_goods_file_id'];
		}else{
			throw new error("操作失败");
		}
		
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>