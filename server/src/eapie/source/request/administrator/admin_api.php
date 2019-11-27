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



namespace eapie\source\request\administrator;
use eapie\main;
use eapie\error;
class admin_api extends \eapie\source\request\administrator {
	
	
	
		
		
	/**
	 * 添加接口
	 * 
	 * ADMINISTRATORADMINAPIADD
	 * {"class":"administrator/admin_api","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_ADD);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'api_id', parent::TABLE_API, array('args', 'register_id'));
		object(parent::ERROR)->check($data, 'api_name', parent::TABLE_API, array('args', 'length'));
		object(parent::ERROR)->check($data, 'api_info', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_explain', parent::TABLE_API, array('args'));
		if( isset($data['api_program']) && $data['api_program'] != "" )
		object(parent::ERROR)->check($data, 'api_program', parent::TABLE_API, array('args'));
		if( isset($data['api_request_args']) && $data['api_request_args'] != "" )
		object(parent::ERROR)->check($data, 'api_request_args', parent::TABLE_API, array('args'));
		if( isset($data['api_response_args']) && $data['api_response_args'] != "" )
		object(parent::ERROR)->check($data, 'api_response_args', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_sort', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_state', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'administrator', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_admin', parent::TABLE_API, array('args'));
		if( isset($data['module_id']) && $data['module_id'] != "" )
		object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args', 'exists_id'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'api_id', 
			'api_name', 
			'api_info', 
			'api_program',
			'api_explain',
			'api_request_args',
			'api_response_args',
			'api_sort',
			'api_state',
			'administrator',
			'api_admin',
			'module_id'
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//收集接口版本信息
		$insert_api_version = array();
		if( isset($data['api_version']) 
		&& is_array($data['api_version'])
		&& !empty($data['api_version']) ){
			$api_version_id = array();
			foreach($data['api_version'] as $value){
				if( empty($value['api_version_id']) || 
				(!is_string($value['api_version_id']) && !is_numeric($value['api_version_id']) ) ||
				isset($api_version_id[$value['api_version_id']])){
					continue;
				}
				$api_version_id[$value['api_version_id']] = $value['api_version_id'];
				$insert_api_version[] = array(
					'user_id' => $_SESSION['user_id'],
					'api_version_id' => $value['api_version_id'],//获取接口版本ID
					'api_id' => $insert_data['api_id'],
					'api_version_program' => empty($value['api_version_program'])? "":$value['api_version_program'],
					'api_version_state' => empty($value['api_version_state'])? 0:1,
					'api_version_insert_time' => time(),
					'api_version_update_time' => time(),
				);
			}
		}
		
		//用户ID
		$insert_data['user_id'] = $_SESSION['user_id'];
		//创建时间
		$insert_data['api_insert_time'] = time();
		//更新时间
		$insert_data['api_update_time'] = time();
		if( object(parent::TABLE_API)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $insert_data);
			
			//插入接口版本
			if( !empty($insert_api_version) ){
				object(parent::TABLE_API_VERSION)->insert_batch($insert_api_version);
			}
			
			return $insert_data['api_id'];
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
	 * ADMINISTRATORADMINAPILIST
	 * {"class":"administrator/admin_api","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('api_id', true),
			'id_asc' => array('api_id', false),
			'name_desc' => array('api_name', true),
			'name_asc' => array('api_name', false),
			'insert_time_desc' => array('api_insert_time', true),
			'insert_time_asc' => array('api_insert_time', false),
			'update_time_desc' => array('api_update_time', true),
			'update_time_asc' => array('api_update_time', false),
			
			'module_id_desc' => array('a.module_id', true),
			'module_id_asc' => array('a.module_id', false),
			
			'sort_desc' => array('api_sort', true),
			'sort_asc' => array('api_sort', false),
			
			'state_desc' => array('api_state', true),
			'state_asc' => array('api_state', false),
			
			'administrator_desc' => array('administrator', true),
			'administrator_asc' => array('administrator', false),
			'admin_desc' => array('api_admin', true),
			'admin_asc' => array('api_admin', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('api_id', false);
		$sql_api_version_count = object(parent::TABLE_API_VERSION)->sql_join_count('a');
		
		if(!empty($data['search'])){
			if( isset($data['search']['api_id']) && is_string($data['search']['api_id']) ){
				$config["where"][] = array('[and] a.api_id=[+]', $data['search']['api_id']);
			}
			if( isset($data['search']['like_api_id']) && is_string($data['search']['like_api_id']) ){
				$config['where'][] = array('[and] a.api_id LIKE "%[-]%"', $data['search']['like_api_id']);
			}
			/*if (isset($data['search']['api_name']) && is_string($data['search']['api_name'])) {
                $config['where'][] = array('[and] a.api_name LIKE "%[-]%"', $data['search']['api_name']);
            }*/

