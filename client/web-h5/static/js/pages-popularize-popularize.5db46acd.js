(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-popularize-popularize"],{1354:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("c5f6");var a={name:"load-more",props:{loadingType:{type:Number,default:0},showImage:{type:Boolean,default:!0},color:{type:String,default:"#777777"},contentText:{type:Object,default:function(){return{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"}}}},data:function(){return{}}};e.default=a},"23b5":function(t,e,i){"use strict";var a=i("3069"),n=i.n(a);n.a},"293d":function(t,e,i){"use strict";var a=i("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("5d73")),o=a(i("7492")),r=a(i("c997")),l=a(i("d148")),s=new r.default,d={data:function(){return{list:[],sharenumber:0,qiniu:"",page:1,loadingType:0,contentText:{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"}}},components:{uniLoadMore:o.default},onReachBottom:function(){if(0===this.loadingType){this.loadingType=1;var t=this;t.page++,uni.showLoading({title:"正在加载",success:function(){s.submit({request:{s:["USERSELFSONLIST",[{page:t.page}]]},callback:function(e){l.default.success("加载完成",function(){console.log("more",e);var i=l.default.checkError(e,"s",function(t,e){l.default.err(e)});if(i.s.data&&i.s.data.length){console.log(i);var a=i.s.data;console.log("详细",t.list);var o=!0,r=!1,s=void 0;try{for(var d,c=(0,n.default)(a);!(o=(d=c.next()).done);o=!0){var u=d.value;t.list.push(u)}}catch(f){r=!0,s=f}finally{try{o||null==c.return||c.return()}finally{if(r)throw s}}i.s.data.length<10?t.loadingType=2:t.loadingType=0}else t.loadingType=2})},error:function(t){l.default.err("err",t,1)}})}})}},onLoad:function(){var t=this;s.submit({request:{s:["USERSELFSONLIST",[{page:t.page}]],sharenumber:["USERSELFSONCOUNT"],config:["APPLICATIONCONFIG"]},callback:function(e){var i=l.default.checkError(e,"s",function(t,i){uni.showToast({title:e.data.s.error,icon:"none"})});i.config&&i.config.qiniu_domain&&(t.qiniu=i.config.qiniu_domain),t.list=i.s.data,i.s.data.length<10?t.loadingType=2:t.loadingType=0,console.log("列表",i),i.sharenumber&&(t.sharenumber=i.sharenumber)},error:function(t){l.default.err("",t,1)}})},methods:{myQRcode:function(){uni.navigateTo({url:"../../pagesB/QRCode/QRCode"})}}};e.default=d},3069:function(t,e,i){var a=i("d34f");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("12af8360",a,!0,{sourceMap:!1,shadowMode:!1})},"357f":function(t,e,i){"use strict";var a=i("8af7"),n=i.n(a);n.a},"4a93":function(t,e,i){"use strict";i.r(e);var a=i("293d"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,function(){return a[t]})}(o);e["default"]=n.a},7492:function(t,e,i){"use strict";i.r(e);var a=i("da0b"),n=i("acea");for(var o in n)"default"!==o&&function(t){i.d(e,t,function(){return n[t]})}(o);i("357f");var r=i("2877"),l=Object(r["a"])(n["default"],a["a"],a["b"],!1,null,"14cbf4b0",null);e["default"]=l.exports},"861b":function(t,e,i){"use strict";i.r(e);var a=i("aa24"),n=i("4a93");for(var o in n)"default"!==o&&function(t){i.d(e,t,function(){return n[t]})}(o);i("23b5");var r=i("2877"),l=Object(r["a"])(n["default"],a["a"],a["b"],!1,null,"6b36c666",null);e["default"]=l.exports},"8af7":function(t,e,i){var a=i("ef63");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("d28d5d98",a,!0,{sourceMap:!1,shadowMode:!1})},aa24:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"conter"},[i("v-uni-view",{staticClass:"abox"},[i("v-uni-text",{staticClass:"a",staticStyle:{float:"left"}},[t._v("已分享 "+t._s(t.sharenumber)+" 人")]),i("v-uni-text",{staticClass:"a",staticStyle:{float:"right"},on:{click:function(e){e=t.$handleEvent(e),t.myQRcode()}}},[t._v("查看我的分享码")])],1),t._l(t.list,function(e,a){return i("v-uni-view",{key:a,staticClass:"detail-box"},[i("v-uni-view",{staticClass:"box-img"},[i("v-uni-image",{attrs:{src:t.qiniu+e.image_id}})],1),i("v-uni-view",{staticClass:"box-title"},[""!=e.nickname?i("v-uni-text",[t._v(t._s(e.nickname))]):i("v-uni-text",[t._v("昵称未填写")]),i("br"),i("v-uni-text",{staticStyle:{"margin-top":"15upx",color:"#666666","font-size":"23upx"}},[t._v(t._s(e.time))])],1),i("v-uni-view",{staticClass:"box-number"},[i("v-uni-text",[t._v(t._s(e.phone))])],1)],1)}),0==t.sharenumber?i("v-uni-view",{staticClass:"imageContent"},[i("v-uni-view",{staticClass:"imageOne"},[i("v-uni-image",{attrs:{src:"http://mp.emshop.eonfox.com/zrhzstatic/muying/invite.png",mode:""}})],1),i("v-uni-view",{staticClass:"imageTwo"},[t._v("...")]),i("v-uni-view",{staticClass:"imageOne"},[i("v-uni-image",{attrs:{src:"http://mp.emshop.eonfox.com/zrhzstatic/muying/order.png",mode:""}})],1),i("v-uni-view",{staticClass:"imageTwo"},[t._v("...")]),i("v-uni-view",{staticClass:"imageOne"},[i("v-uni-image",{attrs:{src:"http://mp.emshop.eonfox.com/zrhzstatic/muying/earnings.png",mode:""}})],1)],1):t._e(),0==t.sharenumber?i("v-uni-view",{staticClass:"titleContent"},[i("v-uni-view",{staticClass:"imageOne"},[t._v("邀请好友")]),i("v-uni-view",{staticClass:"imageTwo"}),i("v-uni-view",{staticClass:"imageOne"},[t._v("好友下单")]),i("v-uni-view",{staticClass:"imageTwo"}),i("v-uni-view",{staticClass:"imageOne"},[t._v("获得收益")])],1):t._e(),0!=t.sharenumber?i("uni-load-more",{attrs:{loadingType:t.loadingType,contentText:t.contentText}}):t._e()],2)},n=[];i.d(e,"a",function(){return a}),i.d(e,"b",function(){return n})},acea:function(t,e,i){"use strict";i.r(e);var a=i("1354"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,function(){return a[t]})}(o);e["default"]=n.a},d34f:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".conter[data-v-6b36c666]{width:100%;height:auto;font-size:%?28?%}.abox[data-v-6b36c666]{width:96%;height:%?80?%;background-color:rgba(242,155,135,.77);line-height:%?80?%;padding-left:2%;padding-right:2%}.a[data-v-6b36c666]{color:#fff;font-size:%?32?%}.detail-box[data-v-6b36c666]{width:98%;height:75px;font-size:15px;padding-left:2%;border-bottom:1px solid #eaeaea;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;background-color:#fbfbfb}.box-img[data-v-6b36c666]{width:17%;height:60px;float:left;padding-top:5px}.box-title[data-v-6b36c666]{width:30%;height:60px;float:left;padding-top:20px;padding-left:3%;color:#da6c3f;font-size:%?30?%}.box-number[data-v-6b36c666]{width:45%;float:left;height:60px;line-height:80px;text-align:center;color:#333}.box-label[data-v-6b36c666]{position:absolute;width:%?160?%;height:%?40?%;border:#f1f1f3 1px solid;right:%?5?%;text-align:center;border-radius:8px;background-color:#f1f1f3;font-size:%?23?%;line-height:%?40?%}.box-img uni-image[data-v-6b36c666]{width:%?120?%;height:%?120?%;border-radius:100%}.imageContent[data-v-6b36c666]{width:80%;height:%?100?%;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;padding-left:14%;margin-top:%?300?%}.titleContent[data-v-6b36c666]{width:80%;height:%?100?%;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;padding-left:14%;margin-top:%?40?%;font-size:%?26?%}.imageOne[data-v-6b36c666]{width:%?120?%;height:%?120?%;text-align:center;color:#de7d67}.imageOne uni-image[data-v-6b36c666]{width:%?120?%;height:%?120?%}.imageTwo[data-v-6b36c666]{width:%?100?%;height:%?100?%;line-height:%?120?%;text-align:center;color:rgba(242,155,135,.77)}",""])},da0b:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"load-more"},[i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:1===t.loadingType&&t.showImage,expression:"loadingType === 1 && showImage"}],staticClass:"loading-img"},[i("v-uni-view",{staticClass:"load1"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1),i("v-uni-view",{staticClass:"load2"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1),i("v-uni-view",{staticClass:"load3"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1)],1),i("v-uni-text",{staticClass:"loading-text",style:{color:t.color}},[t._v(t._s(0===t.loadingType?t.contentText.contentdown:1===t.loadingType?t.contentText.contentrefresh:t.contentText.contentnomore))])],1)},n=[];i.d(e,"a",function(){return a}),i.d(e,"b",function(){return n})},ef63:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".load-more[data-v-14cbf4b0]{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;-ms-flex-direction:row;flex-direction:row;height:%?80?%;-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center}.loading-img[data-v-14cbf4b0]{height:24px;width:24px;margin-right:10px}.loading-text[data-v-14cbf4b0]{font-size:%?28?%;color:#777}.loading-img>uni-view[data-v-14cbf4b0]{position:absolute}.load1[data-v-14cbf4b0],.load2[data-v-14cbf4b0],.load3[data-v-14cbf4b0]{height:24px;width:24px}.load2[data-v-14cbf4b0]{-webkit-transform:rotate(30deg);-ms-transform:rotate(30deg);transform:rotate(30deg)}.load3[data-v-14cbf4b0]{-webkit-transform:rotate(60deg);-ms-transform:rotate(60deg);transform:rotate(60deg)}.loading-img>uni-view uni-view[data-v-14cbf4b0]{width:6px;height:2px;border-top-left-radius:1px;border-bottom-left-radius:1px;background:#777;position:absolute;opacity:.2;-webkit-transform-origin:50%;-ms-transform-origin:50%;transform-origin:50%;-webkit-animation:load-data-v-14cbf4b0 1.56s ease infinite}.loading-img>uni-view uni-view[data-v-14cbf4b0]:first-child{-webkit-transform:rotate(90deg);-ms-transform:rotate(90deg);transform:rotate(90deg);top:2px;left:9px}.loading-img>uni-view uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-transform:rotate(180deg);top:11px;right:0}.loading-img>uni-view uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-transform:rotate(270deg);-ms-transform:rotate(270deg);transform:rotate(270deg);bottom:2px;left:9px}.loading-img>uni-view uni-view[data-v-14cbf4b0]:nth-child(4){top:11px;left:0}.load1 uni-view[data-v-14cbf4b0]:first-child{-webkit-animation-delay:0s;animation-delay:0s}.load2 uni-view[data-v-14cbf4b0]:first-child{-webkit-animation-delay:.13s;animation-delay:.13s}.load3 uni-view[data-v-14cbf4b0]:first-child{-webkit-animation-delay:.26s;animation-delay:.26s}.load1 uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-animation-delay:.39s;animation-delay:.39s}.load2 uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-animation-delay:.52s;animation-delay:.52s}.load3 uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-animation-delay:.65s;animation-delay:.65s}.load1 uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-animation-delay:.78s;animation-delay:.78s}.load2 uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-animation-delay:.91s;animation-delay:.91s}.load3 uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-animation-delay:1.04s;animation-delay:1.04s}.load1 uni-view[data-v-14cbf4b0]:nth-child(4){-webkit-animation-delay:1.17s;animation-delay:1.17s}.load2 uni-view[data-v-14cbf4b0]:nth-child(4){-webkit-animation-delay:1.3s;animation-delay:1.3s}.load3 uni-view[data-v-14cbf4b0]:nth-child(4){-webkit-animation-delay:1.43s;animation-delay:1.43s}@-webkit-keyframes load-data-v-14cbf4b0{0%{opacity:1}to{opacity:.2}}",""])}}]);