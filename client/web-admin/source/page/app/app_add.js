WangAho({
	
	
	id:"app/app_add",
	
	
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
				WangAho("index").view(WangAho().template("page/app/app_add.html", "#content"), data);
				
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
			form_input.user = $.trim($('[name="user"]').val());
			form_input.app_name = $.trim($('[name="app_name"]').val());
			form_input.app_info = $.trim($('[name="app_info"]').val());
			form_input.app_sort = $.trim($('[name="app_sort"]').val());
			form_input.app_key = $.trim($('[name="app_key"]').val());
			form_input.app_state = $.trim($('[name="app_state"]').val());
			
			try {
				if(form_input.app_key == '') throw "应用软件KEY不能为空";
				if(form_input.app_name == '') throw "应用软件名称不能为空";
				if(form_input.app_sort == ""){
					delete form_input.app_sort;
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
				request:["APPADMINADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//跳转到编辑页面
					http("#/app/app_edit/?id="+data).request();
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
		
	},
	
	
	
	
	
	
	
	
});