av({
	
	id: 'page-express-riderList',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/express/riderList.html"},
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
		
		
		this.data.request.list = ['EXPRESSADMINRIDERLIST', [config]];
		
	},
	
	event: {
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		
	},
	data:{
		request: {},
		search:[
			'user_id', 
			'user_nickname', 
			'user_phone', 
			'express_rider_name', 
			'express_rider_on_off', 
			'express_rider_phone',
			'express_rider_province',
			'express_rider_city',
			'express_rider_district'
			],
			
		state: undefined,
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
					request_array.push(["EXPRESSADMINRIDERREMOVE", [{user_id:ids[i]}]]);
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
		
		
		/**
		 * 更新状态
		 * 
		 * @param {Object} bool
		 */
		eventState : function(ele, e, bool){
			var ids = this.checkboxData('data-id');
			var _this = this;
			var state = bool? 1 : 0;
			
			this.actionStateIds(ids, function(){
				
				var request_array = [];
				for(var i in ids){
					request_array.push(["EXPRESSADMINRIDEREDIT", [{user_id:ids[i], express_rider_state:state}]]);
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
			
		}
		
	}
	
	
	
});