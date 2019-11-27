av({
	
	id: 'page-user-searchVerifyCode',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/user/searchVerifyCode.html"},
	'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		
		
	},
	event: {
		
		
		
		
	},
	data:{
		request: {},
		
		userPhoneId:'',//手机号
		userPhoneVerifyCode:null,//手机号的验证码数据
		
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		
		tagName:{
			'sign_up':'注册验证码',
			'reset_password':'重置密码验证码',
			'reset_pay_password':'重置支付密码验证码',
		},
		
		
		submitLock:false,
		eventSubmit: function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			//检查手机号是否合法
			var form_input = {};
			form_input.user_phone_id  	= $.trim($('[name="user_phone_id"]').val());
			
			try {
				if(form_input.user_phone_id == '') throw "请输入手机号";
				//验证面试时间
				var format = /^[0-9]{11}$/;
				if( !format.test(form_input.user_phone_id) ){
					throw "输入的手机号不合法";
				}
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["USERADMINPHONESEARCHVERIFYCODE", [form_input]],
				alert:false,
				error:function(){
					_this.submitLock = false;
				},
				success:function(r){
					_this.submitLock = false;
					_this.userPhoneId = form_input.user_phone_id;
					if(typeof r == 'object' && r.constructor == Array && r.length == 0){
						_this.userPhoneVerifyCode = null;
					}else{
						_this.userPhoneVerifyCode = r;
					}
				}
			});
			
		}
		
		
		
	}
	
	
	
	
	
});