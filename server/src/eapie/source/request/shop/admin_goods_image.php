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
class admin_goods_image extends \eapie\source\request\shop {
	
	
	
	
	/**
	 * 检查编辑图片的权限
	 * 
	 * SHOPADMINGOODSIMAGEEDITCHECK
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_IMAGE_EDIT);
		return true;
	}
	
	
	
	
	
		
	/**
	 * 设为主图\取消主图  编辑主图
	 * 
	 * SHOPADMINGOODSIMAGEMAINEDIT
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_edit_main($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_IMAGE_EDIT);
		//检查数据
		object(parent::ERROR)->check($data, 'shop_goods_image_id', parent::TABLE_SHOP_GOODS_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_image_main', parent::TABLE_SHOP_GOODS_IMAGE, array('args'));
		
		//获取旧数据
		$shop_goods_image_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->find($data['shop_goods_image_id']);
		if( empty($shop_goods_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_image_data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_image_main', 
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		if( !empty($update_data) ){
			foreach($update_data as $key => $value){
				if( isset($shop_goods_image_data[$key]) ){
					if($shop_goods_image_data[$key] == $value){
						unset($update_data[$key]);
					}
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		
		$update_data['shop_goods_image_time'] = time();
		//更新
		if( object(parent::TABLE_SHOP_GOODS_IMAGE)->update( array(array('shop_goods_image_id=[+]', (string)$data['shop_goods_image_id'])), $update_data) ){
			
			object(parent::TABLE_IMAGE)->update(
				array( array('image_id=[+]', $shop_goods_image_data['image_id']) ), 
				array('image_update_time' => time() ) 
			);
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_image_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_image_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	
	
	
	
		
	/**
	 * 编辑图片信息
	 * 
	 * SHOPADMINGOODSIMAGEEDIT
	 * 
	 * @param	array	$data
	 * @return  bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_IMAGE_EDIT);
		
		//检查数据
		object(parent::ERROR)->check($data, 'shop_goods_image_id', parent::TABLE_SHOP_GOODS_IMAGE, array('args'));
		
		if( isset($data['image_name']) )
		object(parent::ERROR)->check($data, 'image_name', parent::TABLE_IMAGE, array('format', 'length'));
		if( isset($data['image_sort']) )
		object(parent::ERROR)->check($data, 'image_sort', parent::TABLE_IMAGE, array('args'));
		
		//获取旧数据
		$shop_goods_image_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->find($data['shop_goods_image_id']);
		if( empty($shop_goods_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_image_data['shop_goods_id']);
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
		if( empty($shop_goods_image_data['image_id']) ){
			throw new error("图片ID为空");
		}
		$image_data = object(parent::TABLE_IMAGE)->find($shop_goods_image_data['image_id']);
		if( empty($image_data) ){
			throw new error("图片ID有误，数据不存在");
		}
		
		$update = array(
			"where" => array( array('image_id=[+]', (string)$image_data['image_id']) ),
			"data"	=> array()
		);
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'image_name', 
			'image_sort', 
			);
		$update["data"] = cmd(array($data, $whitelist), 'arr whitelist');
		if( !empty($update["data"]) ){
			foreach($update["data"] as $key => $value){
				if( isset($image_data[$key]) ){
					if($image_data[$key] == $value){
						unset($update["data"][$key]);
					}
				}
			}
		}
		
		
		if( empty($update["data"]) ){
			throw new error("没有需要更新的数据");
		}
		
		$update["data"]['image_update_time'] = time();
		//更新
		if( object(parent::TABLE_IMAGE)->update( $update["where"], $update["data"]) ){
				
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_image_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update);
			return $data['shop_goods_image_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 删除商品图片
	 * 
	 * SHOPADMINGOODSIMAGEQINIUREMOVE
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_qiniu_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_IMAGE_REMOVE);
		//检查数据
		object(parent::ERROR)->check($data, 'shop_goods_image_id', parent::TABLE_SHOP_GOODS_IMAGE, array('args'));
		
		//获取旧数据
		$shop_goods_image_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->find($data['shop_goods_image_id']);
		if( empty($shop_goods_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_image_data['shop_goods_id']);
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
		if( !empty($shop_goods_image_data['image_id']) ){
			//获取配置
			$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
			if( empty($qiniu_config) ){
				throw new error("配置异常");
			}
			
			$qiniu_config["key"] = $shop_goods_image_data["image_id"];
			$qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($qiniu_config);
			if( !empty($qiniu_uptoken["errno"]) ){
				throw new error($qiniu_uptoken["error"]);
			}
			
			//删除图片登记
			object(parent::TABLE_IMAGE)->remove($shop_goods_image_data['image_id']);
		}
		
		
		if( object(parent::TABLE_SHOP_GOODS_IMAGE)->remove($data['shop_goods_image_id']) ){
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_image_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $shop_goods_image_data);
			return $data['shop_goods_image_id'];
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
	 * SHOPADMINGOODSIMAGELIST
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
			'sort_desc' => array('image_sort', true),
			'sort_asc' => array('image_sort', false),
			'name_desc' => array('image_name', true),
			'name_asc' => array('image_name', false),
			'type_desc' => array('image_type', true),
			'type_asc' => array('image_type', false),
			'size_desc' => array('image_size', true),
			'size_asc' => array('image_size', false),
			'main_desc' => array('shop_goods_image_main', true),
			'main_asc' => array('shop_goods_image_main', false),
			'state_desc' => array('image_state', true),
			'state_asc' => array('image_state', false),
			'insert_time_desc' => array('image_insert_time', true),
			'insert_time_asc' => array('image_insert_time', false),
			'update_time_desc' => array('image_update_time', true),
			'update_time_asc' => array('image_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_image_id', false);
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sg.user_id=[+]', $_SESSION['user_id']);
		}
		
		if(!empty($data['search'])){
			//商品ID 筛选
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sgi.shop_goods_id=[+]', $data['search']['shop_goods_id']);
				}
		}
		
		return object(parent::TABLE_SHOP_GOODS_IMAGE)->select_page($config);
	}
	
	
	
	
	
	
	
	
	
	
	
		
	/**
	 * 获取 商品图片 上传的token
	 * $data = array(
	 * 	"shop_goods_id" 商品ID
	 * 	"image_name"	文件的原名字
	 *  "image_size"	文件的大小
	 *  "image_type"    文件的类型
	 *  "image_format"  文件的后缀
	 * )
	 * 
	 * 返回值：
	 * array(
	 * 	"qiniu_uptoken" 		七牛的上传token
	 * 	"image_id" 				图片ID
	 *  "shop_goods_image_id"	商品图片ID
	 * )
	 * 
	 * SHOPADMINGOODSIMAGEQINIUUPTOKEN
	 * 
	 * @param	array	$data
	 * @return array
	 */
	public function api_qiniu_uptoken($data = array()){
		//执行时间开始
		$microtime2 = microtime(true);
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_IMAGE_UPLOAD);
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'image_name', parent::TABLE_IMAGE, array('format', 'length'));
		object(parent::ERROR)->check($data, 'image_type', parent::TABLE_IMAGE, array('args', 'mime_limit'));
		object(parent::ERROR)->check($data, 'image_size', parent::TABLE_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'image_format', parent::TABLE_IMAGE, array('args'));
		
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
		//执行时间开始
		$microtime = microtime(true);
		//获取配置
		$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
		if( empty($qiniu_config["bucket"]) ){
			throw new error("配置异常");
		}
		file_put_contents(CACHE_PATH."/获取配置", (microtime(true)-$microtime));
		
		
		//执行时间开始
		$microtime = microtime(true);
		//白名单 私密数据不能获取
		$whitelist = array(
			'image_name', 
			'image_type', 
			'image_size', 
			'image_format',
			);
		$image_insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		$image_insert_data['image_id'] = object(parent::TABLE_IMAGE)->get_unique_id();
		$image_insert_data['user_id'] = $_SESSION['user_id'];
		$image_insert_data['image_path'] = $qiniu_config["bucket"];//储存的空间
		$image_insert_data['image_state'] = 0;
		$image_insert_data['image_insert_time'] = time();
		$image_insert_data['image_update_time'] = time();
		//生成一个图片表数据
		if( !object(parent::TABLE_IMAGE)->insert($image_insert_data) ){
			throw new error("图片登记失败");
		}
		
		file_put_contents(CACHE_PATH."/图片登记", (microtime(true)-$microtime));
		
		$insert_data = array(
			"shop_goods_image_id" => object(parent::TABLE_SHOP_GOODS_IMAGE)->get_unique_id(),
			"user_id" => $_SESSION['user_id'],
			"shop_goods_id" => $data["shop_goods_id"],
			"image_id" => $image_insert_data['image_id'],
			"shop_goods_image_time" => time(),
		);
		//生成一个图片表数据
		if( !object(parent::TABLE_SHOP_GOODS_IMAGE)->insert($insert_data) ){
			object(parent::TABLE_IMAGE)->remove($image_insert_data['image_id']);
			throw new error("商品图片登记失败");
		}
		
		//执行时间开始
		$microtime = microtime(true);
		
		//根据文件大小，设置有效时间
		$qiniu_config["expires"] = 3600; //一个小时
		$qiniu_config["policy"] = array(
				'returnBody' => '{"key":"$(key)","hash":"$(etag)","type":$(mimeType),"size":$(fsize),"name":$(fname),"bucket":"$(bucket)","width":"$(imageInfo.width)","height":"$(imageInfo.height)","format":"$(imageInfo.format)"}',
				//限定用户上传的文件类型。
				//'mimeLimit' =>'image/*'
			);
		$qiniu_uptoken = object(parent::PLUGIN_QINIU)->uptoken($qiniu_config);
		if( !empty($qiniu_uptoken["errno"]) ){
			//删除文件
			object(parent::TABLE_IMAGE)->remove($image_insert_data['image_id']);
			object(parent::TABLE_SHOP_GOODS_IMAGE)->remove($insert_data["shop_goods_image_id"]);
			throw new error($qiniu_uptoken["error"]);
		}
		
		
		file_put_contents(CACHE_PATH."/获取七牛云参数", (microtime(true)-$microtime));
		
		
		//执行时间开始
		$microtime = microtime(true);
		//更新商品修改时间
		object(parent::TABLE_SHOP_GOODS)->update( 
			array( array('shop_goods_id=[+]', $data['shop_goods_id']) ), 
			array('shop_goods_update_time' => time() ) 
		);
		file_put_contents(CACHE_PATH."/更新商品修改时间", (microtime(true)-$microtime));
		
		//执行时间开始
		$microtime = microtime(true);
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, array("shop_goods_image"=>$insert_data, "image"=>$image_insert_data));
		file_put_contents(CACHE_PATH."/插入操作日志", (microtime(true)-$microtime));
		file_put_contents(CACHE_PATH."/操作完成", (microtime(true)-$microtime2));
		return array("qiniu_uptoken" => $qiniu_uptoken["data"], "image_id" => $image_insert_data["image_id"], "shop_goods_image_id" => $insert_data["shop_goods_image_id"]);
	}
	
	
	
	
	
	
	/**
	 * 更新上传 商品图片 的状态
	 * $data = array(
	 * 	"shop_goods_image_id" 商品图片ID
	 * 	"image_format"	文件的后缀格式
	 *  "image_width"	文件的宽
	 *  "image_height"   文件的高
	 *  "image_hash"   hash值
	 * )
	 * 
	 * SHOPADMINGOODSIMAGEQINIUSTATE
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_qiniu_state($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_IMAGE_UPLOAD);
		object(parent::ERROR)->check($data, 'shop_goods_image_id', parent::TABLE_SHOP_GOODS_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'image_width', parent::TABLE_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'image_format', parent::TABLE_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'image_width', parent::TABLE_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'image_height', parent::TABLE_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'image_hash', parent::TABLE_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'image_path', parent::TABLE_IMAGE, array('args'));
		
		//获取旧数据
		$shop_goods_image_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->find($data['shop_goods_image_id']);
		if( empty($shop_goods_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_image_data['shop_goods_id']);
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
		if( empty($shop_goods_image_data['image_id']) ){
			throw new error("图片ID为空");
		}
		
		$update = array(
			"where" => array( array('image_id=[+]', (string)$shop_goods_image_data['image_id']) ),
			"data"	=> array()
		);
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'image_format', 
			'image_width', 
			'image_height',
			'image_hash',
			);
		$update["data"] = cmd(array($data, $whitelist), 'arr whitelist');
		//上传情况
		$update["data"]['image_state'] = 1;
		$update["data"]['image_update_time'] = time();
		if( object(parent::TABLE_IMAGE)->update( $update['where'], 	$update["data"]) ){
				
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_image_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update);
			return $data['shop_goods_image_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>