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



namespace eapie\source\request\cms;
use eapie\main;
use eapie\error;
class admin_article extends \eapie\source\request\cms {



	


    /**
     * 添加文章
     * 
     * CMSADMINARTICLEADD
     * {"class":"cms/admin_article","method":"api_add"} 
	 * 
     * @param  array  $input 请求数据
     * @return array         响应数据
     */
    public function api_add($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_ADD);
        //数据检测
        object(parent::ERROR)->check($input, 'cms_article_name', parent::TABLE_CMS_ARTICLE, array('args'));
        object(parent::ERROR)->check($input, 'cms_article_info', parent::TABLE_CMS_ARTICLE, array('args'));
        object(parent::ERROR)->check($input, 'cms_article_sort', parent::TABLE_CMS_ARTICLE, array('args'));
		object(parent::ERROR)->check($input, 'cms_article_state', parent::TABLE_CMS_ARTICLE, array('args'));
		object(parent::ERROR)->check($input, 'cms_article_source', parent::TABLE_CMS_ARTICLE, array('args'));
		object(parent::ERROR)->check($input, 'cms_article_keywords', parent::TABLE_CMS_ARTICLE, array('args'));
		object(parent::ERROR)->check($input, 'cms_article_description', parent::TABLE_CMS_ARTICLE, array('args'));
		
        //白名单
        $whitelist = array(
            'cms_article_name',
            'cms_article_info',
            'cms_article_sort',
            'cms_article_state',
            'cms_article_source',
            'cms_article_keywords',
            'cms_article_description',
        );
        $insert_data = cmd(array($input, $whitelist), 'arr whitelist');

