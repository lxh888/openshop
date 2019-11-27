#### 查询筛选条件

```
db()::where( array(string $sql [,mixed $value][,bool $is_filter])[,array(...)][,closure $closure] );
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|array|array[0]|数组|是| |array[0]|
|array|.....|......|否| |array[......]|
|array|array[n]|数组|否| |array[n]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::where() 操作再一次完整步骤中，使用多次的数据会连接拼凑，不覆盖在前面的操作数据。

##### 每个数组的参数值：

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string	|$sql|	SQL语句|是|	|array[0]|
|mixed	|$value	|可过滤的替换值|否	|	|array[1]|
|bool	|$is_no_filter|是否不过滤数据|	否	|false|array[2]|

- $is_no_filter 针对$value是字符串的情况。为true不过滤，false要过滤(默认)。过滤字符串有利于防止SQL注入。
 

---
##### 预留符
在语句中[]、[-]、[+]表示是否加引号的替换值，[]是默认情况替换，整数和浮点数不加引号。[-]表示无论是什么类型的数据都不加引号。[+]则表示无论什么类型的数据都要加引号。示例：

```
db('测试')
->table("productdetail l")
//自动判断，是整数不加单引号
->where( array("quantity=[]", 1000) )
//[-]是不加单引号，即使是字符串
->where( array("[or] quantity=[-]","这是字符串") )
//这里不加衔接修饰符，会默认加上[and]
//[+]是加单引号，即使是整数、浮点数
->where( array("quantity=[+]", 2000), function($info){
    printexit($info);
});
```

```
/* ******************** 打印结果 ******************** */
......
[query] => Array(
    [base] => Array(
        [test] => Array(
            [0] => productdetail
            )
        )
    [table] => `test`.`productdetail` AS l
    [where] => WHERE ( quantity=1000 ) OR ( quantity=这是字符串 ) AND ( quantity='2000' )
)
......
```

---
#### 数据强制不过滤
> 数据的过滤是有效的防止SQL注入，请根据情况谨慎操作。

在有些情况下，我需要数据强制不过滤，那么需要在数组中放入第三个参数，是一个布尔值。true代表任何类型数据都不过滤，而false是默认过滤。
如下示例：

```
db('测试')
->table("productdetail l")
//第三个参数为true，强制不过滤
->where( array("quantity=[]", "''\\\\", true) )
//第三个参数默认false，会过滤数据
->where( array("quantity=[]", "''\\\\"), function($info){
printexit($info);
});
```

```
/* ******************** 打印结果 ******************** */
......
[query] => Array(
    [base] => Array(
        [test] => Array(
            [0] => productdetail
            )
        )
    [table] => `test`.`productdetail` AS l
    //第一个操作没有过滤数据，会被注入。而第二个过滤了数据
    [where] => WHERE ( quantity='''\' ) AND ( quantity='\'\'\\' )
)
......
```

---
##### 拼凑分组语句
分组条件的方式，同一个操作是一个组条件。如下：

```
db('测试')
	->table("productdetail l")
	->where( 
	array("quantity=[]", 1000),
	array("quantity=[]", "''\\\\"),
	array("quantity=[+]", 2000),
	array("quantity=[]", (string)3000)
	)
	//以[or]修饰连接上一组
	->where(array("[or] quantity=[]", 5000), function($info){
		printexit($info);
	});
```


```
[query] => Array(
    [base] => Array(
        [test] => Array(
            [0] => productdetail
            )
        )
    [table] => `test`.`productdetail` AS l
    //可以看出 后面的 quantity=5000 是被当作了第二组
    [where] => WHERE ( quantity=1000 AND quantity='\'\'\\' AND quantity='2000' AND quantity='3000' ) OR ( quantity=5000 )
        )
```


---

##### like、in、between用法
示例：

```
db('测试')
->table("productdetail l")

//( quantity like '%1000' OR quantity like '666%' AND quantity in(1,3,5) )
->where( 
array("quantity like '%[]'", 1000),
array("[or] quantity like '[-]%'", "666"),
array("[and] quantity in([-])", "1,3,5")
)

//AND ( quantity between 3 AND 5 )
->where(
array("[and] quantity between []", 3),
array("[and] []", 5)
)

//OR ( quantity > 2000 AND quantity < 3000 )
->where(
array("[or] quantity > []", 2000),
array("[and] quantity < []", 3000)
)

->where(function($info){
	printexit($info);
});
```

```
[query] => Array(
    [base] => Array(
        [test] => Array(
            [0] => productdetail
            )
        )
    [table] => `test`.`productdetail` AS l
    [where] => WHERE ( quantity like '%1000' OR quantity like '666%' AND quantity in(1,3,5) ) AND ( quantity between 3 AND 5 ) OR ( quantity > 2000 AND quantity < 3000 )
)
```
