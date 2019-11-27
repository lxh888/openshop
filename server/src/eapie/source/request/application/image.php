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

class image extends \eapie\source\request\application
{

    /**
     * 获取图片上传的token
     *
     * api: APPLICATIONSELFIMAGEQINIUUPTOKEN
     * req: {
     *  image_name    [str] [必填] [文件的原名字]
     *  image_size    [int] [必填] [文件的大小]
     *  image_type    [str] [必填] [文件的类型]
     *  image_format  [str] [必填] [文件的后缀]
     * }
     * 
     * @param   [arr] $input [请求参数]
     * @return  {
     *  qiniu_uptoken [str] [七牛的上传token]
     *  image_id      [str] [图片ID]
     * }
     */
    public function api_self_qiniu_uptoken($input = array())
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();

        //校验数据
        object(parent::ERROR)->check($input, 'image_name', parent::TABLE_IMAGE, array('format', 'length'));
        object(parent::ERROR)->check($input, 'image_type', parent::TABLE_IMAGE, array('args', 'mime_limit'));
        object(parent::ERROR)->check($input, 'image_size', parent::TABLE_IMAGE, array('args'));
        object(parent::ERROR)->check($input, 'image_format', parent::TABLE_IMAGE, array('args'));

        //获取配置
        $qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('qiniu_access'), true);
        if (empty($qiniu_config))
            throw new error('配置异常');

        //白名单
        $whitelist = array(
            'image_name', 
            'image_type', 
            'image_size', 
            'image_format',
        );
        $insert_data = cmd(array($input, $whitelist), 'arr whitelist');

        //格式化数据
        $insert_data['image_id'] = cmd(array(22), 'random autoincrement');
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['image_path'] = $qiniu_config['bucket'];
        $insert_data['image_state'] = 0;
        $insert_data['image_insert_time'] = time();
        $insert_data['image_update_time'] = time();

        //生成一个图片表数据
        if (!object(parent::TABLE_IMAGE)->insert($insert_data))
            throw new error('图片登记失败');

        //根据文件大小，设置有效时间
        $qiniu_config['expires'] = 3600; //一个小时
        $qiniu_config['policy'] = array(
            'returnBody' => '{"key":"$(key)","hash":"$(etag)","type":$(mimeType),"size":$(fsize),"name":$(fname),"bucket":"$(bucket)","width":"$(imageInfo.width)","height":"$(imageInfo.height)","format":"$(imageInfo.format)"}',
            //限定用户上传的文件类型。
            //'mimeLimit' =>'image/*'
        );

        //获取上传token
        $qiniu_uptoken = object(parent::PLUGIN_QINIU)->uptoken($qiniu_config);
        if( !empty($qiniu_uptoken["errno"]) ){
            //删除文件
            object(parent::TABLE_IMAGE)->remove($insert_data['image_id']);
            throw new error($qiniu_uptoken['error']);
        }

        return array(
            'qiniu_uptoken' => $qiniu_uptoken["data"],
            'image_id' => $insert_data['image_id']
        );
    }


    /**
     * 上传图片到七牛云
     * api: APPLICATIONIMAGEUPLOADQINIU
     * 默认入口：{"class":"application/image","method":"api_upload_qiniu"}
     */
    public function api_upload_qiniu( $data=array() )
    {
        
        //获取七牛云上传配置
        $qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('qiniu_access'), true);
        if(empty($qiniu_config['id']) || empty($qiniu_config['secret']) || empty($qiniu_config['bucket'])){
            throw new error('七牛云配置异常');
        }

        if(!empty($data['image_id'])){
            object(parent::REQUEST_APPLICATION)->qiniu_image_remove($data);
        }
        $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
        return $response;

    }


}