av({
	
	id: 'page-shop-goodsEdit',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/shop/goodsEdit.html"},
    'import' : function(e){
        this.template(e.template);
        this.data.templateBasics = e.templateBasics;
        this.data.templateDetails = e.templateDetails;
    },
	main: function(){
		var shop_goods_id = (function(){try{ return av.router().anchor.query.id;}catch(e){return '';}}());
		if( !shop_goods_id ){
			return av.router(av.router().url, '#/shop-goodsList/').request();
		}
		this.data.request.data = ['SHOPADMINPGOODSGET', [{shop_goods_id:shop_goods_id}]];
		if(this.data.applicationCheckMuYing()){
			this.data.request.allMerchant = ['MERCHANTADMINGETALL'];
		}
	},
	event: {
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/shop-goodsList/').request();
		},
		
		loadEnd: function(){
			//加载完成，说明有数据了，将值赋
			if( this.data.data ){
				this.data.formInputInit();
				for(var i in this.data.formInput){
					if(typeof this.data.data[i] != 'undefined'){
						this.data.formInput[i] = this.data.data[i];
					}
				}
			}
		},
		renderEnd: function(){
			//调用 Chosen  先更新
			$('select[name="shop_goods_state"], select[name="shop_goods_stock_mode"], select[name="shop_goods_property"], select[name="shop_goods_index"]').chosen("destroy");
			$('select[name="shop_goods_state"], select[name="shop_goods_stock_mode"], select[name="shop_goods_property"], select[name="shop_goods_index"]').chosen({
				//width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains:true, 
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
		}
	},
	data:{
		request: {},
		allMerchant:undefined,
		state: undefined,
		data: null,
		templateBasics:'',
		templateDetails:'',
		action: '',
		eventAction:function(ele, e, i){
			this.updateFormInput();
			this.action = i;
			if(this.action == 'details'){
				this.explainEditor();
			}
			
		},
		//初始化值
		formInputInit: function(){
			if( this.UEditor ){
				this.UEditor = null;
			}
			this.formInput = {
				shop_goods_id:'',
				shop_goods_sn:'',
				shop_goods_name:'',
				shop_goods_property:0,
				shop_goods_index:0,
				shop_goods_info:'',
				shop_goods_sort:0,
				shop_goods_stock_mode:0,
				shop_goods_keywords:'',
				shop_goods_description:'',
				shop_goods_state:0,
				shop_goods_admin_note:'',
				shop_goods_shipping_price:'',
				shop_goods_ticket_address:'',
				merchant_id:''

			};
			
			//如果是E麦商城
			if( this.applicationCheckEmshop() ){
				this.formInput.shop_goods_recommend_money = 0;
			}
			
		},
		
		/*emshopCheck: function(){
			var objectRequestAPI = new requestAPI();
			if( objectRequestAPI.application() == 'emshop_test' || objectRequestAPI.application() == 'emshop' ){
				return true;
			}else{
				return false;
			}
		},*/
		
		formInput: {},
		
		//更新输入值
		updateFormInput: function(){
			this.formInput.shop_goods_sn = $.trim($('[name="shop_goods_sn"]').val());
			this.formInput.shop_goods_name = $.trim($('[name="shop_goods_name"]').val());
			this.formInput.shop_goods_property = $.trim($('[name="shop_goods_property"]').val());
			//如果不是中润会展
			if(!this.applicationCheckZhongrunhuizhan()){
				this.formInput.shop_goods_index = $.trim($('[name="shop_goods_index"]').val());
			}
			else{
				this.formInput.shop_goods_index=0
				this.formInput.shop_goods_ticket_address = $.trim($('[name="shop_goods_ticket_address"]').val());
			}
			this.formInput.shop_goods_info = $.trim($('[name="shop_goods_info"]').val());
			this.formInput.shop_goods_sort = $.trim($('[name="shop_goods_sort"]').val());
			this.formInput.shop_goods_stock_mode = $.trim($('[name="shop_goods_stock_mode"]').val());
			this.formInput.shop_goods_keywords = $.trim($('[name="shop_goods_keywords"]').val());
			this.formInput.shop_goods_description = $.trim($('[name="shop_goods_description"]').val());
			this.formInput.shop_goods_state = $.trim($('[name="shop_goods_state"]').val());
			this.formInput.shop_goods_admin_note = $.trim($('[name="shop_goods_admin_note"]').val());
			//如果是E麦商城
			if( this.applicationCheckEmshop() ){
				this.formInput.shop_goods_recommend_money = $.trim($('[name="shop_goods_recommend_money"]').val());
			}
			//如果是易淘商城
			if(this.applicationCheckYitaoshop()){
				this.formInput.shop_goods_shipping_price = $.trim($('[name="shop_goods_shipping_price"]').val());
			}
			//如果是母婴或者江油快递
			if(this.applicationCheckMuYing()||this.applicationCheckMuYing()){
				this.formInput.merchant_id= $.trim($('[name="merchant_id"]').val());
			}
			
		},
		
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		checkDecimalDoint:function(str,point){
			var dot=str.toString().indexOf(".");//
			var dotCnt=str.toString().substring(dot+1,str.length);
			if(dot>-1&&dotCnt.length>point)
			return false;
			else
			return true;
		},
		submitLock:false,
		eventSubmit: function(){
			this.updateFormInput();
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			try {
				if(_this.formInput.shop_goods_id == '') throw "商品ID异常";
				if(_this.formInput.shop_goods_name == '') throw "商品名称不能为空";
				if(_this.formInput.shop_goods_sort == ""){
					_this.formInput.shop_goods_sort = 0;
				}
				
				//如果是E麦商城
				if( _this.applicationCheckEmshop() ){
					_this.formInput.shop_goods_recommend_money = $.trim($('[name="shop_goods_recommend_money"]').val());
					if( _this.formInput.shop_goods_recommend_money == "" ){
						_this.formInput.shop_goods_recommend_money = 0;
					}else{
						var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
						if( !money_format.test(_this.formInput.shop_goods_recommend_money) ){
							//恢复值
							_this.formInput.shop_goods_recommend_money = _this.formInput.shop_goods_recommend_money*100;//元转为分
							throw "商品推荐购买奖金的格式输入有误，格式必须是整数或者是两位小数";
						}else{
							_this.formInput.shop_goods_recommend_money = parseInt((parseFloat(_this.formInput.shop_goods_recommend_money).toFixed(2))*100);//元转为分
						}
					}
				}
				//如果是易淘商城
				if(_this.applicationCheckYitaoshop()&&_this.formInput.shop_goods_shipping_price!=''&&!_this.checkDecimalDoint(_this.formInput.shop_goods_shipping_price,2))
				{
					throw '商品邮费小数位不能超过2位';
				}
				//如果是母婴或者江油快递
				// if(this.applicationCheckMuYing()&& _this.formInput.merchant_id==''){
				// 	throw '商家id不能为空';
				// }
				
			}
			catch(err) {
					console.log("err",err);
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
				_this.formInput.shop_goods_shipping_price=parseFloat(_this.formInput.shop_goods_shipping_price*100).toFixed(0);
			//提交数据
			this.submit({
				method:"submit",
				request:["SHOPADMINGOODSEDIT", [_this.formInput]],
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