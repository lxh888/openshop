WangAho({
	
	
	id : "admin/admin_user_edit",
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.user_id ){
			http("#/admin/admin_user_list").request();
			return false;
		}
		
		WangAho("index").data({
			request : {
				admin_option:["ADMINOPTION",[{sort:["sort_asc","update_time_asc"]}]],
				get:["ADMINUSERGET", [config]]
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
				if( !(function(){try{ return data.response.get.user_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/admin/admin_user_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/admin/admin_user_edit.html", "#content"), data);
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
		
		$('[name="submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {admin_user_json:{}};
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.admin_id = $.trim($('[name="admin_id"]').val());
			form_input.admin_user_info = $.trim($('[name="admin_user_info"]').val());
			form_input.admin_user_sort = $.trim($('[name="admin_user_sort"]').val());
			form_input.admin_user_state = $('[name="admin_user_state"]').is(':checked')? 0 : 1;
			
			form_input.admin_user_json.show_region_order = $('[name="show_region_order"]').is(':checked')? 1 : 0;
			
			try {
				if(form_input.admin_id == "") throw "请选择管理角色";
				if(form_input.admin_user_sort == "") delete form_input.admin_user_sort;
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["ADMINUSEREDIT", [form_input]],
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