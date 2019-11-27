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



namespace eapie\source\request\agent;

use eapie\main;
use eapie\error;

class agent extends \eapie\source\request\agent
{
   
    // const TABLE_AGENT_USER = 'eapie\source\table\agent\agent_user';
    
    /**
     * 验证是否是代理
     * Undocumented function
     *
     * api: AGENTSELFCHECK
     * {"class":"express/agent","method":"api_self_check"}
     * 
     * @return void
     */
    public function api_self_check($input=[])
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
        
        $data = object(parent::TABLE_AGENT_USER)->find($_SESSION['user_id']);
        if(empty($data) || $data['agent_user_state'] != 1){
            return ['state'=>0];
        }else{
            return ['state'=>1];
        }
    }

    /**
     * 申请成为代理
     * Undocumented function
     * 
     * api: AGENTSELFARRLY
     * 
     * {"class":"agent/agent","method":"api_self_apply"}
     * 
     * @return void
     */
    public function api_self_apply($input=array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();


        //代理申请记录
        $agent_user = object(parent::TABLE_AGENT_USER)->find_where(array(array('user_id =[+]',$_SESSION['user_id'])));
        //判断是否是代理
        if(!empty($agent_user['agent_user_state']) && $agent_user['agent_user_state'] == 1){throw new error('您已经是代理，不需要再次申请');}


        //获取代理配置
        $agent_user_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("agent_user"), true);

        //代理申请邀请人数限制
        $agent_apply_limit = isset($agent_user_config['agent_apply_limit']) && (int)$agent_user_config['agent_apply_limit']>=0?(int)$agent_user_config['agent_apply_limit']:30;
        

        //用户邀请人数
        $user_son_count = object(parent::TABLE_USER)->find_son_count($_SESSION['user_id']);

        if(empty($user_son_count['count']) || $user_son_count['count'] < $agent_apply_limit){throw new error('邀请人数未达到指定人数，不允许申请代理');}        

        if(empty($agent_user)){
            $time = time();
              
            $insert_data = array(
                'agent_user_id'=>object(parent::TABLE_AGENT_USER)->get_unique_id(),
                'user_id'=>$_SESSION['user_id'],
                'agent_region_id'=>'',
                'agent_user_address'=>$agent_user_config['interview_address'],
                'agent_user_times'=>$agent_user_config['interview_phone'],
                'agent_user_times'=>$agent_user_config['interview_times'],
                'agent_user_insert_time'=>$time,
                'agent_user_update_time'=>$time
            );

            if(object(parent::TABLE_AGENT_USER)->insert($insert_data)){
                return ['agent_user_id'=>$insert_data['agent_user_id']];
            }else{
                throw new error('申请失败');
            }
        }else{

            $where = array(
                'agent_user_id'=>$agent_user['agent_user_id'],
                'user_id'=>$_SESSION['user_id']
            );

            $data = array(
                'agent_user_state'=>2,
            );

            if(object(parent::TABLE_AGENT_USER)->update($where,$data)){
                return ['agent_user_id'=>$data['agent_user_id']];
            }else{
                throw new error('申请失败');
            }
        }
        
        

    }


    /**
     * 获取代理申请信息
     *
     * api: AGENTSELFAUDIT
     * {"class":"agent/agent","method":"api_self_audit"}
     * 
     * @return void
     */
    public function api_self_audit(){
        //检测登录
        object(parent::REQUEST_USER)->check();

        $data = object(parent::TABLE_AGENT_USER)->find($_SESSION['user_id']);
        if( !empty($data) ){
            return $data;
        }else{
        	return '';
        }
		
    }


    /**
     * 代理获取提现记录
     * Undocumented function
     *
     * api: AGENTSELFWITHDRAW
     * {"class":"agent/agent","method":"api_self_withdraw"}
     * 
     * @return void
     */
    public function api_self_withdraw($input=array())
    {

        //检测登录
        object(parent::REQUEST_USER)->check();

        $method = parent::TRANSACTION_TYPE_WITHDRAW;
        //查询配置
        $config = [
            'orderby' => array(
                array('insert_time',false),
            ),
            'where' => array(
                array('user_id =[+]',$_SESSION['user_id']),
                array('user_money_type=[+]',$method)  //交易类型--提现
            ),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
            'select'=>array(
                "user_id AS uid",
                "user_money_type AS type",
                "user_money_minus AS money",
                "user_money_time AS insert_time",
                "FROM_UNIXTIME(user_money_time,'%Y-%m-%d %T') AS create_time"
            )
        ];

        $data = object(parent::TABLE_USER_MONEY)->select_withdraw_page($config);
        return $data;
    }


    /**
     * 获取代理提现总金额
     * Undocumented function
     * 
     * api: AGENTSELFWITHDRAWMONEY
     * 
     * {"class":"agent/agent","method":"api_self_withdraw_money"}
     * 
     * @return void
     */
    public function api_self_withdraw_money($input=array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $method = parent::TRANSACTION_TYPE_WITHDRAW;
        $where = array(
            array('user_id =[+]',$_SESSION['user_id']),
            array('user_money_type =[+]',$method)
        );

        $data = object(parent::TABLE_USER_MONEY)->withdraw_money($where);
        return $data;
    }



    /**
     * 验证当前地区是否有创始人
     * Undocumented function
     * 
     * api: AGENTSELFREGIONCHECK
     * {"class":"agent/agent","method":"api_self_region_check"}
     *
     * @param array $data
     * @return void
     */
    public function api_self_region_check($data=array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //判断省市区参数
        if(empty($data['province']) || empty($data['city']) || empty($data['district'])){
            throw new error('接口数据异常');
        }

        //获取代理地区
        $agent_region = object(parent::TABLE_AGENT_REGION)->find_province_city_district($data['province'],$data['city'],$data['district']);
        

        if(!empty($agent_region)){
            $agent_user_where = array(
                array('agent_region_id =[+]',$agent_region['agent_region_id']),
                array("agent_user_state=1")
            );
            $agent_user_info = object(parent::TABLE_AGENT_USER)->find_where($agent_user_where);
            if(!empty($agent_user_info)){
                return array('id'=>$agent_region['agent_region_id'],'state'=>1);
            }

            $agent_user_where = array(
                array('agent_region_id =[+]',$agent_region['agent_region_id']),
                array("agent_user_state=2")
            );
            $agent_user_info = object(parent::TABLE_AGENT_USER)->find_where($agent_user_where);
            if(!empty($agent_user_info)){
                return array('id'=>$agent_region['agent_region_id'],'state'=>2);
            }

            return array('id'=>$agent_region['agent_region_id'],'state'=>0);
        }


        //无代理地区--新增代理地区信息
        $region_json = array(
            'add_user_id'=>$_SESSION['user_id'],
            'info'=>$data
        );
        $region_insert = array(
            'agent_region_id'=>object(parent::TABLE_AGENT_REGION)->get_unique_id(),
            'agent_region_info'=>'区域负责人申请，新增区域代理地区',
            'agent_region_scope'=>3,
            'agent_region_province'=>$data['province'],
            'agent_region_city'=>$data['city'],
            'agent_region_district'=>$data['district'],
            'agent_region_json'=>cmd(array($region_json),'json encode'),
            'agent_region_insert_time'=>time(),
            'agent_region_update_time'=>time()
        );

        if(object(parent::TABLE_AGENT_REGION)->insert($region_insert)){
            return array('id'=>$region_insert['agent_region_id'],'state'=>0);
        }
        throw new error('接口信息异常');
    }
}