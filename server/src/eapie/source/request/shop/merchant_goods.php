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

/**
 * 商家
 */
class merchant_goods extends \eapie\source\request\shop
{

    /** 
     * 一条数据
     * @author green
     *
     * api: SHOPMERCHANTGOODSGET
     * @param  array  $input [请求参数]
     * @return array
     */
    public function api_get($input = array())
    {
        // 检测身份
        $merchant_id = $this->check_identity();

        object(parent::ERROR)->check($input, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
        $shop_goods = object(parent::TABLE_SHOP_GOODS)->find($input['shop_goods_id']);
        if (empty($shop_goods)) {
            throw new error('商品不存在');
        }
        if ($shop_goods['merchant_id'] != $merchant_id) {
            throw new error('只能操作本商家的商品');
        }

        // 查询附属信息
        $shop_goods = object(parent::TABLE_SHOP_GOODS)->get_additional_data(array($shop_goods));

        return $shop_goods[0];
    }

}