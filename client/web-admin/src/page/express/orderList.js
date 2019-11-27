av({
	
	id: 'page-express-orderList',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/express/orderList.html"},
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
		
		//订单状态
		this.data.routerAnchorQuery('state', function(data){
			_this.data.state = data;
			config.search.state = data;//状态
		}, function(){
			_this.data.state = undefined;
		});
		
		//支付状态
		this.data.routerAnchorQuery('pay_state', function(data){
			_this.data.pay_state = data;
			config.search.pay_state = data;//状态
		},function(){
			_this.data.pay_state = undefined;
		});
		
		//物流状态
		this.data.routerAnchorQuery('shipping_state', function(data){
			_this.data.shipping_state = data;
			config.search.shipping_state = data;//状态
		},function(){
			_this.data.shipping_state = undefined;
		});
		
		this.data.request.list = ['EXPRESSADMINORDERLIST', [config]];
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
		search:[
			'user_id', 
			'user_nickname', 
			'user_phone', 
			'rider_user_id',
			'rider_user_nickname',
			'rider_user_phone', 
			'express_rider_phone',
			'express_order_id',
			'express_order_shipping_no'
			],
			
		state: undefined,
		pay_state: undefined,
		shipping_state: undefined,
		
		list: {
			data : [],
		},
		eventState: function(ele){
			var tempRouter = av.router();
			if( !tempRouter.anchor.query ){
				tempRouter.anchor.query = {};
			}
			
			var data = ele.getAttribute('data');
			if( !data ){
				delete tempRouter.anchor.query.state;
			}else{
				tempRouter.anchor.query.state = data;
			}
			
			av.router(tempRouter).request();
		},
		//删除
		eventRemove: function(ele){
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.removeIds(ids, function(){
				
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
		}
		
		
		
	}
	
	
	
});