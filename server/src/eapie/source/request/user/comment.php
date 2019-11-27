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

//用户评论
class comment extends \eapie\source\request\user
{


    /**
     * 添加
     * 
     * api: USERCOMMENTSELFADD
     * req: {
     *  root_id     [str] [可选] [顶级评论ID]
     *  parent_id   [str] [可选] [父级评论ID]
     *  module      [str] [必填] [模块]
     *  key         [str] [必填] [模块主键ID]
     *  value       [str] [必填] [内容]
     * }
     * 
     * @return [type] [description]
     */
    public function api_self_add($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        if (isset($input['root_id']))
            object(parent::ERROR)->check($input, 'root_id', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_root_id');
        if (isset($input['parent_id']))
            object(parent::ERROR)->check($input, 'parent_id', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_parent_id');
        object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_module');
        object(parent::ERROR)->check($input, 'key', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_key');
        object(parent::ERROR)->check($input, 'value', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_value');

        //白名单
        $whitelist = array(
            'root_id',
            'parent_id',
            'module',
            'key',
            'value',
        );
        $input = cmd(array($input, $whitelist), 'arr whitelist');

        //添加前缀
        $insert_data = cmd(array('user_comment_', $input),  'arr key_prefix');

        $insert_data['user_comment_id'] = object(parent::TABLE_USER_COMMENT)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['user_comment_ip'] = HTTP_IP;
        $insert_data['user_comment_state'] = 1;
        $insert_data['user_comment_insert_time'] = time();
        $insert_data['user_comment_update_time'] = time();

        if (object(parent::TABLE_USER_COMMENT)->insert($insert_data)) {
            return $insert_data['user_comment_id'];
        } else {
            throw new error('操作失败');
        }
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 查询列表
     * 
     * api: USERCOMMENTLIST
     * req: {
     *  module      [str] [必填] [模块]
     *  key         [str] [必填] [模块主键ID]
     * }
     * 
     * @return array
     */
    public function api_list($input = array())
    {
        //检测输入
        object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_module');
        object(parent::ERROR)->check($input, 'key', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_key');

        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        //字段
        $sql_reply_count = object(parent::TABLE_USER_COMMENT)->sql_join_reply_count('uc');
        $config['select'] = array(
            'u.user_nickname AS nick',
            'u.user_logo_image_id AS logo',
            'uc.user_comment_id AS id',
            'uc.user_comment_value AS value',
            'uc.user_comment_insert_time AS timestamp',
            '('.$sql_reply_count.') as reply_count',
        );

        //排序
        $config['orderby'][] = array('uc.user_comment_insert_time', true);

        //条件
        $config['where'][] = array('uc.user_comment_state=1');
        $config['where'][] = array('[and] uc.user_comment_module=[+]', $input['module']);
        $config['where'][] = array('[and] uc.user_comment_key=[+]', $input['key']);
        $config['where'][] = array('[and] uc.user_comment_root_id=""');

        //查询数据
        $data = object(parent::TABLE_USER_COMMENT)->select_page($config);

        //格式化数据
        foreach ($data['data'] as &$val) {
            $val['time'] = date('Y-m-d H:i', $val['timestamp']);
        }

        return $data;
    }


    /**
     * 查询详情
     *
     * api: USERCOMMENTGET
     * req: {
     *  id [str] [必填] [评论ID]
     * }
     * 
     * @return array
     */
    public function api_get($input = array())
    {
        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_id');

        //查询评论数据
        $data_comment = object(parent::TABLE_USER_COMMENT)->find($input['id']);
        if (empty($data_comment))
            throw new error('数据不存在');
        if ($data_comment['user_comment_state'] != 1)
            throw new error('该评论被屏蔽');

        //查询用户数据
        $data_user = object(parent::TABLE_USER)->find($data_comment['user_id']);

        return array(
            'id' => $data_comment['user_comment_id'],
            'value' => $data_comment['user_comment_value'],
            'time' => $data_comment['user_comment_insert_time'],
            'nick' => $data_user['user_nickname'],
            'logo' => $data_user['user_logo_image_id'],
        );
    }


    /**
     * 查询回复列表
     *
     * api: USERCOMMENTREPLYLIST
     * req: {
     *  id [str] [必填] [评论ID]
     * }
     * 
     * @return array
     */
    public function api_reply_list($input = array())
    {
        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_id');

        //查询配置
        $config = array(
            'orderby' => array(),
            'where' => array(),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        //字段
        $config['select'] = array(
            'u.user_nickname AS nick',
            'u.user_logo_image_id AS logo',
            'uc.user_comment_id AS id',
            'uc.user_comment_value AS value',
            'uc.user_comment_insert_time AS time',
        );

        //排序
        $config['orderby'][] = array('uc.user_comment_insert_time', false);

        //条件
        $config['where'][] = array('uc.user_comment_state=1');
        $config['where'][] = array('[and] uc.user_comment_root_id=[+]', $input['id']);

        //查询数据
        $data = object(parent::TABLE_USER_COMMENT)->select_page($config);

        //格式化数据

        return $data;
    }


    /**
     * 获取评论数量
     * 
     * api: USERCOMMENTGETNUM
     * 
     */
    public function api_get_num($data = array())
    {
        $config = array(
            'orderBy'=>array(),
            'where'=>array(),
        );

        $config['where'][] = array('user_comment_state=1');

        //模块
        if(!empty($data['module'])){
            $config['where'][] = array('[and] user_comment_module =[+]',$data['module']);
        }

        //评论对象
        if(!empty($data['id'])){
            $config['where'][] = array('[and] user_comment_key =[+]',$data['id']);
        }

        if(!empty($data['type']) && $data['type'] == 1){
            $config['where'][] = array("[and] user_comment_parent_id = ''");
        }

        $data = object(parent::TABLE_USER_COMMENT)->select_count($config);
        return $data;
    }

}