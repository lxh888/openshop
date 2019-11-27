WangAho({
	
	
	id : "administrator/authority_edit",
	
	
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.authority_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.authority_id ){
			http("#/administrator/authority_list").request();
			return false;
		}
		
		WangAho("index").data({
			request : {
				module_option:["ADMINISTRATORMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]],
				get:["ADMINISTRATORADMINAUTHORITYGET", [config]]
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
				if( !(function(){try{ return data.response.get.authority_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/administrator/authority_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/administrator/authority_edit.html", "#content"), data);
				
				//调用 Chosen
				$('select[name="module_id"]').chosen({
					width: '100%',
					//placeholder_text_single: '-', //默认值
					earch_contains: true,
					no_results_text: "没有匹配结果",
					case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
		        	//group_search: false //选项组是否可搜。此处搜索不可搜
				});
				
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
			form_input.authority_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.authority_name = $.trim($('[name="authority_name"]').val());
			form_input.authority_info = $.trim($('[name="authority_info"]').val());
			form_input.authority_sort = $.trim($('[name="authority_sort"]').val());
			form_input.module_id = $.trim($('[name="module_id"]').val());
			
			try {
				if(form_input.authority_id == '') throw "权限ID不能为空";
				if(form_input.authority_name == '') throw "权限名称不能为空";
				if(form_input.authority_sort == ""){
					delete form_input.authority_sort;
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
				request:["ADMINISTRATORADMINAUTHORITYEDIT", [form_input]],
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