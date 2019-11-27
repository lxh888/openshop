WangAho({
	
	
	id : "user/user_edit",
	
	
	
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	
	
	
	main : function(){
		var _project = WangAho(this.id);
		var config = {};
		var _http = http();
		config.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if( !config.user_id ){
			http("#/user/user_list").request();
			return false;
		}
		
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( !action ){
			action = "user";
		}
		
		//数据请求
		var request = {application_config:["APPLICATIONCONFIG"],get:["USERADMINGET", [config]]};
		var template_data = WangAho().template("page/user/user_edit.html", "#user");
		if( action == "user_phone" ){
			request.user_phone_list = ["USERADMINPHONELIST", [config]]; //获取用户登录手机号
			template_data = WangAho().template("page/user/user_edit.html", "#user_phone");
		}else
		if( action == "user_password" ){
			template_data = WangAho().template("page/user/user_edit.html", "#user_password");
		}else
		if( action == "user_parent" ){
			request.user_parent_get = ["USERADMINPARENTGET", [config]];
			template_data = WangAho().template("page/user/user_edit.html", "#user_parent");
		}
		
		
		WangAho("index").data({
			request : request, 
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.user_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/user/user_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				data.action = action;
				
				//获得配置数据
				data.config = WangAho().data("config.json");
				WangAho("index").view(template_data, data, {
					
					"action-query-href" : function(action){
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
					},
					
				});
				
				if( action == "user" ){
					_project.event_user();
				}else
				if( action == "user_phone" ){
					_project.event_user_phone();
				}else
				if( action == "user_password" ){
					_project.event_user_password();
				}else
				if( action == "user_parent" ){
					_project.event_user_parent();
				}
				
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
		        $(".user_phone_add").first().trigger("click");
		        $(".user_parent_submit").first().trigger("click");
			}
		});
	},
	
	logo_file : null,
	event_user : function(){
		var _project = WangAho(this.id);
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
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.user_nickname = $.trim($('[name="user_nickname"]').val());
			form_input.user_compellation = $.trim($('[name="user_compellation"]').val());
			form_input.user_state = $('[name="user_state"]').is(':checked')? 0 : 1;
			form_input.user_sex = $.trim($('[name="user_sex"]:checked').val());
			
			try {
				if(form_input.user_id == '') throw "用户ID不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["USERADMINEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
		
		//选择上传图片
		$('[name="image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="image-files"]').get(0));
		});
		
		//图片发生改变时执行
		$('[name="image-files"]').unbind("change").change(function(){
			if( $('[name="image-files"]')[0].files.length ){
				_project.logo_file = $('[name="image-files"]')[0].files[0];
				_project.logo_file.src = new eonfox().file_url( _project.logo_file );
				$('[name="image-show"]').attr("src", _project.logo_file.src).attr("data-src", _project.logo_file.src);
				$('[name="image-show"]').show();
			}
		});
		
		
		$('[name="image-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			try {
				if(form_input.user_id == '') throw "用户ID异常";
				if( !_project.logo_file ) throw "没有需要上传的图片";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			eonfox().submit({
				request : JSON.stringify({
					s : ["USERADMINLOGOQINIUUPLOAD", [form_input]],
					}),
				data : {file: _project.logo_file},	
				progress : function(loaded, total, percent){
					//console.log(loaded, total, percent);
					if(percent == 100){
		        		//layer.msg('上传成功', {icon: 1, time: 1000});
		        	}else{
		        		layer.msg( Math.floor(percent)+"%" );
		        	}
				},
				callback : function(r){
					layer.closeAll('loading');//关闭加载
					if( !r ){
						layer.msg("未知错误", {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					if( (function(){try{ return r.data.s.errno;}catch(e){return false;}}()) ){
						layer.msg(r.data.s.error, {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					
					layer.msg("操作成功!", {icon: 1, time: 1000});
					setTimeout(function(){
						//刷新页面
						WangAho("index").scroll_constant(function(){
							_project.main();
						});
					}, 1000);
				}
			});
			
			
		});
		
		
	},
	
	

	event_user_phone : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		
		
		$(".user_phone_edit").unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.user_phone_id = $btn.attr("data-phone");
			var form = $('.user_phone_form-'+form_input.user_phone_id);
			form_input.user_phone_sort = $.trim(form.find('[name="user_phone_sort"]').val());
			form_input.user_phone_type = form.find('[name="user_phone_type"]').is(':checked')? 1 : 0;
			
			try {
				if(form_input.user_id == '') throw "用户ID不能为空";
				if(form_input.user_phone_id == '') throw "手机号码获取失败";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["USERADMINPHONEEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
		});
		
		
		$(".user_phone_add").unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			var form = $('.user_phone_add_form');
			form_input.user_phone_id = $.trim(form.find('[name="user_phone_id"]').val());
			form_input.user_phone_sort = $.trim(form.find('[name="user_phone_sort"]').val());
			form_input.user_phone_type = form.find('[name="user_phone_type"]').is(':checked')? 1 : 0;
			
			try {
				if(form_input.user_id == '') throw "用户ID不能为空";
				if(form_input.user_phone_id == '') throw "手机号码不能为空";
				if(form_input.user_phone_sort == ''){
					delete form_input.user_phone_sort;
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
				request:["USERADMINPHONEADD", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
		
		$(".user_phone_remove").unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			var form = $('.user_phone_add_form');
			form_input.user_phone_id = $btn.attr("data-phone");
			
			try {
				if(form_input.user_id == '') throw "用户ID不能为空";
				if(form_input.user_phone_id == '') throw "手机号码获取失败";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			layer.msg('你确定要删除 “' + form_input.user_phone_id + '” 手机号码么？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function(){
					$btn.removeClass('disabled');
				},
				yes: function(index){
				    layer.close(index);
				   
				    //提交数据
					WangAho("index").submit({
						method:"submit",
						request:["USERADMINPHONEREMOVE", [form_input]],
						error:function(){
							$btn.removeClass('disabled');
						},
						success:function(data){
							$btn.removeClass('disabled');
							//刷新页面
							WangAho("index").scroll_constant(function(){
								_project.main();
							});
						}
					});
				   
				   
				  }
			});
			
			
		});
		
		
	},
	
	
	event_user_password : function(){
		var _project = WangAho(this.id);
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
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.user_password = $.trim($('[name="user_password"]').val());
			form_input.user_confirm_password = $.trim($('[name="user_confirm_password"]').val());
			
			try {
				if(form_input.user_id == '') throw "用户ID不能为空";
				if(form_input.user_password == '') throw "登录密码不能为空";
				if(form_input.user_confirm_password == '') throw "确认密码不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["USERADMINPASSWORDEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	},
	
	
	event_user_parent : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		
		
		$(".user_parent_remove").unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.user_parent_id = "";
			
			try {
				if(form_input.user_id == '') throw "用户ID获取失败";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			layer.msg('你确定要删除该用户的推荐人吗？', {
				time: 0, //不自动关闭
				btn: ['确定', '取消'],
				end: function(){
					$btn.removeClass('disabled');
				},
				yes: function(index){
				    layer.close(index);
				   
				    //提交数据
					WangAho("index").submit({
						method:"submit",
						request:["USERADMINEDIT", [form_input]],
						error:function(){
							$btn.removeClass('disabled');
						},
						success:function(data){
							$btn.removeClass('disabled');
							//刷新页面
							WangAho("index").scroll_constant(function(){
								_project.main();
							});
						}
					});
				   
				   
				  }
			});
			
			
			
			
		});
		
		
		
		$(".user_parent_submit").unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.user_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.user_parent = $.trim($('[name="user_parent"]').val());
			
			try {
				if(form_input.user_id == '') throw "用户ID获取失败";
				if(form_input.user_parent == '') throw "需要输入推荐人ID或者推荐人登录手机号";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["USERADMINEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					$btn.removeClass('disabled');
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			});
			
			
		});
		
	},
	
});