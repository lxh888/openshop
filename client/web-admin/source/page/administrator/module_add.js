WangAho({
	
	
	id:"administrator/module_add",
	
	
	main : function(){
		var _project = WangAho(this.id);
		WangAho("index").data({
			request : {},
			success : function(data){
				if( !data ){
					return false;
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				console.log(data)
				
				WangAho("index").view(WangAho().template("page/administrator/module_add.html", "#content"), data);
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
			form_input.module_id = $.trim($('[name="module_id"]').val());
			form_input.module_name = $.trim($('[name="module_name"]').val());
			form_input.module_info = $.trim($('[name="module_info"]').val());
			form_input.module_sort = $.trim($('[name="module_sort"]').val());
			
			try {
				if(form_input.module_name == '') throw "模块名称不能为空";
				if(form_input.module_sort == ""){
					delete form_input.module_sort;
				}
				if(form_input.module_parent_id == ""){
					delete form_input.module_parent_id;
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
				request:["ADMINISTRATORADMINMODULEADD", [form_input]],
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