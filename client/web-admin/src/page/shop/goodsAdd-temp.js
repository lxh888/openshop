av({
    id:'page-shop-goodsAdd',//工程ID
    include : ["src/common/content.js"],//获取js文件
    extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
    'export' : {
		template: "src/page/shop/goodsAdd.html",
		},//引入模版，可以同时引入多个
    'import' : function(e){
    	// this.data.templateTest = e.template2;//绑定模版
        this.template(e.template);//绑定模版
    },
    
 
    //主函数
    main: function(){
		/*if(this.data.applicationCheckMuYing()){
			this.data.request.allMerchant = ['MERCHANTADMINGETALL'];
		}*/
    },
    event: {
	
    },
    //数据对象
    data:{
    	shopGoodsPropertyOption:[
				{id:0,name:'普通商品'},
				{id:1,name:'积分商品'},
				// {id:2,name:'礼包商品'},
			],
			
	}
	
});