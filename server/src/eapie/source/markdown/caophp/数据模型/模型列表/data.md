#### 获取插入、更新的数据


```
db()::data(array(string $key, string $value = '', bool $is_no_filter = false), [array(......)][,closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|string|$key|键|是||array[0]|
|string|$value|值|否|空字符串|array[1]|
|bool|$is_no_filter|是否不过滤数据|否|false|bool[0]|
|closure|$closure|闭包函数|否|	|closure[0]|

- $key 和 $value 都会自动数据过滤。
- $is_no_filter 针对$value是字符串的情况。为true不过滤，false要过滤(默认)。过滤字符串有利于防止SQL注入。
- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 注意，db()::data() 在一次完整操作步骤中，可以使用多次，后面的数据会与前面的数据合并。
 
> 在语句中[]、[-]、[+]表示是否加引号的替换值，[]是默认情况替换，整数和浮点数不加引号。[-]表示无论是什么类型的数据都不加引号。[+]则表示无论什么类型的数据都要加引号。

```
db('测试')->table('user')
//根据数据类型 来判断是否加引号
->data( array('time=[]', 1234567) )
//强制加引号
->data( array('id=[+]', 110) )
//强制不加引号，强制不过滤
->data( array('info=[-]', '介绍', true) )
//单次定义多个。这里“s1=试一=试”包含了2个=号，而程序只认前面第一个，并且其数据不会被过滤
->data(array('c=[]', '测'), array('s1=试一=试'))
//没有=号，那么系统认为s2=''，并且其数据不会被过滤
->data(array('s2'))
->insert(function($info){
	printexit($info['query']);
});
```


```
/* ******************** 打印结果 ******************** */
Array
(
    [base] => Array
        (
            [caoweblog] => Array
                (
                    [0] => cao_user
                )

        )

    [table] => `caoweblog`.`cao_user`
    [data] => Array
        (
            [time] => 1234567
            [id] => '110'
            [info] => 介绍
            [c] => '测'
            [s1] => 试一=试
            [s2] => ''
        )

    [values] =>  ( `time`,`id`,`info`,`c`,`s1`,`s2` ) values ( 1234567,'110',介绍,'测',试一=试,'' )
    [insert] => INSERT INTO `caoweblog`.`cao_user` ( `time`,`id`,`info`,`c`,`s1`,`s2` ) values ( 1234567,'110',介绍,'测',试一=试,'' )
    [sql] => INSERT INTO `caoweblog`.`cao_user` ( `time`,`id`,`info`,`c`,`s1`,`s2` ) values ( 1234567,'110',介绍,'测',试一=试,'' );
)
```
