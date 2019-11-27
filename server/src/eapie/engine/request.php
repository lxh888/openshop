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



namespace eapie\engine;
class request extends \eapie\engine\init {
	
	
		
	/**
	 * 获取limit
	 * 
	 * @param	array		$data
	 * @param	string		$project
	 * @return array
	 */
	public function limit($data = array(), $project = ""){
		
		//size
		if( isset($data['size']) && is_numeric($data['size']) ){
			$data['page_size'] = $data['size'];
		}
		
		//获取后台的分页配置
		if( empty($data['page_size']) || 
		!is_numeric($data['page_size']) ){
			if(parent::REQUEST_ADMIN == $project){
				if( !empty($_SESSION["admin"]['admin_user_json']['page_size']) ){
					$page_size = $_SESSION["admin"]['admin_user_json']['page_size'];
				}else{
					//获取配置中的设置
					$page_size = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("admin_page_size"), true);
				}
			}else
			if(parent::REQUEST_USER == $project){
				if( !empty($_SESSION['user']['user_json']['page_size']) ){
					$page_size = $_SESSION['user']['user_json']['page_size'];
				}else{
					//获取配置中的设置
					$page_size = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("page_size"), true);
				}
			}
					
		}else{
			$page_size = $data['page_size'];
		}
		
		if( empty($page_size) || !is_numeric($page_size) ){
			throw new error("缺少每页条数的配置");
		}
		
		//return $data;
		if( !empty($data['page']) && (strtolower($data['page']) == "all" || $data['page'] < 0)){
			$limit = array();
		}else{
			//获取当前页
			$page_now = empty($data['page']) || !is_numeric($data['page'])? 1 : $data['page'];
			$limit_start =!isset($data['start']) || !is_numeric($data['start'])? ($page_now-1) * $page_size : $data['start'];
			$limit = array($limit_start, $page_size);
		}
		
		return $limit;
	}
	
	
	
	/**
	 * 获取limit
	 * 
	 * @param	array		$data
	 * @param	array		$orderby_list
	 * @return array
	 */
	public function orderby($data = array(), $orderby_list = array()){
		$orderby = array();
		if( empty($orderby_list) || !is_array($orderby_list)){
			return $orderby;
		}
		if(isset($data['sort']) && is_array($data['sort'])){
			foreach($data['sort'] as $value){
				if( (is_string($value) || is_numeric($value)) && 
				isset($orderby_list[$value]) ){
					$orderby[] = $orderby_list[$value];
				}
			}
		}
		
		return $orderby;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>