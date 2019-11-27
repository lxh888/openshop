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

class slideshow extends \eapie\source\request\application
{

    /**
     * 轮播图
     * 
     * api: APPLICATIONSLIDESHOW
     * req: {
     *  page [str] [可选] [页面标识]
     * }
	 * 
	 * [{"module":"模块名称","label":"标签"}]
	 * 
     *
     * @param  array $input 请求参数
     * @return array
     */
    public function api($input = array())
    {
        //查询配置
        $config = array(
           'where' => array(),
           'orderby' => array(),
        );

        //查询字段
        $config['select'] = array(
            'image_id',
            'slideshow_name AS name',
            'slideshow_info AS info',
            'slideshow_module AS module',
            'slideshow_label AS label',
            'slideshow_comment AS comment',
            'slideshow_json AS json',
        );
		
        //查询条件
        $config['where'][] =array('slideshow_state=1');
		
		if( isset($input['module']) && 
        (is_string($input['module']) || is_numeric($input['module'])) ){
        	$config['where'][] =array('[and] slideshow_module=[+]', $input['module']);
        }else{
        	$config['where'][] =array('[and] slideshow_module=""');
        }
		
        if( isset($input['label']) && 
        (is_string($input['label']) || is_numeric($input['label'])) ){
        	$config['where'][] =array('[and] slideshow_label=[+]', $input['label']);
        }else{
        	$config['where'][] =array('[and] slideshow_label=""');
        }

        //查询排序，正序
        $config['orderby'][] =array('slideshow_sort');
        $config['orderby'][] =array('slideshow_id');

        $data = object(parent::TABLE_SLIDESHOW)->select($config);

        return $data;
    }


}