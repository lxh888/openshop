av({
	
	id: 'page-shop-goodsAttr::spuEdit',
	selector: false,
	include : ["src/common/content.js", "src/common/application.js"],
	extend : ["common-application"],
	'export' : {template : "src/page/shop/goodsAttr/spuEdit.html"},
    'import' : function(e){
        this.template(e.template);
    },
	data:{
		shop_goods_spu_id:'',
		shop_goods_spu_name:'',
		shop_goods_spu_attr:null,
		shopGoodsSpuOption:null,
		//成功的时候回调
		successSubmitCallback:null,
		
		submitLock:false,
		eventSubmit:function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			var form_input = {};
			form_input.shop_goods_id = this.shop_goods_id;
			form_input.shop_goods_spu_id = $.trim($('#goodsAttrSpuEdit').find('[name="shop_goods_spu_id"]').val());
			form_input.shop_goods_spu_name = $.trim($('#goodsAttrSpuEdit').find('[name="shop_goods_spu_name"]').val());
			form_input.shop_goods_spu_sort = $.trim($('#goodsAttrSpuEdit').find('[name="shop_goods_spu_sort"]').val());
			form_input.shop_goods_spu_info = $.trim($('#goodsAttrSpuEdit').find('[name="shop_goods_spu_info"]').val());
			form_input.shop_goods_spu_parent_id = $.trim($('#goodsAttrSpuEdit').find('[name="shop_goods_spu_parent_id"]').val());
			form_input.shop_goods_spu_required = $('#goodsAttrSpuEdit').find('[name="shop_goods_spu_required"]').is(':checked')? 1 : 0;

			try {
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			//提交数据
			av("common-content").data.submit({
				method:"submit",
				request:["SHOPADMINGOODSSPUEDIT", [form_input]],
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