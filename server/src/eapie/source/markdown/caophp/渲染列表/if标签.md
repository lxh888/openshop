
```
//{if 这里放条件}
{if !empty($key) && $key%2 }
		
{elseif  !empty($key) }

{else}

{/if}
```

> 模板渲染为：


```
<?php if(!empty($key) && $key%2 ){ ?>

<?php }else if(!empty($key) ){ ?>

<?php }else{ ?>

<?php } ?>
```

