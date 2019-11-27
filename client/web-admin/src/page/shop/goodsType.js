av({
    id:'page-shop-goodsType',//工程ID
    // selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
    include : ["src/common/content.js"],//获取js文件
    extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
    'export' : {
		template: "src/page/shop/goodsType.html",
		},//引入模版，可以同时引入多个
    'import' : function(e){
    	// this.data.templateTest = e.template2;//绑定模版
        this.template(e.template);//绑定模版
    },
    
 
    //主函数
    main: function(){
		//获得路由上的ID  就是商家ID
		// this.data.getUrlMerchantId();
		
		var shop_goods_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !shop_goods_id ){
			return av.router(av.router().url, '#/shop-goodsList/').request();
		}
		
		this.data.request.data =  ["SHOPADMINPGOODSGET", [{shop_goods_id:shop_goods_id}]];//商品信息
		this.data.request.typeModuleShopGoodsOption =  ["APPLICATIONADMINTYPEOPTION",[{sort:["sort_asc"],module:"shop_goods_type"}]];//分类
		//this.data.request.typeModuleMerchantShopGoodsOption =  ["SHOPADMINPGOODSGET",[{sort:["sort_asc"],module:"shop_goods_type"}]];//分类
		//SHOPADMINPGOODSGET 
		/*this.data.request.platformTypeModuleShopGoodsOption = ["MERCHANTMANAGESELFTYPEOPTION",
			[{
				merchant_id: this.data.merchant_id,
				sort: ["sort_asc"],
				module: "shop_goods_type",
				is_platform: 1
			}]
		];*/
		
		
    },
    event: {
		
		renderEnd: function(){
			$('[name="goods-type-checkbox"]').unbind("background").bind("background", function(){
				$('[name="goods-type-checkbox"]').each(function(){
					$(this).parent().removeClass("checkbox-checked");
				});
				
				$('[name="goods-type-checkbox"]:checked').each(function(){
					$(this).parent().addClass("checkbox-checked");
				});
			});
			$('[name="goods-type-checkbox"]').first().trigger("background");
			$('[name="goods-type-checkbox"]').unbind("click").click(function(){
				$('[name="goods-type-checkbox"]').first().trigger("background");
			});
		}
	
	
    },
    //数据对象
    data:{
		request: {},
		state: undefined,
		list: {
			data : [],
		},
		
		goodsTypeInArray: function(type_id, shop_goods_type){
			if(typeof shop_goods_type != 'object' || !shop_goods_type.length){
				return false;
			}
			
			var exist = false;
			for(var i in shop_goods_type){
				if( shop_goods_type[i].type_id == type_id ){
					exist = true;
					break;
				}
			}
			return exist;
		},
		
		
		submitLock:false,
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		//编辑商品分类
		eventSubmit: function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
	      	var type_id = [];
	      	$('[name="goods-type-checkbox"]:checked').each(function () {
	        	type_id.push($(this).val());
	      	});
	      	//提交数据
	        _this.submit({
	          	method: "submit",
	          	request: ["SHOPADMINGOODSTYPEEDIT", [{ shop_goods_id: _this.data.shop_goods_id, type_id: type_id}]],
	          	error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					//刷新页面
					av().run();
				}
        	});
		}
		
		
	}
});