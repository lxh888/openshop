av({
	
	id: 'page-shop-groupGoodsAdd',
	include : ["src/common/content.js"],
    extend : ["common-content"],
	'export' : {template : "src/page/shop/groupGoodsAdd.html"},
	'import' : function(e){
        this.template(e.template);
    },
	main: function(){
		//console.log("开始加载：", this);
	},
	event:{
		
		error : function(error){
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		
		renderEnd: function(){
			//调用 Chosen
			$('select[name="shop_goods_sku_id"]').chosen("destroy");
			$('select[name="shop_goods_sku_id"]').chosen({
				width: '100%',
				//placeholder_text_single: '-', //默认值
				earch_contains: true,
				no_results_text: "没有匹配结果",
				case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
	        	//group_search: false //选项组是否可搜。此处搜索不可搜
			});
			
			laydate.render({
				elem: '[name="shop_group_goods_time"]'
				,type: 'datetime'
				,theme: '#337ab7'
				,range: '~'
			});
		}
		
	},
	data:{
		request: {},
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		
		getShopGoodsId: '',
		getShopGoodsError: '',//获取商品数据时的错误信息
		getShopGoodsData: null,//商品数据
		optionShopGoodsShow: function(skuV){
			//普通商品
			var scale = 100;//单位
			var precision = 2;//精度
			if( this.getShopGoodsData.shop_goods_property == 1){
				if( this.applicationConfig.credit ){
					var scale = this.applicationConfig.credit.scale;//单位
					var precision = this.applicationConfig.credit.precision;//精度
				}
			}
			
			var s = '';
			if(skuV.shop_goods_spu){
				for(var i in skuV.shop_goods_spu){
					s += '['+skuV.shop_goods_spu[i].parent.shop_goods_spu_name+':'+skuV.shop_goods_spu[i].shop_goods_spu_name+']';
				}
			}
			s += ' 库存:' + skuV.shop_goods_sku_stock + ' / 价格:' + (skuV.shop_goods_sku_price/scale).toFixed(precision);
			
			return s;
		},
		priceUnit:'元/积分',
		priceUnitUpdate: function(){
			if(this.getShopGoodsData && this.getShopGoodsData.shop_goods_property == 0){
				this.priceUnit = '元';
			}else
			if(this.getShopGoodsData && this.getShopGoodsData.shop_goods_property == 1){
				this.priceUnit = '积分数量';
			}else{
				this.priceUnit = '元/积分';
			}
		},
		
		//获取商城商品
		eventGetShopGoods: function(ele, e){
			//console.log('eventGetShopGoods', $(ele).val() );
			var _this = this;
			_this.getShopGoodsId = $(ele).val();
			if( _this.getShopGoodsId == '' ){
				_this.getShopGoodsError = '';
				_this.getShopGoodsData = null;
				_this.priceUnitUpdate();
				return false;
			}
			var requestAPIObject = new requestAPI();
			requestAPIObject.abort();
			//提交数据
			requestAPIObject.submit({
				request : {
					g:["SHOPADMINGOODSQUERY",[{shop_goods_id:_this.getShopGoodsId}]],
					},
				callback:function(r){
					if( (function(){try{ return r.data.g.errno;}catch(e){return false;}}()) ){
						_this.getShopGoodsError = r.data.g.error;
						_this.getShopGoodsData = null;
						_this.priceUnitUpdate();
					}else{
						_this.getShopGoodsError = '';
						_this.getShopGoodsData = r.data.g.data;
						_this.priceUnitUpdate();
					}
				},
			});
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
			form_input.shop_goods_id  	= $.trim($('[name="shop_goods_id"]').val());
			form_input.shop_goods_group_price 	= $.trim($('[name="shop_goods_group_price"]').val());
			form_input.shop_goods_group_people 	= $.trim($('[name="shop_goods_group_people"]').val());
			form_input.shop_goods_sku_id 	= $.trim($('[name="shop_goods_sku_id"]').val());
			
			try {
				if( form_input.shop_goods_id == "" ) throw "请输入商品ID";
				if( form_input.shop_goods_sku_id == "") delete form_input.shop_goods_sku_id;
				if( form_input.shop_goods_group_price == "" ) delete form_input.shop_goods_group_price;
				if( form_input.shop_goods_group_people == "" ) delete form_input.shop_goods_group_people;
				
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
				
				if( !_this.getShopGoodsData ){
					throw "商品ID不合法";
				}
				
				//普通商品
				if( _this.getShopGoodsData.shop_goods_property == 0){
					var scale = 100;//单位
					var precision = 2;//精度
				}else{
					if( !_this.applicationConfig.credit ){
						throw "积分配置异常";
					}
					var scale = _this.applicationConfig.credit.scale;//单位
					var precision = _this.applicationConfig.credit.precision;//精度
				}
				
				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				//价格
				if(form_input.shop_goods_group_price == ""){
					delete form_input.shop_goods_group_price;
				}else{
					if( money_format.test(form_input.shop_goods_group_price) ){
						form_input.shop_goods_group_price = ((parseFloat(form_input.shop_goods_group_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "参团价格输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "参团价格输入有误，格式必须是大于0的整数";
						}
					}
				}
				
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			
			//提交数据
			this.submit({
				method:"submit",
				request:["SHOPADMINGOODSGROUPADD", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
			
		}
		
		
	}
	
	
	
	
	
	
});
