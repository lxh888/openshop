#### 动态给方法传入参数


```
db()::call([string $method_name = ''][array $method_tags = array()][,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$method_name|方法名称|是||string[0]|
|array|$method_tags|方法需要传入的参数|否|空字符串|array[0]|
|closure|$closure|闭包函数|否|	|closure[0]|


- $method_name 支持'joinon'、'where'、'having'、'data'、'orderby'、'groupby'、'table'、'limit'，并且不区分大小写，而且自动清除两边空白符;
- $method_tags 是一个索引数组，是$method_name方法的参数。如array('参数1',参数2,...);
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，如果db()::call() 在一次完整操作步骤中使用多次，不会覆盖在前面的操作数据。


```
db('测试')->table('user')
->call('orderby', array(array('id', true), 'info asc') )
//也可以把参数放在前面
->call(array( array('id=[]', 100) ), 'where')
->select(function($info){
	printexit($info['query']);
});
```


```
/* ******************** 打印结果 ******************** */
Array(
    ......

    [table] => `test`.`user`
    [orderby] => ORDER BY info ASC,id DESC
    [where] => WHERE ( id=100 )
    [select] => SELECT * FROM `test`.`user` WHERE ( id=100 ) ORDER BY info ASC,id DESC
    [sql] => SELECT * FROM `test`.`user` WHERE ( id=100 ) ORDER BY info ASC,id DESC;
)
```
 
