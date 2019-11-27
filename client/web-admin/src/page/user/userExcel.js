av({
	
	id: 'page-user-userExcel',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/user/userExcel.html"},
	'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		//this.data.request.accountExcelOption = ["USERADMINACCOUNTEXCELOPTION"];
	},
	event: {
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		
		renderEnd: function(){
		}
	},
	data: {
		request: {},
		unit:'积分',
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		
		submitLock:false,
		eventChangeType:function(){
			if($.trim($('[name="type"]').val())=='money')
			this.unit="预付款";
			else if($.trim($('[name="type"]').val())=='credit')
			this.unit="积分";
		},
		eventSubmit: function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			//var USERADMINEXCEL='USERADMINEXCEL';
			var form_input = {};
			form_input.type = $.trim($('[name="type"]').val());
			form_input.min_value 	= $.trim($('[name="min_value"]').val());
			form_input.max_value 	= $.trim($('[name="max_value"]').val());
			//读取积分配置
			var creditsConfig=this.applicationCreditConfig();
			
			try {
				
				if( form_input.min_value == ''){
					delete form_input.min_value;
				}else{
					var dotMin = form_input.min_value.indexOf(".");
					var dotCntMin = form_input.min_value.substring(dotMin+1,form_input.min_value.length);
				}
				
				if( form_input.max_value == ''){
					delete form_input.max_value;
				}else{
					var dotMax = form_input.max_value.indexOf(".");
					var dotCntMax = form_input.max_value.substring(dotMax+1,form_input.max_value.length);
				}
				
				//积分
				if(form_input.type=='credit'){
					if((typeof dotMin != 'undefined' && dotMin>-1&&dotCntMin.length>creditsConfig.precision)||
					(typeof dotMax != 'undefined' && dotMax>-1&&dotCntMax.length>creditsConfig.precision))
					throw '积分最小值或最大值小数位不能超过'+creditsConfig.precision+'位';
				}
				//预付款
				if(form_input.type=='money'){
					if((typeof dotMin != 'undefined' && dotMin>-1&&dotCntMin.length>2)||
					(typeof dotMax != 'undefined' && dotMax>-1&&dotCntMax.length>2))
					throw '积分最小值或最大值小数位不能超过2位';
				}
				
				
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
				}
			//积分
			if(form_input.type=='credit'){
				if( typeof form_input.min_value != 'undefined'){
					form_input.min_value=(form_input.min_value*creditsConfig.scale).toFixed(0);
				}
				if( typeof form_input.max_value != 'undefined'){
					form_input.max_value=(form_input.max_value*creditsConfig.scale).toFixed(0);
				}
			}
			//预付款
			if(form_input.type=='money'){
				if( typeof form_input.min_value != 'undefined'){
					form_input.min_value=(form_input.min_value*100).toFixed(0);
				}
				if( typeof form_input.max_value != 'undefined'){
					form_input.max_value=(form_input.max_value*100).toFixed(0);
				}
			}
			console.log(form_input);
			var requestAPIObject = new requestAPI();
			requestAPIObject.leftToken(function(leftToken){
				_this.submitLock = false;
				
				var router = av.router(requestAPIObject.apiServerUrl(), {
					query:{
						data : [['USERADMINEXCEL', [form_input]]],
						token: leftToken
					}
				});
				router.open(500, 500)
				console.log( form_input , router);
			});
			
			
		}
		
		
		
	}
	
	
	
	
});