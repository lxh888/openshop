WangAho({
	
	
	id:"administrator/authority_add",
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {
				module_option:["ADMINISTRATORMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]]
				},
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				console.log(data)
				
				WangAho("index").view(WangAho().template("page/administrator/authority_add.html", "#content"), data);
				//调用 Chosen
				$('select[name="module_id"]').chosen({
					width: '100%',
					//placeholder_text_single: '-', //默认值
					earch_contains:true, 
					no_results_text: "没有匹配结果",
					case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
		        	//group_search: false //选项组是否可搜。此处搜索不可搜
				});
				
				_project.event();
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
	
	
	/**
	 * 提交
	 */
	event : function(){
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
			form_input.authority_id = $.trim($('[name="authority_id"]').val());
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
				if(form_input.authority_parent_id == ""){
					delete form_input.authority_parent_id;
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
				request:["ADMINISTRATORADMINAUTHORITYADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
		
	},
	
	
	
	
	
	
	
	
});