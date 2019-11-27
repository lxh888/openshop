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



namespace eapie\source\table\merchant;

use eapie\main;

//商家表
class merchant_goods_type extends main
{
    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
    const CACHE_KEY = array(__CLASS__);
    
    /**
     * @var [arr] [数据检测]
     */
    public $check = array();


    /**
     * 根据条件获取数据
     */
    public function select( $config=array() ){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant_goods_type ')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->select($select);
        });
    }


    /**
     * join查询商家--商家商品分类
     */
    public function select_join_type( $config=array() ){

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
            ->table('merchant_goods_type')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->select($select);
        });
    }
}

?>