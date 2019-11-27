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


 
namespace eapie\source\table\application;

use eapie\main;

class shipping extends main
{

    /**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(

		"type_module" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少分类模块名称参数"),
                    'echo'=>array("分类模块名称的数据类型不合法"),
                    '!null'=>array("分类模块名称不能为空"),
                    'method'=>array(array(parent::TABLE_SHIPPING, 'check_module'), "分类模块名称输入有误，不能被识别")       
                ),
        ),

		"key" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少优惠模块主键参数"),
                    'echo'=>array("优惠模块主键的数据类型不合法"),
                    '!null'=>array("优惠模块主键不能为空"),
                ),
        )
	);


    /**
     * 获取模块
     * 
     * @param   void
     * @return  array
     */
    public function get_module(){
        return array(
            parent::MODULE_EXPRESS_ORDER_SHIPPING => "快递系统物流分类",
            parent::MODULE_SHOP_ORDER => '商城订单'
        );
    }


    /**
     * 检测模块
     * 
     * @param   string  $data
     * @return  array
     */
    public function check_module($data){
        $module_list = $this->get_module();
        if( isset($module_list[$data]) ){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shipping')
                ->where(array('shipping_id=[+]', $id))
                ->find();
        });
    }



    /**
     * 获取快递分类
     *
     * @param 	array	$config
     * @return void
     */
    public function select( $config = array() ){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
				
			return db(parent::DB_APPLICATION_ID)
			->table('shipping')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
    }
	
	
	
	
	
}