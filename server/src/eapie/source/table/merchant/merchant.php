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
class merchant extends main
{

    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "merchant_user");

    /**
     * @var [arr] [数据检测]
     */
    public $check = array(
        'merchant_id' => array(
            'args'=>array(
                'exist' => array('缺少商家ID参数'),
                'echo'  => array('商家ID类型不合法'),
                '!null' => array('商家ID不能为空')
            ),
            'exists_id' => array(
                'method'=>array(array(parent::TABLE_MERCHANT, 'find_exists_id'), '商家ID有误，商家不存在') 
            )
        ),
        'merchant_name' => array(
            'args'=>array(
                'exist' => array('缺少商家名称参数'),
                'echo'  => array('商家名称数据类型不合法'),
                '!null' => array('商家名称不能为空')
            ),
            'length' => array('<length'=>array(200, '商家名称字符长度太多'))    
        ),
        'merchant_info' => array(
            'args'=>array(
                'exist' => array('缺少商家简介参数'),
                'echo'  => array('商家简介数据类型不合法'),
                '!null' => array('商家简介不能为空')
            )
        ),
        
		'merchant_phone' => array(
            'args'=>array(
            	'exist' => array('缺少商家手机号参数'),
                'echo'  => array('商家手机号的数据类型不合法'),
                '!null' => array('商家手机号不能为空'),
                'match' => array('/^[0-9]{11}$/', "商家手机号格式不合法"),
            )
        ),
		'merchant_tel' => array(
            'args'=>array(
                'echo'  => array('商家电话的数据类型不合法')
            )
        ),
        
		'merchant_email' => array(
            'args'=>array(
                'echo'  => array('商家电子邮件的数据类型不合法')
            )
        ),
		
		'merchant_country' => array(
            'args'=>array(
                'echo'  => array('商家的国家名称的数据类型不合法')
            )
        ),
		'merchant_province' => array(
            'args'=>array(
            	'exist' => array('缺少商家的省份名称参数'),
                'echo'  => array('商家的省份名称的数据类型不合法'),
				'!null' => array('商家的省份名称不能为空')
            )
        ),
		'merchant_city' => array(
            'args'=>array(
            	'exist' => array('缺少商家的市名称参数'),
                'echo'  => array('商家的市名称的数据类型不合法'),
                '!null' => array('商家的市名称不能为空')
            )
        ),
		'merchant_district' => array(
            'args'=>array(
            	'exist' => array('缺少商家的区名称参数'),
                'echo'  => array('商家的区名称的数据类型不合法'),
                '!null' => array('商家的区名称不能为空')
            )
        ),
        'merchant_address' => array(
            'args'=>array(
                'exist' => array('缺少商家地址参数'),
                'echo'  => array('商家地址数据类型不合法'),
                '!null' => array('商家地址不能为空')
            )
        ),
        'merchant_longitude' => array(
            'args' => array(
                'exist'  => array('缺少坐标经度参数'),
                'method' => array(array(parent::TABLE_MERCHANT, 'check_longitude'), '坐标经度不合法')
            )
        ),
        'merchant_latitude' => array(
            'args' => array(
                'exist'  => array('缺少坐标经度参数'),
                'method' => array(array(parent::TABLE_MERCHANT, 'check_latitude'), '坐标纬度不合法')
            )
        ),
        'merchant_logo_image_id' => array(
            'args'=>array(
                'echo'  => array('商家logo图片ID的数据类型不合法')
            )
        ),
        'merchant_state' => array(
            'args'=>array(
                'match'=>array('/^[0-3]$/', '商家状态值必须是0、1、2、3'),
            )
        ),
        'merchant_self' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '商家是否自营的值必须是0或1'),
            )
        ),
		
        
		'merchant_license_name' => array(
            'args'=>array(
                'echo'  => array('商家的营业执照公司名称的数据类型不合法')
            )
        ),
		'merchant_license_number' => array(
            'args'=>array(
                'echo'  => array('商家的注册号/统一社会信用代码的数据类型不合法')
            )
        ),
		'merchant_license_address' => array(
            'args'=>array(
                'echo'  => array('商家的营业执照住所地址的数据类型不合法')
            )
        ),
		'merchant_license_operator' => array(
            'args'=>array(
                'echo'  => array('商家的营业执照法定代表人/经营者的数据类型不合法')
            )
        ),
        'logo_image_id' => array(
            'args'=>array(
                'exist' => array('缺少商家logo'),
                'echo'  => array('商家logo异常'),
                '!null' => array('商家logo不存在')
            ),
            'length' => array('<length'=>array(100, '商家logo异常'))
        ),
        'name' => array(
            'args'=>array(
                'exist' => array('缺少商家名称'),
                'echo'  => array('商家名称错误'),
                '!null' => array('商家名称不存在')
            ),
            'length' => array('<length'=>array(200, '商家名称错误'))
        ),
        'phone' => array(
            'args'=>array(
                'exist' => array('缺少商家联系电话'),
                'echo'  => array('商家联系电话错误'),
                '!null' => array('商家联系电话不存在')
            ),
        ),
        'address' => array(
            'args'=>array(
                'exist' => array('缺少商家地址'),
                'echo'  => array('商家地址错误'),
                '!null' => array('商家地址不存在')
            ),
        ),
        'lng' => array(
            'args'=>array(
                'exist' => array('缺少商家位置信息参数'),
                'echo'  => array('商家位置信息参数错误'),
                '!null' => array('商家位置信息参数不存在')
            ),
        ),
        'lat' => array(
            'args'=>array(
                'exist' => array('缺少商家位置信息参数'),
                'echo'  => array('商家位置信息参数错误'),
                '!null' => array('商家位置信息参数不存在')
            ),
        ),
        'province' => array(
            'args'=>array(
                'echo'  => array('商家所在省份错误'),
            ),
        ),
        'city' => array(
            'args'=>array(
                'echo'  => array('商家所在城市错误'),
            ),
        ),
        'district' => array(
            'args'=>array(
                'echo'  => array('商家所在地区错误'),
            ),
        ),
        'type_id' => array(
            'args'=>array(
                'echo'  => array('商家所属类别错误'),
            ),
        ),
        
		
    );

    // 操作数据 =====================================

    /**
     * 增
     * @param   [arr] $data      [插入的数据]
     * @param   [arr] $call_data [插入的绑定数据]
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->call('data', $call_data)
            ->insert($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    /**
     * 改
     * 
     * @param  array  $where     where条件
     * @param  array  $data      更新的数据
     * @param  array  $call_data 更新的绑定数据
     * @return boolean
     */
    public function update($where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;//$bool;
    }

    /**
     * 删
     * @param   [str] $merchant_id [商家ID]
     * @return  bool
     */
    public function delete($merchant_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->where(array('merchant_id=[+]', $merchant_id))
            ->delete();

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    // 查询数据 =====================================

    
    
    
    /**
     * 获取一个自营商家并且状态正常的数据
     * 
     * @param   [str]  $merchant_id [商家ID]
     * @return  array
     */
    public function find_self_data(){
    	return object(parent::CACHE)->data(__CLASS__, __METHOD__, array(), function(){
	        return db(parent::DB_APPLICATION_ID)
	        ->table('merchant')
	        ->where(array('merchant_self=1'), array('[and] merchant_state=1'))
	        ->find();
		});
    }
    
    
    
    
    /**
     * 获取一个商家的基础数据
     * 
     * @param   [str]  $merchant_id [商家ID]
     * @return  array
     */
    public function find_base_data($merchant_id = '')
    {
    	return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_id), function($merchant_id){
	        return db(parent::DB_APPLICATION_ID)
	        ->table('merchant')
	        ->where(array('merchant_id=[+]', $merchant_id))
	        ->find(array(
				"merchant_id",
				"merchant_name",
				"merchant_state",
				"merchant_update_time",
				"merchant_insert_time"
			));
		});	
    }

    /**
     * 获取一个数据
     * 
     * @param   [str]  $merchant_id [商家ID]
     * @return  array
     */
    public function find($merchant_id = ''){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_id), function($merchant_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->where(array('merchant_id=[+]', $merchant_id))
            ->find();
        });
    }

    /**
     * 获取一条数据
     * 
     * @param   [array]  $call_where [查询条件]
     * @return  array
     */
    public function find_where($call_where = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where){
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->call('where', $call_where)
            ->find();
        });
    }


	/**
     * 获取一个数据
     * 
     * @param   [str]  $merchant_id [商家ID]
     * @return  array
     */
    public function find_join($merchant_id = '', $find = array() ){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_id), function($merchant_id){
        	
			//图片数据
            $logo_image = array(
                'table' => 'image logo_i',
                'type' => 'LEFT',
                'on' => 'logo_i.image_id = m.merchant_logo_image_id'
            );
			
			//图片数据
            $license_image = array(
                'table' => 'image license_i',
                'type' => 'LEFT',
                'on' => 'license_i.image_id = m.merchant_license_image_id'
            );
			
			if( empty($find) ){
				$find = array(
					"m.*",
					"logo_i.image_width as merchant_logo_image_width",
					"logo_i.image_height as merchant_logo_image_height",
					"license_i.image_width as merchant_license_image_width",
					"license_i.image_height as merchant_license_image_height",
				);
			}
			
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant m')
			->joinon($logo_image, $license_image)
            ->where(array('m.merchant_id=[+]', $merchant_id))
            ->find($find);
        });
    }



    
    /**
     * 根据商家名称，获取一个用户数据
     * 
     * @param   string   $merchant_name
     * @return  array
     */
    public function find_like_name($merchant_name = ''){
        if( empty($merchant_name) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_name), function($merchant_name){
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->where(array('[and] merchant_name LIKE "%[-]%"', (string)$merchant_name))
            ->find();
        });
    }
	

    public function select( $config=array() )
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
 
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->select($select);
        });
    }


    /**
     * 查——全部商家用户
     * 
     * @param  [arr] $config [查询配置]
     * @return array
     */
    public function select_join($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            //商家用户
            $merchant_user = array(
                'table' => 'merchant_user mu',
                'type' => 'INNER',
                'on' => 'mu.merchant_id = m.merchant_id'
            );
            
            if( empty($select) ){
                $select = array(
                    "mu.user_id",
                    "mu.merchant_user_name",
                    "mu.merchant_user_info",
                    "mu.merchant_user_state",
                    "m.*",
                );
            }
            
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant m')
            ->joinon($merchant_user)
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    }
	
	
    /**
     * 查——分页数据
     * @param  [arr]  $config [配置参数]
     * @return array
     */
    public function select_page($config = array()) {
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			//查询配置
	        $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
	        $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
	        $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
	        $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
	        $limit = array(
	            (isset($call_limit[0]) ? $call_limit[0] : 0),
	            (isset($call_limit[1]) ? $call_limit[1] : 0)
	        );
			
	        //设置返回的数据
	        $data = array(
	            'row_count'   => 0,
	            'limit_count' => $limit[0] + $limit[1],
	            'page_size'   => $limit[1],
	            'page_count'  => 0,
	            'page_now'    => 0,
	            'data'        => array()
	        );
	
	        //获取总条数
	        $total_count = db(parent::DB_APPLICATION_ID)
	            ->table('merchant m')
	            ->call('where', $call_where)
	            ->find('count(*) as count');
	
	        if (empty($total_count['count'])) {
	            return $data;
	        } else {
	            $data['row_count'] = $total_count['count'];
	            if (!empty($data['page_size'])) {
	                $data['page_count'] = ceil($data['row_count']/$data['page_size']);
	                $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
	            }
	        }
			
			if( empty($select) ){
				$sql_join_merchant_user_count = object(parent::TABLE_MERCHANT_USER)->sql_join_merchant_id_count("m");
				$select = array(
					"m.*",
					'('.$sql_join_merchant_user_count.') as merchant_user_count',
				);
			}
			
	        //查询数据
	        $rows = db(parent::DB_APPLICATION_ID)
	            ->table('merchant m')
	            ->call('where', $call_where)
	            ->call('orderby', $call_orderby)
	            ->call('limit', $call_limit)
	            ->select($select);
	
	        $data['data'] = $rows;
	
	        return $data;
		});
    }
	
	
	
    /**
     * 根据商家ID，判断商家是否存在
     * @param  [str] $merchant_id [商家ID]
     * @return bool
     */
    public function find_exists_id($merchant_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($merchant_id),
            function ($merchant_id) {
                return (bool)db(parent::DB_APPLICATION_ID)
                    ->table('merchant')
                    ->where(array('merchant_id=[+]', $merchant_id))
                    ->find('merchant_id');
        });
    }

    // 其它方法 =====================================

    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    // 检测数据 =====================================

    
    /**
     * 检测——商家是否认证
	 * 
     * @param  string 		$merchant_id 	商家ID
     * @return Boolean
     */
    public function check_authentication($merchant_id = ''){
        if ( empty($merchant_id) ){
        	return false;
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_id), function ($merchant_id) {
            //查询数据
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('merchant')
            ->where(array('merchant_state=1'), array('merchant_id=[+]', $merchant_id))
            ->find('merchant_id');
        });
    }
    
    
    
    
    /**
     * 检测——经度
     * @param  [dec] $v [经度]
     * @return bool
     */
    public function check_longitude($v)
    {
        return is_numeric($v) && ($v >= -180) && ($v <= 180);
    }

    /**
     * 检测——纬度
     * @param  [dec] $v [纬度]
     * @return bool
     */
    public function check_latitude($v)
    {
        return is_numeric($v) && ($v >= -90) && ($v <= 90);
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}