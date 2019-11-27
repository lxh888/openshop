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



namespace eapie\source\request\brand;

use eapie\main;
use eapie\error;

//品牌
class brand extends \eapie\source\request\brand
{


    /**
     * 查询列表数据
     *
     * api : BRANDLIST
     * req: {
     *  list接口通用参数
     * }
     * 
     * @return array
     */
    public function api_list($input = array())
    {
        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //字段
        $config['select'] = array(
            'brand_id AS id',
            'brand_logo_image_id AS image_id', 
            'brand_name AS name',
        );

        //排序
        $config['orderby'][] = array('brand_name', true);
        $config['orderby'][] = array('brand_id', true);

        //筛选
        $config['where'][] = array('brand_state=1');

        //查询数据
        $data = object(parent::TABLE_BRAND)->select_page($config);

        return $data;
    }


    /**
     * 查询详情
     *
     * api: BRANDGET
     * req: {
     *  id  [str] [必填] [品牌ID]
     * }
     * 
     * @return array
     */
    public function api_get($input = array())
    {
        //检测数据
        object(parent::ERROR)->check($input, 'id', parent::TABLE_BRAND, array('args'), 'brand_id');

        //查询数据
        $data = object(parent::TABLE_BRAND)->find($input['id']);
        if (empty($data))
            throw new error('数据不存在');

        return $data;
    }


}