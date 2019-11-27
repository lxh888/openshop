#### 指定查询和操作的数量


```
db()::limit([mixed $begin][mixed $end][,closure $closure]);
```

- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::limit() 在一次完整操作中使用多次，后面数据覆盖在前面的拼凑数据。


##### 示例：

```
//可以是字符串数字
db('测试')->table('user')->limit('1 ', 3)->select(function($info){
		printexit($info['query']);
	});
	
//可以只有一个参数
db('测试')->table('user')->limit(3)->select(function($info){
		printexit($info['query']);
	});
```


```
/* ******************** 打印结果 ******************** */
Array(
    ......

    [table] => `test`.`user`
    [limit] => LIMIT 1,3
    [select] => SELECT * FROM `test`.`user` LIMIT 1,3
    [sql] => SELECT * FROM `test`.`user` LIMIT 1,3;
)


Array(
    ......

    [table] => `test`.`user`
    [limit] => LIMIT 3
    [select] => SELECT * FROM `test`.`user` LIMIT 3
    [sql] => SELECT * FROM `test`.`user` LIMIT 3;
)

```
