#### 获取当前标识的资源

~~~
db()::info(void);
~~~

~~~
//创建一个连接
db('测试');
//再创建一个连接
$db = db('测试2')->info();
//获取的是 测试2 的连接信息
printexit($db);
~~~

~~~
/* ******************** 打印结果 ******************** */
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
~~~
