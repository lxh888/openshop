av({

  id:'orderDetails::checkArea',
  selector:'#checkArea',
  include : ["src/common/content.js"],
  extend : ["common-content"],
  'export' : {template : "src/page/shop/orderDetails/checkArea.html"},
  'import' : function(e){
        this.template(e.template);
  },
  data:{
    areaList:null,
//  shop_order_shipping_no:null,
    shop_order_area:null,
    
    eventChangeSelect:function(){
      var _this=this;
      _this.shop_order_area=$.trim($('[name="areaList"]').val());
    },
//  eventChangeShippingNo:function(){
//    var _this=this;
//    _this.shop_order_shipping_no=$.trim($('[name="shop_order_shipping_no"]').val());
//  }
  }
  
});