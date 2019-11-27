# 前端根据配置做一些特殊操作
场景：当前端需要打包APP上传到软件市场的时候，发现有些模块或者字符等不能审核通过。这个时候需要一个配置来控制其显示状态，等审核通过再放出显示。

## 1) 在数据库中创建一个配置键数据
数据库config表中添加一条数据：

 config_id | config_value | config_type | config_name 
 --- | --- | --- | --- 
  display|  {"state":0} |     json   |  前端临时判断是否显示某个页面的利用参数 

如上配置，当state等于1，那么代表显示，否则代表隐藏。






## 2) 在接口代码中添加这个配置信息，能让前端拿到这个数据
类文件路径：request/application/config.php
```
/**
 * 获取应用配置
 * 
 * @param	void
 * @return	array
 */
public function api(){
    $config = array(
         //......
         'display'	//是否显示的配置
    );
     $select_data = object(parent::TABLE_CONFIG)->select(array(
         "where" => array( array("config_id IN ([-])", "\"".implode("\",\"", $config)."\"", true) ))
    );
    if( empty($select_data)){
        return NULL;
    }
    $data = array();
    foreach($select_data as $value){
        $data[$value["config_id"]] = object(parent::TABLE_CONFIG)->data($value, true);
    }
    return $data;
}
```
## 3) 前端获取这个配置
请求 `APPLICATIONCONFIG` 接口ID，然后根据返回值，做一些特殊操作。

```
import eonfox from '@/components/eonfox/eonfox.js';
import fns from '@/components/eonfox/fns.js';
export default {
    data() {
    },
    onShow: function () {
        ef.submit({
	    request:{
		config:['APPLICATIONCONFIG'],
		},
	    callback: function(req){
		var data = fns.checkError(req,'config',function(errno,error){
		    fns.err(error)
		});
		if( data.config.display.state ){
                    //显示
                }else{
                    //隐藏
                }
	    },
	    error: function(err){
		fns.err(err)
	    },
	});
    }
}
```
