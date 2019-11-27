av({
	
	id: 'page-shop-goodsAttr::spuAdd',
	selector: false,
	include : ["src/common/content.js","src/common/application.js"],
	extend : ["common-application"],
	'export' : {template : "src/page/shop/goodsAttr/spuAdd.html"},
    'import' : function(e){
        this.template(e.template);
    },
	data:{
		shop_goods_id:'',
		shop_goods_spu_parent_id:'',
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
			form_input.shop_goods_id=this.shop_goods_id
			form_input.shop_goods_spu_id = $.trim($('#goodsAttrSpuAdd').find('[name="shop_goods_spu_id"]').val());
			form_input.shop_goods_spu_name = $.trim($('#goodsAttrSpuAdd').find('[name="shop_goods_spu_name"]').val());
			form_input.shop_goods_spu_sort = $.trim($('#goodsAttrSpuAdd').find('[name="shop_goods_spu_sort"]').val());
			form_input.shop_goods_spu_info = $.trim($('#goodsAttrSpuAdd').find('[name="shop_goods_spu_info"]').val());
			form_input.shop_goods_spu_parent_id = $.trim($('#goodsAttrSpuAdd').find('[name="shop_goods_spu_parent_id"]').val());
			form_input.shop_goods_spu_required = $('#goodsAttrSpuAdd').find('[name="shop_goods_spu_required"]').is(':checked')? 1 : 0;

			try {
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
		    }
			
			//提交数据
			av("common-content").data.submit({
				method:"submit",
				request:["SHOPADMINGOODSSPUADD", [form_input]],
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