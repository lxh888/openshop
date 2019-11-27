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



namespace eapie\source\table\user;

use eapie\main;

class user_luck_draw extends main
{
    /**抽奖记录 */

    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
    const CACHE_KEY = array(__CLASS__);
    

    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(

    );


    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id($num=0){
        if($num>0){
            return cmd(array($num), 'random autoincrement');
        }else{
            return cmd(array(10), 'random autoincrement');
        }
    }  


    /**
     * 新增一条数据
     * Undocumented function
     *
     * @param array $data
     * @param array $call_data
     * @return void
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_luck_draw')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 获取抽奖记录--不分页
     * Undocumented function
     *
     * @return void
     */
    public function select($config)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('user_luck_draw ul')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }
}