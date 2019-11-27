av({
	id: 'page-userLogin',
	selector:'view',
    'export' : {template : "src/page/userLogin.html"},
    'import' : function(e){
        this.template(e.template);
    },
	event:{
		
		//渲染完成后
		renderEnd: function(){
			var _this = this;
			//按回车键时提交
			$(document).unbind("keyup").on('keyup', function(e){
				if(e.keyCode === 13){
					if( $("textarea").is(":focus") ){  
				        return false;
				    }
			       	_this.data.eventLoginSubmit();
				}
			});
        },
        
	},
	data:{
		request: {
			
		},
		
		phone:'',
		password:'',
		imageVerifyKey:'userLogin',
		imageVerifyCode:'',
		imageVerifyCodeState: false,
		imageVerifyCodeSrc:'',
		
		inputPhone:function(ele){
			this.phone = ele.value;
			this.checkImageVerifyState();
		},
		inputPassword:function(ele){
			this.password = ele.value;
		},
		inputImageVerifyCode:function(ele){
			this.imageVerifyCode = ele.value;
		},
		
		//刷新验证码
		eventImageVerifyCodeRefresh:function(){
			var _this = this;
			var request = new requestAPI();
			request.leftToken(function(leftToken){
				_this.imageVerifyCodeSrc = request.apiServerUrl() + "?data="+JSON.stringify([
				["SESSIONIMAGEVERIFYCODESHOW", [{image_verify_key:_this.imageVerifyKey, length:5, width: 350, height:44}] ]
				])+"&token="+leftToken+"&time="+ Math.random();
			});
		},
		
		//记录上一次查询手机号
		checkPhone:'',
		//检查验证码图片状态
		checkImageVerifyState:function(){
			var _this = this;
			if( _this.phone && _this.checkPhone != _this.phone ){
				var reg = /^[0-9]{11}$/;// /^[1][3,4,5,7,8][0-9]{9}$/
				if ( !reg.test(_this.phone) ) return false;
			}else{
				return false;
			}
			
			_this.checkPhone = _this.phone;
			//判断是否已经登录
			new requestAPI().submit({
				request : {
					v:["USERLOGINIMAGEVERIFYCODESTATE", [{phone:_this.phone}]]
					},
				callback:function(r){
					try {
						if( !r ){
							throw "未知错误";
						}
						if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
							throw r.error;
						}
					}
					catch(err) {
				        layer.msg(err, {icon: 5, time: 2000});
				        return false;
				    }
					
					//等于真，那么就需要验证图
					if( (function(){try{ return r.data.v.data;}catch(e){return false;}}()) ){
						_this.imageVerifyCodeState = true;
						_this.eventImageVerifyCodeRefresh();//刷新验证码
					}
				}
			});
		},
		
		loginSubmitLock:false,
		eventLoginSubmit:function(ele){
			var _this = this;
			if( _this.loginSubmitLock ){
				return false;
			}else{
				_this.loginSubmitLock = true;
			}
			
			var form_input = {};
			form_input.phone = $.trim($('[name="phone"]').val());
			form_input.password = $.trim($('[name="password"]').val());
			form_input.image_verify_code = $.trim($('[name="imageVerifyCode"]').val());
			form_input.image_verify_key = _this.imageVerifyKey;
			
			try {
				if(form_input.phone == '') throw "手机号不能为空";
				if(form_input.password == '') throw "密码不能为空";
				if( _this.imageVerifyCodeState ){
					if(form_input.image_verify_code == '') throw "请输入验证码";
				}
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.loginSubmitLock = false;
		    }
			
			//提交数据
			new requestAPI().submit({
				request : {
					s:["USERLOGIN", [form_input]],
					v:["USERLOGINIMAGEVERIFYCODESTATE", [{phone:form_input.phone}]]
					},
				callback : function(r){
					
					if( (function(){try{ return r.data.v.data;}catch(e){return false;}}()) ){
						_this.imageVerifyCodeState = true;
					}
					
					try {	
						if( !r ){
							throw "未知错误";
						}
						
						if( (function(){try{ return r.errno;}catch(e){return false;}}()) ){
							throw r.error;
						}
						
						if( (function(){try{ return r.data.s.errno;}catch(e){return false;}}()) ){
							throw r.data.s.error;
						}
					} catch(err) {
				        layer.msg(err, {icon: 5, time: 2000});
				        _this.loginSubmitLock = false;
				        _this.eventImageVerifyCodeRefresh();//刷新验证码
				        return false;
				    }
					
					if( (function(){try{ return r.data.s.data;}catch(e){return false;}}()) ){
						//登录成功
						layer.msg("登录成功!", {icon: 1, time: 2000});
						setTimeout(function(){
							//返回用户中心
							av.router(av.router().url).request();
						}, 2000);
						return false;
					}
					
			}});	
			
		}
	}
	
	
});











