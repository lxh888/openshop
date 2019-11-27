#### 指定操作的数据表


```
db()::table([string $table_name = ''][string $table_name = ''][......][,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$table_name|表名称|否|空字符串|string[0]|
|string|.....|n个表名称|否|空字符串|string[n]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $table_name 可以附加别名称，也不带别名称。如table，也可以带别名称table as t，或者省略as，如table t
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::table() 操作再一次完整步骤中，使用多次的数据会连接拼凑，不覆盖在前面的操作数据。


---
##### 使用闭包函数获得操作信息，示例：

```
db('测试')->table("productdetail", function($info){
    printexit( $info );
});
```

```
/* ******************** 打印结果 ******************** */
Array
(
    [runtime] => Array(
        [0] => 0
        )
	//[query]是专门收集当前操作的SQL语句拼凑
    [query] => Array(
   		 	//这是数据库单元
            [base] => Array(
            		//每个键是数据库名称，里面包含属于他的数据列表
                    [test] => Array(
                            [0] => productdetail
                        )
                )
			//这是表语句完整拼凑，自动添加了数据库名称
            [table] => `test`.`productdetail`
        )

    [id] => 测试
    [config] => Array()
    [register] => mysqli Object()
    [frequency] => 1
    [location] => Z:\WWW\website\localhost\index.php 所在 37 行
    [method_log] => Array()
    [query_log] => Array()
)
```

---
##### 设置多个表名称，示例：


```
db('测试')->table("productdetail", "user u", "book", "admin as a", function($info){
    printexit( $info );
});
```


```
/* ******************** 打印结果 ******************** */
.......
[query] => Array(
    [base] => Array(
        [test] => Array(
            [0] => productdetail
            [1] => user
            [2] => book
            [3] => admin
            )
        )
    //这里根据参数的顺序被完整拼凑
    [table] => `test`.`productdetail`,`test`.`user` AS u,`test`.`book`,`test`.`admin` AS a
)
......
```

---
##### 实现不同的数据库联表操作，下面给出三种方式，结果等价的。示例：

```
//闭包的方式
db('测试', function($obj){
    $obj->table("productdetail as l");
    //换数据库
    $obj->base('test2');
    $obj->table("user u", "book");
    //再换数据库
    $obj->base('test3');
    $obj->table("admin as a",function($info){
    //打印数据
    printexit( $info );
    });
});

//将对象赋值给一个变量的用法
$obj = db('测试');
$obj->table("productdetail as l");
//换数据库
$obj->base('test2');
$obj->table("user u", "book");
//再换数据库
$obj->base('test3');
$obj->table("admin as a",function($info){
    //打印数据
    printexit( $info );
    });

//下面是引用的方式
$db_object = NULL;
db('测试', function($object) use (&$db_object){
    $db_object = $object;
})->table("productdetail as l", function() use (&$db_object){
    //换数据库
    $db_object->base('test2');
})->table("user u", "book", function() use (&$db_object){
    //再换数据库
    $db_object->base('test3');
})->table("admin as a", function($resource){
    //打印数据
    printexit( $resource );
});
```

```
/* ******************** 打印结果 ******************** */
.......
[runtime] => Array(
        [0] => 0.022807836532593
        )

[query] => Array(
    [base] => Array(
        [test] => Array(
            [0] => productdetail
            )
        [test2] => Array(
            [0] => user
            [1] => book
            )
        [test3] => Array(
            [0] => admin
            )
        )
    //三个不同数据库的表被完整拼凑
    [table] => `test`.`productdetail` AS l,`test2`.`user` AS u,`test2`.`book`,`test3`.`admin` AS a
)
......
```
