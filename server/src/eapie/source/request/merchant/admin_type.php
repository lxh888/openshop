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



namespace eapie\source\request\merchant;
use eapie\main;
use eapie\error;
class admin_type extends \eapie\source\request\merchant {
	
	
	//分类
	
	
    /**
     * 编辑商家类别
     *
     * MERCHANTADMINTYPEEDIT
	 * {"class":"merchant/admin_type","method":"api_edit"}
	 *  
     * $data = array(
     *  merchant_id  	[str] [必填] [商家ID]
     *  type_id         [arr] [必填] [类别ID，索引数组]
     * )
     * 
	 * @param	array	$data
     * @return  bool
     */
    public function api_edit($data = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MERCHANT_EDIT);
        //检查输入
        object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args', 'exists_id'));
		
		if( !empty($data['type_id']) && is_array($data['type_id'])){
			//清理数据
			$type_id = array();
			foreach($data['type_id'] as $value){
				if(is_string($value) || is_numeric($value)){
					$type_id[] = cmd(array($value), 'str addslashes');
				}
			}
		}
		
		if( !empty($type_id) ){
			//获取分类数据
			$in_string = "\"".implode("\",\"", $type_id)."\"";
			$type_where = array();
			$type_where[] = array("type_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			$type_where[] = array("[and] type_module=[+]", parent::MODULE_MERCHANT_TYPE);
			//获取商品分类数据
			$type_data = object(parent::TABLE_TYPE)->select(array("where"=>$type_where));
		}
		
		$old_data = object(parent::TABLE_MERCHANT_TYPE)->select(array(
			"where" => array( array("merchant_id=[+]", (string)$data['merchant_id']) )
		));
		
		
		//获得清理数据。1）需要删除的商品分类ID   2）需要增加的分类ID
		$clear_data = array();
		$clear_data["type_id"] = array();
		$clear_data["delete"] = array();
		$clear_data["insert"] = array();
		
		//先收集 旧数据，假设都需要被删除
		if( !empty($old_data) ){
			foreach($old_data as $value){
				$clear_data["delete"][$value["type_id"]] = $value["merchant_type_id"];
			}
		}
		
		//进行筛选
		if( !empty($type_data) ){
			foreach($type_data as $type_value){
				$clear_data["type_id"][] = $type_value["type_id"];
				if( isset($clear_data["delete"][$type_value["type_id"]]) ){
					unset($clear_data["delete"][$type_value["type_id"]]);
				}else{
					//这里就是需要增加的商品分类
					//$type_value["type_id"];
					$insert_data = array(
						"merchant_type_id" => object(parent::TABLE_MERCHANT_TYPE)->get_unique_id(),
						"merchant_id" => $data['merchant_id'],
						"type_id" => $type_value["type_id"],
						"user_id" => $_SESSION["user_id"],
						"merchant_type_time" => time()
					);
					$clear_data["insert"][] = $insert_data;
				}
			}
		}
		
		if( !empty($clear_data["insert"]) ){
			object(parent::TABLE_MERCHANT_TYPE)->insert_batch($clear_data["insert"]);
		}
		
		//再删除
		if( !empty($clear_data["delete"]) ){
			$in_string = "\"".implode("\",\"", $clear_data["delete"])."\"";
			//是不加单引号并且强制不过滤
			object(parent::TABLE_MERCHANT_TYPE)->delete(array( array("merchant_type_id IN([-])", $in_string, true) ));
		}
		
		if( empty($clear_data["insert"]) && empty($clear_data["delete"]) ){
			throw new error("没有需要更新的数据");
		}
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, $clear_data);
		
		//更新商品修改时间
		object(parent::TABLE_MERCHANT)->update( 
			array( array('merchant_id=[+]', $data['merchant_id']) ), 
			array('merchant_update_time' => time() ) 
		);	
		
		return true;
		
    }


}