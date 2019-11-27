WangAho({
	
	
	id:"application/config_pay",

	
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
		
		
		var request = {data:["APPLICATIONADMINCONFIGPAYDATA"]};
		var template_data = WangAho().template("page/application/config_pay.html", "#content");
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( action ){
			request.get = ["APPLICATIONADMINCONFIGPAYDATA",[{config_id:action}]];
			template_data = WangAho().template("page/application/config_pay.html", "#content-"+action);
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
						return template( WangAho().template("page/application/config_pay.html", "#action-button"), function(fn){
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
				
				$('[name="submit-alipay_access"]').first().trigger("click");
				$('[name="submit-alipay_withdraw_access"]').first().trigger("click");
				$('[name="submit-weixin_pay_access"]').first().trigger("click");
				
			}
		});
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		_project.keyup();
		_project.event_alipay_access();
		_project.event_alipay_withdraw_access();
		_project.event_weixin_pay_access();
	},
	
	event_weixin_pay_access : function(){
		var _project = WangAho(this.id);
		$('[name="submit-weixin_pay_access"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.mch_id = $.trim($('[name="mch_id"]').val());
			form_input.pay_key = $.trim($('[name="pay_key"]').val());
			form_input.spbill_create_ip = $.trim($('[name="spbill_create_ip"]').val());
			form_input.ssl_cert = $.trim($('[name="ssl_cert"]').val());
			form_input.ssl_key = $.trim($('[name="ssl_key"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			form_input.service_appid = $.trim($('[name="service_appid"]').val());
			form_input.service_mch_id = $.trim($('[name="service_mch_id"]').val());
			
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
				request:["APPLICATIONADMINCONFIGPAYEDIT", [{weixin_pay_access:form_input}]],
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
	
	
	event_alipay_withdraw_access : function(){
		var _project = WangAho(this.id);
		$('[name="submit-alipay_withdraw_access"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.id = $.trim($('[name="id"]').val());
			form_input.rsa_private_key = $.trim($('[name="rsa_private_key"]').val());
			form_input.alipayrsa_public_key = $.trim($('[name="alipayrsa_public_key"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
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
				request:["APPLICATIONADMINCONFIGPAYEDIT", [{alipay_withdraw_access:form_input}]],
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
	
	event_alipay_access : function(){
		var _project = WangAho(this.id);
		$('[name="submit-alipay_access"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.id = $.trim($('[name="id"]').val());
			form_input.rsa_private_key = $.trim($('[name="rsa_private_key"]').val());
			form_input.alipayrsa_public_key = $.trim($('[name="alipayrsa_public_key"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
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
				request:["APPLICATIONADMINCONFIGPAYEDIT", [{alipay_access:form_input}]],
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









