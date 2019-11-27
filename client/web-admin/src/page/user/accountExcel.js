av({
	
	id: 'page-user-accountExcel',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/user/accountExcel.html"},
	'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		this.data.request.accountExcelOption = ["USERADMINACCOUNTEXCELOPTION"];
	},
	event: {
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		
		renderEnd: function(){
			//调用 Chosen
			$('select[name="occurrence_is_zero"],select[name="balance_is_zero"]').chosen("destroy");
			$('select[name="occurrence_is_zero"],select[name="balance_is_zero"]').chosen({
				width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains: true,
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			laydate.render({
				elem: '[name="time"]'
				,type: 'datetime'
				,theme: '#337ab7'
				,range: '~'
			});
		}
		
	},
	data: {
		request: {},
		
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		
		submitLock:false,
		eventSubmit: function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			var form_input = {};
			var accountExcelProject  	= $.trim($('[name="accountExcelProject"]:checked').val());
			form_input.phone 	= $.trim($('[name="user_phone"]').val());
			
			form_input.occurrence_is_zero 	= $.trim($('[name="occurrence_is_zero"]').val());
			form_input.balance_is_zero 	= $.trim($('[name="balance_is_zero"]').val());
			
			try {
				if(form_input.phone == ''){
					delete form_input.phone;
				}
				
				var times 	= $.trim($('[name="time"]').val());
				if( times != "" ){
					
					var times_split = times.split('~', 2);
					if( times_split[0] ) times_split[0] = $.trim(times_split[0]);
					if( times_split[1] ) times_split[1] = $.trim(times_split[1]);
					
					var format = /^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/;
					if( times_split[0] && format.test(times_split[0]) ){
						form_input.start_time = times_split[0];
					}else{
						throw "开始拼团时间不合法";	
					}
					
					if( times_split[1] && format.test(times_split[1]) ){
						form_input.end_time = times_split[1];
					}else{
						throw "结束拼团时间不合法";	
					}
					
				}
				
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			var requestAPIObject = new requestAPI();
			requestAPIObject.leftToken(function(leftToken){
				_this.submitLock = false;
				
				var router = av.router(requestAPIObject.apiServerUrl(), {
					query:{
						data : [[accountExcelProject, [form_input]]],
						token: leftToken
					}
				});
				router.request()
				console.log( form_input , router);
			});
			
			
		}
		
		
		
	}
	
	
	
	
});