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
class admin extends \eapie\source\request\brand
{


    /**
     * 添加
     * 
     * api: BRANDADMINADD
     * req: {
     *  brand_name  [str] [必填] [200] [名称]
     *  brand_info  [str] [必填] [65535] [简介]
     *  brand_sort  [int] [必填] [20] [排序]
     *  brand_state [int] [必填] [1] [状态]
     *  brand_json  [str] [必填] [65535] [json数据]
     * }
     * 
     * @return string 品牌ID
     */
    public function api_add($data = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_BRAND_ADD);
        
        //数据检测 
        object(parent::ERROR)->check($data, 'brand_name', parent::TABLE_BRAND, array('args'));
        object(parent::ERROR)->check($data, 'brand_info', parent::TABLE_BRAND, array('args'));
        object(parent::ERROR)->check($data, 'brand_sort', parent::TABLE_BRAND, array('args'));
        object(parent::ERROR)->check($data, 'brand_state', parent::TABLE_BRAND, array('args'));
        object(parent::ERROR)->check($data, 'brand_json', parent::TABLE_BRAND, array('args'));

        //白名单
        $whitelist = array(
            'brand_name', 
            'brand_info',
            'brand_json',
            'brand_sort',
            'brand_state',
        );
        $insert_data = cmd(array($data, $whitelist), 'arr whitelist');

        //是否上传图片
        if (!empty($_FILES)) {
            $qiniu_image = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
            $insert_data['brand_logo_image_id'] = $qiniu_image['image_id'];
        }

        //格式化数据
        $insert_data['brand_id'] = object(parent::TABLE_BRAND)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['brand_insert_time'] = time();

