WangAho({
	
	
	id:"merchant/config",

	
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
		
		
		var request = {data:["MERCHANTADMINCONFIGDATA"]};
		var template_data = WangAho().template("page/merchant/config.html", "#content");
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( action ){
			request.get = ["MERCHANTADMINCONFIGDATA",[{config_id:action}]];
			template_data = WangAho().template("page/merchant/config.html", "#content-"+action);
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
						return template( WangAho().template("page/merchant/config.html", "#action-button"), function(fn){
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
				
				$('[name="submit-rmb_withdraw_merchant_money"]').first().trigger("click");
				$('[name="submit-rmb_buy_merchant_credit"]').first().trigger("click");
				
			}
		});
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		_project.keyup();
		_project.event_rmb_withdraw_merchant_money();
		_project.event_rmb_buy_merchant_credit();
	},
	
	
		
	//用户消费平台赠送积分配置
	event_rmb_buy_merchant_credit : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-rmb_buy_merchant_credit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.ratio_credit = $.trim($('[name="ratio_credit"]').val());
			form_input.ratio_rmb = $.trim($('[name="ratio_rmb"]').val());
			form_input.algorithm = $.trim($('[name="algorithm"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				if( !_application_config_credit ){
					throw "积分配置异常";
				}
				var throw_info = "积分比值的格式输入有误，必须是大于0的整数";
				if( parseInt(_application_config_credit.precision) ){
					throw_info = "积分比值的格式输入有误，必须是大于0的整数或"+_application_config_credit.precision+"位小数";
					}
				
				var format = /^([\d]+)(\.[\d]+)?$/;
				var format_foot = /\./;
				
				if( form_input.ratio_credit == "" ||
				!format.test(form_input.ratio_credit) ){
					throw throw_info;
				}else{
					form_input.ratio_credit = parseFloat(form_input.ratio_credit) * _application_config_credit.scale;
				}
				//如果存在小数点则报错
				if( format_foot.test(form_input.ratio_credit) ){
					throw throw_info;
				}
				
				var money_format = /^([\d]+)(\.[\d]{1,2})?$/;
				if( form_input.ratio_rmb == "" ||
				!money_format.test(form_input.ratio_rmb) ){
					throw "人民币比值的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.ratio_rmb = parseInt((parseFloat(form_input.ratio_rmb).toFixed(2))*100);//元转为分
				}
				
				if( form_input.algorithm == "" ){
					throw "运算法则不能为空";
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
				request:["MERCHANTADMINCONFIGEDIT", [{rmb_buy_merchant_credit:form_input}]],
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
	

	
	
	event_rmb_withdraw_merchant_money : function(){
		var _project = WangAho(this.id);
		$('[name="submit-rmb_withdraw_merchant_money"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.min_merchant_money = $.trim($('[name="min_merchant_money"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				var money_format = /^[\d]+(\.[\d]{1,2})?$/;
				if( form_input.min_merchant_money == "" ||
				!money_format.test(form_input.min_merchant_money) ){
					throw "最小提现金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.min_merchant_money = parseInt((parseFloat(form_input.min_merchant_money).toFixed(2))*100);//元转为分
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
				request:["MERCHANTADMINCONFIGEDIT", [{rmb_withdraw_merchant_money:form_input}]],
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









