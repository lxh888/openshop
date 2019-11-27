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
class lock extends main {
	
	
	/* 事务锁 */
	
	
	/**
     * 获取锁事务类型
     * 
     * @param   void
     * @return  array
     */
    public function get_transaction(){
        return array(
            parent::LOCK_STATE => "状态操作",
            parent::LOCK_PAY_STATE =>"支付状态操作",
            parent::LOCK_CREDIT =>"积分操作",
            parent::LOCK_MONEY =>"余额操作",
            parent::LOCK_EVENT =>"事件操作",
			parent::LOCK_MONEY_ANNUITY => "养老金操作",
            parent::LOCK_MONEY_EARNING =>"赠送收益操作",
            parent::LOCK_MONEY_HELP =>"扶贫资金操作",
            parent::LOCK_MONEY_SERVICE =>"服务费操作",
            parent::LOCK_MONEY_SHARE =>"消费共享金操作",
        );
    }
    
	
	
	
	/**
	 * 开启事务
	 * 如果同一个商家存在同一个事务，并且在有效时间之中，那么等待。如果超过 $valid_s 则返回false
	 * 在开始时，就要删除已经失效的同商家同事务的数据，而每次等待的时候，也要删除
	 * 
	 * @param	string	$lock_key				字段名称
	 * @param	string	$lock_value				字段值
	 * @param	string	$lock_transaction		事务名称
	 * @param	int		$valid_s				有效的秒数
	 * @param	int		$not_auto				是否不自动删除
	 * @return	lock ID | bool	返回事务锁ID，事务开启成功，否则返回false 事务开启失败
	 */
	public function start($lock_key = "", $lock_value = "", $lock_transaction = "", $valid_s = 30, $not_auto = false){
		if( !is_numeric($valid_s) ) $valid_s = 30;
		$lock_id = cmd(array(22), 'random autoincrement');//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('lock', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`lock_id`".
			",`lock_key`".
			", `lock_value`".
			", `lock_transaction`".
			", `lock_expire_time`".
			", `lock_insert_time`".
			")";
		});
		
		$sql_exist = db(parent::DB_APPLICATION_ID)
		->table('lock')
		->where(
			array('lock_key=[+]', (string)$lock_key), 
			array('[and] lock_value=[+]', (string)$lock_value),
			array('[and] lock_transaction=[+]', (string)$lock_transaction)
			)
		->find(array('lock_id'), function($q){
			return $q['query']['find'];
		});
		
		$where = array();
		$where[] = array("NOT EXISTS([-])", $sql_exist, TRUE);
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('lock')
		->call('where', $where)
		->select(function($p) use ($lock_id, $lock_key, $lock_value, $lock_transaction, $valid_s){
			return "SELECT ". 
			"\"".$lock_id."\"".
			",\"".$lock_key."\"".
			",\"".$lock_value."\"".
			",\"".$lock_transaction."\"".
			",".(time() + $valid_s).
			",".time().
			" FROM DUAL " . $p['query']['where'];
		});
		
		$sql = $insert_sql." ".$select_sql;
		
		$sleep_s = 3;//每次随眠时间，单位秒
		$total_s = 0;//累计等待的时间，单位秒
		$bool = false;
		while(true){
			if( $total_s >= $valid_s){
				break;
			}	
			$bool = (bool)db(parent::DB_APPLICATION_ID)->query($sql);
			if( empty($bool) ){
				//清理
				$this->clear();
				sleep($sleep_s);
				$total_s += $sleep_s;
			}else{
				break;
			}
		}
		
		if( empty($bool) ){
			return false;
		}
		
		//如果不自动删除
		if( !empty($not_auto) ){
			return $lock_id;
		}
		
		//最后要自动删除
		//获取数据库配置，防止标识资源被销毁
		$info = db(parent::DB_APPLICATION_ID)->info();
		$db_config = $info['config'];
		$db_application_id = parent::DB_APPLICATION_ID;
		destruct('lock.close:'.$lock_id, true, array($db_config, $db_application_id), function($db_config, $db_application_id) use ($lock_id){
			//重置一下 数据库链接
			db($db_application_id, true, $db_config);
			object(parent::TABLE_LOCK)->close($lock_id);//关闭锁
		});
		
		return $lock_id;
	}
	
	
	
	
	
	/**
	 * 检查
	 * 
	 * @param	string	$lock_key				字段名称
	 * @param	string	$lock_value				字段值
	 * @param	string	$lock_transaction		事务名称
	 * @return	bool
	 */
	public function check($lock_key = "", $lock_value = "", $lock_transaction = ""){
		//先清理
		$this->clear();
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('lock')
		->where(
			array('lock_key=[+]', $lock_key), 
			array('[and] lock_value=[+]', $lock_value), 
			array('[and] lock_transaction=[+]', $lock_transaction),
			array('[and] lock_expire_time>[]', time())
			)
		->find("lock_id");
	}
	
	
	
	
	
	
	/**
	 * 关闭事务
	 * 
	 * @param	string | array	$lock_ids	事务锁ID，可以是多个
	 * @return	bool
	 */
	public function close($lock_ids = NULL){
		if( empty($lock_ids) ){
			return false;
		}
		
		$where = array();
		if( is_string($lock_ids) || is_numeric($lock_ids)){
			$where[] = array('lock_id=[+]', (string)$lock_ids);
		}else
		if( is_array($lock_ids)){
			foreach($lock_ids as $lock_id){
				if( empty($where) ){
					$where[] = array('[and] lock_id=[+]', $lock_id);
				}else{
					$where[] = array('[or] lock_id=[+]', $lock_id);
				}
			}
		}
		
		if( empty($where) ){
			return false;
		}
		
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('lock')
		->call('where', $where)
		->delete();
	}
	
	
	
	
	
	
	/**
	 * 清理
	 * 
	 * @param	void
	 * @return	bool
	 */
	public function clear(){
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('lock')
		->where( array('lock_expire_time<[]', time()) )
		->delete();
	}
	
	
	
	
	
	
	/**
	 * 获取锁
	 * 
	 * @param	string | array	$lock_ids	事务锁ID，可以是多个
	 * @return	array
	 */
	public function get($lock_ids = NULL){
		if( empty($lock_ids) ){
			return false;
		}
		
		$where = array();
		if( is_string($lock_ids) || is_numeric($lock_ids)){
			$where[] = array('lock_id=[+]', (string)$lock_ids);
		}else
		if( is_array($lock_ids)){
			foreach($lock_ids as $lock_id){
				if( empty($where) ){
					$where[] = array('[and] lock_id=[+]', $lock_id);
				}else{
					$where[] = array('[or] lock_id=[+]', $lock_id);
				}
			}
		}
		
		if( empty($where) ){
			return false;
		}
		
		return db(parent::DB_APPLICATION_ID)
		->table('lock')
		->call('where', $where)
		->select();
	}
	
	
	
	
	
	
	
	
	
}
?>