			//关键字搜索
			if (isset($data['search']['api_name']) && is_string($data['search']['api_name'])) {
				$keywords = $data['search']['api_name'];
				
				$keywords_where = "a.api_name = '".cmd(array($keywords), 'str addslashes')."' ";
				$config["orderby"][] = array("(a.api_name = '".cmd(array($keywords), 'str addslashes')."' )", true);
				
				$config["orderby"][] = array("(a.api_name like '".cmd(array($keywords), 'str addslashes')."%' )", true);
				$config["orderby"][] = array("(a.api_name like '%".cmd(array($keywords), 'str addslashes')."%' )", true);
				$keywords_where .= " OR a.api_name like '%".cmd(array($keywords), 'str addslashes')."%' ";
				$keywords_length = mb_strlen($keywords);
				$keywords_like = '';
				if( $keywords_length > 1 ){
					for( $i = 0; $i < $keywords_length; $i++ ){
						$str = mb_substr($keywords, $i, 1);
						if( trim($str) == ''){
							continue;//跳出执行下一个
							}
						$keywords_like .= '%'.cmd(array($str), 'str addslashes');
					}
					$keywords_where .= " OR a.api_name like '".$keywords_like."%' ";
					$config["orderby"][] = array("(a.api_name like '".$keywords_like."%')", true);
				}
				
				$config["where"][] = array('[and] ('.$keywords_where.')');
			}
			