        //插入数据
        if (object(parent::TABLE_BRAND)->insert($insert_data)) {
            //记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);

            return $insert_data['brand_id'];
        } else {
            if (!empty($qiniu_image))
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove($qiniu_image);

            throw new error('操作失败');
        }
    }


    /**
     * 删除
     * 
     * api: BRANDADMINREMOVE
     * req: {
     *  brand_id 品牌ID
     * }
     * 
     * @return [type]       [description]
     */
    public function api_remove($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_BRAND_REMOVE);

        //检测输入
        object(parent::ERROR)->check($input, 'brand_id', parent::TABLE_BRAND, array('args'));

        //查询旧数据
        $original = object(parent::TABLE_BRAND)->find($input['brand_id']);
        if (empty($original))
            throw new error('数据不存在');

        //删除数据，记录日志
        if (object(parent::TABLE_BRAND)->remove($input['brand_id'])) {
            //删除图片logo
            if (!empty($original['brand_logo_image_id']))
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove(array('image_id' => $original['brand_logo_image_id']));

            object(parent::TABLE_ADMIN_LOG)->insert($input, $original);

            return $input['brand_id'];
        } else {
            throw new error('删除失败');
        }
    }


    /**
     * 编辑
     * 
     * api: BRANDADMINEDIT
     * req: {
     *  brand_id    [str] [必填] [品牌ID]
     *  brand_name  [str] [可选] [名称]
     *  brand_info  [str] [可选] [简介]
     *  brand_sort  [int] [可选] [排序]
     *  brand_state [int] [可选] [状态]
     *  brand_json  [str] [可选] [json数据]
     * }
     * 
     * @return string 品牌ID
     */
    public function api_edit($data = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_BRAND_EDIT);

        //数据检测
        object(parent::ERROR)->check($input, 'brand_id', parent::TABLE_BRAND, array('args'));
        if (isset($input['brand_name']))
            object(parent::ERROR)->check($data, 'brand_name', parent::TABLE_BRAND, array('args'));
        if (isset($input['brand_info']))
            object(parent::ERROR)->check($data, 'brand_info', parent::TABLE_BRAND, array('args'));
        if (isset($input['brand_sort']))
            object(parent::ERROR)->check($data, 'brand_sort', parent::TABLE_BRAND, array('args'));
        if (isset($input['brand_state']))
            object(parent::ERROR)->check($data, 'brand_state', parent::TABLE_BRAND, array('args'));
        if (isset($input['brand_json']))
            object(parent::ERROR)->check($data, 'brand_json', parent::TABLE_BRAND, array('args'));

        //查询旧数据
        $original = object(parent::TABLE_BRAND)->find($input['brand_id']);
        if (empty($original))
            throw new error('数据不存在');

        //白名单
        $whitelist = array(
            'brand_name', 
            'brand_info',
            'brand_json',
            'brand_sort',
            'brand_state',
        );
        $update_data = cmd(array($data, $whitelist), 'arr whitelist');

        //过滤不需要更新的数据
        foreach ($update_data as $key => &$val) {
            if (isset($original[$key]) && $original[$key] == $val)
                unset($update_data[$key]);
        }

        //是否上传图片
        if (!empty($_FILES)) {
            $qiniu_image = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
            $update_data['brand_logo_image_id'] = $qiniu_image['image_id'];
        }

        if (empty($update_data))
            throw new error('没有需要更新的数据');

        //更新时间
        $update_data['brand_update_time'] = time();
        $update_where = array(array('brand_id=[+]', $input['brand_id']));

        //更新数据
        if (object(parent::TABLE_BRAND)->update($update_where, $update_data) ){
            //删除旧图片
            if (!empty($original['brand_logo_image_id']) && !empty($qiniu_image)) {
                $qiniu_image['image_id'] = $original['brand_logo_image_id'];
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove($qiniu_image);
            }

            //记录日志
            object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);

            return $data['brand_id'];
        } else {
            if (!empty($qiniu_image))
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove($qiniu_image);

            throw new error('操作失败');
        }
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 查询列表
     *
     * api: BRANDADMINLIST
     * req: {
     * }
     * 
     * @return array
     */
    public function api_list($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_BRAND_READ);

        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        $config["orderby"] = object(parent::REQUEST)->orderby($input, array(
            'name_desc' => array('brand_name', true),
            'name_asc' => array('brand_name', false),
            'state_desc' => array('brand_state', true),
            'state_asc' =>  array('brand_state', false),
            'insert_time_desc' => array('brand_insert_time', true),
            'insert_time_asc' => array('brand_insert_time', false),
            'update_time_desc' => array('brand_update_time', true),
            'update_time_asc' => array('brand_update_time', false),
            'sort_desc' => array('brand_sort', true),
            'sort_asc' => array('brand_sort', false),
        ));

        //避免排序重复
        $config['orderby'][] = array('b.brand_id', false);

        //筛选——品牌名称
        if (isset($input['search']['brand_name']) && is_string($input['search']['brand_name'])) {
            $config['where'][] = array('[and] b.brand_name LIKE "%[-]%"', $data['search']['brand_name']);
        }

        //筛选——品牌ID
        if (isset($input['search']['brand_id']) && is_string($input['search']['brand_id'])) {
            $config['where'][] = array('[and] b.brand_id=[+]', $data['search']['brand_id']);
        }

        return object(parent::TABLE_BRAND)->select_page($config);
    }


    /**
     * 查询详情
     * 
     * api: BRANDADMINGET
     * req: {
     *  brand_id 品牌ID
     * }
     * 
     * @return  array
     */
    public function api_get($input = array())
    {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_BRAND_READ);

        //检测输入
        object(parent::ERROR)->check($input, 'brand_id', parent::TABLE_BRAND, array('args'));

        //查询数据
        $data = object(parent::TABLE_TYPE)->find($input['brand_id']);
        if (empty($data))
            throw new error('数据不存在');

        return $data;
    }


    //===========================================
    // 检测
    //===========================================


    /**
     * 检查编辑的权限
     * 
     * api: BRANDADMINEDITCHECK
     * 
     * @return  bool
     */
    public function api_edit_check()
    {
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_BRAND_EDIT);
        return true;
    }


}