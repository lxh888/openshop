## scroll-view 可滚动视图区域 
最外层的scroll-view  中滑动的元素，不能设置为 float 浮动
包裹scroll-view最大的盒子的样式应该设置为:
```
overflow:hidden;
white-space:nowrp; 
```
超出隐藏，不允许换行。
例如 ：https://www.cnblogs.com/miu-key/p/7606024.html