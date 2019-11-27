#### 选择数据库


```
db()::base(string $base_name[,bool $is_create = false][,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$base_name|数据库名称|是||string[0]|
|bool|$is_create|是否自动创建数据库|是|false|string[0]|
|closure|$closure|闭包函数|否|	|closure[0]	|

- $is_create 如果选择的数据库不存在，为true时那么根据数据库名称创建一个，为(默认)false时会报错。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::base() 操作再一次完整步骤中，使用多次的后面会覆盖前面。

---

##### 示例：

```
db('测试')->base('test3');
```

---
##### 不存在的数据库，自动创建：


```
db('测试')->base('test5', true);
$info = db('测试')->info();
printexit($info['config']);
```

```
/* ******************** 打印结果 ******************** */
Array(
    [type] => mysql
    [host] => 127.0.0.1
    [user] => root
    [pass] => root
    [base] => test5
    [port] => 3306
    [charset] => utf8
    [prefix] => 
    [persistent] => 0
    [engine] => mysql
    [cache_folder_name] => db
    [method_log] => 1
    [query_log] => 1
    [lock_time] => 30
    [work_rollback_log_file] => 1
    ......
)
```
