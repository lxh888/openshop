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



namespace eapie\source\request\house;

use eapie\main;
use eapie\error;

//楼盘置顶选项
class top_option extends \eapie\source\request\house
{


    /**
     * 查询列表数据
     * 
     * api: HOUSETOPOPTIONLIST
     * req: {
     * 
     * }
     * 
     * @return array
     */
    public function api_list($input = array())
    {
        //查询配置
        $config = array();

        //字段
        $config['select'] = array(
            'house_top_option_id AS id',
            'house_top_option_name AS name',
            'house_top_option_info AS info',
            'house_top_option_month AS month',
            'house_top_option_money AS money',
            'house_top_option_remarks AS remarks',
        );

        //排序
        $config['orderby'] = array(
            array('house_top_option_sort', true),
            array('house_top_option_id', true),
        );

        //查询数据
        $data = object(parent::TABLE_HOUSE_TOP_OPTION)->select_page($config);

        //格式化数据

        return $data;
    }


}