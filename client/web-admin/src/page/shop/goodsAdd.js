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
		if(this.data.applicationCheckMuYing()){
			this.data.request.allMerchant = ['MERCHANTADMINGETALL'];
		}
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
		request: {},
		state: undefined,
		allMerchant:undefined,
		list: {
			data : [],
		},
		submitLock:false,
		checkDecimalDoint:function(str,point){//2.111
			var dot=str.toString().indexOf(".");//1
			var dotCnt=str.toString().substring(dot+1,str.length);//2 5
			console.log(dotCnt.length,point)
			if(dot>-1&&dotCnt.length>point)
			return false;
			else
			return true;
		},
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		//添加商品
		eventSubmit: function(){
			var _this=this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = {};
			form_input.shop_goods_sn = $.trim($('[name="shop_goods_sn"]').val());
			form_input.shop_goods_name = $.trim($('[name="shop_goods_name"]').val());
			form_input.shop_goods_property = $.trim($('[name="shop_goods_property"]').val());
			//如果不是中润会展
			if(!this.applicationCheckZhongrunhuizhan()){
				form_input.shop_goods_index = $.trim($('[name="shop_goods_index"]').val());
			}
			else{
				form_input.shop_goods_index=0
				form_input.shop_goods_ticket_address = $.trim($('[name="shop_goods_ticket_address"]').val());
			}
			form_input.shop_goods_info = $.trim($('[name="shop_goods_info"]').val());
			form_input.shop_goods_sort = $.trim($('[name="shop_goods_sort"]').val());
			form_input.shop_goods_stock_mode = $.trim($('[name="shop_goods_stock_mode"]').val());
			form_input.shop_goods_keywords = $.trim($('[name="shop_goods_keywords"]').val());
			form_input.shop_goods_description = $.trim($('[name="shop_goods_description"]').val());
			form_input.shop_goods_admin_note = $.trim($('[name="shop_goods_admin_note"]').val());
			//如果是E麦商城
			if( this.applicationCheckEmshop() ){
				form_input.shop_goods_recommend_money = $.trim($('[name="shop_goods_recommend_money"]').val());
			}
			//如果是易淘商城
			if(this.applicationCheckYitaoshop()){
				form_input.shop_goods_shipping_price = $.trim($('[name="shop_goods_shipping_price"]').val());
			}

			//如果是母婴商城或者江油快递
			if(this.applicationCheckMuYing()||this.applicationCheckJiangYouKuaiDi()){
				form_input.merchant_id = $.trim($('[name="merchant_id"]').val());
			}
			
			
			//console.log(form_input);
			try {
				if(form_input.shop_goods_name == '') throw "产品名称不能为空";
				if(form_input.shop_type_sort == ""){
					delete form_input.shop_type_sort;
				}

				//如果是易淘商城
				if(this.applicationCheckYitaoshop()&&form_input.shop_goods_shipping_price!=''&&!this.checkDecimalDoint(form_input.shop_goods_shipping_price,2))
				{
					throw '商品邮费小数位不能超过2位';
				}
				if(this.applicationCheckMuYing()&& form_input.merchant_id==''){
					throw '商家id不能为空';
				}

				//如果是E麦商城
				if( this.applicationCheckEmshop() ){
					form_input.shop_goods_recommend_money = $.trim($('[name="shop_goods_recommend_money"]').val());
					if( form_input.shop_goods_recommend_money == "" ){
						form_input.shop_goods_recommend_money = 0;
					}else{
						var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
						if( !money_format.test(form_input.shop_goods_recommend_money) ){
							//恢复值
							form_input.shop_goods_recommend_money = form_input.shop_goods_recommend_money*100;//元转为分
							throw "商品推荐购买奖金的格式输入有误，格式必须是整数或者是两位小数";
						}else{
							form_input.shop_goods_recommend_money = parseInt((parseFloat(form_input.shop_goods_recommend_money).toFixed(2))*100);//元转为分
						}
					}
				}
			}
			catch(err) {
				console.log(err);
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
				form_input.shop_goods_shipping_price=parseFloat(form_input.shop_goods_shipping_price*100).toFixed(0);
			//提交数据
			this.submit({
				method:"submit",
				request:["SHOPADMINGOODSADD", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(id){
					_this.submitLock = false;
					return av.router(av.router().url, '#/shop-goodsEdit/?id='+id).request();
				}
			});
		}
	}
});