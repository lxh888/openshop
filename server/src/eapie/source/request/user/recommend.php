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
class recommend extends \eapie\source\request\user {


    /**
     * 获取用户的下级人数
     * 
     * USERRECOMMENDSELFLOWERLEVEL
	 * {"class":"user/recommend","method":"api_self_lower_level"}
     */
    public function api_self_lower_level(){
        //检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];
        $data = object(parent::TABLE_USER_RECOMMEND)->select_by_recommend_id($user_id);

        //白名单
        $whitelist = array(
            "user_id",
            'user_recommend_state',             //0为会员 1为非会员
            "user_recommend_level",
            "user_recommend_insert_time"
        );
        // 处理数据
        foreach ($data as $key => &$value) {
            $value = cmd(array($value, $whitelist), 'arr whitelist');
            $value['phone'] = object(parent::TABLE_USER_PHONE)->find_by_user_id($value['user_id']);
            $value['nickname'] = object(parent::TABLE_USER)->find_nickname($value['user_id']);
            $cache = object(parent::TABLE_ADMIN_USER)->find($value['user_id']);
            if(isset($cache['admin_id'])){
                $value['admin_id'] = $cache['admin_id'];
            } else {
                $value['admin_id'] = null;
            }
        }
        $result = array();
        // 处理格式
        foreach ($data as $key => $value) {
            $index = $value['user_recommend_level'] - 1;
            if( !isset($result[$index]) ){
                // 初始化
                $result[$index]['level'] = $value['user_recommend_level'];
                $result[$index]['num'] = 1;
                $result[$index]['data'] = array($value);
            } else {
                $result[$index]['num'] += 1;
                $result[$index]['data'][] = $value;
            }
        }
        return $result;
    }


    /**
     * 获取区域人数
     * 
     * USERRECOMMENDSELFAREALOWERLEVEL
	 * {"class":"user/recommend","method":"api_self_area_lower_level"}
     */
    public function api_self_area_lower_level(){
        //检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];
        // 首先查询身份
        $admin = object(parent::TABLE_ADMIN_USER)->find($user_id);
        if( !isset($admin['admin_id']) || $admin['admin_id'] !== 'area_agent')
            return false;
        $user = object(parent::TABLE_USER)->find($user_id);
        $config = array();
        $config['where'] = array(
            array('user_register_province=[+]',$user['user_register_province']),
            array('[and]user_register_city=[+]',$user['user_register_city']),
            array('[and]user_register_area=[+]',$user['user_register_area'])
        );
        $data = object(parent::TABLE_USER)->select($config);
        //白名单
        $whitelist = array(
            "user_nickname",
            "user_id",
            "user_email",
            "user_qq",
            "user_register_area",
            "user_register_city",
            "user_register_province",
            "user_register_time",
            "user_sex",
            "user_state",
        );
        foreach ($data as $key => &$value) {
            $value = cmd(array($value, $whitelist), 'arr whitelist');
            $value['phone'] = object(parent::TABLE_USER_PHONE)->find_by_user_id($value['user_id']);
            $cache = object(parent::TABLE_ADMIN_USER)->find($value['user_id']);
            if(isset($cache['admin_id'])){
                $value['admin_id'] = $cache['admin_id'];
            } else {
                $value['admin_id'] = null;
            }
        }
        return $data;
    }
}