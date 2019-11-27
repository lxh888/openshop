#### 数据读取

```
//字符串的方式
db()::select([string $field_list][string ...][,bool $is_fetch_object = false][,closure $closure]);
//数组的方式
db()::select([array $field_list][array ...][,bool $is_fetch_object = false][,closure $closure]);
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
- $is_fetch_object 如果是数据，返回的是否为对象。true返回的是对象，false返回的是数组。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。如果闭包函数返回值不为NULL，则最终返回闭包函数返回值，否则最终返回值是SQL执行的结果。
- 注意，闭包函数是在SQL语句执行之前执行，所以如果在闭包函数中返回值不是NULL，那么可以阻止SQL的执行，并且最终也是返回值这个闭包函数的返回值。



##### 字段以数组和字符串的优先级


```
$select = array('商品id as id','产品名称 as name','单位');
//这里的“单位”会被清理，因为已经重复了
$select2 = array('单位');
$data = db('测试')
->table('订单周期表')
->limit(2)
//字符串是具有优先级的，所以次序会排在前面，而不是根据定义的顺序
//'规格, 产地,'明显后面多个逗号不规范，但是系统会自动清理
->select( $select, $select2,'规格, 产地,','订单总数 as sum', function($info){
	printexit($info['query']);
});
printexit($data);
```


```
/* ******************** 打印结果 ******************** */
Array(
    [base] => Array
        (
            [test] => Array
                (
                    [0] => 订单周期表
                )

        )

    [table] => `test`.`订单周期表`
    [limit] => LIMIT 2
    //字符串是具有优先级的，所以次序会排在前面，而不是根据定义的顺序
    //'规格, 产地,'明显后面多个逗号不规范，但是系统会自动清理
    [select] => SELECT 规格, 产地,订单总数 as sum,商品id as id,产品名称 as name,单位 FROM `test`.`订单周期表` LIMIT 2
    [sql] => SELECT 规格, 产地,订单总数 as sum,商品id as id,产品名称 as name,单位 FROM `test`.`订单周期表` LIMIT 2;
)
```



---


##### 返回值为数组的形式



```
$data = db('测试')->table('user')->select();
printexit($data);
```


```
Array(
    [0] => Array
        (
            [id] => 2
            [name] => 888
        )

    [1] => Array
        (
            [id] => 23
            [name] => wangaho
        )

)
```


---

##### 返回值为索引对象的形式
```
$data = db('测试')->table('user')->select('name as n,id as i', true);
printexit($data);
```


```
/* ******************** 打印结果 ******************** */
Array(
    [0] => stdClass Object
        (
            [n] => 888
            [i] => 2
        )

    [1] => stdClass Object
        (
            [n] => wangaho
            [i] => 23
        )
)
```
