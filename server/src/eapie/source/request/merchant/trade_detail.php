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



namespace eapie\source\request\merchant;

use eapie\main;
use eapie\error;

class trade_detail extends \eapie\source\request\merchant
{

    /**
     * 查询商家积分交易明细数据列表
     *
     * api: MERCHANTTRADEDETAILLIST
     * req: {
     *  merchant_id [str] [必填] [商家ID]
     *  size        [int] [可选] [每页的条数]
     *  page        [int] [可选] [当前页数，如果是等于 all 那么则查询所有]
     *  start       [int] [可选] [开始的位置，如果存在，则page无效]
     * }
     *
     * 若商家ID合法，用户合法，同时查询商家交易明细
     *
     * @param [arr] $array [请求参数]
     * @return array
     */
    public function api_list($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测权限
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        if (!object(parent::TABLE_MERCHANT_USER)->check_exist($_SESSION['user_id'], $input['merchant_id'], true))
            throw new error('权限不足');

        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        //查询字段
        $config['select'] = array(
            'mc.merchant_credit_type AS type',
            'mc.merchant_credit_plus AS plus',
            'mc.merchant_credit_minus AS minus',
            'mc.merchant_credit_value AS value',
            'mc.merchant_credit_time AS time',
            'o.order_comment AS comment',
        );

        //排序
        $config['orderby'][] = array('mc.merchant_credit_time', true);
        $config['orderby'][] = array('mc.merchant_credit_id', true);

        //查询条件
        $config['where'][] = array('mc.merchant_id=[+]', $input['merchant_id']);

        //查询数据
        $data = object(parent::TABLE_MERCHANT_CREDIT)->select_order_page($config);

        //格式化数据
        $trade_type = object(parent::TABLE_ORDER)->get_type();
        $trade_method = object(parent::TABLE_ORDER)->get_method();
        foreach ($data['data'] as &$v) {
            //判断符号
            if ($v['plus'] > 0) {
                $v['sign']  = '+';
                $v['trade'] = $v['plus'];
            } else {
                $v['sign']  = '-';
                $v['trade'] = $v['minus'];
            }
            $v['type'] = $trade_type[$v['type']];
            $v['method'] = $trade_method['merchant_credit'];
            $v['time'] = date('Y-m-d H:i:s', $v['time']);

            unset($v['plus'], $v['minus']);
        }

        return $data;
    }

}