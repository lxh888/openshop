av({

  id:'orderDetails::shippingSen',
  selector:'#shippingSen',
  include : ["src/common/content.js"],
  extend : ["common-content"],
  'export' : {template : "src/page/shop/orderDetails/shippingSen.html"},
  'import' : function(e){
        this.template(e.template);
  },
  data:{
    expressType:null,
    shop_order_shipping_no:null,
    shop_order_express_type:null,
    eventChangeSelect:function(){
      var _this=this;
      _this.shop_order_express_type=$.trim($('[name="expressType"]').val());
    },
    eventChangeShippingNo:function(){
      var _this=this;
      _this.shop_order_shipping_no=$.trim($('[name="shop_order_shipping_no"]').val());
    }
  }
  
});