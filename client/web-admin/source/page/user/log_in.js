WangAho({
	
	
	id : "user/log_in",
	
	
	//路由发送变化时
	hashchange : function(){
		console.log('登录页面，路由发送了变化', http() );
		
	},
	
	
	image_verify_key : 'user/log_in',//验证码的键
	
	main : function(){
		
		var session = null;//判断是否登录使用
		//判断是否已经登录
		eonfox().submit({
			request : JSON.stringify({s:["USERSELF"]}),
			async : false,
			callback:function(r){
				
				try {
					if( !r ){
						throw "未知错误";
					}
					
					if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
						throw r.error;
				        return false;
					}
					
				}
				catch(err) {
			        layer.msg(err, {icon: 5, time: 2000});
			        config.error(err);
			        return false;
			    }
				
				if( (function(){try{ return r.data.s.data;}catch(e){return false;}}()) ){
					session = r.data.s.data;
				}
				if( (function(){try{ return r.data.v.data;}catch(e){return false;}}()) ){
					verify_code_state = r.data.v.data;
				}
		}});
		//JSON
		
		if( session ){
			layer.msg('亲，您已经登录了', {icon: 1, time: 2000});
			setTimeout(function(){
				//返回用户中心
				var _http = http();
				if(!_http.anchor.path){
					_http.anchor.path = new Array();
				}
				_http.anchor.path[0] = 'home';
				_http.anchor.path[1] = 'home';
				http(_http).request();
			}, 2000);
			return false;
		}
		
		
		var tpl = WangAho().template("page/user/log_in.html", "#content");
		template(tpl, function(fn){
			WangAho().view(fn({s:123456}));
		});
		//重置页面布局
		this.resize();
		this.image_verify_state();
		this.submit();
		
	},
	
	
	resize : function(){
		$(window).unbind("resize").resize(function(){
			var h = $(".content_log_in").height()+20;
			if($(window).height() >= h ){
				$(".content_log_in").css("top", (($(window).height() - h)/4) + "px" );
			}
		});
		$(window).trigger("resize");
	},
	
	
	//判断图片验证码状态
	image_verify_state : function(){
		var this_id = this.id;
		$('input[name="phone"]').unbind("input propertychange").bind("input propertychange", function(event){
			var phone = $(this).val();
			
			if( phone ){
				var reg = /^[0-9]{11}$/;// /^[1][3,4,5,7,8][0-9]{9}$/
				if ( !reg.test(phone) ) {
                	return false;
            	}
			}else{
				return false;
			}
			
			//判断是否已经登录
			eonfox().submit({
				request : JSON.stringify({v:["USERLOGINIMAGEVERIFYCODESTATE", [{phone:phone}]]}),
				callback:function(r){
					try {
						if( !r ){
							throw "未知错误";
						}
						
						if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
							throw r.error;
					        return false;
						}
						
					}
					catch(err) {
				        layer.msg(err, {icon: 5, time: 2000});
				        config.error(err);
				        return false;
				    }
				
					var verify_state = 0;
					if( (function(){try{ return r.data.v.data;}catch(e){return false;}}()) ){
						verify_state = r.data.v.data;
					}
					
					//显示图片验证码
					WangAho().source[this_id].image_verify(verify_state);
			}});
			//JSON
		});
		$('input[name="phone"]').trigger("input propertychange");
	},
	
	
	
	/**
	 * 显示图片验证码
	 */
	image_verify : function(state, refresh){
		var this_id = this.id;
		if(state){
			//如果已经显示，并且没有强制刷新，则不再刷新。
			if( $(".image-verify-code-div").is(':visible') && !refresh ){
				return false;
			}
			$(".image-verify-code-div").show();
			WangAho().source[this_id].resize();
		}else{
			$(".image-verify-code-div").hide();
			return false;
		}
		
		var image_verify_key = this.image_verify_key;//验证码的键
		//验证码
		$(".image-verify-code-img").unbind("click").click(function(){
			var src = eonfox().api_server_url + "?data="+JSON.stringify([
				["SESSIONIMAGEVERIFYCODESHOW", [{image_verify_key:image_verify_key, length:5, width: 350, height:44}] ]
				])+"&token="+eonfox().left_token()+"&time="+ Math.random();
			
			$(this).attr("src", src);
		});
		$(".image-verify-code-img").trigger("click");
	},
	
	
	/**
	 * 提交数据
	 */
	submit : function(){
		
		var image_verify_key = this.image_verify_key;//验证码的键
		var this_id = this.id;
		$('[name="log_in_submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			
			var form_input = {};
			form_input.phone = $.trim($('[name="phone"]').val());
			form_input.password = $.trim($('[name="password"]').val());
			form_input.image_verify_code = $.trim($('[name="image_verify_code"]').val());
			form_input.image_verify_key = image_verify_key;
			
			try {
				if(form_input.phone == '') throw "手机号不能为空";
				if(form_input.password == '') throw "密码不能为空";
				if( $(".image-verify-code-div").is(':visible') ){
					if(form_input.image_verify_code == '') throw "请输入验证码";
				}
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			eonfox().submit({
				request : JSON.stringify({
					s:["USERLOGIN", [form_input]],
					v:["USERLOGINIMAGEVERIFYCODESTATE", [{phone:form_input.phone}]]
					}),
				callback : function(r){
					
					var verify_state = 0;
					if( (function(){try{ return r.data.v.data;}catch(e){return false;}}()) ){
						verify_state = r.data.v.data;
					}
					
					
					try {	
						if( !r ){
							throw "未知错误";
						}
						
						if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
							throw r.error;
					        return false;
						}
						
						//console.log(r);
						if( (function(){try{ return r.data.s.errno;}catch(e){return false;}}()) ){
							throw r.data.s.error;
						}
					} catch(err) {
				        layer.msg(err, {icon: 5, time: 2000});
				        $btn.removeClass('disabled');
				        //刷新验证码
				        WangAho().source[this_id].image_verify(verify_state, true);
				        return false;
				    }
					
					if( (function(){try{ return r.data.s.data;}catch(e){return false;}}()) ){
						//登录成功
						layer.msg("登录成功!", {icon: 1, time: 2000});
						setTimeout(function(){
							//返回用户中心
							var _http = http();
							if(!_http.anchor.path){
								_http.anchor.path = new Array();
							}
							_http.anchor.path[0] = 'home';
							_http.anchor.path[1] = 'home';
							http(_http).request();
						}, 2000);
						return false;
					}
					
			}});	
			
			
			
		});
		
		
		//按回车键时提交
		$(document).unbind("keyup").on('keyup', function(e){
			if(e.keyCode === 13){
		        $('[name="log_in_submit"]').first().trigger("click");
			}
		});
		
	},
	
	
	
	
	
	
	
	
});
