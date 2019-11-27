#### 数据插入

```
db()::insert([array $field_list][array ...][,closure $closure]);
```


|类型|	参数|	备注|	是否必须|	默认值|	顺序|
|:-----------:| ---------- | --- | --- | --- | --- |
|array |	$field_list|数据列表|	否| |	array[0]|
|array |	......|......|	否|	|	array[n]|
|closure|	$closure|	闭包函数|	否|	|closure[0]|	

- db()::update()的 $field_list 的参数形式与db()::insert()的 $field_list 参数形式一致。
- 在db()::insert()传入的数据参数是具有优先级的，会覆盖db()::data()传入的数据。
- 在db()::insert()传入的数据，会进行数据过滤和加引号，其中整数和浮点数不过滤，不加引号。
- 插入成功后，如果表存在自增id，那么返回值就是这个刚插入的数据的自增id值。插入失败，返回false。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是SQL执行的结果。
- 注意，闭包函数是在SQL语句执行之前执行，所以如果在闭包函数中返回值不是NULL，那么可以阻止SQL的执行，并且最终也是返回值这个闭包函数的返回值。



```
$data = array('name'=>'王阿和', 'time'=>time());
//name键会被清理一个，后面的覆盖前面的。
$data2 = array('name'=>'曹操', 'info'=>"是一个人才");
db('测试')->table('user')->insert($data, $data2, function($info){
	printexit($info['query']);
});
```


```
/* ******************** 打印结果 ******************** */
Array (
    [base] => Array
        (
            [test] => Array
                (
                    [0] => user
                )

        )

    [table] => `test`.`user`
    [data] => Array
        (
            [name] => '曹操'
            [time] => 1520966809
            [info] => '是一个人才'
        )

    [values] =>  ( `name`,`time`,`info` ) values ( '曹操',1520966809,'是一个人才' )
    [insert] => INSERT INTO `test`.`user` ( `name`,`time`,`info` ) values ( '曹操',1520966809,'是一个人才' )
    [sql] => INSERT INTO `test`.`user` ( `name`,`time`,`info` ) values ( '曹操',1520966809,'是一个人才' );
)

```