        //格式化数据
        $insert_data['cms_article_id'] = object(parent::TABLE_CMS_ARTICLE)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['cms_article_insert_time'] = time();
        $insert_data['cms_article_update_time'] = time();
        //插入数据
        if (object(parent::TABLE_CMS_ARTICLE)->insert($insert_data)) {
            //记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $insert_data);
            return $insert_data['cms_article_id'];
        } else {
            throw new error('添加失败');
        }

    }




	
		
	/**
	 * 检查编辑文章的权限
	 * 
	 * CMSADMINARTICLEEDITCHECK
	 * {"class":"cms/admin_article","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_EDIT);
		return true;
	}
	
	
	





    /**
     * 编辑文章
     * 
     * CMSADMINARTICLEEDIT
     * {"class":"cms/admin_article","method":"api_edit"} 
     * 
     * @param  array  $input 请求数据
     * @return array         响应数据
     */
    public function api_edit($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_EDIT);

        //检测请求参数
        object(parent::ERROR)->check($input, 'cms_article_id', parent::TABLE_CMS_ARTICLE, array('args'));
        if (isset($input['cms_article_name']))
        object(parent::ERROR)->check($input, 'cms_article_name', parent::TABLE_CMS_ARTICLE, array('args'));
        if (isset($input['cms_article_info']))
        object(parent::ERROR)->check($input, 'cms_article_info', parent::TABLE_CMS_ARTICLE, array('args'));
        if (isset($input['cms_article_content']))
        object(parent::ERROR)->check($input, 'cms_article_content', parent::TABLE_CMS_ARTICLE, array('args'));
        if (isset($input['cms_article_state']))
        object(parent::ERROR)->check($input, 'cms_article_state', parent::TABLE_CMS_ARTICLE, array('args'));
		if (isset($input['cms_article_sort']))
		object(parent::ERROR)->check($input, 'cms_article_sort', parent::TABLE_CMS_ARTICLE, array('args'));
		if (isset($input['cms_article_source']))
		object(parent::ERROR)->check($input, 'cms_article_source', parent::TABLE_CMS_ARTICLE, array('args'));
		if (isset($input['cms_article_keywords']))
		object(parent::ERROR)->check($input, 'cms_article_keywords', parent::TABLE_CMS_ARTICLE, array('args'));
		if (isset($input['cms_article_description']))
		object(parent::ERROR)->check($input, 'cms_article_description', parent::TABLE_CMS_ARTICLE, array('args'));
		
		
        //查询原始数据
        $original = object(parent::TABLE_CMS_ARTICLE)->find($input['cms_article_id']);
        if (empty($original))
            throw new error('ID有误，数据不存在');

        //白名单
        $whitelist = array(
            'cms_article_name',
            'cms_article_info',
            'cms_article_content',
            'cms_article_state',
            'cms_article_sort',
            'cms_article_source',
            'cms_article_keywords',
            'cms_article_description',
        );
        $update_data = cmd(array($input, $whitelist), 'arr whitelist');
		
        //过滤不需要更新的数据
        foreach ($update_data as $k => &$v) {
            if (isset($original[$k]) && $original[$k] == $v)
                unset($update_data[$k]);
        }
        if (empty($update_data))
            throw new error('没有需要更新的数据');

        //格式化数据
        $update_data['cms_article_update_time'] = time();

        //更新数据
        if (object(parent::TABLE_CMS_ARTICLE)->update(array(array('cms_article_id=[+]', $input['cms_article_id'])), $update_data)) {
            // 推送消息
            if (!empty($update_data['cms_article_state'])) {
                $this->_push_to_app($input['cms_article_id']);
            }

            // 记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);

            return $input['cms_article_id'];
        } else {
            throw new error('操作失败');
        }
    }




    /**
     * 查询列表数据
     *
     * CMSADMINARTICLELIST
     * {"class":"cms/admin_article","method":"api_list"}
     * 
     * @param  array  $data 请求数据
     * @return array         响应数据
     */
    public function api_list($data = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_READ);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );

        //排序
        $config['orderby'] = object(parent::REQUEST)->orderby($data, array(
            'name_desc' => array('cms_article_name', true),
            'name_asc' => array('cms_article_name', false),
            'state_desc' => array('cms_article_state', true),
            'state_asc' => array('cms_article_state', false),
            
			'sort_desc' => array('cms_article_sort', true),
            'sort_asc' => array('cms_article_sort', false),
			
            'insert_time_desc' => array('cms_article_insert_time', true),
            'insert_time_asc' => array('cms_article_insert_time', false),
            'update_time_desc' => array('cms_article_update_time', true),
            'update_time_asc' => array('cms_article_update_time', false),
            
            'click_desc' => array('cms_article_click', true),
            'click_asc' => array('cms_article_click', false),
        ));
        $config['orderby'][] = array('cms_article_id', false);

		$config["where"][] = array('[and] ca.cms_article_trash=0');
		if(!empty($data['search'])){
			
			if( isset($data['search']['cms_article_id']) && is_string($data['search']['cms_article_id']) ){
				$config["where"][] = array('[and] ca.cms_article_id=[+]', $data['search']['cms_article_id']);
			}
			
			if (isset($data['search']['cms_article_name']) && is_string($data['search']['cms_article_name'])) {
                $config['where'][] = array('[and] ca.cms_article_name LIKE "%[-]%"', $data['search']['cms_article_name']);
            }
			
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2", "3")) ){
				$config["where"][] = array('[and] ca.cms_article_state=[+]', $data['search']['state']);
				}
		}
		
        //查询数据
        $data = object(parent::TABLE_CMS_ARTICLE)->select_page($config);
		if( !empty($data["data"]) ){
			$data["data"] = object(parent::TABLE_CMS_ARTICLE)->get_additional_data($data["data"]);
		}
        return $data;
    }





    /**
     * 查询一条数据
     *
     * CMSADMINARTICLEGET
	 * {"class":"cms/admin_article","method":"api_get"}
     * $input = array(
	 * 		cms_article_id [str] [必填] [唯一ID]
	 * )
     * 
     * @param  array  $input 请求数据
     * @return array         响应数据
     */
    public function api_get($input = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_READ);
        //检测请求参数
        object(parent::ERROR)->check($input, 'cms_article_id', parent::TABLE_CMS_ARTICLE, array('args'));
        //查询数据
        $get_data = object(parent::TABLE_CMS_ARTICLE)->find($input['cms_article_id']);
        if( !empty($get_data) ){
			$data = array($get_data);
			$data = object(parent::TABLE_CMS_ARTICLE)->get_additional_data($data);
			$get_data = $data[0];
		}
		return $get_data;
    }






	
	/**
	 * 逻辑回收文章
	 * 
	 * CMSADMINARTICLETRASH
	 * {"class":"cms/admin_article","method":"api_trash"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_TRASH);
		object(parent::ERROR)->check($data, 'cms_article_id', parent::TABLE_CMS_ARTICLE, array('args'));
		
		//获取旧数据
		$cms_article_data = object(parent::TABLE_CMS_ARTICLE)->find($data['cms_article_id']);
		if( empty($cms_article_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($cms_article_data["cms_article_trash"]) ){
			throw new error("该文章已经在回收站");
		}
		
		//更新回收状态
		$update_data["cms_article_trash"] = 1;
		$update_data['cms_article_trash_time'] = time();
		if( object(parent::TABLE_CMS_ARTICLE)->update( array(array('cms_article_id=[+]', (string)$data['cms_article_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['cms_article_id'];
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
	 * CMSADMINARTICLETRASHLIST
	 * {"class":"cms/admin_article","method":"api_trash_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_trash_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_TRASH_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('cms_article_name', true),
			'name_asc' => array('cms_article_name', false),
			'state_desc' => array('cms_article_state', true),
			'state_asc' => array('cms_article_state', false),
			'trash_time_desc' => array('cms_article_trash_time', true),
			'trash_time_asc' => array('cms_article_trash_time', false),
			'update_time_desc' => array('cms_article_update_time', true),
			'update_time_asc' => array('cms_article_update_time', false),
			'sort_desc' => array('cms_article_sort', true),
			'sort_asc' => array('cms_article_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('cms_article_id', false);
		
		$config["where"][] = array('[and] ca.cms_article_trash=1');
		if(!empty($data['search'])){
			if( isset($data['search']['cms_article_id']) && is_string($data['search']['cms_article_id']) ){
				$config["where"][] = array('[and] ca.cms_article_id=[+]', $data['search']['cms_article_id']);
			}
			
			if (isset($data['search']['cms_article_name']) && is_string($data['search']['cms_article_name'])) {
                $config['where'][] = array('[and] ca.cms_article_name LIKE "%[-]%"', $data['search']['cms_article_name']);
            }
		}
		
		return object(parent::TABLE_CMS_ARTICLE)->select_page($config);
	}
	
	
	
	
	
	/**
	 * 恢复回收文章
	 * 
	 * CMSADMINARTICLETRASHRESTORE
	 * {"class":"cms/admin_article","method":"api_trash_restore"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash_restore($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ARTICLE_TRASH_RESTORE);
		object(parent::ERROR)->check($data, 'cms_article_id', parent::TABLE_CMS_ARTICLE, array('args'));
		
		//获取旧数据
		$cms_article_data = object(parent::TABLE_CMS_ARTICLE)->find($data['cms_article_id']);
		if( empty($cms_article_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( empty($cms_article_data["cms_article_trash"]) ){
			throw new error("该文章不在回收站");
		}
		
		//更新回收状态
		$update_data["cms_article_trash"] = 0;
		$update_data["cms_article_update_time"] = time();
		if( object(parent::TABLE_CMS_ARTICLE)->update( array(array('cms_article_id=[+]', (string)$data['cms_article_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['cms_article_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	/**
     * 推送到APP
     * @param  string $cms_article_id [文章ID]
     * @return bool
     */
	private function _push_to_app($cms_article_id = '')
    {
        // 查询文章信息
        $cms_article = object(parent::TABLE_CMS_ARTICLE)->find($cms_article_id);
        if (empty($cms_article) || $cms_article['cms_article_state'] != 1 || $cms_article['cms_article_trash'] == 1) {
            return false;
        }

        // 查询推送配置
        $getui_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('getui_access'), true);
        if (empty($getui_access['state'])) {
            return false;
        }

        $getui = object('eapie\source\plugin\getui\getui')->init($getui_access);
        if (!empty($getui['errno'])) {
            throw new error($getui['error']);
        }
        $template = array(
            'logo' => 'logo.png',
            'title' => $cms_article['cms_article_name'],
            'text' => $cms_article['cms_article_info'],
            'transmission_content' => '!@#$%^&*()_+',
            'ring' => true,
            'vibrate' => true,
            'clearable' => true,
        );
        $getui_res = object('eapie\source\plugin\getui\getui')->push_message_to_app($template);
        if (!empty($getui_res['errno'])) {
            throw new error($getui_res['error']);
        }

        return true;
    }
}