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
class event extends main {
	
	
	
	/* 事件表 */
	
	
		
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(
		__CLASS__, 
		"user_credit", 
		"user_money_share", 
		"user_money_annuity",
		"user_money_earning",
		"user_money_help",
		"order"
		);
	
	
	
	
	
	
	/**
	 * 获取一个id号
	 * 
	 * @param	void
	 * @return	string
	 */
	public function get_unique_id(){
		return cmd(array(22), 'random autoincrement');
	}	
	
	
	
	
	/**
	 * 插入一个事件
	 * 如果存在，那么修改 更新时间，获取事件ID
	 * 
	 * @param	string		$event_name			事件名称
	 * @param	int			$event_stamp		事件标记
	 */
	public function get_event_data( $event_name = "", $event_stamp = '' ){
		if( empty($event_name) || empty($event_stamp) ){
			return false;
		}
		
		$insert_data = array(
			"event_id" => $this->get_unique_id(),
			"event_name" => $event_name,
			"event_stamp" => (string)$event_stamp,
			"event_state" => 0,
			"event_update_time" => time(),
			"event_insert_time" => time()
		);
		
		$sql_insert = db(parent::DB_APPLICATION_ID)
		->table('event')
		->insert($insert_data, function($info){
			return $info['query']['insert'];
		});
		//INSERT 中 ON DUPLICATE KEY UPDATE的使用
		$update_data = array(
			"event_update_time" => time()
		);
		$sql_update = db(parent::DB_APPLICATION_ID)
		->table('event')
		->update($update_data, function($info){
			return $info['query']['set'];
		});
		db(parent::DB_APPLICATION_ID)->query($sql_insert.' ON DUPLICATE KEY UPDATE '.$sql_update);
		
		return db(parent::DB_APPLICATION_ID)
		->table('event')
		->where( array('event_name=[+]', (string)$event_name), array('[and] event_stamp=[+]', (string)$event_stamp) )
		->find();
	}
	
	
	
				
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$event_id
	 * @return	array
	 */
	public function find($event_id = ''){
		if( empty($event_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($event_id), function($event_id){
			return db(parent::DB_APPLICATION_ID)
			->table('event')
			->where(array('event_id=[+]', (string)$event_id))
			->find();
		});
		
	}	
	
	
	
	
	
	
	
	
	/**
	 * 插入一个事件
	 * 如果存在，那么修改 更新时间，获取事件ID
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	bool
	 */
	public function insert( $data = array(), $call_data = array() ){
		if( empty($data['event_name']) || 
		empty($data['event_stamp']) ){
			return false;
		}
		
		if( empty($data['event_id']) ){
			$data['event_id'] = $this->get_unique_id();
		}
		
		if( empty($data['event_update_time']) ){
			$data['event_update_time'] = time();
		}
		if( empty($data['event_insert_time']) ){
			$data['event_insert_time'] = time();
		}
		if( empty($data['event_state']) ){
			$data['event_state'] = 0;
		}
		
		if( !empty($data["event_info"]) && is_array($data["event_info"]) ){
			$data["event_info"] = cmd(array($data["event_info"]), "json encode");
		}else{
			$data["event_info"] = "";
		}
		if( !empty($data["event_json"]) && is_array($data["event_json"]) ){
			$data["event_json"] = cmd(array($data["event_json"]), "json encode");
		}else{
			$data["event_json"] = "";
		}
		if( !empty($data["event_error"]) && is_array($data["event_error"]) ){
			$data["event_error"] = cmd(array($data["event_error"]), "json encode");
		}else{
			$data["event_error"] = "";
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('event')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			return $data['event_id'];
		}else{
			return false;
		}
		
		
	}
	
	
	
	
	/**
	 * 更新数据
	 * 
	 * @param	string		$event_id
	 * @param	array		$data
	 * @return 	void
	 */
	public function update_data($data = array()){
		if( empty($data["event_id"])  ){
			return false;
		}
		
		$event_id = $data["event_id"];
		unset($data["event_id"]);
		if( empty($data) ){
			return false;
		}
		
		if( !isset($data['event_update_time']) ){
			$data['event_update_time'] = time();
		}
		
		if( !empty($data["event_info"]) && is_array($data["event_info"]) ){
			$data["event_info"] = cmd(array($data["event_info"]), "json encode");
		}else{
			$data["event_info"] = "";
		}
		
		if( !empty($data["event_json"]) && is_array($data["event_json"]) ){
			$data["event_json"] = cmd(array($data["event_json"]), "json encode");
		}else{
			$data["event_json"] = "";
		}
		
		if( !empty($data["event_error"]) && is_array($data["event_error"]) ){
			$data["event_error"] = cmd(array($data["event_error"]), "json encode");
		}else{
			$data["event_error"] = "";
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('event')
		->where( array("event_id=[+]", (string)$event_id) )
		->update($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
	
	
	
	
	/**
	 * 根据当天最初时间戳、事件名称 获得事件数据
	 * 
	 * @param	string		$event_name			事件名称
	 * @param	int			$event_stamp		事件标记
	 */	
	public function find_name_stamp($event_name = "", $event_stamp = 0){
		if( empty($event_name) || empty($event_stamp) ){
			return false;
		}
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($event_name, $event_stamp), function($event_name, $event_stamp){
			return db(parent::DB_APPLICATION_ID)
			->table('event')
			->where( array("event_name=[+]", (string)$event_name), array("[and] event_stamp=[+]", (string)$event_stamp) )
			->find();
		});
		
	}
	
	
	
	
	

	
		

	
	
	
	
	
	
	
}
?>