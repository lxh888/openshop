WangAho({
	
	
	id:"user/money_help_list",
	
	
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
				list:["USERADMINMONEYHELPLIST", [config]]
			};
		var template_html = WangAho().template("page/user/money_help_list.html", "#content");
		_project.action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( _project.action == "serial" ){
			request.list = ["USERADMINMONEYHELPSERIALLIST", [config]];
			template_html = WangAho().template("page/user/money_help_list.html", "#serial-content");
		}else{
			request.total = ["USERADMINMONEYHELPTOTAL", [config.search]]
		}
		
		if( config.search.user_id ){
			request.user_get = ["USERADMINGET", [{user_id : config.search.user_id}]];
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
		//筛选
		$(".search").unbind("click").click(function(){
			layer.closeAll();
			var _http = http();
			var search = (function(){try{ return _http.anchor.query.search;}catch(e){return false;}}());
			if( search ){
				search = _http.decode(search);
				search = JSON.parse(search);
			}
			
			var search_template = WangAho().template("page/user/money_help_list.html", "#search");
			if( _project.action == "serial" ){
				search_template = WangAho().template("page/user/money_help_list.html", "#serial-search");
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
					return fn({search:search});
					})
			});
			
			$('input[search]').first().focus();//失去焦点
			_project.search_event();
		});
		
		$('[name="search-submit"]').unbind("click").click(function(){
			var _http = http();
			if( !_http.anchor.query ) _http.anchor.query = {};
			var search = {};
			search.user_id = $.trim($('[name="user_id"]').val());
			search.user_nickname = $.trim($('[name="user_nickname"]').val());
			search.user_phone = $.trim($('[name="user_phone"]').val());
			if( _project.action == "serial" ){
				search.user_money_help_id = $.trim($('[name="user_money_help_id"]').val());
				search.type_name = $.trim($('[name="type_name"]').val());
				search.type = $.trim($('[name="type"]').val());
				search.order_action_user_id = $.trim($('[name="order_action_user_id"]').val());
				search.order_action_user_nickname = $.trim($('[name="order_action_user_nickname"]').val());
				search.order_action_user_phone = $.trim($('[name="order_action_user_phone"]').val());
			}else{
				search.min_value = $.trim($('[name="min_value"]').val());
				search.max_value = $.trim($('[name="max_value"]').val());
				
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				if( search.min_value != "" ){
					if( money_format.test(search.min_value) ){
						search.min_value = parseInt((parseFloat(search.min_value).toFixed(2))*100);//元转为分
					}else{
						layer.msg("最小余额格式输入有误，格式必须是整数或者是两位小数", {icon: 5, time: 2000});
						return false;
					}
				}
				
				if( search.max_value != "" ){
					if( money_format.test(search.max_value) ){
						search.max_value = parseInt((parseFloat(search.max_value).toFixed(2))*100);//元转为分
					}else{
						layer.msg("最大余额格式输入有误，格式必须是整数或者是两位小数", {icon: 5, time: 2000});
						return false;
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
		this.keyup();
		
		//筛选
		this.search_event();
		
		//增加商家积分
		$(".form_plus").unbind("click").click(function(){
			layer.closeAll();
			var user_id = $(this).attr("data-id");
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 增加用户扶贫基金",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/user/money_help_list.html", "#plus"), function(fn){
					return fn({user_id : user_id});
					})
			});
			
			$('input[name="value"]').first().focus();//失去焦点
			_project.event();
		});
		
		
		$('[name="plus-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = $.trim($('[name="user_id"]').val());
			form_input.value = $.trim($('[name="value"]').val());
			form_input.comment = $.trim($('[name="comment"]').val());
			form_input.type = "admin_plus";
			
			try {
				if(form_input.user_id == '') throw "用户ID异常";
				//价格
				if(form_input.value == ""){
					throw "金额不能为空";
				}else{
					var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
					if( money_format.test(form_input.value) ){
						form_input.value = parseInt((parseFloat(form_input.value).toFixed(2))*100);//元转为分
					}else{
						throw "金额格式输入有误，格式必须是整数或者是两位小数";
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
				request:["USERADMINMONEYHELPEDIT", [form_input]],
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
			var user_id = $(this).attr("data-id");
			var value = $(this).attr("data-value");
			
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-minus\"></span> 减少用户扶贫基金",
			  	type: 1,
			  	shadeClose: true,
			  	area: ['auto'], //宽高
			  	maxWidth: 1000,
			  	content: template( WangAho().template("page/user/money_help_list.html", "#minus"), function(fn){
					return fn({user_id : user_id, value : value});
					})
			});
			
			$('input[name="value"]').first().focus();//失去焦点
			_project.event();
		});
		
		
		$('[name="minus-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = $.trim($('[name="user_id"]').val());
			form_input.value = $.trim($('[name="value"]').val());
			form_input.comment = $.trim($('[name="comment"]').val());
			form_input.type = "admin_minus";
			
			try {
				if(form_input.user_id == '') throw "用户ID异常";
				//价格
				if(form_input.value == ""){
					throw "金额不能为空";
				}else{
					var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
					if( money_format.test(form_input.value) ){
						form_input.value = parseInt((parseFloat(form_input.value).toFixed(2))*100);//元转为分
					}else{
						throw "金额格式输入有误，格式必须是整数或者是两位小数";
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
				request:["USERADMINMONEYHELPEDIT", [form_input]],
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




