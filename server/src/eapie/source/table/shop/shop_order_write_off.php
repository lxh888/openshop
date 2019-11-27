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




namespace eapie\source\table\shop;

use eapie\main;
class shop_order_write_off extends main {


    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);

    /**
     * 根据主键,查一条记录
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        $data = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_write_off')
            ->where(array('shop_order_write_off_code=[+]', $id))
            ->find();

        return $data;
    }

    /**
     * 自定义条件,查询一条记录
     */
    public function find_where($call_where=array()){
        $data = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_write_off')
            ->call('where', $call_where)
            ->find();
        return $data;
    }

    /**
     * 更新数据
     * 
     * @param   array       $where
     * @param   array       $data
     * @param   array       $call_data
     * @return  bool
     */
    public function update($where = array(), $data = array(), $call_data = array())
    {
        if( empty($where) || (empty($data) && empty($call_data)) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('shop_order_write_off')
        ->call('where', $where)
        ->call('data', $call_data)
        ->update($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }   


    /**
	 * 插入核销订单数据
	 * @param $data array
	 * @return $result bool
	 */
	public function insert($data = array(), $call_data = array()){
		if (empty($data) && empty($call_data))
			return false;

		$bool = db(parent::DB_APPLICATION_ID)
			->table('shop_order_write_off')
			->call('data', $call_data)
			->insert($data);

		//清理缓存
		if ($bool)
			object(parent::CACHE)->clear(self::CACHE_KEY);

		return $bool;
    }
    


    /**
     * 获取订单列表--分页
     * Undocumented function
     *  
     * @return void
     */
    public function select_page($config=[])
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $limit = array(
                (isset($call_limit[0])? $call_limit[0] : 0),
                (isset($call_limit[1])? $call_limit[1] : 0)
            );

            //设置返回的数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 0,
                'data' => array()
            );

            //左连订单表
            $join_shop_order = array(
                'table' => 'shop_order so',
                'type' => 'left',
                'on' => 'sowo.shop_order_id = so.shop_order_id',
            );
            
			//左连订单商品表
            $join_shop_order_goods = array(
                'table' => 'shop_order_goods sog',
                'type' => 'left',
                'on' => 'sog.shop_order_id = sowo.shop_order_id',
            );
			
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
                ->table('shop_order_write_off sowo')
                ->joinon($join_shop_order_goods, $join_shop_order)
                ->call('where', $call_where)
                ->find('count(*) as count');

            if (empty($counts['count'])) {
                return $data;
            } else {
                $data['row_count'] = $counts['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }
			
			if( empty($select) ){
				$select = array('sowo.*','so.*');
			}
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('shop_order_write_off sowo')
            ->call('where', $call_where)
			->joinon($join_shop_order_goods, $join_shop_order)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            return $data;
        });    
    }
}