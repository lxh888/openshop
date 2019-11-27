av({
	
	id: 'page-express-riderAdd',
	include : ["src/common/content.js", 'src/module/citypicker/citypicker.js'],
    extend : ["common-content"],
	'export' : {template : "src/page/express/riderAdd.html"},
    'import' : function(e){
        this.template(e.template);
    },
    
	main: function(){
		//this.data.request.list = ['EXPRESSADMINRIDERLIST'];
	},
	event: {
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/express-riderList/').request();
		},
		
		//当渲染的时候
		renderEnd: function(){
			//调用 Chosen
			$('select[name="express_rider_state"]').chosen("destroy");
			$('select[name="express_rider_state"]').chosen({
				//width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains:true, 
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
		},
		
		loadEnd : function(){
			this.render("refresh");
			av('module-citypicker').data.defaultLabel(['四川省','绵阳市','游仙区']);
			av('module-citypicker').render("refresh");//渲染 城市选择器
		}
		
	},
	data:{
		request: {},
		state: undefined,
		list: {
			data : [],
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
			form_input.user  				= $.trim($('[name="user"]').val());
			form_input.express_rider_name  	= $.trim($('[name="express_rider_name"]').val());
			form_input.express_rider_phone 	= $.trim($('[name="express_rider_phone"]').val());
			form_input.express_rider_info 	= $.trim($('[name="express_rider_info"]').val());
			form_input.express_rider_on_off = $('[name="express_rider_on_off"]').is(':checked')? 1 : 0;
			form_input.express_rider_state 	= $.trim($('[name="express_rider_state"]').val());
			
			form_input.express_rider_province 	= av('module-citypicker').data.provinceLabel;
			form_input.express_rider_city 	= av('module-citypicker').data.cityLabel;
			form_input.express_rider_district 	= av('module-citypicker').data.areaLabel;
			
			try {
				if(form_input.user == '') throw "请输入用户ID或登录手机号";
				if(form_input.express_rider_name == '') throw "骑手名称不能为空";
				if(form_input.express_rider_phone == '') throw "手机号不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["EXPRESSADMINRIDERADD", [form_input]],
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