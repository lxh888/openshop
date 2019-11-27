#### 集合函数运算结果的输出进行限制

```
db()::having( array(string $sql [,mixed $value][,bool $is_filter])[,array(...)][,closure $closure] );
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|array|array[0]|数组|是| |array[0]|
|array|.....|......|否| |array[......]|
|array|array[n]|数组|否| |array[n]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::having() 操作再一次完整步骤中，使用多次的数据会连接拼凑，不覆盖在前面的操作数据。

##### 每个数组的参数值：

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string	|$sql|	SQL语句|是|	|array[0]|
|mixed	|$value	|可过滤的替换值|否	|	|array[1]|
|bool	|$is_no_filter|是否不过滤数据|	否	|false|array[2]|

- $is_no_filter 针对$value是字符串的情况。为true不过滤，false要过滤(默认)。过滤字符串有利于防止SQL注入。

##### 可以使用函数运算
基础操作细节可参考where()，两者基础用法一样。而having可以在里面使用函数。
~~~
$having = array("SUM(sales) > []", 1500);
db('测试')->table("user u")->having($having, function($info){
    printexit($info['query']);
});
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
    [base] => Array(
        [test] => Array(
            [0] => user
            )
        )
    [table] => `test`.`user` AS u
    [having] => HAVING ( SUM(sales) > 1500 )
)
~~~