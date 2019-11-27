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

// 用户评价
class evaluate extends \eapie\source\request\user
{

    /**
     * 添加
     * 
     * api: USEREVALUATESELFADD
     * req: {
     *  module      [str] [必填] [模块]
     *  key         [str] [必填] [模块主键ID]
     *  score       [int] [必填] [评分]
     *  value       [str] [必填] [评价]
     *  json        [jsn] [可选] [json个性化数据]
     * }
     * 
     * @return string [ID]
     */
    public function api_self_add($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();

        // 是否已评价
        $where = array(
            array('user_id = [+]', $_SESSION['user_id']),
            array('[and] user_evaluate_module = [+]', $input['module']),
            array('[and] user_evaluate_key = [+]', $input['key']),
        );
        $evaluate = object(parent::TABLE_USER_EVALUATE)->find_where($where);
        if ($evaluate)
            throw new error('您已评价过了');

        // 检测输入
        object(parent::ERROR)->check($input, 'module', parent::TABLE_USER_EVALUATE, array('args'), 'user_evaluate_module');
        object(parent::ERROR)->check($input, 'key', parent::TABLE_USER_EVALUATE, array('args'), 'user_evaluate_key');
        object(parent::ERROR)->check($input, 'score', parent::TABLE_USER_EVALUATE, array('args'), 'user_evaluate_score');
        object(parent::ERROR)->check($input, 'value', parent::TABLE_USER_EVALUATE, array('args'), 'user_evaluate_value');

        $json = array();
        if (!empty($input['json']) && is_array($input['json'])) {
            $json = $input['json'];
        }

        $insert_data = array(
            'user_evaluate_id' => object(parent::TABLE_USER_EVALUATE)->get_unique_id(),
            'user_id' => $_SESSION['user_id'],
            'user_evaluate_module' => $input['module'],
            'user_evaluate_key' => $input['key'],
            'user_evaluate_score' => $input['score'],
            'user_evaluate_value' => $input['value'],
            'user_evaluate_json' => cmd(array($json), 'json encode'),
            'user_evaluate_insert_time' => time(),
            'user_evaluate_update_time' => time(),
        );

        // 插入数据
        if (object(parent::TABLE_USER_EVALUATE)->insert($insert_data)) {
            return $insert_data['user_evaluate_id'];
        } else {
            throw new error('操作失败');
        }
    }
}