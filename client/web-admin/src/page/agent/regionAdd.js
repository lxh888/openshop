av({
	
	id: 'page-agent-regionAdd',
	include : ["src/common/content.js", 'src/module/citypicker/citypicker.js'],
    extend : ["common-content"],
	'export' : {template : "src/page/agent/regionAdd.html"},
    'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		//this.data.request.list = ['EXPRESSADMINRIDERLIST'];
	},
	event: {
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/agent-regionList/').request();
		},
		
		loadEnd : function(){
			this.render("refresh");
			//删除子级
			$('[module="citypicker"]').children().remove();
			this.data.initSelectedScope();
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
		
		
		submitLock:false,
		eventSubmit: function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			var form_input = {};
			form_input.agent_region_scope 	= $.trim($('[name="agent_region_scope"]').val());
			form_input.agent_region_details = $.trim($('[name="agent_region_details"]').val());
			
			if( form_input.agent_region_scope == 1 ){
				form_input.agent_region_province 	= av('module-citypicker').data.provinceLabel;
			}else
			if( form_input.agent_region_scope == 2 ){
				form_input.agent_region_province 	= av('module-citypicker').data.provinceLabel;
				form_input.agent_region_city 	= av('module-citypicker').data.cityLabel;
			}else
			if( form_input.agent_region_scope == 3 ){
				form_input.agent_region_province 	= av('module-citypicker').data.provinceLabel;
				form_input.agent_region_city 	= av('module-citypicker').data.cityLabel;
				form_input.agent_region_district 	= av('module-citypicker').data.areaLabel;
			}
			
			form_input.agent_region_state = $('[name="agent_region_state"]').is(':checked')? 0 : 1;
			form_input.agent_region_sort = $.trim($('[name="agent_region_sort"]').val());
			form_input.agent_region_info = $.trim($('[name="agent_region_info"]').val());
			
			try {
				if( form_input.agent_region_sort == '' ){
					delete form_input.agent_region_sort;
				}
				
				if( form_input.agent_region_province == '' && form_input.agent_region_city && form_input.agent_region_district) 
				throw "省市区选择不合法";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["AGENTADMINREGIONADD", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
			
		},
		
		
		
	}
	
	
	
});