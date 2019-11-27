#### 设置表前缀

```
db()::prefix(string $prefix[,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$prefix|字符编码名称|否|空字符串|string[0]|
|closure|$closure|闭包函数|否|	|closure[0]	|

- $prefix 可以接受空字符串，不存在该参数则默认为空字符串。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::prefix() 操作再一次完整步骤中，使用多次的后面会覆盖前面。操作后，一直保持到该连接关闭。
 


```
//如果要将 user 修改为 cp_user 在表方法之前使用
db('测试')->prefix('cp_')->table('user', function($info){
	//printexit($info);
});

//打印,这里的前缀 还是 'cp_'。
//意思是操作后，一直保持到该连接关闭。
printexit( db('测试')->info() );
```



```
Array(
    ......

    [query] => Array(
        [base] => Array(
            [test] => Array(
                [0] => cp_user
                )
            )
            
        //表名称添加了前缀
        [table] => `test`.`cp_user`
        )
)
```

