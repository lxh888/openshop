#### 对操作的结果排序
```
//字符串的形式
db()::orderby([string $sort = ''][string $sort = ''][......][,closure $closure]);

//或者数组的形式：
db()::orderby([array $sort = array()][array $sort = array()][......][,closure $closure]);
```


|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$sort|名称及排序类型|否|空字符串|string[0]|
|string|.....|n个名称及排序类型|否|空字符串|string[n]|
|array|$sort|名称及排序类型|否|空数组|array[0]|
|array|.....|n个名称及排序类型|否|空数组|array[n]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $sort 需要配合 desc 和 asc 对操作的结果排序。
- 当 $sort 为数组时，$sort[0] 是名称，$sort[1](默认)为false时，为asc排序。$sort[1]为true时，为desc排序。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::orderby() 操作再一次完整步骤中，使用多次的数据会连接拼凑，不覆盖在前面的操作数据。
- 注意，多个$sort参数，字符串和数组两个类型同时存在的话，字符串具有优先级，会排在前面。

##### 字符串参数的示例：

如果你的字段和mysql关键字有冲突，那就要注意细节了，空一格然后接desc和asc，并且是字符串结尾，这样就被认定为mysql关键字，并且关键字会被转还大写，避免关键字冲突
~~~
//这样的写法也可以，但是很容易与mysql关键字有冲突
db('测试')->orderby("user_id ASC,user_name desc ", function($info){
    printexit($info['query']);
});
//空一格然后接desc和asc。这样就被认定为mysql关键字，并且关键字会被转还大写。
db('测试')->orderby("user_id asc ")->orderby("user_name desc ", function($info){
    printexit($info['query']);
});
//与上面的用法是等价的
db('测试')->orderby("user_id asc ","user_name desc", function($info){
    printexit($info['query']);
});
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
    [orderby] => ORDER BY user_id ASC,user_name DESC
)
~~~


##### 数组参数的示列：



```
//可以多次使用
db('测试')->table('user')
->orderby( array('id', true))
//默认false 以ASC类型排序
->orderby( array('name'))
->select(function($info){
	printexit($info['query']);
});
```

```
/* ******************** 打印结果 ******************** */
Array(
    ......
    
    [table] => `test`.`user`
    [orderby] => ORDER BY id DESC,name ASC
    //可以看见多次，根据定义顺序排次序
    [select] => SELECT * FROM `test`.`user` ORDER BY id DESC,name ASC
    [sql] => SELECT * FROM `test`.`user` ORDER BY id DESC,name ASC;
)
```


##### 字符串和数组的优先级


```
db('测试')->table('user')
//只有在同次操作中，字符串具有优先级
->orderby( array('id', true), 'info desc' )
->orderby( array('name') )
//而这是最后操作，所以排在最后
->orderby( 'time asc' )
->select(function($info){
	printexit($info['query']);
});
```


```
/* ******************** 打印结果 ******************** */
Array(
    ......
    
    [table] => `test`.`user`
    [orderby] => ORDER BY info DESC,id DESC,name ASC,time ASC
    [select] => SELECT * FROM `test`.`user` ORDER BY info DESC,id DESC,name ASC,time ASC
    [sql] => SELECT * FROM `test`.`user` ORDER BY info DESC,id DESC,name ASC,time ASC;
)
```









