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

//楼盘项目图片
class product_image extends \eapie\source\request\house
{


    /**
     * 上传楼盘图片
     *
     * api: HOUSEPRODUCTIAMAGESELFUPLOAD
     * req: {
     *  product_id    [str] [可选] [楼盘项目ID]
     *  type          [str] [必填] [图片类型]
     * }
     * 
     * @param  array  $input 请求参数
     * @return string 图片ID
     */
    public function api_self_upload($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检测参数
        object(parent::ERROR)->check($input, 'product_id', parent::TABLE_HOUSE_PRODUCT, array('args', 'exist'), 'house_product_id');
        object(parent::ERROR)->check($input, 'type', parent::TABLE_HOUSE_PRODUCT_IMAGE, array('args'), 'house_product_image_type');

        //上传图片
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();

        //插入数据
        $insert_data['house_product_image_id'] = object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->get_unique_id();
        $insert_data['image_id'] = $response['image_id'];
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['house_product_id'] = $input['product_id'];
        $insert_data['house_product_image_type'] = $input['type'];
        $insert_data['house_product_image_time'] = time();

        if (object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->insert($insert_data)) {
            //更新楼盘项目状态为编辑中
            object(parent::TABLE_HOUSE_PRODUCT)->update(
                array(array('house_product_id=[+]', $input['product_id'])),
                array(
                    'house_product_update_time' => time()
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
     * 删除图片
     *
     * api: HOUSEPRODUCTIMAGESELFREMOVE
     * req: {
     *  image_id [str] [必填] [图片ID]
     * }
     * 
     * @return bool
     */
    public function api_self_remove($input = array())
    {
        //检测经纪人状态
        $this->_check_agent_state_($input);

        //检查输入
        object(parent::ERROR)->check($input, 'image_id', parent::TABLE_IMAGE, array('args'));

        //获取旧数据
        $original = object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->find_where(array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] image_id=[+]', $input['image_id']),
        ));
        if (empty($original))
            throw new error('ID有误，数据不存在');

        //获取配置
        $qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('qiniu_access'), true);
        if( empty($qiniu_config) ){
            throw new error('配置异常');
        }

        //删除七牛云图片
        $qiniu_config['key'] = $input['image_id'];
        $qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($qiniu_config);
        if (!empty($qiniu_uptoken['errno']))
            throw new error($qiniu_uptoken['error']);

        //删除图片登记
        object(parent::TABLE_IMAGE)->remove($input['image_id']);
        object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->remove($original['house_product_image_id']);

        return $input['image_id'];
    }


    /**
     * 查询楼盘项目图片类型
     *
     * api: HOUSEPRODUCTIMAGECONFIGTYPE
     * req: null
     * 
     * @return array
     */
    public function api_config_type()
    {
        return object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_product_image_type'), true);
    }


}