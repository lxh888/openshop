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
class article extends \eapie\source\request\cms {
	
	
	//文章
	
	
    /**
     * 查询列表
     *
     * api: CMSARTICLELIST
     * search: {
     *  type_id [str] [可选] [类别ID]
     * }
     * 
     * @return array
     */
    public function api_list($input = array())
    {
        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        //字段
        $config['select'] = array(
            'ca.cms_article_id',
            'ca.cms_article_name as name',
            'ca.cms_article_info as info',
            'ca.cms_article_insert_time as time',
        );

        //排序
        $config['orderby'] = array();
        $config['orderby'][] = array('cms_article_insert_time', true);
        $config['orderby'][] = array('ca.cms_article_id', false);

        //查询条件
        $config['where'][] = array('ca.cms_article_state=1');
		$config['where'][] = array('[and] ca.cms_article_trash=0');//没有被删除的

        //筛选——搜索
        if (!empty($input['search']['keywords']) && is_string($input['search']['keywords'])) {
            $keywords = cmd(array($input['search']['keywords']), 'str addslashes');
            $keywords = "%{$keywords}%";
            $config['where'][] = array('[and] ca.cms_article_name like [+]', $keywords);
        }

        //筛选——类别
        if (!empty($input['search']['type_id']) && is_string($input['search']['type_id'])) {
			//查询类别信息，根据ID
            $data_type = object(parent::TABLE_TYPE)->find($input['search']['type_id']);
            if( $data_type ){
                //是否一级类别
                if (empty($data_type['type_parent_id'])) {
                    $sql_join_article_id = object(parent::TABLE_CMS_ARTICLE_TYPE)->sql_join_type_parent_article_id($data_type['type_id']);
                } else {
                    $sql_join_article_id = object(parent::TABLE_CMS_ARTICLE_TYPE)->sql_join_type_son_article_id($data_type['type_id']);
                }
                $config['where'][] = array('[and] ca.cms_article_id in ([-])', $sql_join_article_id, true);
            }
        }
		
        //查询数据
        $data = object(parent::TABLE_CMS_ARTICLE)->select_page($config);
		if( empty($data["data"]) ){
			return $data;
		}
		
		$data["data"] = object(parent::TABLE_CMS_ARTICLE)->get_additional_data($data["data"]);
        //格式化数据
        foreach ($data['data'] as &$v) {
        	$v['type'] = $v['cms_article_type'];
			unset($v['cms_article_type']);
			$v['image_main'] = $v['cms_article_image_main'];
			unset($v['cms_article_image_main']);
			
			$v['id'] = $v['cms_article_id'];
			unset($v['cms_article_id']);
			
			$v['timestamp'] = $v['time'];
            $v['time'] = date('Y.m.d', $v['time']);
        }

        return $data;
    }


    /**
     * 查询详情
     * 
     * api: CMSARTICLEGET
     * req: {
     *  id  [str] [必填] [文章ID]
     * }
     * 
     * @param  array  $input 请求数据
     * @return array         响应数据
     */
    public function api_get($input = array()){
        //校验数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_CMS_ARTICLE, array('args'), 'cms_article_id');

        //查询数据
        $data = object(parent::TABLE_CMS_ARTICLE)->find($input['id']);
        if (empty($data) || $data['cms_article_state'] != 1 || $data['cms_article_trash'] == 1)
            throw new error('文章不存在，或者没有发布');

        //替换键名
        $replace = array(
            'cms_article_id' => 'id',
            'cms_article_name' => 'name',
            'cms_article_info' => 'info',
            'cms_article_content' => 'content',
            'cms_article_click' => 'click',
            'cms_article_insert_time' => 'time'
        );
        $data = cmd(array($data, $replace), 'arr key_replace');
        //白名单
        $whitelist = array(
            'id',
            'name',
            'info',
            'content',
            'click',
            'time'
        );
        $data = cmd(array($data, $whitelist), 'arr whitelist');

        //格式化数据
        $data['time'] = date('Y.m.d', $data['time']);

        return $data;
    }


}