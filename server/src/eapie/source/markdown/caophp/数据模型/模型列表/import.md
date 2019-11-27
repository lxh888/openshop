
```
db()::import([string $sql][string $is_file = false][,closure $closure]);
```
|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$sql |SQL语句字符串或者SQL文件路径地址|否||string[0]|
|bool|$is_file|$sql是否是文件|否|false	|bool[0]	|
|closure|$closure|闭包函数|否|	|closure[0]	|

- $is_file 默认是false，不是文件，即$sql是字符串类型。如果为true，那么就是$sql就是文件地址。
- $closure 最多接受2个参数，如function($fetch, $resource){}，$fetch就是执行后的返回值，$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是$fetch。


```
$path = LOCALHOST_PATH.'/cs/db_export.sql';
//要把文件内容转成sql字符串来执行
$data = db('测试')->base('test5', true)->import( file_get_contents($path) );

//或者以文件的方式执行(推荐这样干，能执行大文件)
$data = db('测试')->base('test5', true)->import( $path, true );

printexit($data);
```



```
/* ******************** 打印结果 ******************** */
Array(
    //开始时间
    [start_time] => 2018-03-13 04:30:38
    [host] => 127.0.0.1
    [port] => 3306
    [user] => root
    [base] => test5
    //执行时错误的次数
    [error_count] => 0
    //执行SQL语句的次数
    [run_count] => 15
    //结束时间
    [end_time] => 2018-03-13 04:30:40
    //运行消耗时间，秒
    [runtime] => 1.9837629795074
)
```

