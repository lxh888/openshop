WangAho({
	
	
	id:"admin/admin_add",
	
	data : null,
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {
				authority_option:["ADMINISTRATORAUTHORITYMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]]
			},
			success : function(data){
				if( !data ){
					return false;
				}
				
				_project.data = data;
				WangAho("index").view(WangAho().template("page/admin/admin_add.html", "#content"), data);
				
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
			form_input.admin_name = $.trim($('[name="admin_name"]').val());
			form_input.admin_info = $.trim($('[name="admin_info"]').val());
			form_input.admin_sort = $.trim($('[name="admin_sort"]').val());
			form_input.admin_state = $('[name="admin_state"]').is(':checked')? 0 : 1;
			form_input.authority_id = [];
			$('[name="authority_id-checkbox"]:checked').each(function(){
				form_input.authority_id.push($(this).val());
			});
			
			try {
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
				request:["ADMINADD", [form_input]],
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
