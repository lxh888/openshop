> 引入为文件地址的变量值。会判断是不是一个文件，不是则已，是则引入：


```
{include $filename}
```

> 模板渲染为：


```
<?php if(isset($filename) && is_file($filename)) include $filename; ?>
```

