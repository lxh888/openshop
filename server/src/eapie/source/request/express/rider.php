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



namespace eapie\source\request\express;

use eapie\main;
use eapie\error;

class rider extends \eapie\source\request\express
{

	
    /**
     * 获取骑手信息
     * Undocumented function
     *
     * api: EXPRESSRIDERSELFCHECK
     * {"class":"express/rider","method":"api_self_check"}
     * req: {
     * 
     * }
     * 
     * @param array $input
     * @return void
     */
    public function api_self_check($input=array())
    {
        
        //检测登录
        object(parent::REQUEST_USER)->check();
        
        $data = object(parent::TABLE_EXPRESS_RIDER)->find($_SESSION['user_id']);
        if(empty($data) || $data['express_rider_state'] != 1){
            return ['state'=>0];
        }else{
            return ['state'=>1,'rider'=>$data];
        }
    }

    /**
     * 骑手开启关闭接单状态
     * Undocumented function
     * api: EXPRESSRIDERSELFSWITCH
     * {"class":"express/rider","method":"api_self_switch"}
     *
     * @return void
     */
    public function api_self_switch($input=array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        if(!isset($input['state']) || !in_array($input['state'],[0,1]))
            throw new error('参数错误');

        $order_where = array(
            // array('express_rider_id =[+]',$_SESSION['user_id']),
            array('express_rider_user_id =[+]',$_SESSION['user_id']),
            array('express_order_delete_state=0'),
            array('express_order_trash=0'),
            array('express_order_state=0')
        );    
        
        $express_rider_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($order_where);

        if($express_rider_order && $input['state'] == 0)
            throw new error('您有快递单未接，不能关闭接单');
        $where = array(
            array('user_id=[+]',$_SESSION['user_id'])
        );
        
        $data = [
            'express_rider_on_off'=>$input['state']
        ];

        if(object(parent::TABLE_EXPRESS_RIDER)->update($where,$data)){
            return ['express_user'=>$_SESSION['user_id']];
        }
        throw new error('操作失败');
        
    }
}