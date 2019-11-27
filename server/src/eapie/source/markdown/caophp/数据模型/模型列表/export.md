```
db()::export([string $filename][array $table_list][,closure $closure]);
```
|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$filename|文件地址名称|否||string[0]|
|string|$table_list|将被导出的表名称列表|否||string[0]|
|closure|$closure|闭包函数|否|	|closure[0]	|

- $filename 文件如果有内容，会被覆盖。如果存在没有创建的目录将会创建。
- $table_list 如果为空，那么就是导出数据库的所有的表。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。

##### 命令参考：

| 命令 | 备注 |
|---|---| ---| 
|s | structure 备份表结构 |
|d | data 备份表数据 |

- 命令是不区分大小写的。
- 命令可以同时存在。


##### 筛选表来导出数据与结构
```
$path = LOCALHOST_PATH.'/cs/db_export.sql';
$tables = array('订单周期表','user', 'cs_view_user');
//导出结构和数据
db('测试')->export($path, $tables, 'sd');

//只导出结构
db('测试')->export($path, $tables, 's');
//只导出数据
db('测试')->export($path, $tables, 'd');
```


---

##### 导出数据库所有表的数据

```
$db_config = config('db');
$db_config['base'] = 'test3';
$path = LOCALHOST_PATH.'/cs/db_export.sql';
db('测试', $db_config)->export($path, 'sd');
```
