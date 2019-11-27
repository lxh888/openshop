WangAho({
	
	
	id:"user/config",

	
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
		
		
		var request = {data:["USERADMINCONFIGDATA"]};
		var template_data = WangAho().template("page/user/config.html", "#content");
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( action ){
			request.get = ["USERADMINCONFIGDATA",[{config_id:action}]];
			template_data = WangAho().template("page/user/config.html", "#content-"+action);
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
						return template( WangAho().template("page/user/config.html", "#action-button"), function(fn){
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
				
				$('[name="submit-rmb_withdraw_user_money_earning"]').first().trigger("click");
				$('[name="submit-daily_attendance_earn_user_credit"]').first().trigger("click");
				$('[name="submit-parent_recommend_user_credit"]').first().trigger("click");
				$('[name="submit-rmb_consume_user_credit"]').first().trigger("click");
				$('[name="submit-user_credit_conversion_user_money_share"]').first().trigger("click");
				$('[name="submit-user_identity"]').first().trigger("click");
				$('[name="submit-user_money_earning_transfer_user_money"]').first().trigger("click");
				$('[name="submit-user_money_earning_transfer_user_money_help"]').first().trigger("click");
				$('[name="submit-user_money_share_conversion_annuity_earning_help"]').first().trigger("click");
				$('[name="submit-rmb_withdraw_user_money"]').first().trigger("click");
				$('[name="submit-recommend_reward_user_money"]').first().trigger("click");
				
			}
		});
	},
	
	
	event : function(){
		var _project = WangAho(this.id);
		_project.keyup();
		_project.event_rmb_withdraw_user_money_earning();
		_project.event_daily_attendance_earn_user_credit();
		_project.event_parent_recommend_user_credit();
		_project.event_rmb_consume_user_credit();
		_project.event_user_credit_conversion_user_money_share();
		_project.event_user_identity();
		_project.event_user_money_earning_transfer_user_money();
		_project.event_user_money_earning_transfer_user_money_help();
		_project.event_user_money_share_conversion_annuity_earning_help();
		_project.event_rmb_withdraw_user_money();
		_project.event_recommend_reward_user_money();
		
	},
	
	
		
	//用户推荐奖励钱包配置
	event_recommend_reward_user_money : function(){
		var _project = WangAho(this.id);
		$('[name="submit-recommend_reward_user_money"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.money = $.trim($('[name="money"]').val());
			
			form_input.random = $('[name="random"]').is(':checked')? 1 : 0;
			form_input.money_min = $.trim($('[name="money_min"]').val());
			form_input.money_max = $.trim($('[name="money_max"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				
				if( form_input.money == "" ||
				!money_format.test(form_input.money) ){
					throw "固定金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.money = parseInt((parseFloat(form_input.money).toFixed(2))*100);//元转为分
				}
				
				if( form_input.money_min == "" ||
				!money_format.test(form_input.money_min) ){
					throw "最小随机金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.money_min = parseInt((parseFloat(form_input.money_min).toFixed(2))*100);//元转为分
				}
				
				if( form_input.money_max == "" ||
				!money_format.test(form_input.money_max) ){
					throw "最大随机金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.money_max = parseInt((parseFloat(form_input.money_max).toFixed(2))*100);//元转为分
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
				request:["USERADMINCONFIGEDIT", [{recommend_reward_user_money:form_input}]],
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
	

	
	
	//用户钱包提现配置
	event_rmb_withdraw_user_money : function(){
		var _project = WangAho(this.id);
		$('[name="submit-rmb_withdraw_user_money"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.min_user_money = $.trim($('[name="min_user_money"]').val());
			form_input.max_user_money = $.trim($('[name="max_user_money"]').val());
			form_input.ratio_service_money = $.trim($('[name="ratio_service_money"]').val());
			form_input.algorithm = $.trim($('[name="algorithm"]').val());
			form_input.pay_password_state = $('[name="pay_password_state"]').is(':checked')? 1 : 0;
			form_input.user_identity_state = $('[name="user_identity_state"]').is(':checked')? 1 : 0;
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				if( form_input.min_user_money == "" ||
				!money_format.test(form_input.min_user_money) ){
					throw "最小提现金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.min_user_money = parseInt((parseFloat(form_input.min_user_money).toFixed(2))*100);//元转为分
				}
				
				if( form_input.max_user_money == "" ||
				!money_format.test(form_input.max_user_money) ){
					throw "最大提现金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.max_user_money = parseInt((parseFloat(form_input.max_user_money).toFixed(2))*100);//元转为分
				}
				
				var ratio_format = /^([\d]+)(\.[\d]+)?$/;
				if( form_input.ratio_service_money == "" ||
				!ratio_format.test(form_input.ratio_service_money) ){
					throw "收取的服务费比值的格式输入有误，格式必须是整数或小数";
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
				request:["USERADMINCONFIGEDIT", [{rmb_withdraw_user_money:form_input}]],
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
	
	

	
	
	//用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置
	event_user_money_share_conversion_annuity_earning_help : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-user_money_share_conversion_annuity_earning_help"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.multiple_user_money_share = $.trim($('[name="multiple_user_money_share"]').val());
			form_input.ratio_user_money_help = $.trim($('[name="ratio_user_money_help"]').val());
			form_input.ratio_user_money_annuity = $.trim($('[name="ratio_user_money_annuity"]').val());
			form_input.ratio_user_money_earning = $.trim($('[name="ratio_user_money_earning"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				
				var money_format = /^([\d]+)(\.[\d]{1,2})?$/;
				if( form_input.multiple_user_money_share == "" ||
				!money_format.test(form_input.multiple_user_money_share) ){
					throw "消费共享金倍数的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.multiple_user_money_share = parseInt((parseFloat(form_input.multiple_user_money_share).toFixed(2))*100);//元转为分
				}
				
				var format_ratio = /^0(\.[\d]+)?$/;
				if( form_input.ratio_user_money_help == "" ||
				!format_ratio.test(form_input.ratio_user_money_help) ){
					throw "消费共享金转换到扶贫资金账户比例的格式输入有误，格式必须是小于1的小数";
				}
				if( form_input.ratio_user_money_annuity == "" ||
				!format_ratio.test(form_input.ratio_user_money_annuity) ){
					throw "消费共享金转换到养老金比例的格式输入有误，格式必须是小于1的小数";
				}
				if( form_input.ratio_user_money_earning == "" ||
				!format_ratio.test(form_input.ratio_user_money_earning) ){
					throw "消费共享金转换到赠送收益账户比例的格式输入有误，格式必须是小于1的小数";
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
				request:["USERADMINCONFIGEDIT", [{user_money_share_conversion_annuity_earning_help : form_input}]],
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
	
	
		
	//用户的赠送收益金额转账到扶贫账户
	event_user_money_earning_transfer_user_money_help : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		
		$('[name="submit-user_money_earning_transfer_user_money_help"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.min_user_money_earning = $.trim($('[name="min_user_money_earning"]').val());
			form_input.max_user_money_earning = $.trim($('[name="max_user_money_earning"]').val());
			form_input.ratio_user_money_service = $.trim($('[name="ratio_user_money_service"]').val());
			form_input.algorithm = $.trim($('[name="algorithm"]').val());
			form_input.pay_password_state = $('[name="pay_password_state"]').is(':checked')? 1 : 0;
			form_input.user_identity_state = $('[name="user_identity_state"]').is(':checked')? 1 : 0;
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				
				var money_format = /^([\d]+)(\.[\d]{1,2})?$/;
				if( form_input.min_user_money_earning == "" ||
				!money_format.test(form_input.min_user_money_earning) ){
					throw "最小转账金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.min_user_money_earning = parseInt((parseFloat(form_input.min_user_money_earning).toFixed(2))*100);//元转为分
				}
				
				if( form_input.max_user_money_earning == "" ||
				!money_format.test(form_input.max_user_money_earning) ){
					throw "最大转账金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.max_user_money_earning = parseInt((parseFloat(form_input.max_user_money_earning).toFixed(2))*100);//元转为分
				}
				
				var ratio_format = /^([\d]+)(\.[\d]+)?$/;
				if( form_input.ratio_user_money_service == "" ||
				!ratio_format.test(form_input.ratio_user_money_service) ){
					throw "收取的用户服务费比值的格式输入有误，格式必须是整数或小数";
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
				request:["USERADMINCONFIGEDIT", [{user_money_earning_transfer_user_money_help : form_input}]],
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
	
	
	
	
	
	
	//用户的赠送收益金额转账到用户钱包
	event_user_money_earning_transfer_user_money : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		
		$('[name="submit-user_money_earning_transfer_user_money"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.min_user_money_earning = $.trim($('[name="min_user_money_earning"]').val());
			form_input.max_user_money_earning = $.trim($('[name="max_user_money_earning"]').val());
			form_input.ratio_user_money_service = $.trim($('[name="ratio_user_money_service"]').val());
			form_input.algorithm = $.trim($('[name="algorithm"]').val());
			form_input.pay_password_state = $('[name="pay_password_state"]').is(':checked')? 1 : 0;
			form_input.user_identity_state = $('[name="user_identity_state"]').is(':checked')? 1 : 0;
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				
				var money_format = /^([\d]+)(\.[\d]{1,2})?$/;
				if( form_input.min_user_money_earning == "" ||
				!money_format.test(form_input.min_user_money_earning) ){
					throw "最小转账金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.min_user_money_earning = parseInt((parseFloat(form_input.min_user_money_earning).toFixed(2))*100);//元转为分
				}
				
				if( form_input.max_user_money_earning == "" ||
				!money_format.test(form_input.max_user_money_earning) ){
					throw "最大转账金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.max_user_money_earning = parseInt((parseFloat(form_input.max_user_money_earning).toFixed(2))*100);//元转为分
				}
				
				var ratio_format = /^([\d]+)(\.[\d]+)?$/;
				if( form_input.ratio_user_money_service == "" ||
				!ratio_format.test(form_input.ratio_user_money_service) ){
					throw "收取的用户服务费比值的格式输入有误，格式必须是整数或小数";
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
				request:["USERADMINCONFIGEDIT", [{user_money_earning_transfer_user_money : form_input}]],
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
	
	
	//用户实名认证配置
	event_user_identity : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-user_identity"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.expire_time = $.trim($('[name="expire_time"]').val());
			form_input.expire_state = $('[name="expire_state"]').is(':checked')? 1 : 0;
			form_input.auto_state = $('[name="auto_state"]').is(':checked')? 1 : 0;
			
			try {
				
				var format = /^[\d]+$/;
				if( form_input.expire_time == "" ||
				!format.test(form_input.expire_time) ){
					throw "认证时间有效期的格式输入有误，格式必须是整数";
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
				request:["USERADMINCONFIGEDIT", [{user_identity:form_input}]],
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
	
	
	//【系统自动转换】用户积分兑换为共享金配置
	event_user_credit_conversion_user_money_share : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-user_credit_conversion_user_money_share"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.basic_conversion_ratio = $.trim($('[name="basic_conversion_ratio"]').val());
			form_input.min_conversion_ratio = $.trim($('[name="min_conversion_ratio"]').val());
			form_input.max_conversion_ratio = $.trim($('[name="max_conversion_ratio"]').val());
			form_input.precision_conversion_ratio = $.trim($('[name="precision_conversion_ratio"]').val());
			form_input.multiple_user_credit = $.trim($('[name="multiple_user_credit"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				
				var format_ratio = /^([\d]+)(\.[\d]+)?$/;
				if( form_input.basic_conversion_ratio == "" ||
				!format_ratio.test(form_input.basic_conversion_ratio) ){
					throw "转换率基数的格式输入有误，格式必须是整数或者是小数";
				}
				if( form_input.min_conversion_ratio == "" ||
				!format_ratio.test(form_input.min_conversion_ratio) ){
					throw "最小转换率的格式输入有误，格式必须是整数或者是小数";
				}
				if( form_input.max_conversion_ratio == "" ||
				!format_ratio.test(form_input.max_conversion_ratio) ){
					throw "最大转换率的格式输入有误，格式必须是整数或者是小数";
				}
				
				var format_precision = /^[\d]+$/;
				if( form_input.precision_conversion_ratio == "" ||
				!format_precision.test(form_input.precision_conversion_ratio) ){
					throw "转换率精度的格式输入有误，格式必须是整数";
				}
				
				
				if( !_application_config_credit ){
					throw "积分配置异常";
				}
				var throw_info = "积分倍数的格式输入有误，必须是大于0的整数";
				if( parseInt(_application_config_credit.precision) ){
					throw_info = "积分倍数的格式输入有误，必须是大于0的整数或"+_application_config_credit.precision+"位小数";
					}
				
				var format = /^([\d]+)(\.[\d]+)?$/;
				var format_foot = /\./;
				
				if( form_input.multiple_user_credit == "" ||
				!format.test(form_input.multiple_user_credit) ){
					throw throw_info;
				}else{
					form_input.multiple_user_credit = parseFloat(form_input.multiple_user_credit) * _application_config_credit.scale;
				}
				//如果存在小数点则报错
				if( format_foot.test(form_input.multiple_user_credit) ){
					throw throw_info;
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
				request:["USERADMINCONFIGEDIT", [{user_credit_conversion_user_money_share:form_input}]],
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
	
	
	
	//用户消费平台赠送积分配置
	event_rmb_consume_user_credit : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-rmb_consume_user_credit"]').unbind("click").click(function(){
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
				request:["USERADMINCONFIGEDIT", [{rmb_consume_user_credit:form_input}]],
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
	
	
	
	//消费用户推荐人与商家用户推荐人的平台积分奖励配置
	event_parent_recommend_user_credit : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-parent_recommend_user_credit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.ratio_user_credit = $.trim($('[name="ratio_user_credit"]').val());
			form_input.ratio_merchant_user_credit = $.trim($('[name="ratio_merchant_user_credit"]').val());
			form_input.algorithm = $.trim($('[name="algorithm"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				var ratio_format = /^([\d]+)(\.[\d]+)?$/;
				if( form_input.ratio_user_credit == "" ||
				!ratio_format.test(form_input.ratio_user_credit) ){
					throw "消费用户推荐人平台赠送积分比值的格式输入有误，格式必须是整数或小数";
				}
				if( form_input.ratio_merchant_user_credit == "" ||
				!ratio_format.test(form_input.ratio_merchant_user_credit) ){
					throw "商家用户推荐人平台赠送积分比值的格式输入有误，格式必须是整数或小数";
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
				request:["USERADMINCONFIGEDIT", [{parent_recommend_user_credit:form_input}]],
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
	
	
	
	
	//用户每日签到赠送积分
	event_daily_attendance_earn_user_credit : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		$('[name="submit-daily_attendance_earn_user_credit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.credit = $.trim($('[name="credit"]').val());
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				
				if( !_application_config_credit ){
					throw "积分配置异常";
				}
				var throw_info = "积分数量的格式输入有误，必须是大于0的整数";
				if( parseInt(_application_config_credit.precision) ){
					throw_info = "积分数量的格式输入有误，必须是大于0的整数或"+_application_config_credit.precision+"位小数";
					}
				
				var format = /^([\d]+)(\.[\d]+)?$/;
				var format_foot = /\./;
				
				if( form_input.credit == "" ||
				!format.test(form_input.credit) ){
					throw throw_info;
				}else{
					form_input.credit = parseFloat(form_input.credit) * _application_config_credit.scale;
				}
				//如果存在小数点则报错
				if( format_foot.test(form_input.credit) ){
					throw throw_info;
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
				request:["USERADMINCONFIGEDIT", [{daily_attendance_earn_user_credit:form_input}]],
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
	
	
	//用户的赠送收益金额提现配置
	event_rmb_withdraw_user_money_earning : function(){
		var _project = WangAho(this.id);
		$('[name="submit-rmb_withdraw_user_money_earning"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			form_input.min_user_money_earning = $.trim($('[name="min_user_money_earning"]').val());
			form_input.max_user_money_earning = $.trim($('[name="max_user_money_earning"]').val());
			form_input.ratio_user_money_service = $.trim($('[name="ratio_user_money_service"]').val());
			form_input.algorithm = $.trim($('[name="algorithm"]').val());
			form_input.pay_password_state = $('[name="pay_password_state"]').is(':checked')? 1 : 0;
			form_input.user_identity_state = $('[name="user_identity_state"]').is(':checked')? 1 : 0;
			form_input.state = $('[name="state"]').is(':checked')? 1 : 0;
			
			try {
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				if( form_input.min_user_money_earning == "" ||
				!money_format.test(form_input.min_user_money_earning) ){
					throw "最小提现金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.min_user_money_earning = parseInt((parseFloat(form_input.min_user_money_earning).toFixed(2))*100);//元转为分
				}
				
				if( form_input.max_user_money_earning == "" ||
				!money_format.test(form_input.max_user_money_earning) ){
					throw "最大提现金额的格式输入有误，格式必须是整数或者是两位小数";
				}else{
					form_input.max_user_money_earning = parseInt((parseFloat(form_input.max_user_money_earning).toFixed(2))*100);//元转为分
				}
				
				var ratio_format = /^([\d]+)(\.[\d]+)?$/;
				if( form_input.ratio_user_money_service == "" ||
				!ratio_format.test(form_input.ratio_user_money_service) ){
					throw "收取的用户服务费比值的格式输入有误，格式必须是整数或小数";
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
				request:["USERADMINCONFIGEDIT", [{rmb_withdraw_user_money_earning:form_input}]],
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









