#### 数据批量插入
```
db()::batch(void);
```
只要使用了该方法，在插入的时候就会以批量的方式传入插入数据。示例：
```
//在插入的时候就会以批量的方式传入插入数据，注意字段要保持一致
$data = array(
    array('name':'名称1','info':'信息1'),
    array('name':'名称2','info':'信息2'),
    array('name':'名称3','info':'信息3'),
);
$bool = db(self::DB)->table('test')->batch()->insert($data);//返回的是成功次数
```
