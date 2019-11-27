av({
	id:'project-zrhzfw-orderList',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include : ["src/common/content.js"],//获取js文件
	extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export' : {
	template: "src/project/zrhzfw/orderList.html",},
	'import' : function(e){
			this.template(e.template);//绑定模版
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
	
	//交易状态
	this.data.routerAnchorQuery('transaction_state', function(data){
		_this.data.transaction_state = data;
		config.search.transaction_state = data;//状态
	}, function(){
		_this.data.transaction_state = undefined;
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
	}, function(){
		_this.data.pay_state = undefined;
	});
	
	this.data.request.list= ['SHOPADMINORDERLIST', [config]];
	},
	event: {
			error : function(error){
		console.log('error 跳转', error);
		return av.router(av.router().url, '#/').request();
	},

	},
	//数据对象
	data:{
		request: {},
	search:[
		'shop_order_id', 
		'user_id', 
		'user_nickname', 
		'user_phone'
		],
		list:{
		data : [],
	},
	//回收
	eventTrash: function(ele) {
		var ids = this.checkboxData('data-id');
		var _this = this;
		if(ids.length < 1){
			layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
			return false;
		}
		//回收站
		layer.msg('你确定要将数据丢进回收站么？('+ids.length+'条数据)', {time: 0 //不自动关闭
			,btn: ['确定', '取消'],yes: function(index){
			layer.close(index);
			var request_array = [];
			for(var i in ids) {
				request_array.push(["SHOPADMINORDERTRASH", [{shop_order_id: ids[i]}]]);
			}
			//提交数据
			_this.submit({
				method: "list",
				request: request_array,
				success: function(bool) {
					if(bool) {
						//刷新页面
						av().run();
					}
				}
			});
		}
	});
	},
	
	}
});