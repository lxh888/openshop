#### 清除query拼凑的参数数据


```
db()::clear([string $key][......][,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$key|query拼凑的键名称|否|空字符串|string[0]|
|string|.....|n个表名称|否|空字符串|string[n]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $key 名称，在query拼凑的参数数据中存在则删除。
- 如果没有传入任何$key参数，那么就是清空所有query拼凑的参数数据
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::clear() 在一次完整操作步骤中，可以使用多次，但只对当前操作的前面拼凑的数据清除有效。


```
db('测试')->table('user')
->call('orderby', array(array('id', true), 'info asc') )
//也可以把参数放在前面
->call(array( array('id=[]', 100) ), 'where')

//参数为空，清除$info['query']所有的数据
->clear()

->where(array('name=[]', '王阿和'))
->orderby( array('name') )
->limit(1000)

//指定清理$info['query']['limit']和$info['query']['orderby']数据
->clear('limit', 'orderby')

->select(function($info){
	printexit($info['query']);
});
```


```
/* ******************** 打印结果 ******************** */
Array(
    //有些参数已经被清除掉了
    [where] => WHERE ( name='王阿和' )
    [table] => 
    [select] => SELECT * FROM  WHERE ( name='王阿和' )
    [sql] => SELECT * FROM  WHERE ( name='王阿和' );
)
```




