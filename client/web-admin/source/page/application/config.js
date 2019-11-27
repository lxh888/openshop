WangAho({
	
	
	id:"application/config",

	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	data : null,
	main : function(){
		var _project = WangAho(this.id);
		var config = {search:{}};
		var _http = http();
		
		
		var request = {data:["APPLICATIONADMINCONFIGDATA"]};
		var template_data = WangAho().template("page/application/config.html", "#content");
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( action ){
			request.get = ["APPLICATIONADMINCONFIGDATA",[{config_id:action}]];
			template_data = WangAho().template("page/application/config.html", "#content-"+action);
		}else{
			action = "";
		}
		
		
		WangAho("index").data({
			request : request, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.data.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.data.error, {icon: 5, time: 2000});
				}
				
				data.action = action;
				//获得配置数据
				data.config = WangAho().data("config.json");
				_project.data = data;
				WangAho("index").view(template_data, data, {
					"action-button" : function(){
						return template( WangAho().template("page/application/config.html", "#action-button"), function(fn){
							this.helper("action-query-href", function(action){
								var _h = http();
								if(!_h.anchor.query){
									_h.anchor.query = {};
								}
								if( !action){
									delete _h.anchor.query.action;
								}else{
									_h.anchor.query.action = action;
								}
								
								if( _h.anchor.query.page ){
									delete _h.anchor.query.page;//删除分页
								}
								
								return http(_h).href;
							});
							
							return fn(data);
						});
					},
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
				
				$('[name="submit-app_android_version"]').first().trigger("click");
				$('[name="submit-weixin_applet_access"]').first().trigger("click");
				$('[name="submit-weixin_app_access"]').first().trigger("click");
			}
		});
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		_project.keyup();
		_project.event_app_android_version();
		_project.event_weixin_applet_access();
		_project.event_weixin_app_access();
		
	},
	
	
		
	event_weixin_app_access : function(){
		var _project = WangAho(this.id);
		$('[name="submit-weixin_app_access"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.id = $.trim($('[name="id"]').val());
			form_input.secret = $.trim($('[name="secret"]').val());
			
			try {
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["APPLICATIONADMINCONFIGEDIT", [{weixin_app_access:form_input}]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	},

	

	
	
	
	
		
	event_weixin_applet_access : function(){
		var _project = WangAho(this.id);
		$('[name="submit-weixin_applet_access"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.id = $.trim($('[name="id"]').val());
			form_input.secret = $.trim($('[name="secret"]').val());
			
			try {
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["APPLICATIONADMINCONFIGEDIT", [{weixin_applet_access:form_input}]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	},

	

	
	
	
	event_app_android_version : function(){
		var _project = WangAho(this.id);
		$('[name="submit-app_android_version"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.name = $.trim($('[name="name"]').val());
			form_input.info = $.trim($('[name="info"]').val());
			form_input.number = $.trim($('[name="number"]').val());
			form_input.download = $.trim($('[name="download"]').val());
			form_input.required = $('[name="required"]').is(':checked')? 1 : 0;
			
			try {
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["APPLICATIONADMINCONFIGEDIT", [{app_android_version:form_input}]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	}

	
	
	
	
	
	
	
});









