av({
	id:'page-shop-orderDetailsPrint',//工程ID
	// selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
	include : ["src/common/content.js","src/page/shop/orderDetails/shippingSen.js","src/page/shop/orderDetails/checkArea.js"],//获取js文件
	extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
	'export' : {
	template: "src/page/shop/orderDetailsPrint.html",},
	'import' : function(e){
			this.template(e.template);//绑定模版
		},
	
	main: function(){
		this.data.shop_order_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !this.data.shop_order_id ){
			return av.router(av.router().url, '#/shop-orderList/').request();
		}
		this.data.request.data = ['SHOPADMINORDERDETAILS', [{shop_order_id:this.data.shop_order_id}]];
		this.data.request.expressType=['APPLICATIONADMINSHIPPINGOPTIONS',[{module:'express_order_shipping'}]];
		if( this.data.applicationCheckYouli() ){
			this.data.request.areaList=['SHOPORDERSELFREGIONIDLIST'];
		}
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
		data: null,
		shop_order_id : "",
		submitLock:false,
		
		eventCountNumber:function(){
			var eventCountNumber=0;
			this.data.shop_order_goods.forEach(element => {
				eventCountNumber=eventCountNumber+parseInt(element.shop_order_goods_number);
			});
			console.log('2222',eventCountNumber);
			return eventCountNumber
		},
		
		eventPrint:function(){
			// var headstr = "<html><head><title></title></head><body>";  
			// var footstr = "</body>";  
			// var printData = document.getElementById("dvData").innerHTML; //获得 div 里的所有 html 数据
			// var oldstr = document.body.innerHTML;  
			// document.body.innerHTML = headstr+newstr+footstr;  
			// window.print();  
			// document.body.innerHTML = oldstr;  
			// return false;  
			console.log("231231")
			let go = confirm("是否需要打印？");
		// 	setInterval(function(){
		// 		console.log(document.execCommand("print"));
		//  },2000);
			if(go){
				var oldstr = document.body.innerHTML;
				var headStr = "<html><head><title></title></head><body>";
				var footStr = "</body>";
				var content = $("#dvData").html();
				var newStr = headStr + content + footStr;
				document.body.innerHTML = headStr + content + footStr;
				window.print();
				document.body.innerHTML = oldstr;
				//console.log(1111, document.execCommand("print"));
				av.router().reload();

			}

		},
		

	},
	
	

});