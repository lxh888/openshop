av({
	
	id: 'page-user-userAdd',
	include : ["src/common/content.js", 'src/module/citypicker/citypicker.js'],
    extend : ["common-content"],
	'export' : {template : "src/page/user/userAdd.html"},
	'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		this.data.request.adminOption = ["ADMINOPTION",[{sort:["sort_asc","update_time_asc"]}]];
	},
	event: {
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		loadEnd : function(){
			this.render("refresh");
			//删除子级
			$('[module="citypicker"]').children().remove();
			this.data.initSelectedScope();
			av('module-citypicker').data.defaultLabel(['四川省','绵阳市','游仙区']);
			av('module-citypicker').render("refresh");//渲染 城市选择器
		},
		//当渲染的时候
		renderEnd: function(){
			//调用 Chosen
			$('select[name="admin_id"], select[name="user_sex"]').chosen("destroy");
			$('select[name="admin_id"], select[name="user_sex"]').chosen({
				//width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains:true, 
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
		},
		
		
	},
	
	data: {
		request: {},
		adminOption: null,
		selectedScope : 1,
		
		//选中的范围
		eventSelectedScope: function(ele, e){
			this.selectedScope = $(ele).val();
			this.initSelectedScope();
			
			console.log( 'eventSelectedScope', $(ele).val() );
		},
		
		/**
		 * 初始化
		 */
		initSelectedScope: function(){
			
			if( this.selectedScope == 1 ){
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = false;
				av('module-citypicker').data.areaShow = false;
			}
			
			if( this.selectedScope == 2 ){
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = true;
				av('module-citypicker').data.areaShow = false;
			}
			
			if( this.selectedScope == 3 ){
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = true;
				av('module-citypicker').data.areaShow = true;
			}
			//console.log('44444444444', av('module-citypicker').data.pickerValue );
			//console.log( 'initSelectedScope', this.selectedScope );
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
			form_input.user_nickname  	= $.trim($('[name="user_nickname"]').val());
			form_input.user_password 	= $.trim($('[name="user_password"]').val());
			form_input.user_phone 	= $.trim($('[name="user_phone"]').val());
			form_input.user_sex 	= $.trim($('[name="user_sex"]').val());
			
			form_input.user_parent  = $.trim($('[name="user_parent"]').val());
			
			form_input.work_phone 	= $.trim($('[name="work_phone"]').val());
			form_input.work_address = $.trim($('[name="work_address"]').val());
			form_input.admin_id 	= $.trim($('[name="admin_id"]').val());
			form_input.user_money 	= $.trim($('[name="user_money"]').val());
			form_input.user_credit 	= $.trim($('[name="user_credit"]').val());
			
			//e麦，添加用户时加上所属省市区
			if(_this.applicationCheckEmshop()){
				var user_register_scope 	= $.trim($('[name="agent_region_scope"]').val());
				if( user_register_scope == 1 ){
					form_input.user_register_province 	= av('module-citypicker').data.provinceLabel;
				}else
				if( user_register_scope == 2 ){
					form_input.user_register_province 	= av('module-citypicker').data.provinceLabel;
					form_input.user_register_city 	= av('module-citypicker').data.cityLabel;
				}else
				if( user_register_scope == 3 ){
					form_input.user_register_province 	= av('module-citypicker').data.provinceLabel;
					form_input.user_register_city 	= av('module-citypicker').data.cityLabel;
					form_input.user_register_area 	= av('module-citypicker').data.areaLabel;
				}
			}

			try {
				if(form_input.user_phone == '') throw "请输入用户的登录手机号";
				
				if( form_input.user_money != '' ){
					var scale = 100;//单位
					var precision = 2;//精度
					var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
					
					if( money_format.test(form_input.user_money) ){
						form_input.user_money = ((parseFloat(form_input.user_money).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "用户钱包金额输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "用户钱包金额输入有误，格式必须是大于0的整数";
						}
					}
					
				}else{
					delete form_input.user_money;
				}
				
				if( form_input.user_credit != '' ){
					var creditConfig = _this.applicationCreditConfig();
					if( !creditConfig ){
						layer.msg("积分配置异常", {icon: 5, time: 2000});
						return false;
					}
					
					var scale = creditConfig.scale;//单位
					var precision = creditConfig.precision;//精度
					var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
					if( money_format.test(form_input.user_credit) ){
						form_input.user_credit = ((parseFloat(form_input.user_credit).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "用户积分数量输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "用户积分数量输入有误，格式必须是大于0的整数";
						}
					}
					
				}else{
					delete form_input.user_credit;
				}
				
				
			}
			catch( err ){
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["USERADMINADD", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					//刷新页面
					av().compiler("reload").render("refresh").run();
				}
			});
			
		}
		
		
		
		
	}
	
	
	
	
	
});