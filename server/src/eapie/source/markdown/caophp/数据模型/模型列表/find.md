#### 单条数据读取

```
//字符串的方式
db()::find([string $field_list][string ...][,bool $is_fetch_object = false][,closure $closure]);
//数组的方式
db()::find([array $field_list][array ...][,bool $is_fetch_object = false][,closure $closure]);
```



|类型|	参数|	备注|	是否必须|	默认值|	顺序|
|:-----------:| ---------- | --- | --- | --- | --- |
|string |	$field_list|是数据字段列表名称|	否| |	string[0]|
|string |	......|.......|	否|	 |	string[n]|
|array |	$field_list|是数据字段列表名称|	否| |	array[0]|
|array |	......|......|	否|	|	array[n]|
|bool   |$is_fetch_object|返回数据是否对象|	否|	false|	bool[0]|
|closure|	$closure|	闭包函数|	否|	|closure[0]|	

- $field_list 如果数据字段列表为空，那么默认是 * ，获取所有的字段数据。
- $field_list 如果同时存在字符串和数组，那么字符串具有优先级。
- db()::find()本质上和db()::select()用法一致。
- $is_fetch_object 如果是数据，返回的是否为对象。true返回的是对象，false返回的是数组。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是SQL执行的结果。
- 注意，闭包函数是在SQL语句执行之前执行，所以如果在闭包函数中返回值不是NULL，那么可以阻止SQL的执行，并且最终也是返回值这个闭包函数的返回值。
- 注意，db()::limit() 如果数据为空，那么默认是limit 0,1 取一条数据。而就算设置limit多条数据，但也只是返回取出数据的第一条。
 

##### 字段以数组形式传入参数


```
//find和select的用法是一致，字段字符串参数比数组有优先级
$find = array('商品id as id','产品名称 as name','单位');
$data = db('测试')
->table('订单周期表')
->find( $find );

//返回值非索引数组，而是一个关联数组
printexit($data);
```



```
/* ******************** 打印结果 ******************** */
Array
(
    [id] => 528
    [name] => 牙科石膏
    [单位] => 袋
)
```



##### 返回数组的形式

```
$data = db('测试')->table('user')->find('id as 标识,name as 姓名');
printexit($data);
```



```
/* ******************** 打印结果 ******************** */
Array
(
    [标识] => 2
    [姓名] => 888
)
```



---

##### 返回对象的形式


```
$data = db('测试')->table('user')->find('id, name', true);
printexit("id:".$data->id, "name:".$data->name);
```


```
/* ******************** 打印结果 ******************** */
id:2
name:888
```


