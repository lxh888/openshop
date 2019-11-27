#### 强制全部行为


```
db()::all([closure $closure]);
```

|类型|参数|备注|是否必须|默认值|顺序|
| ----| ----|----|----|----|----|
|closure|$closure|闭包函数|否|	|closure[0]|


- $closure 最多接受1个参数，如function($resource){}，$resource就是当前标识的资源。不接受闭包函数的返回值。
- 在db()::update()或db()::delete()没有where语句时,db()::call()将有效，强制进行全部行为。
- 这种方式，主要是为了误操作等数据的安全。


 
```
//全部数据都进行修改
db('测试')->table('user')->all()->update(array('name'=>'wangahe'));
//全部数据都删除
db('测试')->table('user')->all()->delete();
```

