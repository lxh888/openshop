av({
	
	id:'module-setting',
	selector: false,
    'export' : {
    	template : "src/module/setting/setting.html",
    },
    'import' : function(e){
    	this.template(e.template);
    },
	data: {
		
		//模板
		template: '',
		
		//管理数据
		admin:{
			admin_user_json:{}
		},
		submitDisabled: false,
		//提交数据
		submit: function(){
			console.log('setting submit');
			var _this = this;
			if( _this.submitDisabled ){
				return false;
			}else{
				_this.submitDisabled = true;
			}
			
			var form_input = {};
			form_input.page_size = $.trim($('[name="page_size"]').val());
			
			try {
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        _this.submitDisabled = false;
		        return false;
		    }
			
			//提交数据
			av("common-content").data.submit({
				method:"submit",
				request:["ADMINUSERSELFCONFIG", [form_input]],
				error:function(){
					_this.submitDisabled = false;
				},
				success:function(data){
					_this.submitDisabled = false;
				}
			});
			
			
			
			
		},
		
		
	}
	
	
});