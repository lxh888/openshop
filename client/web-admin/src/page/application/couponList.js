av({
	
	id: 'page-application-couponList',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/application/couponList.html"},
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
		//请求优惠券列表接口
		this.data.request.list = ['APPLICATIONADMINCOUPONLIST', [config]];
		this.data.request.moduleOption = ["APPLICATIONADMINCOUPONMODULEOPTION"];
		this.data.request.typeOption = ["APPLICATIONADMINCOUPONTYPEOPTION"];
	},
	event:{
		
		error: function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		
	},
	data:{
		request: {},
		search:[
			'coupon_property', 
			'shop_goods_id',
			'coupon_name',
			'coupon_id', 
			'coupon_type'		],
		//数据请求成功的回调	
		callback: function(){
			av('module-search').data.showData.couponTypeOption = this.typeOption;
		},
		
		
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
					request_array.push(["APPLICATIONADMINCOUPONREMOVE", [{coupon_id:ids[i]}]]);
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
