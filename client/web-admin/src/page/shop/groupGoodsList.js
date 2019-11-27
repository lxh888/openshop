av({
	
	id: 'page-shop-groupGoodsList',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/shop/groupGoodsList.html"},
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
		
		//是否成团 
		this.data.routerAnchorQuery('is_success', function(data){
			_this.data.is_success = data;
			config.search.is_success = data;//状态
		}, function(){
			_this.data.is_success = undefined;
		});
		
		//是否结束 
		this.data.routerAnchorQuery('is_end', function(data){
			_this.data.is_end = data;
			config.search.is_end = data;//状态
		}, function(){
			_this.data.is_end = undefined;
		});
		
		this.data.request.list = ['SHOPADMINGOODSGROUPLIST', [config]];
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
		is_success: undefined,
		is_end: undefined,
		search:[
			'user_id', 
			'user_nickname', 
			'user_phone',
			'shop_goods_id',
			'shop_goods_name',
			'shop_goods_group_id'
			],
		list: {
			data : [],
		},
		//删除
		eventRemove: function(ele){
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionRemoveIds(ids, function(){
				
				var request_array = [];
				for(var i in ids){
					request_array.push(["SHOPADMINGOODSGROUPREMOVE", [{shop_goods_group_id:ids[i]}]]);
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
