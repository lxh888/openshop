
##### 键值对循环

```
//{foreach 数组变量 as 键 => 值 }
{php}$name = array(11,22,33); {/php}
{foreach $name as $key=>$value}

{/foreach}
```


> 模板渲染为：

```
<?php $name = array(11,22,33);  ?>
<?php foreach($name as $key=>$value){ ?>

<?php } ?>
```


---


##### 省略键的值循环


```
//{foreach 数组变量 as 值 }
{foreach $name as $value}

{/foreach}
```

> 模板渲染为：

```
<?php foreach($name as $value){ ?>

<?php } ?>
```


---

##### 注意事项

> 在循环的时候，应该判断一下这个变量是否存在，或者是不是数组，然后再循环。


```
{if  !empty($name) && is_array($name)}
{foreach $name as $key=>$value}

{/foreach}
{/if}
```

> 模板渲染为：


```
<?php if(!empty($name) && is_array($name)){ ?>
<?php foreach($name as $key=>$value){ ?>

<?php } ?>
<?php } ?>
```
