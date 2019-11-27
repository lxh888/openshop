av({
	
	id: 'page-administrator-apiEdit::apiVersionApplication',
	selector: false,
	'export' : {template : "src/page/administrator/apiEdit/apiVersionApplication.html"},
    'import' : function(e){
        this.template(e.template);
    },
	data:{
		api_id:'',
		api_name:'',
		api_version:null,
		applicationOption:null,
		//初始化
		init:function(){
			this.checkboxChecked = {};
			this.submitLock = false;
		},
		eventCheckbox:function(ele, e, application_id){
			if( $(ele).is(':checked') ){
				$(ele).parent().addClass('checkbox-checked');
				this.checkboxChecked[application_id] = true;
				//console.log('真，添加checkbox-checked');
			}else{
				$(ele).parent().removeClass('checkbox-checked');
				this.checkboxChecked[application_id] = false;
				//console.log('假，删除checkbox-checked');
			}
		},
		checkboxChecked: {},
		checkChecked:function(application_id){
			//先判断当下已经选择的
			if( typeof this.checkboxChecked[application_id] != 'undefined' ){
				if(this.checkboxChecked[application_id]){
					return true;
				}else{
					return false;
				}
			}
			
			if(this.api_version.application_list && typeof this.api_version.application_list == 'object'){
				for(var i in this.api_version.application_list){
					if(this.api_version.application_list[i] == application_id){
						return true;
					}
				}
			}
			
			return false;
		},
		submitLock:false,
		eventSubmit:function(){
			console.log('apiVersionApplication eventSubmit');
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			var formInput = {};
			formInput.primary_key = _this.api_id;
			formInput.api_version_id = _this.api_version.api_version_id;
			formInput.application_id = [];
			$('[name="apiVersionApplication-checkbox"]:checked').each(function(){
				formInput.application_id.push($(this).val());
			});
			
			try {
				if(formInput.primary_key == '') throw "接口ID异常";
				if(formInput.api_version_id == '') throw "接口版本ID异常";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			//提交数据
			av("common-content").data.submit({
				method:"submit",
				request:["ADMINISTRATORADMINAPIVERSIONAPPLICATION", [formInput]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
				}
			});
			
			
		}
		
		
	}
	
	
	
	
	
});