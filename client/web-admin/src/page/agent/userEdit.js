av({
	
	id: 'page-agent-userEdit',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/agent/userEdit.html"},
    'import' : function(e){
        this.template(e.template);
    },
	
	main: function(){
		this.data.agent_user_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !this.data.agent_user_id ){
			return av.router(av.router().url, '#/agent-userList/').request();
		}
		this.data.request.data = ['AGENTADMINUSERGET', [{agent_user_id:this.data.agent_user_id}]];
		this.data.request.agentRegionOption = ['AGENTADMINREGIONOPTION'];
	},
	
	event: {
    	error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/agent-userList/').request();
		},
    	
    	renderEnd: function(){
			//调用 Chosen
			$('select[name="agent_region_id"], select[name="agent_region_state"]').chosen("destroy");
			$('select[name="agent_region_id"], select[name="agent_region_state"]').chosen({
				//width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains:true, 
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			
			laydate.render({
				elem: '[name="agent_user_interview_time"]'
				,type: 'datetime'
				,theme: '#337ab7'
				
			});
		},
    	
    	loadEnd: function(){
    		if(this.data.data.agent_user_json && 
    			this.data.data.agent_user_json.user_credit_award && 
    			this.data.data.agent_user_json.user_credit_award.state){
    			this.data.userCreditAwardState = true;
    		}else{
    			this.data.userCreditAwardState = false;
    		}
    		
    	}
    },
	
	data: {
    	request: {},
		state: undefined,
		data: null,
		agent_user_id: '',
		
		agentRegionOption: null,
		agentRegionOptionShow: function(arV){
			if(arV.agent_region_scope == 1){
				return '[省级代理] '+arV.agent_region_province;
			}else
			if(arV.agent_region_scope == 2){
				return '[市级代理] '+arV.agent_region_province+'/'+arV.agent_region_city;
			}else
			if(arV.agent_region_scope == 3){
				return '[区级代理] '+arV.agent_region_province+'/'+arV.agent_region_city+'/'+arV.agent_region_district;
			}
		},
		
		userCreditAwardState: false,
		eventUserCreditAward: function(ele, e){
			console.log('eventUserCreditAward');
			this.userCreditAwardState = $(ele).is(':checked')? true : false;
		},
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
			form_input.agent_user_id  				= _this.agent_user_id;
			form_input.agent_region_id  			= $.trim($('[name="agent_region_id"]').val());
			form_input.agent_user_interview_phone	= $.trim($('[name="agent_user_interview_phone"]').val());
			form_input.agent_user_interview_address = $.trim($('[name="agent_user_interview_address"]').val());
			form_input.agent_user_interview_time 	= $.trim($('[name="agent_user_interview_time"]').val());
			
			form_input.agent_user_json = {
				user_credit_award: {
					state:0,
					ratio:0,
					algorithm:'floor',
				}
			};
			form_input.agent_user_award_state = $('[name="agent_user_json.user_credit_award.state"]').is(':checked')? 1 : 0;
			if( form_input.agent_user_award_state ){
				form_input.agent_user_json.user_credit_award.ratio 		= $.trim($('[name="agent_user_json.user_credit_award.ratio"]').val());
				form_input.agent_user_json.user_credit_award.algorithm 	= $.trim($('[name="agent_user_json.user_credit_award.algorithm"]').val());
			}
			
			try {
				if(form_input.agent_user_id == '') throw "代理用户ID异常";
				if( form_input.agent_region_id == '') delete form_input.agent_region_id;
				if( form_input.agent_user_interview_phone == '') delete form_input.agent_user_interview_phone;
				if( form_input.agent_user_interview_address == '') delete form_input.agent_user_interview_address;
				if( form_input.agent_user_interview_time == ''){
					delete form_input.agent_user_interview_time;
				}else{
					//验证面试时间
					var timeFormat = /^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/;
					if( !timeFormat.test(form_input.agent_user_interview_time) ){
						throw "面试时间不合法";	
					}
				}
				if( form_input.agent_user_award_state ){
					var ratioFormat = /^(0\.[0-9]{1,}|0|1)$/;
					if( !ratioFormat.test(form_input.agent_user_json.user_credit_award.ratio) ){
						throw "积分赠送比例的格式输入有误，格式必须是小数";
					}
					
					var algorithmFormat = /^(round|ceil|floor)$/;
					if( !algorithmFormat.test(form_input.agent_user_json.user_credit_award.algorithm) ){
						throw "积分赠送比例的格式输入有误，格式必须是小数";
					}
					
				}
				
				
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			console.log(form_input);
			//提交数据
			this.submit({
				method:"submit",
				request:["AGENTADMINUSEREDIT", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
			
		}
    	
    	
    }
    
    
	
	
});
