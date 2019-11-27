WangAho({
	
	
	id:"admin/admin_user_add",
	
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {
				admin_option:["ADMINOPTION",[{sort:["sort_asc","update_time_asc"]}]]
			},
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/admin/admin_user_add.html", "#content"), data);
				
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
		//调用 Chosen
		$('select[name="admin_id"]').chosen({
			width: '100%',
			//placeholder_text_single: '-', //默认值
			earch_contains:true, 
			no_results_text: "没有匹配结果",
			case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
        	//group_search: false //选项组是否可搜。此处搜索不可搜
		});
				
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
			form_input.user = $.trim($('[name="user"]').val());
			form_input.admin_id = $.trim($('[name="admin_id"]').val());
			form_input.admin_user_info = $.trim($('[name="admin_user_info"]').val());
			form_input.admin_user_sort = $.trim($('[name="admin_user_sort"]').val());
			form_input.admin_user_state = $('[name="admin_user_state"]').is(':checked')? 0 : 1;

			try {
				if(form_input.admin_id == "") throw "请选择管理角色";
				if(form_input.admin_sort == "") delete form_input.admin_sort;
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["ADMINUSERADD", [form_input]],
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
