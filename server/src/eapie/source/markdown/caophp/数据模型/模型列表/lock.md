#### 锁表、锁库

```
db()::lock([string $command][,closure $closure]);
```
|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$command|操作命令|否||string[0]|
|closure|$closure|闭包函数|否|	|closure[0]	|

- 这是独占锁。
- $command 如果为空，则锁类型全部为false。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::lock() 可以多次操作，后面会覆盖前面。
- 开启锁失败会报错。
- 锁是有超时时间配置的，超过了这个时间就会报错。注意了，超时报错的时候会删除锁表文件的，如果想保证操作数据的安全，可以将超时的时间设为更长，但是超时时间太长，会成为死锁，需谨慎操作。
- 注意，这是框架内部的数据表锁，该锁对 db()::query() 方法是无效的，但是锁库是有效的。
- 锁表的检测是在 db()::insert()、db()::update()、db()::delete()、db()::select()、db()::find() 方法的闭包函数之前执行的。
- 锁库的检测是在 db()创建连接标识时、db()::query()方法的闭包函数之前执行的。
- 一定要注意最后执行的代码与锁表的关系，尤其是开启会话时，锁表一定要提前关闭。
- 注意，同个进程，多个标识连接，混乱使用很容易将自己锁住。
- 锁库是最危险的，要谨慎使用。
 


##### 命令参考：

| 命令 | 备注 | 触发方法|
|---|---| ---| ---|
|b | 开启base锁(数据库锁) |db()创建连接时、db()::query()、db()::insert()、db()::update()、db()::delete()、db()::select()、db()::find()|
|s | 开启select锁 |db()::select()、db()::find()|
|i | 开启insert锁 |db()::insert()|
|u | 开启update锁|db()::update()|
|d | 开启delete锁|db()::delete()|
|c | close 关闭锁 |程序结束、终止、报错，或手动关闭 |

- 命令是不区分大小写的。
- 命令可以同时存在。
- 注意，c命令是关闭锁，如果跟其他命令同时存在，那么c具有优先级。
- 锁开启后，业务完毕后，尽量用关闭命令将其关闭。


```
db('测试')->lock('b');
//只要是当前操作获得了锁权限，那么可以更新锁表，会覆盖之前的 ->lock('b') 设置
db('测试')->table('user')->lock('s');
db('测试', function($db){
	$db->table('user');
	$db->lock();
	//这是设置 查询、插入、更新、删除 4个表锁
	$db->lock('siud');
	//命令不区分大小写，并且可以空格
	$db->lock('S I');
	sleep(10);
	//关闭锁表
	$db->lock('c');
	sleep(10);
});
```


---

##### 开启多个不同数据库的锁

> 操作A:

```
db('多个数据库锁')
->joinon(array('base' => 'test2'), array('base' => 'test3'))
->lock('b');
```
> 操作B:

```
$db_config = config('db');
$db_config['base'] = 'test2';
//这个时候就会等待，等到操作A锁关闭才可执行
db('测试', $db_config, function($p){
	printexit('2333', $p->info());
});
```

---

##### 开启不同多个主机或端口的锁
根据不同的主机，或者不同的端口，可以开启不同的锁。在不同的进程或操作中，互不影响。

> 操作A:

```
$db_config = config('db');
$db_config['host'] = '127.0.0.1';
db('A锁', $db_config)
->lock('b');
```

> 操作B:


```
$db_config = config('db');
$db_config['host'] = '127.1.1.1';
db('B锁', $db_config)
->lock('b');
	
```
