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



namespace eapie\source\request\admin;
use eapie\main;
use eapie\error;
class user extends \eapie\source\request\admin {
	
	
	
	
	/**
	 * 获取当前用户的管理员数据
	 * 
	 * ADMINSELF
	 * {"class":"admin/user","method":"api_self"}
	 * 
	 * @param	void
	 * @return 	bool
	 */
	public function api_self($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_ADMIN)->check();
		return $_SESSION['admin'];
	}
	
	/**
	 * 获取当前用户的管理员身份
	 * 
	 * ADMINSELFADMINID
	 * {"class":"admin/user","method":"api_self_amdin_id"}
	 * 
	 * @param	void
	 * @return 	bool
	 */
	public function api_self_amdin_id($data = array()){
		//检测登录
        object(parent::REQUEST_USER)->check();
		$user_id = $_SESSION['user_id'];
		$admin = object(parent::TABLE_ADMIN_USER)->find($user_id);
		$name = '';
		if(!empty($admin) && isset($admin['admin_id'])){
			switch ($admin['admin_id']) {
				case 'king':
					$name = '王者';
					break;
				case 'platinum':
					$name = '赤金';
					break;
				case 'silver':
					$name = '白银';
					break;
				case 'gold':
					$name = '黄金';
					break;
				case 'bronze':
					$name = '青铜';
					break;
				default:
					$name = '暂无等级';
					break;
			}
		} else {
			$name = '暂无等级';
		}
		return $name;
	}
	
	
	
	/**
	 * 设置/获取，当前管理员的配置
     * 请求参数为空，则是获取。请求参数不为空，则是设置
	 * 
     * ADMINUSERSELFCONFIG
	 * {"class":"admin/user","method":"api_self_config"}
     * 
	 * @param	array	$data
	 * @return array | bool
	 */
	public function api_self_config( $data = array() ){
		//检查是否已初始化
		object(parent::REQUEST_ADMIN)->check();
		if( empty($_SESSION["admin"]['admin_user_json']) || !is_array($_SESSION["admin"]['admin_user_json'])){
			$_SESSION["admin"]['admin_user_json'] = array();
		}
		
		if( empty($data) ){
			return $_SESSION["admin"]['admin_user_json'];
		}
		
		//旧数据
		$old_string = cmd(array($_SESSION["admin"]['admin_user_json']), "json encode");
		
		if( isset($data['page_size']) ){
			object(parent::ERROR)->check($data, 'page_size', parent::TABLE_CONFIG, array('args'));
			$_SESSION["admin"]['admin_user_json']['page_size'] = $data['page_size'];
		}
		
		//新数据
		$now_string = cmd(array($_SESSION["admin"]['admin_user_json']), "json encode");
		
		if($old_string == $now_string){
			throw new error("没有需要更新的数据");
		}
		
		$bool = object(parent::TABLE_ADMIN_USER)->update(array(array('user_id=[+]', $_SESSION["user_id"])), array('admin_user_json'=>$now_string));
		if( empty($bool) ){
			throw new error("操作失败");
		}else{
			return true;
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>