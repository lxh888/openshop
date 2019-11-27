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
class type extends \eapie\source\request\application {
	
	
	// 类别
	
	
    /**
     * 查询分类列表
     *
     * api: APPLICATIONTYPELIST
     * req: {
     *  module      [str] [必填] [模块]
     *  parent_id   [str] [可选] [父级ID，查某类别的子类]
     *  type_name   [str] [可选] [类别名称]
     * }
     *
     * @return array
     */
    public function api_list($input = array()){
    	return array();
		
		
        //检测输入
        object(parent::ERROR)->check($input, 'module', parent::TABLE_TYPE, array('args'), 'type_module');
        if (isset($input['parent_id']))
            object(parent::ERROR)->check($input, 'parent_id', parent::TABLE_TYPE, array('args'), 'type_parent_id');
        if (isset($input['type_name']))
            object(parent::ERROR)->check($input, 'type_name', parent::TABLE_TYPE, array('args'));
        

        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        // 字段
        $config['select'] = array(
            't.type_id',
            't.type_parent_id',
            't.type_logo_image_id AS image_id',
            't.type_name AS name',
            't.type_info AS info',
            't.type_label AS label',
        );

        // 条件
        $config['where'][] = array('t.type_state=1');
        $config['where'][] = array('[and] t.type_module=[+]', $input['module']);

        // 排序
        $config['orderby'] = object(parent::REQUEST)->orderby($input, array(
            'name_desc' => array('t.type_name', true),
            'name_asc' => array('t.type_name', false),
            'module_desc' => array('t.type_module', true),
            'module_asc' => array('t.type_module', false),
            'label_desc' => array('t.type_label', true),
            'label_asc' => array('t.type_label', false),
            'insert_time_desc' => array('t.type_insert_time', true),
            'insert_time_asc' => array('t.type_insert_time', false),
            'update_time_desc' => array('t.type_update_time', true),
            'update_time_asc' => array('t.type_update_time', false),
            'sort_desc' => array('t.type_sort', true),
            'sort_asc' => array('t.type_sort', false),
        ));
        // 默认排序
        if (empty($input['sort']))
            $config['orderby'][] = array('t.type_sort', false);

        $config['orderby'][] = array('t.type_id', false);

        // 筛选——父级
        if (isset($input['parent_id'])) {
            $config['where'][] = array('[and] t.type_parent_id=[+]', $input['parent_id']);
        }

        // 筛选——名称
        if (isset($input['type_name'])) {
            $config['where'][] = array('[and] parent_t.type_name=[+]', $input['type_name']);
        }

        return object(parent::TABLE_TYPE)->select_page($config);
    }


    /**
     * 查询分类选项
     *
     * APPLICATIONTYPEOPTION
     * {"class":"application/type","method":"api_option"}
	 * {"module":"模块","label":"标签","parent_id":"分类父ID","id":"分类ID"}
	 * 
     * @return array
     */
    public function api_option($input = array()){
        //检测输入
        object(parent::ERROR)->check($input, 'module', parent::TABLE_TYPE, array('args'), 'type_module');

        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => array(),
        );

        //字段
        $config['select'] = array(
            'type_id',
            'type_parent_id',
            'type_logo_image_id AS image_id',
            'type_name AS name',
            'type_info AS info',
            'type_label AS label',
            'type_json AS json',
        );

        //条件
        $config['where'][] = array('type_state=1');
        $config['where'][] = array('[and] type_module=[+]', $input['module']);
		
        //排序
        $config['orderby'] = object(parent::REQUEST)->orderby($input, array(
            'name_desc' => array('type_name', true),
            'name_asc' => array('type_name', false),
            'module_desc' => array('type_module', true),
            'module_asc' => array('type_module', false),
            'label_desc' => array('type_label', true),
            'label_asc' => array('type_label', false),
            'insert_time_desc' => array('type_insert_time', true),
            'insert_time_asc' => array('type_insert_time', false),
            'update_time_desc' => array('type_update_time', true),
            'update_time_asc' => array('type_update_time', false),
            'sort_desc' => array('type_sort', true),
            'sort_asc' => array('type_sort', false),
        ));
        //默认排序
        if (empty($input['sort']))	$config['orderby'][] = array('type_sort', false);
        $config['orderby'][] = array('type_id', false);
        
        $parent_config 	= 	$config;
        $son_config 	= 	$config;
        
		//判断是否是子级
		if( isset($input["parent_id"]) && 
		(is_string($input["parent_id"]) || is_numeric($input["parent_id"])) && 
		$input["parent_id"] != "" ){
			$parent_config["where"][] = array('[and] type_parent_id=[+]', $input["parent_id"]);
		}else{
			$parent_config["where"][] = array("[and] type_parent_id=\"\"");
		}
		
		//判断标签
		if( isset($input["label"]) && 
		(is_string($input["label"]) || is_numeric($input["label"])) && 
		$input["label"] != "" ){
			$parent_config["where"][] = array('[and] type_label=[+]', $input["label"]);
		}
		
		//判断ID
		if( isset($input["id"]) && 
		(is_string($input["id"]) || is_numeric($input["id"])) && 
		$input["id"] != "" ){
			$parent_config["where"][] = array('[and] type_id=[+]', $input["id"]);
		}
		// return $parent_config;
        return object(parent::TABLE_TYPE)->select_parent_son_all($parent_config, $son_config);
    }


}