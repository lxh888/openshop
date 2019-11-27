WangAho({
	
	
	id:"administrator/application_add",
	
	data : null,
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {
				authority_option:["ADMINISTRATORADMINAUTHORITYMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]]
				},
			success : function(data){
				if( !data ){
					return false;
				}
				_project.data = data;
				//获得配置数据
				WangAho("index").view(WangAho().template("page/administrator/application_add.html", "#content"), data);
				//调用 Chosen
				$('select[name="ss_type_parent_id"]').chosen({
					width: '100%',
					//placeholder_text_single: '-', //默认值
					earch_contains:true, 
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
	
	
	/**
	 * 提交
	 */
	submit : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		//注册 checkbox
		if(_project.data.response && _project.data.response.authority_option){
			for( var i in _project.data.response.authority_option){
				//console.log("checkbox-"+_project.data.response.authority_option[i].module_id, "checkbox-all-"+_project.data.response.authority_option[i].module_id);
				WangAho("index").checkbox("checkbox-"+_project.data.response.authority_option[i].module_id, "checkbox-all-"+_project.data.response.authority_option[i].module_id);
			}
		}
		
		$('[name="submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.application_id = $.trim($('[name="application_id"]').val());
			form_input.application_name = $.trim($('[name="application_name"]').val());
			form_input.application_info = $.trim($('[name="application_info"]').val());
			form_input.application_json = $.trim($('[name="application_json"]').val());
			form_input.application_state = $('[name="application_state"]').is(':checked')? 0 : 1;
			form_input.application_on_off = $('[name="application_on_off"]').is(':checked')? 0 : 1;
			form_input.application_warning = $.trim($('[name="application_warning"]').val());
			form_input.administrator = $('[name="administrator"]').is(':checked')? 1 : 0;
			
			form_input.authority_id = [];
			$('[name="authority_id-checkbox"]:checked').each(function(){
				form_input.authority_id.push($(this).val());
			});
			
			try {
				if(form_input.application_id == '') throw "应用ID不能为空";
				if(form_input.application_name == '') throw "应用名称不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["ADMINISTRATORADMINAPPLICATIONADD", [form_input]],
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