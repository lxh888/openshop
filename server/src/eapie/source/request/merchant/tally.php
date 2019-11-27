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

//线下订单
class tally extends \eapie\source\request\merchant
{


    /**
     * 添加
     *
     * api: MERCHANTTALLYSELFADD
     * req: {
     *  merchant_id     [str] [必填] [商家ID]
     *  goods_name      [str] [必填] [商品名称]
     *  goods_number    [int] [必填] [商品数量]
     *  goods_money     [int] [必填] [商品单价]
     *  client_phone    [str] [必填] [顾客手机号]
     *  comment         [str] [选填] [备注]
     * }
     * 需要上传凭证
     *
     * @return string [订单ID]
     */
    public function api_self_add($input = array())
    {
        //检测输入
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        object(parent::ERROR)->check($input, 'goods_name', parent::TABLE_MERCHANT_TALLY, array('args'), 'merchant_tally_goods_name');
        object(parent::ERROR)->check($input, 'goods_number', parent::TABLE_MERCHANT_TALLY, array('args'), 'merchant_tally_goods_number');
        object(parent::ERROR)->check($input, 'goods_money', parent::TABLE_MERCHANT_TALLY, array('args'), 'merchant_tally_goods_money');
        object(parent::ERROR)->check($input, 'client_phone', parent::TABLE_MERCHANT_TALLY, array('args'), 'merchant_tally_client_phone');
        if (isset($input['comment']))
            object(parent::ERROR)->check($input, 'comment', parent::TABLE_MERCHANT_TALLY, array('args'), 'merchant_tally_comment');

        //检测商家用户状态
        $this->_check_merchant_user_state_($input['merchant_id']);

        //查询编辑中的数据
        $original = object(parent::TABLE_MERCHANT_TALLY)->find_where(array(
            array('user_id = [+]', $_SESSION['user_id']),
            array('[and] merchant_id = [+]', $input['merchant_id']),
            array('[and] merchant_tally_state=2'),
        ));
        if (empty($original))
            throw new error('请先上传凭证图片');

        //更新条件
        $update_where = array(array('merchant_tally_id = [+]', $original['merchant_tally_id']));

        //更新数据
        $update_data = array();
        $update_data['merchant_tally_goods_name'] = $input['goods_name'];
        $update_data['merchant_tally_goods_number'] = $input['goods_number'];
        $update_data['merchant_tally_goods_money'] = $input['goods_money'];
        $update_data['merchant_tally_client_phone'] = $input['client_phone'];
        $update_data['merchant_tally_state'] = 1;
        $update_data['merchant_tally_update_time'] = time();

        //更新数据
        if (object(parent::TABLE_MERCHANT_TALLY)->update($update_where, $update_data)) {
            return $original['merchant_tally_id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 上传收据凭证
     *
     * api: MERCHANTTALLYSELFUPLOADVOUCHER
     * req: 图片
     * 
     * @return string [图片ID]
     */
    public function api_self_upload_voucher($input = array())
    {
        //检测输入
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));

        //检测商家用户状态
        $this->_check_merchant_user_state_($input['merchant_id']);

        //上传图片
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
        $image_id = $response['image_id'];

        //查询编辑中的数据
        $original = object(parent::TABLE_MERCHANT_TALLY)->find_where(array(
            array('user_id = [+]', $_SESSION['user_id']),
            array('[and] merchant_id = [+]', $input['merchant_id']),
            array('[and] merchant_tally_state=2'),
        ));

        //是否存在
        if (empty($original)) {
            $insert_data = array();
            $insert_data['merchant_tally_id'] = object(parent::TABLE_MERCHANT_TALLY)->get_unique_id();
            $insert_data['user_id'] = $_SESSION['user_id'];
            $insert_data['merchant_id'] = $input['merchant_id'];
            $insert_data['merchant_tally_voucher'] = $image_id;
            $insert_data['merchant_tally_state'] = 2;
            $insert_data['merchant_tally_insert_time'] = time();

            //插入数据
            if (object(parent::TABLE_MERCHANT_TALLY)->insert($insert_data)) {
                return $image_id;
            } else {
                throw new error('上传失败');
            }
        } else {
            $update_where = array(array('merchant_tally_id = [+]', $original['merchant_tally_id']));

            $update_data = array();
            $update_data['merchant_tally_voucher'] = $image_id;
            $update_data['merchant_tally_update_time'] = time();

            //更新数据
            if (object(parent::TABLE_MERCHANT_TALLY)->update($update_where, $update_data)) {
                //删除旧图片
                if (!empty($original['merchant_tally_voucher'])) {
                    $response['image_id'] = $original['merchant_tally_voucher'];
                    object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
                }

                return $image_id;
            }
                
        }
    }


}