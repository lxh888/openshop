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



namespace eapie\source\request\application;
use eapie\main;
use eapie\error;
class admin_slideshow extends \eapie\source\request\application {
	
	
		
	/**
	 * 获取轮播图的模块选项列表
	 * 
	 * APPLICATIONADMINSLIDESHOWMODULEOPTION
	 * {"class":"application/admin_slideshow","method":"api_module_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_module_option($data = array()){
		object(parent::REQUEST_ADMIN)->check();
		return object(parent::TABLE_SLIDESHOW)->get_module();
	}
	
	
	
	
	
	/**
	 * 添加轮播图
	 * 
	 * APPLICATIONADMINSLIDESHOWADD
	 * {"class":"application/admin_slideshow","method":"api_add"}
	 * 
	 * 
	 * @param	array	$data
	 * @return	string
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_SLIDESHOW_ADD);
		object(parent::ERROR)->check($data, 'slideshow_name', parent::TABLE_SLIDESHOW, array('args', 'length'));
		object(parent::ERROR)->check($data, 'slideshow_info', parent::TABLE_SLIDESHOW, array('args'));
		object(parent::ERROR)->check($data, 'slideshow_module', parent::TABLE_SLIDESHOW, array('args'));
		object(parent::ERROR)->check($data, 'slideshow_label', parent::TABLE_SLIDESHOW, array('args'));
		object(parent::ERROR)->check($data, 'slideshow_comment', parent::TABLE_SLIDESHOW, array('args'));
		if( isset($data['slideshow_json']) && $data['slideshow_json'] != "" )
		object(parent::ERROR)->check($data, 'slideshow_json', parent::TABLE_SLIDESHOW, array('args'));
		object(parent::ERROR)->check($data, 'slideshow_sort', parent::TABLE_SLIDESHOW, array('args'));
		object(parent::ERROR)->check($data, 'slideshow_state', parent::TABLE_SLIDESHOW, array('args'));
		$qiniu_image = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
		
		//白名单
        $whitelist = array(
        	'slideshow_module',
            'slideshow_name',
            'slideshow_info',
            'slideshow_label',
            'slideshow_comment',
            'slideshow_json',
            'slideshow_sort',
            'slideshow_state',
        );
        $insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//格式化数据
        $insert_data['slideshow_id'] = object(parent::TABLE_SLIDESHOW)->get_unique_id();
		$insert_data['image_id'] = $qiniu_image['image_id'];
		$insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['slideshow_update_time'] = time();
        $insert_data['slideshow_insert_time'] = time();
		
		//插入数据，记录日志
        if (object(parent::TABLE_SLIDESHOW)->insert($insert_data)) {
        	
            object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
            return $insert_data['slideshow_id'];
        } else {
        	object(parent::REQUEST_APPLICATION)->qiniu_image_remove($qiniu_image);
            throw new error('添加失败');
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
	 * APPLICATIONADMINSLIDESHOWLIST
	 * {"class":"application/admin_slideshow","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_SLIDESHOW_READ);
		
		//查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );
		
		//排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'id_desc' => array('slideshow_id', true),
            'id_asc' => array('slideshow_id', false),
            'name_desc' => array('slideshow_name', true),
            'name_asc' => array('slideshow_name', false),
            'state_desc' => array('slideshow_state', true),
            'state_asc' => array('slideshow_state', false),
            'insert_time_desc' => array('slideshow_insert_time', true),
            'insert_time_asc' => array('slideshow_insert_time', false),
            'update_time_desc' => array('slideshow_update_time', true),
            'update_time_asc' => array('slideshow_update_time', false),
            
            'sort_desc' => array('slideshow_sort', true),
            'sort_asc' => array('slideshow_sort', false),
            
			'module_desc' => array('slideshow_module', true),
			'module_asc' => array('slideshow_module', false),
			
            'label_desc' => array('slideshow_label', true),
            'label_asc' => array('slideshow_label', false),
        ));
		
		//避免排序重复
        $config["orderby"][] = array('slideshow_id', false);
		
		//搜索
        if( !empty($data['search']) ){
        	if (isset($data['search']['slideshow_id']) && is_string($data['search']['slideshow_id'])) {
                $config['where'][] = array('[and] s.slideshow_id=[+]', $data['search']['slideshow_id']);
            }

        	if (isset($data['search']['slideshow_name']) && is_string($data['search']['slideshow_name'])) {
                $config['where'][] = array('[and] s.slideshow_name LIKE "%[-]%"', $data['search']['slideshow_name']);
            }
			if( isset($data['search']['slideshow_module']) && is_string($data['search']['slideshow_module']) ){
				$config["where"][] = array('[and] s.slideshow_module=[+]', $data['search']['slideshow_module']);
			}
			if (isset($data['search']['slideshow_label']) && is_string($data['search']['slideshow_label'])) {
                $config['where'][] = array('[and] s.slideshow_label=[+]', $data['search']['slideshow_label']);
            }
			
			if( isset($data['search']['slideshow_state']) && 
			(is_string($data['search']['slideshow_state']) || is_numeric($data['search']['slideshow_state'])) &&
			in_array($data['search']['slideshow_state'], array("0", "1")) ){
				$config["where"][] = array('[and] s.slideshow_state=[+]', $data['search']['slideshow_state']);
				}
        }

        //查询数据
        return object(parent::TABLE_SLIDESHOW)->select_page($config);
	}
	
	
	/**
	 * 删除
	 * 
	 * APPLICATIONADMINSLIDESHOWREMOVE
	 * {"class":"application/admin_slideshow","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	string
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_SLIDESHOW_REMOVE);
        //校验数据
        object(parent::ERROR)->check($data, 'slideshow_id', parent::TABLE_SLIDESHOW, array('args'));
        //查询旧数据
        $original = object(parent::TABLE_SLIDESHOW)->find($data['slideshow_id']);
        if (empty($original)) throw new error('数据不存在');
		
        //删除数据，记录日志
        if ( object(parent::TABLE_SLIDESHOW)->remove($original['slideshow_id']) ) {
            //logo存在，那么要删除旧图片
            if( !empty($original["image_id"]) ){
            	object(parent::REQUEST_APPLICATION)->qiniu_image_remove(array( "image_id" => $original["image_id"] ));
            }
            object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
            return $data['slideshow_id'];
        } else {
            throw new error('删除失败');
        }
		
	}
	
	
	
	
	/**
	 * 检查编辑的权限
	 * 
	 * APPLICATIONADMINSLIDESHOWEDITCHECK
	 * {"class":"application/admin_slideshow","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_SLIDESHOW_EDIT);
		return true;
	}
	
	
	
	
	/**
	 * 编辑
	 * 
	 * APPLICATIONADMINSLIDESHOWEDIT
	 * {"class":"application/admin_slideshow","method":"api_edit"}
	 * 
	 * @param	array		$data
	 * @return	string
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_SLIDESHOW_EDIT);
        //校验数据
        object(parent::ERROR)->check($data, 'slideshow_id', parent::TABLE_SLIDESHOW, array('args'));
        if( isset($data['slideshow_name']) )
		object(parent::ERROR)->check($data, 'slideshow_name', parent::TABLE_SLIDESHOW, array('args', 'length'));
		if( isset($data['slideshow_info']) )
		object(parent::ERROR)->check($data, 'slideshow_info', parent::TABLE_SLIDESHOW, array('args'));
		if( isset($data['slideshow_module']) )
		object(parent::ERROR)->check($data, 'slideshow_module', parent::TABLE_SLIDESHOW, array('args'));
		if( isset($data['slideshow_label']) )
		object(parent::ERROR)->check($data, 'slideshow_label', parent::TABLE_SLIDESHOW, array('args'));
		if( isset($data['slideshow_comment']) )
		object(parent::ERROR)->check($data, 'slideshow_comment', parent::TABLE_SLIDESHOW, array('args'));
		if( isset($data['slideshow_json']) && $data['slideshow_json'] != "" )
		object(parent::ERROR)->check($data, 'slideshow_json', parent::TABLE_SLIDESHOW, array('args'));
		if( isset($data['slideshow_sort']) )
		object(parent::ERROR)->check($data, 'slideshow_sort', parent::TABLE_SLIDESHOW, array('args'));
		if( isset($data['slideshow_state']) )
		object(parent::ERROR)->check($data, 'slideshow_state', parent::TABLE_SLIDESHOW, array('args'));
		
		//查询原始数据
        $original = object(parent::TABLE_SLIDESHOW)->find($data['slideshow_id']);
        if (empty($original)) throw new error('ID有误，数据不存在');
		
		//白名单
        $whitelist = array(
        	'slideshow_module',
            'slideshow_name',
            'slideshow_info',
            'slideshow_label',
            'slideshow_comment',
            'slideshow_json',
            'slideshow_sort',
            'slideshow_state',
        );
        $update_data = cmd(array($data, $whitelist), 'arr whitelist');
		//过滤不需要更新的数据
		if( !empty($update_data) ){
			foreach($update_data as $k => &$v) {
	            if(isset($original[$k]) && $original[$k] == $v){
	            	unset($update_data[$k]);
	            }
	        }
		}
        
        if ( empty($update_data) ) throw new error('没有需要更新的数据');
		
		//格式化数据
        $update_data['slideshow_update_time'] = time();
		//更新数据，记录日志
        if (object(parent::TABLE_SLIDESHOW)->update(array(array('slideshow_id=[+]', $data['slideshow_id'])), $update_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
            return $data['slideshow_id'];
        } else {
            throw new error('操作失败');
        }
		
		
	}
	
	
	
	
	
	
}
?>