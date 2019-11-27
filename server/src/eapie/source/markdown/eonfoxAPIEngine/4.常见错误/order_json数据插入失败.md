## 在order表中，order_json数据插入失败
在平时操作当中，order_json字段基本上是当作数组操作。再最后插入数据库中的时候，检查order_json的数据类型是否是字符串。如果不是字符串，需要JSON编码。代码如下：
```
//将 order_json 转为字符串
if( !empty($order['order_json']) ){
	$order['order_json'] = cmd(array($order['order_json']), 'json encode');
}
if( !object(parent::TABLE_ORDER)->insert($order) ){
	throw new error ('资金订单登记失败');
}
```