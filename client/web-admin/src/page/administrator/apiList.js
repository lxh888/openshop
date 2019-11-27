av({
	
	id: 'page-administrator-apiList',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/administrator/apiList.html"},
	'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		var _this = this;
		var config = { search:{} };
		
		//搜索
		this.data.routerAnchorQuery('search', function(data){
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});
		
		//排序
		this.data.routerAnchorQuery('sort', function(data){
			config.sort = [data];
		});
		
		//分页
		this.data.routerAnchorQuery('page', function(data){
			config.page = data;
		});
		
		//状态
		this.data.routerAnchorQuery('state', function(data){
			_this.data.state = data;
			config.search.state = data;//状态
		}, function(){
			_this.data.state = undefined;
		});
		this.data.routerAnchorQuery('is_administrator', function(data){
			_this.data.is_administrator = data;
			config.search.administrator = data;//状态
		}, function(){
			_this.data.is_administrator = undefined;
		});
		this.data.routerAnchorQuery('is_admin', function(data){
			_this.data.is_admin = data;
			config.search.admin = data;//状态
		}, function(){
			_this.data.is_admin = undefined;
		});
		this.data.routerAnchorQuery('is_version', function(data){
			_this.data.is_version = data;
			config.search.is_version = data;//状态
		}, function(){
			_this.data.is_version = undefined;
		});
		
		this.data.request.moduleOption = ['ADMINISTRATORMODULEOPTION', [{sort:["sort_asc","update_time_asc"]}]];
		this.data.request.list = ['ADMINISTRATORADMINAPILIST', [config]];
		if( config.search.module_id ){
			this.data.request.moduleData = ["ADMINISTRATORMODULEGET", [{module_id:config.search.module_id}]];
		}else{
			if(this.data.request.moduleData) delete this.data.request.moduleData;
			this.data.moduleData = null;
		}
		
		//console.log("开始加载：", this);
	},
	event: {
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		
	},
	data:{
		request: {},
		is_administrator: undefined,
		is_admin: undefined,
		is_version: undefined,
		state: undefined,
		moduleData: undefined,
		
		search:[
			'api_id', 
			'like_api_id', 
			'api_name',
			'module_id',
			],
		//数据请求成功的回调	
		callback: function(){
			av('module-search').data.showData.moduleOption = this.moduleOption;
		},
			
			
		list: {
			data : [],
		},
		
		//查看详细
		evnetShowDetails:function(ele, e, api_id, api_name){
			var title = api_name;
			var id = api_id;
			var href = av.router(av.router().href,'index.html#/administrator/api_details/?id='+id).href;
			layer.open({
		      type: 2,
		      title: title+"“"+id+"”",
		      shadeClose: true,
		      shade: false,
		      maxmin: true, //开启最大化最小化按钮
		      area: ['893px', '600px'],
		      content: href
		    });
		},
		
		
		//删除
		eventRemove: function(ele){
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionRemoveIds(ids, function(){
				
				var request_array = [];
				for(var i in ids){
					request_array.push(["ADMINISTRATORADMINAPIREMOVE", [{api_id:ids[i]}]]);
				}
				
				//提交数据
				_this.submit({
					
					method:"list",
					request:request_array,
					success:function(bool){
						if(bool){
							//刷新页面
							av().compiler("reload").render().run();
						}
					}
					
				});
				
				
			});
		},
		
	}
	
	
	
	
	
	
	
	
});
