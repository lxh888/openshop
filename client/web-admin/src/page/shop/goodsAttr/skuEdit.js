av({
	
	id: 'page-shop-goodsAttr::skuEdit',
	selector: false,
	include : ["src/common/content.js", "src/common/application.js"],
	extend : ["common-application","common-content"],
	'export' : {template : "src/page/shop/goodsAttr/skuEdit.html"},
    'import' : function(e){
        this.template(e.template);
    },
    event:{
    	//当渲染的完成之后
		renderEnd: function(){
			//注册选中状态
			this.data.checkboxRegister("checkbox-spu-skuEdit");
		},
    	
    },
	data:{
		shop_goods_id:'',//商品id
		shop_goods_sku_id:'',//规格id
		shop_goods_index:'',//1是门槛商品
		shop_goods_sku:null,
		shop_goods_property:'',//商品类型
		shopGoodsSpuOption:null,
		adminOption:null,//角色数据
		admin_id:'',
		//yitaoshopCheck:null,//检测应用
		//成功的时候回调
		successSubmitCallback:null,
		
		submitLock:false,
		eventSelectRoleChange:function(ele,e){
			this.admin_id=$(ele).val();
			console.log(this.admin_id);
		},
		checkDecimalDoint:function(str,point){
			var dot=str.toString().indexOf(".");
			var dotCnt=str.toString().substring(dot+1,str.length);
			if(dot>-1&&dotCnt.length>point)
			return false;
			else
			return true;
		},
		eventSubmit:function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			var form_input = {};
			var shop_goods_property = this.shop_goods_property;
			form_input.shop_goods_sku_id=this.shop_goods_sku_id;
			form_input.shop_goods_id = this.shop_goods_id;
			form_input.shop_goods_sku_price = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_price"]').val());
			form_input.shop_goods_sku_cost_price = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_cost_price"]').val());
			form_input.shop_goods_sku_market_price = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_market_price"]').val());
			form_input.shop_goods_sku_stock = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_stock"]').val());
			form_input.shop_goods_sku_info = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_info"]').val());
			//如果应用是yitaoshop才传递指定售卖角色、差价、提成、区域管理费参数
			if(this.applicationCheckYitaoshop()){
				/*游客和门槛商品不设置差价、提成和区域管理费*/
				form_input.shop_goods_sku_admin_id = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_admin_id"]').val());
				//店长、总监、创始人没有店长差价
				if(this.admin_id!='visitor' && this.shop_goods_index!=1 && 'shop_manager,chief_inspector,founder'.indexOf(this.admin_id)==-1){//找不到相应admin_id时
					form_input.shop_manager_difference=$.trim($('#goodsAttrSkuEdit').find('[name="shop_manager_difference"]').val());
				}
				//总监、创始人没有店长提成
				if(this.admin_id!='visitor' && this.shop_goods_index!=1 && 'chief_inspector,founder'.indexOf(this.admin_id)==-1){
					form_input.shop_manager_royalty=[]
					$('#goodsAttrSkuEdit').find('[name="shop_manager_royalty"]').each(function(){
						form_input.shop_manager_royalty.push( $(this).val() );
					});
				}
				//总监、创始人没有总监差价
				if(this.admin_id!='visitor' && this.shop_goods_index!=1 && 'chief_inspector,founder'.indexOf(this.admin_id)==-1){
					form_input.chief_inspector_difference=$.trim($('#goodsAttrSkuEdit').find('[name="chief_inspector_difference"]').val());
				}
				//创始人没有总监提成
				if(this.admin_id!='visitor' && this.shop_goods_index!=1 && 'founder'.indexOf(this.admin_id)==-1){
					form_input.chief_inspector_royalty=[]
					$('#goodsAttrSkuEdit').find('[name="chief_inspector_royalty"]').each(function(){
						form_input.chief_inspector_royalty.push( $(this).val() );
					});
				}
				//创始人没有创始人差价
				if(this.admin_id!='visitor' && this.shop_goods_index!=1 && 'founder'.indexOf(this.admin_id)==-1){
					form_input.founder_difference=$.trim($('#goodsAttrSkuEdit').find('[name="founder_difference"]').val());
				}

				//创始人提成
				if(this.admin_id!='visitor' && this.shop_goods_index!=1){
					form_input.founder_royalty=[]
					$('#goodsAttrSkuEdit').find('[name="founder_royalty"]').each(function(){
						form_input.founder_royalty.push( $(this).val() );
					});
				}
				
				//创始人区域管理费
				if(this.admin_id!='visitor' && this.shop_goods_index!=1){
					form_input.founder_region_money = $.trim($('#goodsAttrSkuEdit').find('[name="founder_region_money"]').val());
				}
			}

			//获取 库存售价的属性 数组
			form_input.shop_goods_spu_id = [];
			$('#goodsAttrSkuEdit').find('[id="shop_goods_spu_id"]:checked').each(function(){
				form_input.shop_goods_spu_id.push( $(this).val() );
			});

			//e麦
			if(this.applicationCheckEmshop()){
				//e麦积分加人民币购买   附加人民币 附加积分
				form_input.shop_goods_sku_additional_money = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_additional_money"]').val());
				form_input.shop_goods_sku_additional_credit = $.trim($('#goodsAttrSkuEdit').find('[name="shop_goods_sku_additional_credit"]').val());
				
			}

			//读取积分配置
			var creditsConfig=this.applicationCreditConfig();
			
			try {
				if( !form_input.shop_goods_spu_id.length ) throw "规格的属性不能为空";
				
				//普通商品
				if( shop_goods_property == 0){
					var scale = 100;//单位
					var precision = 2;//精度
				}else{
					var scale = creditsConfig.scale;//单位
					var precision = creditsConfig.precision;//精度
				}
				//如果是易淘
				if(this.applicationCheckYitaoshop() && this.admin_id!='visitor' && this.shop_goods_index!=1)
				{
					//店长差价 店长、总监、创始人没有
					if('shop_manager,chief_inspector,founder'.indexOf(this.admin_id)==-1 && form_input.shop_manager_difference==''){throw '店长差价不能为空！';}
					else if('shop_manager,chief_inspector,founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.shop_manager_difference,2)){throw '店长差价小数位最多两位！';}
					else if('shop_manager,chief_inspector,founder'.indexOf(this.admin_id)==-1){
						form_input.shop_manager_difference = ((parseFloat(form_input.shop_manager_difference).toFixed(2))*100).toFixed(0);
					}
					//店长一级提成 总监、创始人没有
					if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && form_input.shop_manager_royalty[0]==''){throw '店长一级提成不能为空！';}
					else if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.shop_manager_royalty[0],2)){throw '店长一级提成小数位最多两位！';}
					else if('chief_inspector,founder'.indexOf(this.admin_id)==-1){
						form_input.shop_manager_royalty[0] = ((parseFloat(form_input.shop_manager_royalty[0]).toFixed(2))*100).toFixed(0);
					}
					//店长二级提成 总监、创始人没有
					if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && form_input.shop_manager_royalty[1]==''){throw '店长二级提成不能为空！';}
					else if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.shop_manager_royalty[1],2)){throw '店长二级提成小数位最多两位！';}
					else if('chief_inspector,founder'.indexOf(this.admin_id)==-1){
						form_input.shop_manager_royalty[1] = ((parseFloat(form_input.shop_manager_royalty[1]).toFixed(2))*100).toFixed(0);
					}
					//店长三级提成 总监、创始人没有
					// if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && form_input.shop_manager_royalty[2]==''){throw '店长三级提成不能为空！';}
					// else if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.shop_manager_royalty[2],2)){throw '店长三级提成小数位最多两位！';}
					// else if('chief_inspector,founder'.indexOf(this.admin_id)==-1){
					// 	form_input.shop_manager_royalty[2] = ((parseFloat(form_input.shop_manager_royalty[2]).toFixed(2))*100).toFixed(0);
					// }
					//总监差价 总监、创始人没有
					if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && form_input.chief_inspector_difference==''){throw '总监差价不能为空！';}
					else if('chief_inspector,founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.chief_inspector_difference,2)){throw '总监差价小数位最多两位！';}
					else if('chief_inspector,founder'.indexOf(this.admin_id)==-1){
						form_input.chief_inspector_difference = ((parseFloat(form_input.chief_inspector_difference).toFixed(2))*100).toFixed(0);
					}
					//总监一级提成 创始人没有
					if('founder'.indexOf(this.admin_id)==-1 && form_input.chief_inspector_royalty[0]==''){throw '总监一级提成不能为空！';}
					else if('founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.chief_inspector_royalty[0],2)){throw '总监一级提成小数位最多两位！';}
					else if('founder'.indexOf(this.admin_id)==-1){
						form_input.chief_inspector_royalty[0] = ((parseFloat(form_input.chief_inspector_royalty[0]).toFixed(2))*100).toFixed(0);
					}
					//总监二级提成 创始人没有
					// if('founder'.indexOf(this.admin_id)==-1 && form_input.chief_inspector_royalty[1]==''){throw '总监二级提成不能为空！';}
					// else if('founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.chief_inspector_royalty[1],2)){throw '总监二级提成小数位最多两位！';}
					// else if('founder'.indexOf(this.admin_id)==-1){
					// 	form_input.chief_inspector_royalty[1] = ((parseFloat(form_input.chief_inspector_royalty[1]).toFixed(2))*100).toFixed(0);
					// }
					//创始人差价 创始人没有
					if('founder'.indexOf(this.admin_id)==-1 && form_input.founder_difference==''){throw '创始人差价不能为空！';}
					else if('founder'.indexOf(this.admin_id)==-1 && !this.checkDecimalDoint(form_input.founder_difference,2)){throw '创始人差价小数位最多两位！';}
					else if('founder'.indexOf(this.admin_id)==-1){
						form_input.founder_difference = ((parseFloat(form_input.founder_difference).toFixed(2))*100).toFixed(0);
					}
					//创始人一级提成
					if(form_input.founder_royalty[0]==''){throw '创始人一级提成不能为空！';}
					else if(!this.checkDecimalDoint(form_input.founder_royalty[0],2)){throw '创始人一级提成小数位最多两位！';}
					else{
						form_input.founder_royalty[0] = ((parseFloat(form_input.founder_royalty[0]).toFixed(2))*100).toFixed(0);
					}
					//创始人二级提成
					// if(form_input.founder_royalty[1]==''){throw '创始人二级提成不能为空！';}
					// else if(!this.checkDecimalDoint(form_input.founder_royalty[1],2)){throw '创始人二级提成小数位最多两位！';}
					// else{
					// 	form_input.founder_royalty[1] = ((parseFloat(form_input.founder_royalty[1]).toFixed(2))*100).toFixed(0);
					// }
					//创始人区域管理费
					if(form_input.founder_region_money==''){throw '创始人区域管理费不能为空！';}
					else if(form_input.founder_region_money==0){throw '创始人区域管理费不能为0！';}
					else if(!this.checkDecimalDoint(form_input.founder_region_money,2)){throw '创始人区域管理费小数位最多两位！';}
					else{
						form_input.founder_region_money = ((parseFloat(form_input.founder_region_money).toFixed(2))*100).toFixed(0);
					}
				}
				//易淘 如果是游客将id改为''
				if(this.applicationCheckYitaoshop() && form_input.shop_goods_sku_admin_id=='visitor'){
					form_input.shop_goods_sku_admin_id='';
				}

				//e麦
				if(this.applicationCheckEmshop()){
					//e麦积分加人民币购买   积分附加人民币  人民币附加积分
					if(form_input.shop_goods_sku_additional_money==''){
						delete form_input.shop_goods_sku_additional_money
					}else if(!this.checkDecimalDoint(form_input.shop_goods_sku_additional_money,2)){
						throw '附加人民币小数位数不能超过2位';
					}
					else{
						form_input.shop_goods_sku_additional_money = ((parseFloat(form_input.shop_goods_sku_additional_money).toFixed(2))*100).toFixed(0);
					}

					if(form_input.shop_goods_sku_additional_credit==''){
						delete form_input.shop_goods_sku_additional_credit
					}
					else if(!this.checkDecimalDoint(form_input.shop_goods_sku_additional_credit,precision)){
						throw '附加积分小数位数不能超过'+precision+'位';
					}
					else{
						form_input.shop_goods_sku_additional_credit = ((parseFloat(form_input.shop_goods_sku_additional_credit).toFixed(precision))*scale).toFixed(0);
					}
				}

				var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
				//成本价
				if(form_input.shop_goods_sku_cost_price == ""){
					delete form_input.shop_goods_sku_cost_price;
				}else{
					if( money_format.test(form_input.shop_goods_sku_cost_price) ){
						form_input.shop_goods_sku_cost_price = ((parseFloat(form_input.shop_goods_sku_cost_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "成本价输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "成本价输入有误，格式必须是大于0的整数";
						}
					}
				}
				//市场价格
				if(form_input.shop_goods_sku_market_price == ""){
					delete form_input.shop_goods_sku_market_price;
				}else{
					if( money_format.test(form_input.shop_goods_sku_market_price) ){
						form_input.shop_goods_sku_market_price = ((parseFloat(form_input.shop_goods_sku_market_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "市场价输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "市场价输入有误，格式必须是大于0的整数";
						}
					}
				}
				
				//价格
				if(form_input.shop_goods_sku_price == ""){
					delete form_input.shop_goods_sku_price;
				}else{
					if( money_format.test(form_input.shop_goods_sku_price) ){
						form_input.shop_goods_sku_price = ((parseFloat(form_input.shop_goods_sku_price).toFixed(precision))*scale).toFixed(0);
					}else{
						if( parseInt(precision) ){
							throw "售卖单价输入有误，格式必须是大于0的整数或者"+precision+"位小数";
						}else{
							throw "售卖单价输入有误，格式必须是大于0的整数";
						}
					}
				}
				
			}
			catch(err) {
						//console.log(err);
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
				}
			//console.log(form_input);
			//提交数据
			av("common-content").data.submit({
				method:"submit",
				request:["SHOPADMINGOODSSKUEDIT", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
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