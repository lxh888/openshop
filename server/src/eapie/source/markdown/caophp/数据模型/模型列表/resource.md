#### 获取当前类的所有标识资源
~~~
db()::resource(void)
~~~
示例：
~~~
//创建一个连接
db('测试');
//再创建一个连接
$db = db('测试2')->resource();
printexit($db);
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
    [测试] => Array(
        [runtime] => Array(
            [0] => 0
            )
        [query] => Array()
        [id] => 测试
        [config] => Array()
        [register] => mysqli Object()
        [frequency] => 1
        [location] => Z:\WWW\website\localhost\index.php 所在 37 行
        [method_log] => Array()
        [query_log] => Array()
        )

    [测试2] => Array(
        [runtime] => Array(
            [0] => 0
            )

        [query] => Array
            (
            )
        [id] => 测试2
        [config] => Array()
        [register] => mysqli Object()
        [frequency] => 1
        [location] => Z:\WWW\website\localhost\index.php 所在 38 行
        [method_log] => Array()
        [query_log] => Array()
        )
)
~~~