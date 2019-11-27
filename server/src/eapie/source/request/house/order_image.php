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

//楼盘订单图片
class order_image extends \eapie\source\request\house
{

    /**
     * 上传图片
     *
     * api: HOUSEORDERIMAGESELFUPLOAD
     * req: {
     *  order_id      [str] [可选] [订单ID]
     *  type          [str] [必填] [图片类型]
     * }
     * @param  array  $input 请求参数
     * @return string 图片ID
     */
    public function api_self_upload($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测参数
        object(parent::ERROR)->check($input, 'order_id', parent::TABLE_HOUSE_ORDER, array('args', 'exist'), 'house_order_id');
        object(parent::ERROR)->check($input, 'type', parent::TABLE_HOUSE_ORDER_IMAGE, array('args'), 'house_order_image_type');

        //上传图片
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();

        //插入数据
        $insert_data['house_order_image_id'] = object(parent::TABLE_HOUSE_ORDER_IMAGE)->get_unique_id();
        $insert_data['image_id'] = $response['image_id'];
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['house_order_id'] = $input['order_id'];
        $insert_data['house_order_image_type'] = $input['type'];
        $insert_data['house_order_image_time'] = time();

        if (object(parent::TABLE_HOUSE_ORDER_IMAGE)->insert($insert_data)) {
            //更新订单状态为编辑中
            object(parent::TABLE_HOUSE_ORDER)->update(
                array(array('house_order_id=[+]', $input['order_id'])),
                array(
                    'house_order_state' => 2,
                    'house_order_update_time' => time()
                )
            );

            return $response['image_id'];
        } else {
            //删除图片
            object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
            throw new error('上传失败');
        }
    }


    /**
     * 查询楼盘订单图片类型
     *
     * api: HOUSEORDERIMAGECONFIGTYPE
     * req: null
     * 
     * @return array
     */
    public function api_config_type()
    {
        return object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_order_image_type'), true);
    }

}