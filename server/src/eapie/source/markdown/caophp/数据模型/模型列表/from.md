#### 预语句头

```
db()::from(string $from[,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$from|语句字符串|否|空字符串|string[0]|
|closure|$closure|闭包函数|否|	|closure[0]	|

- $from 可以接受空字符串，不存在该参数则默认为空字符串。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，可以多次使用，但是最后的会覆盖之前的。
- 注意，db()::from() 该用法具有优先级，如果不是空字符串，那么会覆盖 db()::table() 所产生的拼凑数据。


```
db('测试')->table('user')->from('这是测试from')->orderby('id desc')->select(function($info){
	printexit($info['query']);
});
```

```
/* ******************** 打印结果 ******************** */
Array(
    ......
    [table] => `test`.`user`
    [from] => 这是测试from
    [orderby] => ORDER BY id DESC
    
    //下面可以看出来，表名称拼凑的数据已被覆盖
    [select] => SELECT * FROM 这是测试from ORDER BY id DESC
    [sql] => SELECT * FROM 这是测试from ORDER BY id DESC;
)
```



