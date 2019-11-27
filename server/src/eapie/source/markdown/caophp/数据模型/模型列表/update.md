#### 数据更新

```
db()::update([array $field_list][array ...][,closure $closure]);
```


|类型|	参数|	备注|	是否必须|	默认值|	顺序|
|:-----------:| ---------- | --- | --- | --- | --- |
|array |	$field_list|数据列表|	否| |	array[0]|
|array |	......|......|	否|	|	array[n]|
|closure|	$closure|	闭包函数|	否|	|closure[0]|	

- db()::update()的 $field_list 的参数形式与db()::insert()的 $field_list 参数形式一致。
- 在db()::update()传入的数据参数是具有优先级的，会覆盖db()::data()传入的数据。
- 在db()::update()传入的数据，会进行数据过滤和加引号，其中整数和浮点数不过滤，不加引号。
- 执行成功返回true，执行失败返回false;
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是SQL执行的结果。
- 注意，闭包函数是在SQL语句执行之前执行，所以如果在闭包函数中返回值不是NULL，那么可以阻止SQL的执行，并且最终也是返回值这个闭包函数的返回值。

```
$data = array('name'=>'王阿和', 'time'=>time());
//name键会被清理一个，后面的覆盖前面的。
$data2 = array('name'=>'曹操', 'info'=>"是一个人才");
db('测试')->table('user')->update($data, $data2, function($info){
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
            [time] => 1520967120
            [info] => '是一个人才'
        )

    [set] => `name`='曹操',`time`=1520967120,`info`='是一个人才'
    [update] => UPDATE `test`.`user` SET `name`='曹操',`time`=1520967120,`info`='是一个人才'
    [sql] => UPDATE `test`.`user` SET `name`='曹操',`time`=1520967120,`info`='是一个人才';
)
```

> 然后执行后会报错：


```
错误信息：update() 的 where() 条件为空 ,无法执行该语句！可以使用 all() 将操作该表字段的所有记录，请慎重使用！但 all()在有 where() 条件 的情况下无效！
错误位置：Z:\WWW\website\localhost\index.php 所在 41 行
```

> 原因就是没有db()::where()条件数据。解决方式，可以用db()::all()，来强制执行：


```
db('测试')->table('user')->all()->update($data, $data2);
```

> 当然有db()::where()条件数据的情况下，则不需要db()::all()的强制方式了：


```
db('测试')->table('user')->where(array('id = 1'))->update($data, $data2);
```

