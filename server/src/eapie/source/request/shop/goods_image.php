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
class goods_image extends \eapie\source\request\shop {
	
	
	
			
	/**
	 * 前台获取商城商品非主图的图片全部数据
	 * $data = arrray(
	 * 	shop_goods_id 商品ID
	 * )
	 * 
	 * SHOPGOODSIMAGE
	 * [{"id 必须":"商品ID","sort":["sort_desc 排序号倒序","sort_asc 排序号正序","name_desc","name_asc","type_desc","type_asc","size_desc","size_asc","state_desc","state_asc","insert_time_desc","insert_time_asc","update_time_desc","update_time_asc"]}]
	 * 
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_data($data = array()){
		object(parent::ERROR)->check($data, 'id', parent::TABLE_SHOP_GOODS, array('args', 'legal_id'), 'shop_goods_id');
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'select' => array(
				'sgi.image_id',
				'i.image_width as width',
				'i.image_height as height',
				'i.image_size as size',
				'i.image_sort as sort',
				'i.image_format as format',
				'i.image_type as type',
			),
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
			'state_desc' => array('image_state', true),
			'state_asc' => array('image_state', false),
			'insert_time_desc' => array('image_insert_time', true),
			'insert_time_asc' => array('image_insert_time', false),
			'update_time_desc' => array('image_update_time', true),
			'update_time_asc' => array('image_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_image_id', false);
		
		$config["where"] = array(
				array("[and] sgi.shop_goods_id=[+]", $data['id']),
				array("[and] sgi.shop_goods_image_main=0"),
				array("[and] i.image_state=1")
		);
		
		//查询数据
		$data = object(parent::TABLE_SHOP_GOODS_IMAGE)->select_join($config);
		return $data;
		//格式化数据
		/*$output = array();
		foreach ($data as $v) {
			$output[] = $v['image_id'];
		}
		return $output;*/
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>