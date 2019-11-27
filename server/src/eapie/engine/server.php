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



namespace eapie\engine;
class server extends \eapie\engine\init {
	
	
	public function database_config(){
		$table_session_class = parent::TABLE_SESSION;
		return array(
			'host'=> '127.0.0.1', //主机    localhost
			'user'=> 'root',//数据库账号,用户名
			'pass'=> 'e6ybGKLeRezfX7QD', //密码
			'base'=> 'db_server', //数据库名
			'prefix'=> 'server_',
			'charset' => 'utf8mb4',
			'persistent' =>false,//开启长连接的时候，会连接丢失(时间超时)报错
			'method_log' => false,
			'query_log'=> false,//是否记录query语句信息。
			'lock_time' => 30,
			
			'session' => array(
				//session的id
				'id' => function() use ($table_session_class) {
					return object($table_session_class)->get_unique_id();
					},
				//失效时间
				'expire_time' => function() use ($table_session_class) {
					return object($table_session_class)->get_expire_time();
					},
				'clear' => true,//开启自动清理
				'found' => true,//如果数据表不存在则自动创建
				'found_log_file' => true,//自动创建数据表时，生成日志文件
				'lock_expire_time' => 30,//会话锁有效时间，为0代表不锁。单位秒
				'lock_timeout_time' => 15,//会话锁超时时间，为0代表只要已经被锁直接返回超时信息。单位秒
				//表结构
				'table' => array(
					'name' => 'session',//表名称
					'engine' => 'MyISAM',//表引擎
					'comment' => '每个访问都会记录会话数据',//表备注
					//表字段列表
					'field' => array(
						'session_id' => 'id',
						'session_left_token' => 'unique',
						'session_right_token' => 'unique',
						'session_websocket_token' => 'unique',
						'application_id' => 'var',
						'user_id' => 'var',
						'session_ip' => 'var',//ip地址
						'session_browser' => 'var',//浏览器类型。""是未知，电脑网页、手机网页、"wechat"微信端
						'session_public' => 'json',//公开的json数据
						'session_private' => 'serialize',//私有的serialize数据,安全性更高
						'session_expire_time' => 'expire_time',
						'session_found_time' => 'found_time',
						'session_now_time' => 'now_time',
						'session_lock' => 'lock' //会话存在锁为1，否则为0。存在锁则等待
					),
				),
						
			),
		);
	}
	
	
	
}
?>