			if( isset($data['search']['module_id']) && is_string($data['search']['module_id']) ){
				$config["where"][] = array('[and] a.module_id=[+]', $data['search']['module_id']);
			}

			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1")) ){
				$config["where"][] = array('[and] a.api_state=[+]', $data['search']['state']);
				}

			if( isset($data['search']['administrator']) && 
			(is_string($data['search']['administrator']) || is_numeric($data['search']['administrator'])) &&
			in_array($data['search']['administrator'], array("0", "1")) ){
				$config["where"][] = array('[and] a.administrator=[+]', $data['search']['administrator']);
				}
			
			if( isset($data['search']['admin']) && 
			(is_string($data['search']['admin']) || is_numeric($data['search']['admin'])) &&
			in_array($data['search']['admin'], array("0", "1")) ){
				$config["where"][] = array('[and] a.api_admin=[+]', $data['search']['admin']);
				}
			
			if( isset($data['search']['is_version']) && 
			(is_string($data['search']['is_version']) || is_numeric($data['search']['is_version'])) ){
				if($data['search']['is_version'] == 0){
					$config["where"][] = array('[and] ('.$sql_api_version_count.') = 0');
				}else
				if($data['search']['is_version'] == 1){
					$config["where"][] = array('[and] ('.$sql_api_version_count.') > 0');
				}
			}
		}
		
		
		$config['select'] = array(
			'a.api_id',
			'a.administrator',
			'a.module_id',
			'a.api_admin',
			'a.api_name',
			'a.api_info',
			'a.api_explain',
			'a.api_request_args',
			'a.api_response_args',
			'a.api_sort',
			'a.api_state',
			'a.api_insert_time',
			'a.api_update_time',
			'm.*',
			'('.$sql_api_version_count.') as api_version_count',
		);
		
		return object(parent::TABLE_API)->select_page($config);
	}
	
	
	
	
	/**
	 * 获取一条数据
	 * $data = array(
	 * 	api_id 权限ID
	 * )
	 * 
	 * ADMINISTRATORADMINAPIGET
	 * {"class":"administrator/admin_api","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_READ);
		object(parent::ERROR)->check($data, 'api_id', parent::TABLE_API, array('args'));
		
		$get_data = object(parent::TABLE_API)->find($data['api_id']);
		if( empty($get_data) ){
			throw new error("数据不存在");
		}
		
		if( empty($_SESSION['admin']['authority_id']) || !in_array(parent::AUTHORITY_API_EDIT, $_SESSION['admin']['authority_id']) ){
			if( isset($get_data['api_program']) ){
				unset($get_data['api_program']);
			}
		}else{
			//获取  接口版本
			$api_version = object(parent::TABLE_API_VERSION)->select(array(
				'where' => array(
					array('api_id=[+]', $data['api_id'])
				)
			));
			if( empty($api_version) ){
				$get_data['api_version'] = '';
			}else{
				$get_data['api_version'] = $api_version;
			}
		}
		
		return $get_data;
	}
	
	
	
	
	
				
	/**
	 * 获取一条数据
	 * $data = array(
	 * 	api_id 权限ID
	 * )
	 * 
	 * ADMINISTRATORADMINAPIEDITGET
	 * {"class":"administrator/admin_api","method":"api_edit_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_edit_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		object(parent::ERROR)->check($data, 'api_id', parent::TABLE_API, array('args'));
		
		$get_data = object(parent::TABLE_API)->find($data['api_id']);
		if( empty($get_data) ){
			throw new error("数据不存在");
		}
		
		//获取  接口版本
		$sql_join_list = object(parent::TABLE_APPLICATION_API)->sql_join_list('av');
		$api_version = object(parent::TABLE_API_VERSION)->select(array(
			'where' => array(
				array('av.api_id=[+]', $data['api_id'])
			),
			'orderby' => array(
				array('av.api_version_update_time', true)
			),
			'select' => array(
				'av.*',
				'('.$sql_join_list.') as application_list',
			)
		));
		if( empty($api_version) ){
			$get_data['api_version'] = '';
		}else{
			//获取已经绑定的应用
			foreach($api_version as $k => $v){
				if( !empty($v['application_list']) ){
					$api_version[$k]['application_list'] = explode(',', $v['application_list']);
				}
			}
			$get_data['api_version'] = $api_version;
		}
		
		return $get_data;
	}
	
	
	
	
		
	/**
	 * 检查编辑的权限
	 * 
	 * ADMINISTRATORADMINAPIEDITCHECK
	 * {"class":"administrator/admin_api","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		return true;
	}
	
	
	/**
	 * 编辑权限
	 * 
	 * ADMINISTRATORADMINAPIEDIT
	 * {"class":"administrator/admin_api","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'primary_key', parent::TABLE_API, array('args'));
		/*if( isset($data['api_id']) && $data['api_id'] != $data['primary_key'] )
		object(parent::ERROR)->check($data, 'api_id', parent::TABLE_API, array('args', 'register_id'));*/
		if( isset($data['api_name']) )
		object(parent::ERROR)->check($data, 'api_name', parent::TABLE_API, array('args', 'length'));
		if( isset($data['api_info']) )
		object(parent::ERROR)->check($data, 'api_info', parent::TABLE_API, array('args'));
		if( isset($data['api_explain']) )
		object(parent::ERROR)->check($data, 'api_explain', parent::TABLE_API, array('args'));
		if( isset($data['api_program']) && $data['api_program'] != "" )
		object(parent::ERROR)->check($data, 'api_program', parent::TABLE_API, array('args'));
		if( isset($data['api_request_args']) && $data['api_request_args'] != "" )
		object(parent::ERROR)->check($data, 'api_request_args', parent::TABLE_API, array('args'));
		if( isset($data['api_response_args']) && $data['api_response_args'] != "" )
		object(parent::ERROR)->check($data, 'api_response_args', parent::TABLE_API, array('args'));
		if( isset($data['api_sort']) )
		object(parent::ERROR)->check($data, 'api_sort', parent::TABLE_API, array('args'));
		if( isset($data['api_state']) )
		object(parent::ERROR)->check($data, 'api_state', parent::TABLE_API, array('args'));
		if( isset($data['administrator']) )
		object(parent::ERROR)->check($data, 'administrator', parent::TABLE_API, array('args'));
		if( isset($data['api_admin']) )
		object(parent::ERROR)->check($data, 'api_admin', parent::TABLE_API, array('args'));
		if( isset($data['module_id']) && $data['module_id'] != "" )
		object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args', 'exists_id'));
		
		//获取旧数据
		$api_data = object(parent::TABLE_API)->find($data['primary_key']);
		if( empty($api_data) ){
			throw new error("接口主键有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			//'api_id', 
			'api_name', 
			'api_info', 
			'api_program',
			'api_explain',
			'api_request_args',
			'api_response_args',
			'api_sort',
			'api_state',
			'administrator',
			'api_admin',
			'module_id'
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($api_data[$key]) ){
				if($api_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['api_update_time'] = time();
		if( object(parent::TABLE_API)->update( array(array('api_id=[+]', (string)$data['primary_key'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $update_data);
			return $data['api_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
			
	/**
	 * 删除接口
	 * 
	 * ADMINISTRATORADMINAPIREMOVE
	 * {"class":"administrator/admin_api","method":"api_remove"}
	 * 
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'api_id', parent::TABLE_API, array('args'));
		//查询旧数据
        $original = object(parent::TABLE_API)->find($data['api_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		//判断版本号存在则无法删除
		if( object(parent::TABLE_API_VERSION)->find_exists_api($data['api_id']) ){
			throw new error("该接口下存在版本，请先清理版本才能删除该接口");
		}
		
		if( object(parent::TABLE_API)->remove($data['api_id']) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $original);
			return $data['api_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	/**
	 * 添加接口版本
	 * 
	 * ADMINISTRATORADMINAPIVERSIONADD
	 * {"class":"administrator/admin_api","method":"api_version_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_version_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		//数据检测 
		object(parent::ERROR)->check($data, 'primary_key', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_version_id', parent::TABLE_API_VERSION, array('args'));
		if( isset($data['api_version_program']) && $data['api_version_program'] != '' )
		object(parent::ERROR)->check($data, 'api_version_program', parent::TABLE_API_VERSION, array('args'));
		object(parent::ERROR)->check($data, 'api_version_state', parent::TABLE_API_VERSION, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_API_VERSION)->find_unique($data['primary_key'], $data['api_version_id']);
        if( !empty($original) ) throw new error('该接口版本ID已经存在，请勿重复添加');
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'api_version_id', 
			'api_version_program', 
			'api_version_state', 
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//用户ID
		$insert_data['user_id'] = $_SESSION['user_id'];
		$insert_data['api_id'] = $data['primary_key'];
		$insert_data['api_version_insert_time'] = time();
		$insert_data['api_version_update_time'] = time();
		
		if( object(parent::TABLE_API_VERSION)->insert($insert_data) ){
			//更新时间
			object(parent::TABLE_API)->update( array(array('api_id=[+]', (string)$data['primary_key'])), array(
				'api_update_time' => time()
			));
			
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $insert_data);
			return $insert_data['api_version_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	/**
	 * 编辑接口版本
	 * 
	 * ADMINISTRATORADMINAPIVERSIONEDIT
	 * {"class":"administrator/admin_api","method":"api_version_edit"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_version_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		//数据检测 
		object(parent::ERROR)->check($data, 'primary_key', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_version_id', parent::TABLE_API_VERSION, array('args'));
		if( isset($data['api_version_program']) && $data['api_version_program'] != '' )
		object(parent::ERROR)->check($data, 'api_version_program', parent::TABLE_API_VERSION, array('args'));
		object(parent::ERROR)->check($data, 'api_version_state', parent::TABLE_API_VERSION, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_API_VERSION)->find_unique($data['primary_key'], $data['api_version_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'api_version_program', 
			'api_version_state'
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($original[$key]) ){
				if($original[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['api_version_update_time'] = time();
		if( object(parent::TABLE_API_VERSION)->update( array(
			array('api_id=[+]', (string)$data['primary_key']),
			array('api_version_id=[+]', (string)$data['api_version_id']),
		), $update_data) ){
			//更新时间
			object(parent::TABLE_API)->update( array(array('api_id=[+]', (string)$data['primary_key'])), array(
				'api_update_time' => time()
			));
			
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $update_data);
			return $data['api_version_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 删除接口版本
	 * 
	 * ADMINISTRATORADMINAPIVERSIONREMOVE
	 * {"class":"administrator/admin_api","method":"api_version_remove"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_version_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		//数据检测 
		object(parent::ERROR)->check($data, 'primary_key', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_version_id', parent::TABLE_API_VERSION, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_API_VERSION)->find_unique($data['primary_key'], $data['api_version_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		//删除绑定应用的版本号
		object(parent::TABLE_APPLICATION_API)->delete(array(
			array('api_id=[+]', $data['primary_key']),
			array('[and] api_version_id=[+]', $data['api_version_id'])
		));
				
		if( object(parent::TABLE_API_VERSION)->remove_unique($data['primary_key'], $data['api_version_id']) ){
			//更新时间
			object(parent::TABLE_API)->update( array(array('api_id=[+]', (string)$data['primary_key'])), array(
				'api_update_time' => time()
			));
			
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $original);
			return $data['primary_key'];
		}else{
			throw new error("操作失败");
		}
	}
	
	
	
	
	
	
	/**
	 * 接口版本绑定应用
	 * 
	 * ADMINISTRATORADMINAPIVERSIONAPPLICATION
	 * {"class":"administrator/admin_api","method":"api_version_application"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_version_application($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		//数据检测 
		object(parent::ERROR)->check($data, 'primary_key', parent::TABLE_API, array('args'));
		object(parent::ERROR)->check($data, 'api_version_id', parent::TABLE_API_VERSION, array('args'));
		//查询旧数据
        $original = object(parent::TABLE_API_VERSION)->find_unique($data['primary_key'], $data['api_version_id']);
        if( empty($original) ) throw new error('接口版本数据不存在');
		
		if( !empty($data['application_id']) && is_array($data['application_id'])){
			//清理数据
			$application_id = array();
			foreach($data['application_id'] as $value){
				if(is_string($value) || is_numeric($value)){
					$application_id[] = cmd(array($value), 'str addslashes');
				}
			}
		}
		
		if( !empty($application_id) ){
			//获取IN数据
			$in_string = "\"".implode("\",\"", $application_id)."\"";
			$application_where = array();
			$application_where[] = array("application_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			//获取商品分类数据
			$application_data = object(parent::TABLE_APPLICATION)->select(array("where"=>$application_where));
		}
		
		
		//获取商品的旧分类数据
		$application_api_data = object(parent::TABLE_APPLICATION_API)->select(array(
			"where" => array( 
				array("api_id=[+]", (string)$data['primary_key']),
				array("api_version_id=[+]", (string)$data['api_version_id']),
			)
		));
		
		
		//获得清理数据。1）需要删除的应用接口版本ID   2）需要增加的应用接口版本ID
		$clear_data = array();
		$clear_data["application_id"] = array();
		$clear_data["delete"] = array();
		$clear_data["insert"] = array();
		$clear_data["insert_application_id"] = array();
		//先收集 旧数据，假设都需要被删除
		if( !empty($application_api_data) ){
			foreach($application_api_data as $value){
				$clear_data["delete"][$value["application_id"]] = $value["application_api_id"];
			}
		}
		
		//进行筛选
		if( !empty($application_data) ){
			foreach($application_data as $application_value){
				$clear_data["application_id"][] = $application_value["application_id"];
				if( isset($clear_data["delete"][$application_value["application_id"]]) ){
					unset($clear_data["delete"][$application_value["application_id"]]);
				}else{
					//这里就是需要增加的商品分类
					//$application_value["type_id"];
					$insert_data = array(
						"application_api_id" => object(parent::TABLE_APPLICATION_API)->get_unique_id(),
						"api_id" => $data['primary_key'],
						"api_version_id" => $data['api_version_id'],
						"application_id" => $application_value["application_id"],
						"application_api_time" => time()
					);
					$clear_data["insert"][] = $insert_data;
					$clear_data["insert_application_id"][] = $application_value["application_id"];
				}
			}
		}
		
		if( !empty($clear_data["insert"]) ){
			//删除绑定应用的版本号
			if( !empty($clear_data["insert_application_id"]) ){
				$application_in_string = "\"".implode("\",\"", $clear_data["insert_application_id"])."\"";
				object(parent::TABLE_APPLICATION_API)->delete(array(
					array('api_id=[+]', $data['primary_key']),
					array('[and] application_id IN([-])', $application_in_string, true)
				));
			}
			object(parent::TABLE_APPLICATION_API)->insert_batch($clear_data["insert"]);
		}
		
		//再删除
		if( !empty($clear_data["delete"]) ){
			$in_string = "\"".implode("\",\"", $clear_data["delete"])."\"";
			//是不加单引号并且强制不过滤
			object(parent::TABLE_APPLICATION_API)->delete(array( array("application_api_id IN([-])", $in_string, true) ));
		}
		
		if( empty($clear_data["insert"]) && empty($clear_data["delete"]) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		object(parent::TABLE_API)->update( array(array('api_id=[+]', (string)$data['primary_key'])), array(
			'api_update_time' => time()
		));
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, $clear_data);
		
		return true;
	}
	
	
	
	
	
	
	
	
}
?>