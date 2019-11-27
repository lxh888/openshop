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



namespace eapie\source\request;
use eapie\main;
use eapie\error;
class admin extends main  {
	
	
	
			
	/**
	 * 检查权限
	 * 是否已登陆
	 * 初始化管理员信息
	 * 
	 * @param	string		$authority
	 * @param   bool    $return_bool    是否返回布尔值
	 * @return	mixed
	 */
	public function check( $authority = "", $return_bool = false ){
		//检查用户数据
		if( empty($return_bool) ){
            //检查是否已初始化
            object(parent::REQUEST_USER)->check();
        }else{
            $bool = object(parent::REQUEST_USER)->check(true);
            if( empty($bool) ){
                return false;
            }
        }
		
		
		
		//获取管理员数据
		if( empty($_SESSION['admin']) ){
			$_SESSION['admin'] = object(parent::TABLE_ADMIN)->find_info();
			if( !empty($_SESSION['admin']['authority_id']) ){
				$_SESSION['admin']['authority_id'] = explode(',', trim($_SESSION['admin']['authority_id'], ','));
				}
			
			if( !empty($_SESSION['admin']) &&
			(empty($_SESSION['admin']['authority_id']) || !is_array($_SESSION['admin']['authority_id'])) ){
				$_SESSION['admin']['authority_id'] = array();
			}
			
			//将管理用户个人的权限与角色权限合并
			if( !empty($_SESSION['admin']['self_authority_id']) ){
				$_SESSION['admin']['self_authority_id'] = explode(',', trim($_SESSION['admin']['self_authority_id'], ','));
				if( is_array($_SESSION['admin']['self_authority_id']) ){
					foreach($_SESSION['admin']['self_authority_id'] as $v){
						if(!in_array($v, $_SESSION['admin']['authority_id'])){
							$_SESSION['admin']['authority_id'][] = $v;
						}
					}
				}
			}
			
			//获取配置
			if( !empty($_SESSION['admin']['admin_user_json']) ){
				$_SESSION['admin']['admin_user_json'] = cmd(array($_SESSION['admin']['admin_user_json']), "json decode");
			}
			
		}
		
		
		//获取当前应用的所有分配的权限
		$application = object(parent::MAIN)->api_application();
		if( !empty($application['authority_id']) ){
			$application_authority_ids = explode(',', trim($application['authority_id'], ','));
		}
		//应用没有权限，那么管理员的权限就为空
		if( empty($application_authority_ids) || !is_array($application_authority_ids) ){
			$_SESSION['admin']['authority_id'] = array();
		}
		//清理权限
		if( !empty($_SESSION['admin']['authority_id']) ){
			foreach($_SESSION['admin']['authority_id'] as $k => $v){
				if( !in_array($v, $application_authority_ids) ){
					unset($_SESSION['admin']['authority_id'][$k]);
				}
			}
		}
		
		
		if( empty($_SESSION['admin']) ){
			if( empty($return_bool) ){
				throw new error ('不是管理员');
			}else{
				return false;
			}
			
		}
		if( empty($_SESSION['admin']['admin_state']) ){
			if( empty($return_bool) ){
				throw new error ('该管理角色已停用');
			}else{
				return false;
			}	
		}
		if( empty($_SESSION['admin']['admin_user_state']) ){
			if( empty($return_bool) ){
				throw new error ('该管理员已被封禁');
			}else{
				return false;
			}		
		}
		
		
		//判断传入进来的权限
		if( !empty($authority) ){
			if( !is_string($authority) ){
				if( empty($return_bool) ){
					throw new error ('权限检测异常');
				}else{
					return false;
				}		
			}
			if( empty($_SESSION['admin']['authority_id']) || 
			!in_array($authority, $_SESSION['admin']['authority_id']) ){
				//获取权限名称
				$authority_data = object(parent::TABLE_AUTHORITY)->find($authority);
				$error_message = "权限不足！未知权限";
				if( isset($authority_data['authority_name']) ){
					$error_message = "权限不足！没有 “".$authority_data['authority_name']."” 权限";
				}
				if( empty($return_bool) ){
					throw new error ($error_message);
				}else{
					return false;
				}	
			}
		}
		
		
		return true;
	}		
	
	
	
	
	/*管理员角色*/
	const AUTHORITY_ADMIN_ADD = "admin_add";//添加权限
	const AUTHORITY_ADMIN_READ = "admin_read";//读取权限
	const AUTHORITY_ADMIN_EDIT = "admin_edit";//编辑权限
	const AUTHORITY_ADMIN_REMOVE = "admin_remove";//删除权限
	
	
	
	
	/*管理人员*/
	const AUTHORITY_ADMIN_USER_ADD = "admin_user_add";//添加权限
	const AUTHORITY_ADMIN_USER_READ = "admin_user_read";//读取权限
	const AUTHORITY_ADMIN_USER_EDIT = "admin_user_edit";//编辑权限
	const AUTHORITY_ADMIN_USER_REMOVE = "admin_user_remove";//删除权限
	
	
	const AUTHORITY_LOG_READ = "admin_log_read";//读取权限

	const AUTHORITY_ADMIN_USER_ADD_SOCKET = "admin_user_add_socket";//更新创始人库存
	
}
?>