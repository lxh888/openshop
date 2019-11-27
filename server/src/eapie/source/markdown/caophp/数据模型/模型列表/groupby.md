#### 分组运算

```
db()::groupby([string $field_name = ''][string $field_name = ''][......][,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$field_name|字段名称|否|空字符串|string[0]|
|string|.....|n个字段名称|否|空字符串|string[n]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::groupby() 操作再一次完整步骤中，使用多次的数据会连接拼凑，不覆盖在前面的操作数据。

##### 结果集分组
通常用于结合合计函数，根据一个或多个列对结果集进行分组 。
~~~
$having = array("SUM(sales) > []", 1500);
db('测试')->table("user u")
->groupby("user_id")
->groupby("user_name")
->having($having, function($info){
    printexit($info['query']);
});

//与上面的用法是等价的
db('测试')->table("user u")
->groupby("user_id", "user_name")
->having($having, function($info){
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
    [groupby] => GROUP BY user_id,user_name
    [having] => HAVING ( SUM(sales) > 1500 )
)
~~~