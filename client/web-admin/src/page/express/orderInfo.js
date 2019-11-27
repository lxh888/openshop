av({
	
	id: 'page-express-orderInfo',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/express/orderInfo.html"},
    'import' : function(e){
        this.template(e.template);
    },
    
	main: function(){
		this.data.express_order_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !this.data.express_order_id ){
			return av.router(av.router().url, '#/express-orderInfo/').request();
		}
		this.data.request.data = ['EXPRESSADMINORDERDETAILS', [{express_order_id:this.data.express_order_id}]];
	},
	event: {
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/express-orderList/').request();
		},
	},
	data:{
		request: {},
		state: undefined,
		data: null,
		
		express_order_id: '',
		
		/**
		 * 配送状态
		 * 
		 * @param {Object} ele
		 * @param {Object} e
		 * @param {Object} state
		 */
		eventShippingSend: function(ele, e){
			var _this = this;
			layer.prompt({
				title: '确认发货，请输入运单号', 
				formType: 2, 
				area: ['500px', '200px'] //自定义文本域宽高
			}, function(text, index){
			    	layer.close(index);
			    	
			    	console.log('确认发货，运单号:', $.trim(text));
			    	//提交数据
					_this.submit({
						method:"submit",
						request:["EXPRESSADMINORDERSHIPPING", [{
							express_order_id:_this.express_order_id, 
							express_order_shipping_state:2, 
							express_order_shipping_no: $.trim(text)
						}]],
						error:function(){
						},
						success:function(){
							//刷新页面
							av().render("refresh").run();
						}
					});
			    	//_project.shipping_send(text);
			});
			
			console.log('确认发货');
		},
		
		
		
		
	}
	
	
	
});