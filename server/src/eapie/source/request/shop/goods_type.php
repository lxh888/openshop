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



namespace eapie\source\request\shop;

use eapie\main;
use eapie\error;

class goods_type extends \eapie\source\request\shop
{

    /**
     * 查询类别列表
     *
     * api: SHOPGOODSTYPELIST
     * req: {
     *  parent_id   [str] [可选] [列表的父级ID，默认顶级]
     * }
     * res: {
     *  id          [str] [类别ID]
     *  parent_id   [str] [类别父级ID]
     *  image_id    [str] [类别图片ID]
     *  name        [str] [类别名称]
     * }
     * 
     * @param  array  $input 请求参数
     * @return array         响应数据
     */
    public function api_list($input = array())
    {
        //查询数据
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => array(),
        );

        $config['select'] = array(
            'type_id AS id',
            'type_parent_id AS parent_id',
            'type_logo_image_id AS image_id',
            'type_name AS name',
        );

        $config['where'][] = array('type_state=1');
		$config['where'][] = array('type_module="shop_goods"');
        if (isset($input['parent_id']) && is_string($input['parent_id']))
            $config['where'][] = array('[and] type_parent_id=[+]', $input['parent_id']);
        else
            $config['where'][] = array('[and] type_parent_id=""');

        $config['orderby'][] = array('type_sort', false);

        //查询数据
        $data = object(parent::TABLE_TYPE)->select_parent_son_all($config);

        $output = array();
        return $data;
    }

}