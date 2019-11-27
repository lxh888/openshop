#### 替换key键名称
$search的key键与$replace的key键比较，如果相等，那么将$search的key键名称替换成$replace的value值。替换后不会改变次序。
~~~
cmd(array(array $search[,array $replace = array()]), 'arr key_replace');
~~~
| 参数类型  |  参数名称  |  参数备注  |   是否必须    |   默认值   |  传参顺序   |
| --- | --- | --- | --- | --- | --- |
|  array  |  $search  |  查找的目标值  |  是  |    |  array[0] |
|  array  |  $replace  |  $search 的替换值，是个关联数组  |  否  |  array() |  array[1] |

示例：
~~~
$search = array("wang" => "我是老王", "张山" => array(11,22),"li" => "我是李四", "年龄" => 26);
$replace = array("张山" => "张老大", "年龄" => "age");
$arr = cmd(array($search, $replace), 'arr key_replace');
printexit( $arr );
~~~
~~~
/* ******************** 打印结果 ******************** */
Array(
[wang] => 我是老王
[张老大] => Array(
    [0] => 11
    [1] => 22
    )
[li] => 我是李四
[age] => 26
) 
~~~


* * * * *

####  返回白名单中的键值单元
当$is_value为false，$need的value值与$search的key键比较，如果相等则保留，否则移除。
当$is_value为true，$need的value值与$search的value键比较，如果相等则保留，否则移除。
~~~
cmd(array(array $search[,array $need = array()][,bool $is_value = false]), 'arr whitelist');
~~~

| 参数类型  |  参数名称  |  参数备注  |   是否必须    |   默认值   |  传参顺序   |
| --- | --- | --- | --- | --- | --- |--- |
|  array  |  $search  |  查找的目标值  |  是  |    |  array[0] |
|  array  |  $need  |  $search 的保留值，是个索引数组  |  否  |  array() |  array[1] |
|  bool  |  $is_value  |  是否是value键比较  |  是  |  false  |  array[2] |

示例：
~~~
$search = array("wang" => "我是老王", "张三" => array(11,22), "li" => "我是李四", "年龄" => 26);
$need = array("张三", "李四", "年龄");
$arr = cmd(array($search, $need), 'arr whitelist');
printexit( $arr );
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
[张三] => Array(
    [0] => 11
    [1] => 22
    )
[年龄] => 26
)
~~~


* * * * *


####  移除黑名单中的键值单元
当$is_value为false，$remove的value值与$search的key键比较，如果相等则移除，否则保留。
当$is_value为true，$remove的value值与$search的value键比较，如果相等则移除，否则保留。
~~~
cmd(array(array $search[,array $remove = array()][,bool $is_value = false]), 'arr blacklist');
~~~

| 参数类型  |  参数名称  |  参数备注  |   是否必须    |   默认值   |  传参顺序   |
| --- | --- | --- | --- | --- | --- |--- |
|  array  |  $search  |  查找的目标值  |  是  |    |  array[0] |
|  array  |  $remove  |  $search 的移除值，是个索引数组  |  否  |  array() |  array[1] |
|  bool  |  $is_value  |  是否是value键比较  |  是  |  false  |  array[2] |

示例：
~~~
$search = array("wang" => "我是老王", "张三" => array(11,22), "li" => "我是李四", "年龄" => 26);
$remove = array("张三", "李四", "年龄");
$arr = cmd(array($search, $remove), 'arr blacklist');
printexit( $arr );
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
  [wang] => 我是老王
  [li] => 我是李四
)
~~~


* * * * *

#### 将数组转换为一维索引数组

当$array是二维数组，如果$field为空，那么会将一维和二维的value值放在一起，返回一个索引数组。
如果$field不为空，那么只将二维数组中$field字段的value值合并成一个索引数组，其他的value值将舍弃。

~~~
cmd(array(array $array[,string $field = '']), 'arr indexedvalue');
~~~

| 参数类型  |  参数名称  |  参数备注  |   是否必须    |   默认值   |  传参顺序   |
| --- | --- | --- | --- | --- | --- |--- |
|  array  |  $array  |  查找的目标值  |  是  |    |  array[0] |
|  string  |  $field | $array数组 key键名称 |  否  |  array() |  array[1] |

示例：
~~~
$array = array("wang" => "我是老王", "张三" => array(11,22), "li" => "我是李四", "年龄" => 26);
$arr = cmd(array($array), 'arr indexedvalue');
printexit( $arr );
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
  [0] => 我是老王
  [1] => 11
  [2] => 22
  [3] => 我是李四
  [4] => 26
)
~~~

$field 用法示例：
~~~
$array = array(
    array("name"=>"王莽","时代"=>"西汉末年"),
    array("name"=>"刘邦","时代"=>"西汉建国"),
    array("name"=>"秦始皇","时代"=>"战国七雄")
	);
$arr = cmd(array($array, "name"), 'arr indexedvalue');
printexit( $arr );
~~~

~~~
/* ******************** 打印结果 ******************** */
Array(
  [0] => 王莽
  [1] => 刘邦
  [2] => 秦始皇
)
~~~