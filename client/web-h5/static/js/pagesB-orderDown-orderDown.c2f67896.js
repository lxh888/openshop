(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesB-orderDown-orderDown"],{3256:function(e,t,i){t=e.exports=i("2350")(!1),t.push([e.i,".image[data-v-e843e1fa]{width:100%}.demo[data-v-e843e1fa]{padding:%?50?%;margin-top:%?30?%;border:%?1?% #d8d8d8 solid;color:#8f8f94}.content[data-v-e843e1fa]{width:94%;height:auto;margin-left:2%;font-size:15px}.head-box[data-v-e843e1fa]{width:100%;height:60px;border-bottom:1px solid #e8e8e8 ;line-height:60px}.box-title[data-v-e843e1fa]{width:30%;height:60px;line-height:60px;float:left}.box-inp[data-v-e843e1fa]{width:50%;height:60px;line-height:60px;float:right;text-align:right}.box-inp uni-input[data-v-e843e1fa]{float:left;width:100%;height:60px}uni-button[data-v-e843e1fa]{margin-top:80px;background:#f76968;color:#fff}uni-image[data-v-e843e1fa]{width:40px;height:40px;margin-top:10px}",""])},3350:function(e,t,i){var n=i("3256");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var a=i("4f06").default;a("3c20e0b4",n,!0,{sourceMap:!1,shadowMode:!1})},"4ad9":function(e,t,i){"use strict";i.r(t);var n=i("5c26"),a=i.n(n);for(var o in n)"default"!==o&&function(e){i.d(t,e,function(){return n[e]})}(o);t["default"]=a.a},"5c26":function(e,t,i){"use strict";var n=i("288e");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var a=n(i("f499")),o=n(i("c997")),s=n(i("d148")),c=new o.default,u={data:function(){return{title:"uploadFile",headImg:"",merchantID:"",goodsName:"",goodsNumber:"",goodsPrice:"",phone:"",remarks:""}},onLoad:function(){this.load()},methods:{chooseImage:function(){var e=this;uni.chooseImage({count:1,sizeType:["compressed"],sourceType:["album"],success:function(t){console.log("chooseImage success, temp path is",t.tempFilePaths[0]);var i=t.tempFilePaths[0];c.left_token(function(t){var n=c.api_server_url+"?"+encodeURI('data=[["MERCHANTTALLYSELFUPLOADVOUCHER",[{"merchant_id":"'+e.merchantID+'"}]]]')+"&token="+t;uni.uploadFile({url:n,filePath:i,fileType:"image",name:"file",success:function(t){console.log("上传成功:",t.data),uni.showToast({title:"上传成功",icon:"success",duration:1e3,success:function(){e.headImg=i}})},fail:function(e){console.log("uploadImage fail",e),uni.showModal({content:e.errMsg,showCancel:!1})}})})},fail:function(e){console.log("chooseImage fail",e),uni.showToast({title:(0,a.default)(e),icon:"none",duration:2e3})}})},next:function(){var e=this;c.submit({request:{s:["MERCHANTTALLYSELFADD",[{merchant_id:e.merchantID,goods_name:e.goodsName,goods_number:e.goodsNumber,goods_money:100*e.goodsPrice,client_phone:e.phone,comment:e.comment}]]},callback:function(e){console.log("回调成功",e);s.default.checkError(e,["s"],function(e,t){uni.showToast({title:t,icon:"none"})});e.data.s.data&&uni.showToast({title:"提交成功",success:function(){setTimeout(function(){uni.switchTab({url:"../../pages/me/me"})},1500)}})},error:function(e){uni.showToast({title:"出错啦",icon:"none"})}})},load:function(){var e=this;c.submit({request:{sj:["MERCHANTSELF"]},callback:function(t){s.default.checkError(t,["sj"],function(e,t){uni.showToast({title:t,icon:"none"})});var i=t.data.sj.data;e.merchantID=i[0].id,console.log(e.merchantID)},error:function(e){uni.showToast({title:"出错啦",icon:"none"})}})}}};t.default=u},6152:function(e,t,i){"use strict";i.r(t);var n=i("f7ad"),a=i("4ad9");for(var o in a)"default"!==o&&function(e){i.d(t,e,function(){return a[e]})}(o);i("bd6f");var s=i("2877"),c=Object(s["a"])(a["default"],n["a"],n["b"],!1,null,"e843e1fa",null);t["default"]=c.exports},bd6f:function(e,t,i){"use strict";var n=i("3350"),a=i.n(n);a.a},f7ad:function(e,t,i){"use strict";var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("v-uni-view",{staticClass:"content"},[i("v-uni-view",{staticClass:"head-box"},[i("v-uni-view",{staticClass:"box-title"},[i("v-uni-text",[e._v("商品名称")])],1),i("v-uni-view",{staticClass:"box-inp"},[i("v-uni-input",{attrs:{type:"text",maxlength:"15",placeholder:"请输入商品名称"},model:{value:e.goodsName,callback:function(t){e.goodsName=t},expression:"goodsName"}})],1)],1),i("v-uni-view",{staticClass:"head-box"},[i("v-uni-view",{staticClass:"box-title"},[i("v-uni-text",[e._v("商品数量")])],1),i("v-uni-view",{staticClass:"box-inp"},[i("v-uni-input",{attrs:{type:"number",maxlength:"15",placeholder:"请输入商品数量"},model:{value:e.goodsNumber,callback:function(t){e.goodsNumber=t},expression:"goodsNumber"}})],1)],1),i("v-uni-view",{staticClass:"head-box"},[i("v-uni-view",{staticClass:"box-title"},[i("v-uni-text",[e._v("商品单价")])],1),i("v-uni-view",{staticClass:"box-inp"},[i("v-uni-input",{attrs:{type:"text",maxlength:"15",placeholder:"请输入商品单价"},model:{value:e.goodsPrice,callback:function(t){e.goodsPrice=t},expression:"goodsPrice"}})],1)],1),i("v-uni-view",{staticClass:"head-box"},[i("v-uni-view",{staticClass:"box-title"},[i("v-uni-text",[e._v("客户手机号")])],1),i("v-uni-view",{staticClass:"box-inp"},[i("v-uni-input",{attrs:{type:"number",maxlength:"11",placeholder:"请输入客户手机号"},model:{value:e.phone,callback:function(t){e.phone=t},expression:"phone"}})],1)],1),i("v-uni-view",{staticClass:"head-box"},[i("v-uni-view",{staticClass:"box-title"},[i("v-uni-text",[e._v("备注")])],1),i("v-uni-view",{staticClass:"box-inp"},[i("v-uni-input",{attrs:{type:"text",maxlength:"30",placeholder:"备注信息选填"},model:{value:e.remarks,callback:function(t){e.remarks=t},expression:"remarks"}})],1)],1),i("v-uni-view",{staticClass:"uni-padding-wrap uni-common-mt"},[i("v-uni-view",{staticClass:"demo",on:{click:function(t){t=e.$handleEvent(t),e.chooseImage(t)}}},[e.headImg?[i("v-uni-image",{staticClass:"image",attrs:{src:e.headImg,mode:"widthFix"}})]:[i("v-uni-view",{staticClass:"uni-hello-addfile"},[e._v("+ 选择图片")])]],2)],1),i("v-uni-button",{attrs:{type:"primary"},on:{click:function(t){t=e.$handleEvent(t),e.next(t)}}},[e._v("提交订单")])],1)},a=[];i.d(t,"a",function(){return n}),i.d(t,"b",function(){return a})}}]);