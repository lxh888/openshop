WangAho({
	
	
	id:"merchant/credit_list",
	
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	data : null,
	action : null,
	main : function(){
		var _project = WangAho(this.id);
		var config = {search:{}};
		var _http = http();
		
		//排序
		if( (function(){try{ return _http.anchor.query.sort;}catch(e){return false;}}()) ){
			config.sort = [_http.anchor.query.sort];
		}else{
			
			if( !_http.anchor.query ){
				_http.anchor.query = {};
			}
			WangAho().history_remove();//删除本页的记录
			_http.anchor.query.sort = "time_desc";
			http(_http).request();
			return false;
			
		}
		
		var search = (function(){try{ return _http.anchor.query.search;}catch(e){return false;}}());
		if( search ){
			search = _http.decode(search);
			config.search = JSON.parse(search);
		}
		
		//分页
		if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
			config.page = _http.anchor.query.page;
		}
			
		var request = {
				application_config:["APPLICATIONCONFIG"],
				list:["MERCHANTADMINCREDITLIST", [config]]
			};
		var template_html = WangAho().template("page/merchant/credit_list.html", "#content");
		_project.action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( _project.action == "serial" ){
			request.list = ["MERCHANTADMINCREDITSERIALLIST", [config]];
			template_html = WangAho().template("page/merchant/credit_list.html", "#serial-content");
		}else{
			request.total = ["MERCHANTADMINCREDITTOTAL", [config.search]]
		}
		
		if( config.search.merchant_id ){
			request.merchant_get = ["MERCHANTADMINGET", [{merchant_id : config.search.merchant_id}]];
		}
		
		WangAho("index").data({
			request : request, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.list.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.list.error, {icon: 5, time: 2000});
				}
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				_project.data = data;
				WangAho("index").view(template_html, data, {
					
					"anchor-query-action" : function(s){
						var h = http();
						if(!h.anchor.query) h.anchor.query = {};
						if(typeof s == 'undefined'){
							if(typeof h.anchor.query.action != 'undefined') delete h.anchor.query.action;
						}else{
							h.anchor.query.action = s;
						}
						
						if(typeof h.anchor.query.search != 'undefined') delete h.anchor.query.search;
						if(typeof h.anchor.query.page != 'undefined') delete h.anchor.query.page;
						if(typeof h.anchor.query.sort != 'undefined') delete h.anchor.query.sort;
						return http(h).href;
					}
					
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
				
				$('[name="plus-submit"]').first().trigger("click");
				$('[name="minus-submit"]').first().trigger("click");
				$('[name="search-submit"]').first().trigger("click");
			}
		});
	},
	
	
	search_event : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		
		//筛选
		$(".search").unbind("click").click(function(){
			layer.closeAll();
			var _http = http();
			var search = (function(){try{ return _http.anchor.query.search;}catch(e){return false;}}());
			if( search ){
				search = _http.decode(search);
				search = JSON.parse(search);
			}
			
			var search_template = WangAho().template("page/merchant/credit_list.html", "#search");
			if( _project.action == "serial" ){
				search_template = WangAho().template("page/merchant/credit_list.html", "#serial-search");
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-filter\"></span> 筛选",
			  	type: 1,
			  	shadeClose: true,
			  	area: 'auto', //宽高
			  	maxWidth: $(window).width() > 1200? 1200 : $(window).width()-50,
			  	maxHeight: $(window).height()-50,
			  	content: template( search_template, function(fn){
					return fn({search:search, application_config_credit : _application_config_credit});
					})
			});
			
			$('input[search]').first().focus();//失去焦点
			_project.search_event();
		});
		
		$('[name="search-submit"]').unbind("click").click(function(){
			var _http = http();
			if( !_http.anchor.query ) _http.anchor.query = {};
			var search = {};
			search.merchant_id = $.trim($('[name="merchant_id"]').val());
			search.merchant_name = $.trim($('[name="merchant_name"]').val());
			if( _project.action == "serial" ){
				search.merchant_credit_id = $.trim($('[name="merchant_credit_id"]').val());
				search.type_name = $.trim($('[name="type_name"]').val());
				search.type = $.trim($('[name="type"]').val());
				search.order_action_user_id = $.trim($('[name="order_action_user_id"]').val());
				search.order_action_user_nickname = $.trim($('[name="order_action_user_nickname"]').val());
				search.order_action_user_phone = $.trim($('[name="order_action_user_phone"]').val());
			}else{
				search.min_value = $.trim($('[name="min_value"]').val());
				search.max_value = $.trim($('[name="max_value"]').val());
				
				if( !_application_config_credit ){
					layer.msg("积分配置异常", {icon: 5, time: 2000});
					return false;
				}
				
				var money_format_1 = /^[0-9\.]+$/;
				var money_format_2 = /\./;
				
				if( search.min_value != ''){
					if( money_format_1.test(search.min_value) ){
						search.min_value = parseFloat(search.min_value) * _application_config_credit.scale;
					}else{
						search.min_value = null;
					}
					
					if( !search.min_value || money_format_2.test(search.min_value) ){
						if( parseInt(_application_config_credit.precision) ){
							layer.msg("最小积分数量格式输入有误，格式必须是大于0的整数或者"+_application_config_credit.precision+"位小数", {icon: 5, time: 2000});
							return false;
						}else{
							layer.msg("最小积分数量格式输入有误，格式必须是大于0的整数", {icon: 5, time: 2000});
							return false;
						}
					}
				}
				
				if( search.max_value != ''){
					if( money_format_1.test(search.max_value) ){
						search.max_value = parseFloat(search.max_value) * _application_config_credit.scale;
					}else{
						search.max_value = null;
					}
					
					if( !search.max_value || money_format_2.test(search.max_value) ){
						if( parseInt(_application_config_credit.precision) ){
							layer.msg("最大积分数量格式输入有误，格式必须是大于0的整数或者"+_application_config_credit.precision+"位小数", {icon: 5, time: 2000});
							return false;
						}else{
							layer.msg("最大积分数量格式输入有误，格式必须是大于0的整数", {icon: 5, time: 2000});
							return false;
						}
					}
				}
				
			}
			
			for(var i in search){
				if(search[i] == ""){
					delete search[i];
				}
			}
			search = JSON.stringify(search);
			if(search == "{}"){
				if( _http.anchor.query.search ) delete _http.anchor.query.search;
			}else{
				_http.anchor.query.search = _http.encode(search);
			}
			
			http(_http).request();
			layer.closeAll();
		});
		
		//清理筛选
		$('[name="search-clear"]').unbind("click").click(function(){
			var _http = http();
			if( _http.anchor.query && _http.anchor.query.search ){
				delete _http.anchor.query.search;
			}
			
			http(_http).request();
			layer.closeAll();
		});
		
	},
	
	event : function(){
		var _project = WangAho(this.id);
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		this.keyup();
		
		//筛选
		this.search_event();
		
		//增加商家积分
		$(".form_plus").unbind("click").click(function(){
			layer.closeAll();
			var merchant_id = $(this).attr("data-id");
			
			if( !_application_config_credit ){
				layer.msg("积分配置异常", {icon: 5, time: 2000});
				return false;
			}
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 增加商家积分",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/merchant/credit_list.html", "#plus"), function(fn){
					return fn({merchant_id : merchant_id, application_config_credit : _application_config_credit});
					})
			});
			
			$('input[name="value"]').first().focus();//失去焦点
			_project.event();
		});
		
		
		$('[name="plus-submit"]').unbind("click").click(function(){
			if( !_application_config_credit ){
				layer.msg("积分配置异常", {icon: 5, time: 2000});
				return false;
			}
			
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = $.trim($('[name="merchant_id"]').val());
			form_input.value = $.trim($('[name="value"]').val());
			form_input.comment = $.trim($('[name="comment"]').val());
			form_input.type = "admin_plus";
			
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				if(form_input.value == ''){
					throw "要增加的积分不能为空";
				}else{
					var money_format = /^[0-9\.]+$/;
					if( money_format.test(form_input.value) ){
						form_input.value = parseFloat(form_input.value) * _application_config_credit.scale;
					}else{
						form_input.value = null;
					}
				}
				
				var money_format = /\./;
				if( !form_input.value || money_format.test(form_input.value) ){
					if( parseInt(_application_config_credit.precision) ){
						throw "积分数量格式输入有误，格式必须是大于0的整数或者"+_application_config_credit.precision+"位小数";
					}else{
						throw "积分数量格式输入有误，格式必须是大于0的整数";
					}
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
				request:["MERCHANTADMINCREDITEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					layer.closeAll();
					$btn.removeClass('disabled');
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
		
		//减少商家积分
		$(".form_minus").unbind("click").click(function(){
			layer.closeAll();
			var merchant_id = $(this).attr("data-id");
			var value = $(this).attr("data-value");
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-minus\"></span> 减少商家积分",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/merchant/credit_list.html", "#minus"), function(fn){
					return fn({merchant_id : merchant_id, value : value, application_config_credit : _application_config_credit});
					})
			});
			
			$('input[name="value"]').first().focus();//失去焦点
			_project.event();
		});
		
		
		$('[name="minus-submit"]').unbind("click").click(function(){
			if( !_application_config_credit ){
				layer.msg("积分配置异常", {icon: 5, time: 2000});
				return false;
			}
			
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = $.trim($('[name="merchant_id"]').val());
			form_input.value = $.trim($('[name="value"]').val());
			form_input.comment = $.trim($('[name="comment"]').val());
			form_input.type = "admin_minus";
			
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				if(form_input.value == ''){
					throw "要减少的积分不能为空";
				}else{
					var money_format = /^[0-9\.]+$/;
					if( money_format.test(form_input.value) ){
						form_input.value = parseFloat(form_input.value) * _application_config_credit.scale;
					}else{
						form_input.value = null;
					}
				}
				
				var money_format = /\./;
				if( !form_input.value || money_format.test(form_input.value) ){
					if( parseInt(_application_config_credit.precision) ){
						throw "积分数量格式输入有误，格式必须是大于0的整数或者"+_application_config_credit.precision+"位小数";
					}else{
						throw "积分数量格式输入有误，格式必须是大于0的整数";
					}
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
				request:["MERCHANTADMINCREDITEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					layer.closeAll();
					$btn.removeClass('disabled');
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
		
		
		
		
		
	},
	
	
	
	
	
	
	
});




