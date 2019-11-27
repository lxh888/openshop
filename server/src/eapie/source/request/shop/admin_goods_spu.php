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
class admin_goods_spu extends \eapie\source\request\shop {
	
	
	
	
	/**
	 * 获取商品属性的所有父级与子级关系列表
	 *  $data = array(
	 * 	"type" => "son" 只获取子级，为空获取父级
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * SHOPADMINGOODSSPUOPTION
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_option($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('shop_goods_spu_name', true),
			'name_asc' => array('shop_goods_spu_name', false),
			'insert_time_desc' => array('shop_goods_spu_insert_time', true),
			'insert_time_asc' => array('shop_goods_spu_insert_time', false),
			'update_time_desc' => array('shop_goods_spu_update_time', true),
			'update_time_asc' => array('shop_goods_spu_update_time', false),
			'sort_desc' => array('shop_goods_spu_sort', true),
			'sort_asc' => array('shop_goods_spu_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_spu_id', false);
		
		
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] shop_goods_spu_id=[+]', $data['search']['id']);
				}
			
			if( isset($data['search']['parent_id']) && is_string($data['search']['parent_id']) ){
				$config["where"][] = array('[and] shop_goods_spu_parent_id=[+]', $data['search']['parent_id']);
				}
			
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] shop_goods_id=[+]', $data['search']['shop_goods_id']);
				}

		}
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["type"]) && $data["type"] == "son" ){
			$parent_config["where"][] = array('[and] shop_goods_spu_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] shop_goods_spu_parent_id=\"\"");
		}
		
		return object(parent::TABLE_SHOP_GOODS_SPU)->select_parent_son_all($parent_config, $son_config);
	}
	
	
		
	/**
	 * 商品属性的添加
	 * $data = array(
	 * 	'shop_goods_id' => string 商品ID
	 * )
	 * 
	 * SHOPADMINGOODSSPUADD
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SPU_ADD);
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'image_id', parent::TABLE_IMAGE, array('format', 'legal_id'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_name', parent::TABLE_SHOP_GOODS_SPU, array('args', 'length'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_parent_id', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_info', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_sort', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_required', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		
		
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
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_id', 
			'image_id', 
			'shop_goods_spu_parent_id', 
			'shop_goods_spu_name', 
			'shop_goods_spu_info',
			'shop_goods_spu_sort',
			'shop_goods_spu_required'
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//判断该分类是否存在、是否为顶级分类
		if( !empty($insert_data["shop_goods_spu_parent_id"]) ){
			$parent_data = object(parent::TABLE_SHOP_GOODS_SPU)->find($insert_data['shop_goods_spu_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			if( !empty($parent_data['shop_goods_spu_parent_id']) ){
				throw new error("所编辑的父级并不是顶级分类");
			}
		}
		
		//获取id号
		$insert_data['shop_goods_spu_id'] = object(parent::TABLE_SHOP_GOODS_SPU)->get_unique_id();
		//用户数据
		$insert_data['user_id'] = $_SESSION['user_id'];
		//创建时间
		$insert_data['shop_goods_spu_insert_time'] = time();
		//更新时间
		$insert_data['shop_goods_spu_update_time'] = time();
		
		if( object(parent::TABLE_SHOP_GOODS_SPU)->insert($insert_data) ){
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['shop_goods_spu_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
		
	/**
	 * 编辑规格属性的权限检测
	 * 
	 * SHOPADMINGOODSSPUEDITCHECK
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SPU_EDIT);
		return true;
	}
	
	
	
		
	/**
	 * 编辑属性
	 * 
	 * SHOPADMINGOODSSPUEDIT
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SPU_EDIT);
		//数据检测 
		object(parent::ERROR)->check($data, 'shop_goods_spu_id', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		
		if( isset($data['shop_goods_id']) )
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['image_id']) )
		object(parent::ERROR)->check($data, 'image_id', parent::TABLE_IMAGE, array('format'));
		if( isset($data['shop_goods_spu_name']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_name', parent::TABLE_SHOP_GOODS_SPU, array('args', 'length'));
		if( isset($data['shop_goods_spu_parent_id']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_parent_id', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		if( isset($data['shop_goods_spu_info']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_info', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		if( isset($data['shop_goods_spu_sort']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_sort', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		if( isset($data['shop_goods_spu_required']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_required', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		
		//获取旧数据
		$shop_goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->find($data['shop_goods_spu_id']);
		if( empty($shop_goods_spu_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_spu_data['shop_goods_id']);
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
			'shop_goods_id', 
			'image_id', 
			'shop_goods_spu_parent_id', 
			'shop_goods_spu_name', 
			'shop_goods_spu_info',
			'shop_goods_spu_sort',
			'shop_goods_spu_required'
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($shop_goods_spu_data as $key => $value){
			if(isset($shop_goods_spu_data[$key]) && isset($update_data[$key]) ){
				if($update_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		//修改商品ID， 不能为空
		if( isset($update_data["shop_goods_id"]) ){
			object(parent::ERROR)->check($update_data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('exists_id'));
		}
		
		//修改图片ID
		if( !empty($update_data["image_id"]) ){
			object(parent::ERROR)->check($update_data, 'image_id', parent::TABLE_SHOP_GOODS, array('legal_id'));
		}
		
		//父级不能是自己
		if( !empty($update_data["shop_goods_spu_parent_id"]) ){
			if( $update_data["shop_goods_spu_parent_id"] == $data['shop_goods_spu_id'] ){
				throw new error("不能将自己设为父级");
			}
			
			//判断该分类是否存在、是否为顶级分类
			$parent_data = object(parent::TABLE_SHOP_GOODS_SPU)->find($update_data['shop_goods_spu_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			
			if( !empty($parent_data['shop_goods_spu_parent_id']) ){
				throw new error("所编辑的父级并不是顶级的产品规格属性");
			}
			
		}
		
		
		//如果父级 编辑成 子级，必须要去掉子级
		if( empty($shop_goods_spu_data["shop_goods_spu_parent_id"]) &&
		isset($update_data["shop_goods_spu_parent_id"]) ){
			if( object(parent::TABLE_SHOP_GOODS_SPU)->find_exists_son_id($data['shop_goods_spu_id']) ){
				throw new error("该数据下存在子级，请先清理子级才能变更该数据的父级");
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		
		//更新时间
		$update_data['shop_goods_spu_update_time'] = time();
		if( object(parent::TABLE_SHOP_GOODS_SPU)->update( array(array('shop_goods_spu_id=[+]', (string)$data['shop_goods_spu_id'])), $update_data) ){
			
			if( isset($update_data["shop_goods_id"]) ){
				//更新商品修改时间
				object(parent::TABLE_SHOP_GOODS)->update( 
					array( array('shop_goods_id=[+]', $update_data['shop_goods_id']) ), 
					array('shop_goods_update_time' => time() ) 
				);	
			}
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_spu_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_spu_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 删除属性
	 * 
	 * SHOPADMINGOODSSPUREMOVE
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SPU_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'shop_goods_spu_id', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		
		//获取旧数据
		$shop_goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->find($data['shop_goods_spu_id']);
		if( empty($shop_goods_spu_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_spu_data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		
		//存在下级则无法删除
		if( object(parent::TABLE_SHOP_GOODS_SPU)->find_exists_son_id($data['shop_goods_spu_id']) ){
			throw new error("该产品规格属性下存在子级，请先清理子级才能删除该产品规格属性");
			}
		
		if( object(parent::TABLE_SHOP_GOODS_SPU)->remove($data['shop_goods_spu_id']) ){
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_spu_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $shop_goods_spu_data);
			return $data['shop_goods_spu_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	

	
	
	
	
	
	
}
?>