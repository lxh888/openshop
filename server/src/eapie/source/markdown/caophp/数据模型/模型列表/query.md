#### 执行一条SQL语句


```
db()::query([string $sql = ''][,bool $is_fetch_object = false][,closure $closure]);
```



|类型|	参数|	备注|	是否必须|	默认值|	顺序|
|:-----------:| ---------- | --- | --- | --- | --- |
|string |	$sql|一条SQL语句字符串|	否|	空字符串|	string[0]|
|bool   |$is_fetch_object|返回数据是否对象|	否|	false|	bool[0]|
|closure|	$closure|	闭包函数|	否|	|closure[0]|	

- $is_fetch_object 如果是数据，返回的是否为对象。true返回的是对象，false返回的是数组。
- $closure 最多接受2个参数，如function($fetch, $resource){}，$fetch就是执行SQL后的返回值，$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是$fetch。
- 注意，闭包函数是在SQL语句执行后执行。


---

##### 执行一条SQL语句，返回是索引数组。示例：

```
$data = db('测试')->query("show databases");
printexit($data, db());
```

```
/* ******************** 打印结果 ******************** */
Array(
    [0] => Array(
        [Database] => information_schema
        )
    [1] => Array(
        [Database] => mysql
        )
    [2] => Array(
        [Database] => performance_schema
        )
    [3] => Array(
       [Database] => test
        )
    [4] => Array(
       [Database] => test2
        )
)
```

##### 注意数据库的选择
在执行的时候，他是连接当前选择的数据库来执行，如：

```
$config = config('db');
$config = array(
'base'=>'test',
);
//下面是查询 test 数据库的 session 数据表 
db('测试', $config)->query('select * from session limit 0,1', function($p, $info){
	printexit($p);
});
```



---

##### 执行一条SQL语句，返回索引对象。示例：

```
//返回索引对象
$data = db('测试')->query("show databases", true);
printexit($data, db());

//循环打印对象值
$i = 1;
foreach($data as $value){
    echo "tset ".$i." info:".$value->Database."<br/>";
    $i ++;
}
```


```
/* ******************** 打印结果 ******************** */
Array
(
    [0] => stdClass Object (
        [Database] => information_schema
        )
    [1] => stdClass Object (
        [Database] => mysql
        )
    [2] => stdClass Object (
        [Database] => performance_schema
        )
    [3] => stdClass Object (
       [Database] => test
        )
    [4] => stdClass Object (
       [Database] => test2
        )
)

//循环打印对象值
tset 1 info:information_schema
tset 2 info:mysql
tset 3 info:performance_schema
tset 4 info:test
tset 5 info:test2
```

---

##### 闭包函数的用法：

```
db('测试')->query("show databases", function($data, $info){
    printexit($data, $info);
    //如果闭包函数返回值为NULL(或者无返回值)，那么自动返回的是 $data 数据
    //如果闭包函数返回值，如 return 123，那么最终返回的是 123 数据。
});
```

```
/* ******************** 打印结果 ******************** */

//这是打印 $data 的数据
Array
(
    [0] => Array(
        [Database] => information_schema
        )
    [1] => Array(
        [Database] => mysql
        )
    [2] => Array(
        [Database] => performance_schema
        )
    [3] => Array(
       [Database] => test
        )
    [4] => Array(
       [Database] => test2
        )
)

//这是打印 $info 的数据
Array(
    [runtime] => Array(
        [0] => 0
        )
    [query] => Array()
    
    //标识是“测试2”说明获取的是 测试2 的连接信息
    [id] => 测试2
    [config] => Array()
    [register] => mysqli Object()
    [frequency] => 1
    [location] => Z:\WWW\website\localhost\index.php 所在 40 行
    [method_log] => Array()
    [query_log] => Array()
)
```

---

##### 闭包函数也可以接受一个参数，或者不接受参数：


```
db('测试')->query("show databases", function($data){
    pre($data);
});
$data = db('测试')->query("show databases", function(){
    return "这是测试，闭包没有传入参数~~~";
});
echo $data;
```


```
/* ******************** 打印结果 ******************** */
//闭包函数中打印
Array(
    [0] => Array(
        [Database] => information_schema
        )
    [1] => Array(
        [Database] => mysql
        )
    [2] => Array(
        [Database] => performance_schema
        )
    [3] => Array(
       [Database] => test
        )
    [4] => Array(
       [Database] => test2
        )
)

//最后打印 $data
这是测试，闭包没有传入参数~~~
```
