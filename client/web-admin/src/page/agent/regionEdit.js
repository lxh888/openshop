av({
	
	id: 'page-agent-regionEdit',
	include : ["src/common/content.js", 'src/module/citypicker/citypicker.js'],
    extend : ["common-content"],
	'export' : {template : "src/page/agent/regionEdit.html"},
    'import' : function(e){
        this.template(e.template);
    },
    
    main: function(){
		this.data.agent_region_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !this.data.agent_region_id ){
			return av.router(av.router().url, '#/agent-regionList/').request();
		}
		this.data.request.data = ['AGENTADMINREGIONGET', [{agent_region_id:this.data.agent_region_id}]];
	},
    
    event: {
    	
    	
    	error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/agent-regionList/').request();
		},
    	
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
    		this.render("refresh");//重新渲染
    		
    		//删除子级
			$('[module="citypicker"]').children().remove();
    		//判断一下 selectedScope
    		this.data.selectedScope = this.data.data.agent_region_scope;
    		this.data.initSelectedScope();
    		av('module-citypicker').data.defaultLabel([
				this.data.data.agent_region_province,
				this.data.data.agent_region_city,
				this.data.data.agent_region_district
			]);
			av('module-citypicker').render("refresh");//渲染 城市选择器
    		
    		var _this = this;
			
    	}
    	
    	
    	
    },
    
    data: {
    	
    	request: {},
		state: undefined,
		data: null,
		
		agent_region_id: '',
		
		selectedScope : 1,
		
		
		getSelectedScope:function(){
			if(!this.data || this.data.agent_region_scope == 0){
				return '未知';
			}
			if(this.data.agent_region_scope == 1){
				return '省(直辖市)';
			}
			if(this.data.agent_region_scope == 2){
				return '市';
			}
			if(this.data.agent_region_scope == 3){
				return '区(县)';
			}
			
		},
		getSelected:function(){
			if(!this.data || this.data.agent_region_scope == 0){
				return '未知';
			}
			if(this.data.agent_region_scope == 1){
				return this.data.agent_region_province;
			}
			if(this.data.agent_region_scope == 2){
				return this.data.agent_region_province+'/'+ this.data.agent_region_city;
			}
			if(this.data.agent_region_scope == 3){
				return this.data.agent_region_province+'/'+ this.data.agent_region_city+'/'+this.data.agent_region_district;
			}
		},
		
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
			
			
			form_input.agent_region_id	= this.agent_region_id;
			/*form_input.agent_region_details 	= $.trim($('[name="agent_region_details"]').val());
			form_input.agent_region_scope 	= $.trim($('[name="agent_region_scope"]').val());
			
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
			*/
			if(_this.applicationCheckYouli()){
				form_input.agent_region_details = $.trim($('[name="agent_region_details"]').val());
			}
			form_input.agent_region_state = $('[name="agent_region_state"]').is(':checked')? 0 : 1;
			form_input.agent_region_sort = $.trim($('[name="agent_region_sort"]').val());
			form_input.agent_region_info = $.trim($('[name="agent_region_info"]').val());
			
			try {
				if( form_input.agent_region_sort == '' ){
					delete form_input.agent_region_sort;
				}
				/*
				if( form_input.agent_region_province == '' && form_input.agent_region_city && form_input.agent_region_district) 
				throw "省市区选择不合法";*/
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["AGENTADMINREGIONEDIT", [form_input]],
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