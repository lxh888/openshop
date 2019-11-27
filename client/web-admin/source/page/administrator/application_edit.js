WangAho({
	
	
	id : "administrator/application_edit",
	
	data : null, 
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.application_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.application_id ){
			http("#/administrator/application_list").request();
			return false;
		}
		
		WangAho("index").data({
			request : {
				authority_option:["ADMINISTRATORADMINAUTHORITYMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]],
				get:["ADMINISTRATORADMINAPPLICATIONGET", [config]]
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
				if( !(function(){try{ return data.response.get.application_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/administrator/application_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				
				//获得配置数据
				_project.data = data;
				WangAho("index").view(WangAho().template("page/administrator/application_edit.html", "#content"), data, {
					
					"authority in_array" : function(admin_authority_id, admin_authority_ids){
						if(typeof admin_authority_id != 'string' ||
						!admin_authority_ids || 
						typeof admin_authority_ids != 'string'){
							return false;
							}
							
						var m = admin_authority_ids.match( new RegExp(','+ admin_authority_id +',') );
						
						console.log(m, new RegExp(','+ admin_authority_id +','))
						if(m) return true; else return false;
					}
					
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
			var _http = http();
			form_input.primary_key = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
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
				if(form_input.primary_key == '') throw "接口主键异常";
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
				request:["ADMINISTRATORADMINAPPLICATIONEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					
					if( form_input.primary_key != form_input.application_id){
						_http.anchor.query.id = form_input.application_id;
						http(_http).request();
					}else{
						//刷新页面
						WangAho("index").scroll_constant(function(){
							_project.main();
						});
					}
					
				}
			});
			
			
		});
		
	}
	
	
	
	
	
	
	
});