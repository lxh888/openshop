#### 设置字符编码

```
db()::charset(string $charset[,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$charset|字符编码名称|是||string[0]|
|closure|$closure|闭包函数|否|	|closure[0]	|

- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::charset() 操作是更换当前默认连接的编码，操作后，一直保持到该连接关闭。
 

##### 示例：

```
//更改字符编码  将默认的 utf8 修改为 gbk
db('测试')->charset('gbk');
//打印
printexit( db('测试')->info() );
```

```
/* ******************** 打印结果 ******************** */
Array(
    ......
    [id] => 测试
    [config] => Array(
        [type] => mysql
        [host] => 127.0.0.1
        [user] => root
        [pass] => root
        [base] => test
        [port] => 3306
        
        //字符编码已经被修改
        [charset] => gbk
        [prefix] => 
        [persistent] => 0
        [engine] => mysql
        [cache_folder_name] => db
        [method_log] => 1
        [query_log] => 1
        [lock_time] => 30
        [work_rollback_log_file] => 1
        [session] => Array
            (
            )

        )

   ......

)
```
