#### 两个或多个联表

```
db()::joinon( 
        array( 'table'=>string $table_name 
        [,'type' => string $type = 'INNER' ]
        [,'on' => string $on = '' ]
        [,'prefix' => $prefix = config('db')['prefix'] ]
        [,'base' => $base_name = config('db')['base'] ] )
        
    	[,array(...)]
        
   		[,closure $closure]
    );
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|array|array[0]|数组|是| |array[0]|
|array|.....|......|否| |array[......]|
|array|array[n]|数组|否| |array[n]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::joinon() 操作再一次完整步骤中，使用多次的数据会连接拼凑，不覆盖在前面的操作数据。

##### 每个数组的参数值：

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string	|$table_name |	表名称|是| 	|array['table']|
|string	|$type |类型|否| INNER	|array['type']|
|string	|$on |条件|否| 	|array['on']|
|string	|$prefix |表前缀|否| config('db')['prefix']	|array['prefix']|
|string	|$base_name  |选择数据库|否| config('db')['base']	|array['base']|

* $table_name 的名称，如db()::table() 操作一样，数据表名称将会被收集起来，而且收录的是加上前缀的数据表名称。
* $prefix和$base_name 只是局部自定义，不会影响其他操作的配置。
* $type 默认“INNER”，通常有下面几种类型：
~~~
INNER: 如果表中有至少一个匹配，则返回行，等同于 JOIN 
LEFT: 即使右表中没有匹配，也从左表返回所有的行 
RIGHT: 即使左表中没有匹配，也从右表返回所有的行 
FULL: 只要其中一个表中存在匹配，就返回行 
~~~

##### 用法示例

~~~
$joinon = array(
    //也可以使用别名称，可以省略as
    'table' => 'user u',
    //可以小写
    'type' => 'inner',
    //这是条件
    'on' => 'l.user_id = u.id',
    //自动定义表前缀，当前有效，不会影响后面方法操作的配置
    'prefix' => 'cs_',
    //自定义数据库，当前有效，不会影响后面方法操作的配置
    'base' => 'test4'
);

$joinon2 = array(
    'table' => 'mobile as m',
    'type' => 'inner',
    'on' => 'l.user_id = m.user_id'
);

db('测试')->table('log as l')->joinon($joinon)->joinon($joinon2, function($info){
    printexit($info['query']);
});

//与上面的用法是等价的
db('测试')->table('log as l')->joinon($joinon,$joinon2, function($info){
    printexit($info['query']);
});
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
    [base] => Array(
        [test] => Array(
            [0] => log
            [1] => mobile
        )
        [test4] => Array(
            [0] => cs_user
        )
    )
    [table] => `test`.`log` AS l
    //自定义了 test4数据库，后面不设置默认test。可见自定义不会影响后面方法操作的配置。
    [joinon] => INNER JOIN `test4`.`cs_user` AS u ON l.user_id = u.id INNER JOIN `test`.`mobile` AS m ON l.user_id = m.user_id
)
~~~
