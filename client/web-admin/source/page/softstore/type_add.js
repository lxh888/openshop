WangAho({
	
	
	id:"softstore/type_add",
	
	
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {
				softstore_type_option:["SOFTSTOREADMINTYPEOPTION",[{sort:["sort_asc"]}]]
				},
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(WangAho().template("page/softstore/type_add.html", "#content"), data);
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
			form_input.ss_type_name = $.trim($('[name="ss_type_name"]').val());
			form_input.ss_type_info = $.trim($('[name="ss_type_info"]').val());
			form_input.ss_type_sort = $.trim($('[name="ss_type_sort"]').val());
			form_input.ss_type_parent_id = $.trim($('[name="ss_type_parent_id"]').val());
			form_input.ss_type_state = $('[name="ss_type_state"]').is(':checked')? 0 : 1;
			
			try {
				if(form_input.ss_type_name == '') throw "分类名称不能为空";
				if(form_input.ss_type_sort == ""){
					delete form_input.ss_type_sort;
				}
				if(form_input.ss_type_parent_id == ""){
					delete form_input.ss_type_parent_id;
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
				request:["SOFTSTOREADMINTYPEADD", [form_input]],
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