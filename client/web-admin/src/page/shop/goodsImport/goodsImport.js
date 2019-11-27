av({
    id:'page-shop-goodsList::import',//工程ID
    // selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
    include : ["src/common/content.js"],//获取js文件
    extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
    'export' : {
		template: "src/page/shop/goodsImport/goodsImport.html",
		},//引入模版，可以同时引入多个
    'import' : function(e){
    	// this.data.templateTest = e.template2;//绑定模版
        this.template(e.template);//绑定模版
    },
    
 
    //主函数
    main: function(){
			// if(this.data.applicationCheckMuYing()){
			// 	this.data.request.allMerchant = ['MERCHANTADMINGETALL'];
			// }
			
    },
    event: {
	
    },
    //数据对象
    data:{
		request: {},
		
		submitLock:false,
		
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		//添加商品
		eventSubmit: function(){
			var _this=this;
			var form_input={}
		//	form_input.key = $.trim($('[name="key"]').val());
			form_input.seller_nick = $.trim($('[name="seller_nick"]').val());
			
			
			//console.log(form_input);
			try {
				if(form_input.key == '') throw "API KEY不能为空";
				if(form_input.seller_nick == "")throw "卖家昵称不能为空";
			
			}
			catch(err) {
				console.log(err);
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
			}
				
			//提交数据
			this.submit({
				method:"submit",
				request:["PROJECTXILETAOIMOPORTGOODS", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(id){
					
					_this.submitLock = false;
					//成功提交的回调
					if( typeof _this.successSubmitCallback == 'function'){
						_this.successSubmitCallback();
					}
				}
			});
		}
	}
});