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



namespace eapie\source\request\user;

use eapie\main;
use eapie\error;

class follow extends \eapie\source\request\user
{


    //===========================================
    // 操作
    //===========================================



    /**
     * 添加关注
     *
     * api: USERFOLLOWSELFADD
     * req: {
     *  module  [str] [必填] [关注的模块]
     *  key     [str] [必填] [模块主键ID]
     *  label   [str] [可选] [标签]
     *  comment [str] [可选] [备注]
     *  sort    [int] [可选] [排序]
     * }
     * 
     * @return string 关注ID
     */
    public function api_self_add($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_module');
        object(parent::ERROR)->check($input, 'key', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_key');
        if (isset($input['label']))
            object(parent::ERROR)->check($input, 'label', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_label');
        if (isset($input['comment']))
            object(parent::ERROR)->check($input, 'comment', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_comment');
        if (isset($input['sort']))
            object(parent::ERROR)->check($input, 'sort', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_sort');

        //是否已关注
        $original = object(parent::TABLE_USER_FOLLOW)->find_where(array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] user_follow_module=[+]', $input['module']),
            array('[and] user_follow_key=[+]', $input['key']),
        ));
        if ($original)
            throw new error('已经关注');

        //白名单
        $whitelist = array(
            'module',
            'key',
            'label',
            'comment',
            'sort',
        );
        $input = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $insert_data = cmd(array('user_follow_', $input),  'arr key_prefix');

        $insert_data['user_follow_id'] = object(parent::TABLE_USER_FOLLOW)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['user_follow_insert_time'] = time();
        $insert_data['user_follow_update_time'] = time();

        if (object(parent::TABLE_USER_FOLLOW)->insert($insert_data)) {
            return $insert_data['user_follow_id'];
        } else {
            throw new error('操作失败');
        }
    }


    public function api_self_edit($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
    }


    /**
     * 取消关注
     *
     * api: USERFOLLOWSELFREMOVE
     * req: {
     *  id      [str] [必填] [关注ID]
     *  module  [str] [必填] [关注的模块]
     *  key     [str] [必填] [模块主键ID]
     * }
     *
     * @return [type]        [description]
     */
    public function api_self_remove($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //条件
        $delete_where = array(
            array('user_id=[+]', $_SESSION['user_id'])
        );

        //是否根据主键删除
        if (isset($input['id'])) {
            object(parent::ERROR)->check($input, 'id', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_id');
            $delete_where[] = array('[and] user_follow_id=[+]', $input['id']);
        } else {
            object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_module');
            object(parent::ERROR)->check($input, 'key', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_key');
            $delete_where[] = array('[and] user_follow_module=[+]', $input['module']);
            $delete_where[] = array('[and] user_follow_key=[+]', $input['key']);
        }

        if (object(parent::TABLE_USER_FOLLOW)->delete($delete_where)) {
            return true;
        } else {
            throw new error('操作失败');
        }
    }



    //===========================================
    // 查询
    //===========================================


    public function api_self_list($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
    }


    /**
     * 查询是否已关注
     *
     * api: USERFOLLOWSELFGET
     * req: {
     *  module  [str] [必填] [关注的模块]
     *  key     [str] [必填] [模块主键ID]
     * }
     * 
     * @return bool
     */
    public function api_self_get($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_module');
        object(parent::ERROR)->check($input, 'key', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_key');

        $where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] user_follow_module=[+]', $input['module']),
            array('[and] user_follow_key=[+]', $input['key'])
        );

        $data = object(parent::TABLE_USER_FOLLOW)->find_where($where);

        return $data;
    }


    /**
     * 查询关注数量
     *
     * api: USERFOLLOWSELFCOUNT
     * req: {
     *  module  [str] [可选] [关注的模块]
     * }
     * 
     * @return integer
     */
    public function api_self_count($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $where = array();
        $where[] = array('user_id = [+]', $_SESSION['user_id']);

        //是否指定某模块
        if (isset($input['module'])) {
            object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_FOLLOW, array('args'), 'user_follow_module');
            $where[] = array('[and] user_follow_module = [+]', $input['module']);
        }

        return object(parent::TABLE_USER_FOLLOW)->get_count($where);
    }


}