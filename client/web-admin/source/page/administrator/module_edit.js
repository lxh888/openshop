WangAho({
	
	
	id : "administrator/module_edit",
	
	
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.module_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.module_id ){
			http("#/administrator/module_edit").request();
			return false;
		}
		
		WangAho("index").data({
			request : {
				module_option:["ADMINISTRATORMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]],
				get:["ADMINISTRATORMODULEGET", [config]]
			}, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.module_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/administrator/module_edit").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/administrator/module_edit.html", "#content"), data);
				
				_project.submit();
				
			}
		});
		
		
		
	},
	
	
	
	keyup : function(){
		//按回车键时提交
		$(document).unbind("keyup").on('keyup', function(e){
			if(e.keyCode === 13){
			    if( $("textarea").is(":focus") ){  
			        return false;
			    }
		        $('[name="submit"]').first().trigger("click");
			}
		});
	},
	
	
	submit : function(){
		//按回车键时提交
		this.keyup();
		
		$('[name="submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.module_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.module_name = $.trim($('[name="module_name"]').val());
			form_input.module_info = $.trim($('[name="module_info"]').val());
			form_input.module_sort = $.trim($('[name="module_sort"]').val());
			
			try {
				if(form_input.module_id == '') throw "模块ID不能为空";
				if(form_input.module_name == '') throw "模块名称不能为空";
				if(form_input.module_sort == ""){
					delete form_input.module_sort;
				}
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["ADMINISTRATORADMINMODULEEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
		
	}
	
	
	
	
	
	
	
});