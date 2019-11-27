> 对变量输出，但是不html实体转换。比如，$content 是一个javascript代码字符串，如\<script\>alert(1)\<\/script\>那么就会执行这段代码了。


```
{code $content}
```

> 模板渲染为：


```
<?php if(isset($content)) echo $content; ?>
```
