#### 数据删除

```
db()::delete([closure $closure]);
```


|类型|	参数|	备注|	是否必须|	默认值|	顺序|
|:-----------:| ---------- | --- | --- | --- | --- |
|closure|	$closure|	闭包函数|	否|	|closure[0]|	


- 执行成功返回true，执行失败返回false;
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是SQL执行的结果。
- 注意，闭包函数是在SQL语句执行之前执行，所以如果在闭包函数中返回值不是NULL，那么可以阻止SQL的执行，并且最终也是返回值这个闭包函数的返回值。



```
//删除user表中所有的数据
db('测试')->table('user')->delete(function($info){
	printexit($info['query']);
});
```


```
Array (
    [base] => Array
        (
            [test] => Array
                (
                    [0] => user
                )

        )

    [table] => `test`.`user`
    [delete] => DELETE FROM `test`.`user`
    [sql] => DELETE FROM `test`.`user`;
)
```




> 然而在执行时，报错了：

```
错误信息：delete() 的 where() 条件为空 ,无法执行该语句！可以使用 all() 将操作该表字段的所有记录，请慎重使用！但 all()在有 where() 条件 的情况下无效！
错误位置：Z:\WWW\website\localhost\index.php 所在 36 行
```

> 原因就是没有db()::where()条件数据。解决方式，可以用db()::all()，来强制执行：


```
db('测试')->table('user')->all()->delete();
```

> 当然有db()::where()条件数据的情况下，则不需要db()::all()的强制方式了：


```
db('测试')->table('user')->where(array('id = 1'))->delete();
```

