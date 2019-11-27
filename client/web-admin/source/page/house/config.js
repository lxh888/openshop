WangAho({
	
	
	id:"house/config",

	
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
		
		
		var request = {data:["HOUSEADMINCONFIGDATA"]};
		var template_data = WangAho().template("page/shop/config.html", "#content");
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( action ){
			request.get = ["HOUSEADMINCONFIGDATA",[{config_id:action}]];
			template_data = WangAho().template("page/house/config.html", "#content-" + action);
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
						return template( WangAho().template("page/house/config.html", "#action-button"), function(fn){
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
				
				$('[name="submit-house_product_add_reward_user_money"]').first().trigger("click");
			}
		});
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		_project.keyup();
		_project.event_house_product_add_reward_user_money();
	},
	
	
		
	//用户消费平台赠送积分配置
	event_house_product_add_reward_user_money : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-house_product_add_reward_user_money"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.user_money = $.trim($('[name="user_money"]').val());
			form_input.random_min_user_money = $.trim($('[name="random_min_user_money"]').val());
			form_input.random_max_user_money = $.trim($('[name="random_max_user_money"]').val());
			form_input.random_state = $('[name="random_state"]').is(':checked')? 1 : 0;
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				
				if( form_input.user_money == "" ||
				!money_format.test(form_input.user_money) ){
					throw "固定金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.user_money = parseInt((parseFloat(form_input.user_money).toFixed(2))*100);//元转为分
				}
				
				if( form_input.random_min_user_money == "" ||
				!money_format.test(form_input.random_min_user_money) ){
					throw "最小随机金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.random_min_user_money = parseInt((parseFloat(form_input.random_min_user_money).toFixed(2))*100);//元转为分
				}
				
				if( form_input.random_max_user_money == "" ||
				!money_format.test(form_input.random_max_user_money) ){
					throw "最大随机金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.random_max_user_money = parseInt((parseFloat(form_input.random_max_user_money).toFixed(2))*100);//元转为分
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
				request:["HOUSEADMINCONFIGEDIT", [{house_product_add_reward_user_money:form_input}]],
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
	

	
	
	
	
});









