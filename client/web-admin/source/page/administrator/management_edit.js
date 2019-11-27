WangAho({
	
	
	id:"administrator/management_edit",
	
	management_id : null,
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {search:{}};
		var _http = http();
		config.management_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.management_id ){
			http("#/administrator/management_list").request();
			return false;
		}
		
		_project.management_id = config.management_id;
		
		WangAho("index").data({
			request : {
				authority_option:["ADMINISTRATORADMINAUTHORITYMODULEOPTION",[{sort:["sort_asc","update_time_asc"]}]],
				management_parent_option:["ADMINISTRATORADMINMANAGEMENTOPTION",[{sort:["sort_asc","update_time_asc"]}]],
				get:["ADMINISTRATORADMINMANAGEMENTGET", [config]]
			},
			
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				WangAho("index").view(WangAho().template("page/administrator/management_edit.html", "#content"), data);
				
				//调用 Chosen
				$('select[name="management_parent_id"],select[name="authority_id"]').chosen({
					width: '100%',
					//placeholder_text_single: '-', //默认值
					earch_contains: true, 
					no_results_text: "没有匹配结果",
					case_sensitive_search: false, //搜索大小写敏感。此处设为不敏感
		        	group_search: true, //选项组是否可搜。此处搜索不可搜
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
		var _project = WangAho(this.id);
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
			form_input.management_id = _project.management_id;
			form_input.management_name = $.trim($('[name="management_name"]').val());
			form_input.management_info = $.trim($('[name="management_info"]').val());
			form_input.management_page = $.trim($('[name="management_page"]').val());
			form_input.management_parent_id = $.trim($('[name="management_parent_id"]').val());
			form_input.authority_id = $.trim($('[name="authority_id"]').val());
			form_input.management_sort = $.trim($('[name="management_sort"]').val());
			form_input.management_hide = $('[name="management_hide"]').is(':checked')? 1 : 0;
			form_input.management_state = $('[name="management_state"]').is(':checked')? 0 : 1;
			form_input.management_label_before = $.trim($('[name="management_label_before"]').val());
			form_input.management_label_after = $.trim($('[name="management_label_after"]').val());
			form_input.management_href = $.trim($('[name="management_href"]').val());
			form_input.management_target = $.trim($('[name="management_target"]').val());
			form_input.management_query = $.trim($('[name="management_query"]').val());
			form_input.management_path = $.trim($('[name="management_path"]').val());
			
			try {
				if( !form_input.management_id ) throw "菜单ID异常";
				if( form_input.management_name == '' ) throw "菜单名称不能为空";
				if( form_input.management_sort == "" ){
					delete form_input.management_sort;
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
				request:["ADMINISTRATORADMINMANAGEMENTEDIT", [form_input]],
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
