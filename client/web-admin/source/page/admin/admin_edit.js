WangAho({
	
	
	id : "admin/admin_edit",
	
	
	data : null,
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.admin_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.admin_id ){
			http("#/admin/admin_list").request();
			return false;
		}
		
		WangAho("index").data({
			request : {
				authority_option:["ADMINISTRATORAUTHORITYMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]],
				get:["ADMINGET", [config]]
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
				if( !(function(){try{ return data.response.get.admin_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/admin/admin_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				_project.data = data;
				WangAho("index").view(WangAho().template("page/admin/admin_edit.html", "#content"), data, {
					
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
	
	
	event : function(){
		//按回车键时提交
		this.keyup();
		var _project = WangAho(this.id);
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
			form_input.admin_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.admin_name = $.trim($('[name="admin_name"]').val());
			form_input.admin_info = $.trim($('[name="admin_info"]').val());
			form_input.admin_sort = $.trim($('[name="admin_sort"]').val());
			form_input.admin_state = $('[name="admin_state"]').is(':checked')? 0 : 1;
			form_input.authority_id = [];
			$('[name="authority_id-checkbox"]:checked').each(function(){
				form_input.authority_id.push($(this).val());
			});
			
			try {
				if(form_input.admin_id == '') throw "角色ID不能为空";
				if(form_input.admin_name == '') throw "角色名称不能为空";
				if(form_input.admin_sort == ""){
					delete form_input.admin_sort;
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
				request:["ADMINEDIT", [form_input]],
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