av({
	
	id: 'page-shop-groupGoodsEdit',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/shop/groupGoodsEdit.html"},
    'import' : function(e){
        this.template(e.template);
    },
    
	main: function(){
		this.data.shop_goods_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !this.data.shop_goods_id ){
			return av.router(av.router().url, '#/shop-groupGoodsList/').request();
		}
		this.data.request.data = ['SHOPADMINGOODSGROUPGET', [{shop_goods_group_id:this.data.shop_goods_id}]];
	},
	event: {
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/shop-groupGoodsList/').request();
		},
		
		renderEnd: function(){
			laydate.render({
				elem: '[name="shop_group_goods_time"]'
				,type: 'datetime'
				,theme: '#337ab7'
				,range: '~'
			});
		},
		
		loadEnd : function(){
			this.render("refresh");//重新渲染
		}
		
	},
	data:{
		request: {},
		state: undefined,
		data: null,
		
		shop_goods_id: '',
		
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
			form_input.shop_goods_group_id  	= _this.shop_goods_id;
			
			try{
				if(form_input.shop_goods_group_id == '') throw "团购ID异常";
				
				var times = $.trim($('[name="shop_group_goods_time"]').val());
				if( times == "" ){
					throw "请选择拼团活动的时间范围";	
				}
				
				var times_split = times.split('~', 2);
				if( times_split[0] ) times_split[0] = $.trim(times_split[0]);
				if( times_split[1] ) times_split[1] = $.trim(times_split[1]);
				
				var format = /^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/;
				if( times_split[0] && format.test(times_split[0]) ){
					form_input.shop_goods_group_start_time = times_split[0];
				}else{
					throw "开始拼团时间不合法";	
				}
				
				if( times_split[1] && format.test(times_split[1]) ){
					form_input.shop_goods_group_end_time = times_split[1];
				}else{
					throw "结束拼团时间不合法";	
				}
				
			}
			catch(err){
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["SHOPADMINGOODSGROUPEDIT", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					//刷新页面
					av().compiler("reload").render().run();
				}
			});
			
		}
	
		
		
	}
	
	
	
});