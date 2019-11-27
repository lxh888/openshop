#### 会话模块


```
db()::session([array $data = array()][,closure $closure]);
```

|类型|	参数|	备注|	是否必须|	默认值|	顺序|
|:-----------:| ---------- | --- | --- | --- | --- |
|array |	$data|需要更新的session数据|	否|	array()|	array[0]|
|closure|	$closure|	闭包函数|	否|	|closure[0]|	

- $data 是需要更新的session数据，自动会判断数组里面是否存在session的id字段，不存在则报错，存在以此为更新条件。
- $closure 最多接受2个参数，如function($fetch, $resource){}，$fetch就是执行后的返回值，如果是开启会话，那么返回数据表示成功，false表示失败;如果是更新数据，默认成功返回true，失败返回false，但是有自定义的情况，所以可能会有其他自定义的返回值。$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是$bool。

##### 初始化、开启数据库会话并返回会话数据
参数为空，则是对数据库的会话初始化，注意，一定要对id初始化值进行设置，否则系统会自动生成一个默认id。
~~~
/*先要定义session的id初始值*/
$db_config = config('db');
$db_config['session']['id'] = function() {
    //判断session_id在cookie中是否存在，不存在则生成一个
    if( empty($_COOKIE['SESSIONID']) ){
        $_COOKIE['SESSIONID'] = cmd(array(22), 'random autoincrement');
    }
    /* 创建cookie信息 */
    //time() + 2592000 当前时间+ 30天的秒数
    setcookie('SESSIONID', $_COOKIE['SESSIONID'], time() + 2592000, "/");
    //最后返回这个id
    return $_COOKIE['SESSIONID'];
};
//更新配置
config('db', $db_config);
    
//开启会话，返回的是会话数据
$_SESSION = db('测试')->session();
~~~

或者只让局部有效的用法：
~~~
$db_config = config('db');
$db_config['session']['id'] = function() {
	//......
};
//不更新配置
//config('db', $db_config);
//开启会话，返回的是会话数据
$_SESSION = db('测试', $db_config)->session();
~~~



* * * * *

##### 设置自动更新
开启会话后，需要手动添加析构方式的自动更新。为了保证自动更新会话的配置与开启会话的配置一致性。那么在定义析构时，就把当前配置传入参数中，如下：


```
$db_config = config('db');
$db_config['session']['id'] = function() {
	//......
};
$_SESSION = db('测试', $db_config)->session();

//将$db_config 传入参数中，保持与开启会话时的配置一致性
destruct('更新会话', array($db_config), function($db_config){
	db('测试', $db_config)->session($_SESSION);
});
```


---

##### 自定义过期时间
过期时间默认是一个小时(也就是3600秒)。在定义过期时间的时候，可以在开启会话之前，或者在更新会话之前。下面是定义示例：


```
$db_config = config('db');
$db_config['session']['expire_time'] = function() {
    if(!empty($_SESSION['user_id'])){
        //当前时间+ 30天的秒数
    	return time() + 2592000;
    }else{
    	//当前时间+ 2个小时的秒数
    	return time() + 7200;
        }
};

//更新配置
config('db', $db_config);

```


* * * * *

##### 关闭或打开自动清理、设置自定义自动清理
自动清理默认是打开的。在初始化数据库会话的时候，就会生成一个自动清理过期会话数据的析构。但有时候，我有关闭自动清理和自定义自动清理的需求。

> 下面可以从两个方面进行操作：
~~~
//从[配置]操作。是否开启自动清理，默认true开启，false不开启
config('db')['session']['clear'];

//从[析构]操作
destruct();
~~~

可以在开启会话的时候，就应该配置是否开启自动清理，因为在开启的时候会才会生成自动清理的析构。而在会话开启之后，使用修改配置是无效的。因为在生成的析构中，已经把开启会话之时的配置复制一份，最后清理时只认该配置。

> 所以在开启会话之前，就应该设置是否开启自动清理
~~~
$db_config = config('db');
$db_config['session']['session']['table']['data']['id'] = function() {
	//......
};
//关闭自动清理
$db_config['session']['clear'] = false;
//开启会话，返回的是会话数据
$_SESSION = db('测试', $db_config)->session();
~~~

但是有时候会出现特殊需求，比如在开启会话后生成了自动清理析构，但是后面又需要关闭他，或者要修改其配置参数，这个时候我们可以用析构覆盖的方式来操作。

> 首先要知道会话 [自动清理] 析构标识的命名规则：
~~~
//[自动清理] 标识的规则
db.session.clear:session的id

//比如，session的id为:6de7f85eb25662995e62d71520688054271
//获取 当前会话的自动清理 析构
destruct('db.session.clear:6de7f85eb25662995e62d71520688054271')
~~~


> 那么我们就可以进行覆盖操作了：

```
$session_destruct = 'db.session.clear:6de7f85eb25662995e62d71520688054271';
//获取旧的析构信息
$destruct_array = destruct($session_destruct);
//$destruct_array['args'] 是一个索引数组，[0]即是第一个参数
//配置参数不变，只替换闭包函数
//覆盖要加上布尔值 true
destruct($session_destruct, $destruct_array['args'][0], true, function($config){
	db('测试.会话清理', $config, function($db){
		/*设置有效时间*/
		//未登录状态下，保存的有效时间。3600为一个小时
		$no_login_time = 3600;
		//已经登录状态下，保存的有效时间。((3600*24)*30)为一个月
		$login_time = ((3600*24)*30);
	
		$db->table('session');
		//非登录的会话
		$db->where(
			array('session_now_time<[-]', time()-$no_login_time), 
			array('[and] user_id = ""')
		);
		//登录的会话
		$db->where(
			array('[or] session_now_time<[-]', time()-$login_time), 
			array('[and] user_id <> ""')
		);
		//开始清理
		$db->delete(function($p){
			//printexit($p);
			});
		});
});
```
> 删除的方式即用无操作闭包函数覆盖：

```
destruct($session_destruct, true, function(){
	return NULL;
});
```


