(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesB-newList-newList"],{"071e":function(t,e,i){"use strict";var a=i("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("7492")),o=a(i("6d58")),s=a(i("c997")),d=a(i("d148")),l=new s.default,r={name:"news",data:function(){return{newslist:[],newsId:"",newsListID:"",address:"",page:10,loadingType:0,contentText:{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"},status:"",displayl:!1,keywords:""}},onLoad:function(t){console.log("消息列表",t),this.keywords=t.searchName,this.newsListID=t.newsId,this.load()},components:{uniIcon:o.default,uniLoadMore:n.default},onReachBottom:function(){if(0===this.loadingType){this.loadingType=1;var t=this;uni.showLoading({title:"正在加载",success:function(){t.load()}})}},methods:{load:function(){var t=this;t.page=t.page+10,l.submit({request:{s:["CMSARTICLELIST",[{search:{type_id:t.newsListID,keywords:t.keywords},size:t.page}]],config:["APPLICATIONCONFIG"]},callback:function(e){console.log(e),d.default.checkError(e,["s","config"],function(t,e){uni.showToast({title:e,icon:"none"})}),t.address=e.data.config.data.qiniu_domain,e.data.s.data.data&&(t.newslist=e.data.s.data.data,t.newslist.length<10&&(t.loadingType=2)),0==t.newslist.length?(t.status="暂无数据",t.displayl=!1):t.displayl=!0,console.log("输出",t.newslist),console.log("输出",t.address)},error:function(t){uni.showToast({title:"出错啦",icon:"none"})}})},goDetails:function(t){var e=t.currentTarget.dataset.businessid;console.log("目标",e),uni.navigateTo({url:"../../pagesA/InformationDetails/InformationDetails?newsId="+e})}}};e.default=r},"12f7":function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".content[data-v-716d66f4]{width:96%;padding-left:2%;padding-right:2%;height:auto;font-size:%?24?%}.newsContent[data-v-716d66f4]{width:100%;height:%?170?%;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;overflow:hidden;padding-top:%?10?%;padding-bottom:%?10?%;border-bottom:#e3e3e3 solid %?1?%}.newsContentBox[data-v-716d66f4]{width:%?150?%;height:%?150?%;margin-top:%?10?%;margin-right:%?8?%}.newsContentBox uni-image[data-v-716d66f4]{width:100%;height:100%}.newsContentText[data-v-716d66f4]{\n\t\t/* width: 580upx; */height:%?150?%;width:100%}.newsContentTime[data-v-716d66f4]{width:100%;height:%?50?%;color:#555}.newsContentTextTitle[data-v-716d66f4]{width:100%;height:%?80?%;font-size:%?30?%;overflow:hidden;margin-top:%?30?%;color:#444}.newHidden[data-v-716d66f4]{float:left;width:70%;overflow:hidden;-o-text-overflow:ellipsis;text-overflow:ellipsis;height:%?30?%;white-space:nowrap}.newsData[data-v-716d66f4]{float:right;width:%?130?%;height:%?50?%}",""])},1354:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("c5f6");var a={name:"load-more",props:{loadingType:{type:Number,default:0},showImage:{type:Boolean,default:!0},color:{type:String,default:"#777777"},contentText:{type:Object,default:function(){return{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"}}}},data:function(){return{}}};e.default=a},"357f":function(t,e,i){"use strict";var a=i("8af7"),n=i.n(a);n.a},"4cd7":function(t,e,i){"use strict";var a=i("7014"),n=i.n(a);n.a},"54e8":function(t,e,i){"use strict";i.r(e);var a=i("071e"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,function(){return a[t]})}(o);e["default"]=n.a},7014:function(t,e,i){var a=i("12f7");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("08dac501",a,!0,{sourceMap:!1,shadowMode:!1})},7492:function(t,e,i){"use strict";i.r(e);var a=i("da0b"),n=i("acea");for(var o in n)"default"!==o&&function(t){i.d(e,t,function(){return n[t]})}(o);i("357f");var s=i("2877"),d=Object(s["a"])(n["default"],a["a"],a["b"],!1,null,"14cbf4b0",null);e["default"]=d.exports},"8af7":function(t,e,i){var a=i("ef63");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("d28d5d98",a,!0,{sourceMap:!1,shadowMode:!1})},a73f:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"content"},[t._l(t.newslist,function(e){return i("v-uni-view",{staticClass:"newsContent",attrs:{"data-Businessid":e.id},on:{click:function(e){e=t.$handleEvent(e),t.goDetails(e)}}},[e.image_main[0]?i("v-uni-view",{staticClass:"newsContentBox"},[i("v-uni-image",{attrs:{src:t.address+e.image_main[0].image_id,mode:"aspectFill"}})],1):t._e(),i("v-uni-view",{staticClass:"newsContentText"},[i("v-uni-view",{staticClass:"newsContentTextTitle"},[i("v-uni-text",[t._v(t._s(e.name))])],1),i("v-uni-view",{staticClass:"newsContentTime"},[i("v-uni-view",{staticClass:"newHidden"},[t._v(t._s(e.info))]),i("v-uni-view",{staticClass:"newsData",staticStyle:{float:"right"}},[t._v(t._s(e.time))])],1)],1)],1)}),t.status?i("v-uni-view",{staticStyle:{width:"100%","text-align":"center","font-size":"32upx",color:"#B2B2B2"}},[t._v(t._s(t.status))]):t._e(),t.displayl?i("uni-load-more",{attrs:{loadingType:t.loadingType,contentText:t.contentText}}):t._e()],2)},n=[];i.d(e,"a",function(){return a}),i.d(e,"b",function(){return n})},acea:function(t,e,i){"use strict";i.r(e);var a=i("1354"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,function(){return a[t]})}(o);e["default"]=n.a},d31b:function(t,e,i){"use strict";i.r(e);var a=i("a73f"),n=i("54e8");for(var o in n)"default"!==o&&function(t){i.d(e,t,function(){return n[t]})}(o);i("4cd7");var s=i("2877"),d=Object(s["a"])(n["default"],a["a"],a["b"],!1,null,"716d66f4",null);e["default"]=d.exports},da0b:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"load-more"},[i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:1===t.loadingType&&t.showImage,expression:"loadingType === 1 && showImage"}],staticClass:"loading-img"},[i("v-uni-view",{staticClass:"load1"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1),i("v-uni-view",{staticClass:"load2"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1),i("v-uni-view",{staticClass:"load3"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1)],1),i("v-uni-text",{staticClass:"loading-text",style:{color:t.color}},[t._v(t._s(0===t.loadingType?t.contentText.contentdown:1===t.loadingType?t.contentText.contentrefresh:t.contentText.contentnomore))])],1)},n=[];i.d(e,"a",function(){return a}),i.d(e,"b",function(){return n})},ef63:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".load-more[data-v-14cbf4b0]{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;-ms-flex-direction:row;flex-direction:row;height:%?80?%;-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center}.loading-img[data-v-14cbf4b0]{height:24px;width:24px;margin-right:10px}.loading-text[data-v-14cbf4b0]{font-size:%?28?%;color:#777}.loading-img>uni-view[data-v-14cbf4b0]{position:absolute}.load1[data-v-14cbf4b0],.load2[data-v-14cbf4b0],.load3[data-v-14cbf4b0]{height:24px;width:24px}.load2[data-v-14cbf4b0]{-webkit-transform:rotate(30deg);-ms-transform:rotate(30deg);transform:rotate(30deg)}.load3[data-v-14cbf4b0]{-webkit-transform:rotate(60deg);-ms-transform:rotate(60deg);transform:rotate(60deg)}.loading-img>uni-view uni-view[data-v-14cbf4b0]{width:6px;height:2px;border-top-left-radius:1px;border-bottom-left-radius:1px;background:#777;position:absolute;opacity:.2;-webkit-transform-origin:50%;-ms-transform-origin:50%;transform-origin:50%;-webkit-animation:load-data-v-14cbf4b0 1.56s ease infinite}.loading-img>uni-view uni-view[data-v-14cbf4b0]:first-child{-webkit-transform:rotate(90deg);-ms-transform:rotate(90deg);transform:rotate(90deg);top:2px;left:9px}.loading-img>uni-view uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-transform:rotate(180deg);top:11px;right:0}.loading-img>uni-view uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-transform:rotate(270deg);-ms-transform:rotate(270deg);transform:rotate(270deg);bottom:2px;left:9px}.loading-img>uni-view uni-view[data-v-14cbf4b0]:nth-child(4){top:11px;left:0}.load1 uni-view[data-v-14cbf4b0]:first-child{-webkit-animation-delay:0s;animation-delay:0s}.load2 uni-view[data-v-14cbf4b0]:first-child{-webkit-animation-delay:.13s;animation-delay:.13s}.load3 uni-view[data-v-14cbf4b0]:first-child{-webkit-animation-delay:.26s;animation-delay:.26s}.load1 uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-animation-delay:.39s;animation-delay:.39s}.load2 uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-animation-delay:.52s;animation-delay:.52s}.load3 uni-view[data-v-14cbf4b0]:nth-child(2){-webkit-animation-delay:.65s;animation-delay:.65s}.load1 uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-animation-delay:.78s;animation-delay:.78s}.load2 uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-animation-delay:.91s;animation-delay:.91s}.load3 uni-view[data-v-14cbf4b0]:nth-child(3){-webkit-animation-delay:1.04s;animation-delay:1.04s}.load1 uni-view[data-v-14cbf4b0]:nth-child(4){-webkit-animation-delay:1.17s;animation-delay:1.17s}.load2 uni-view[data-v-14cbf4b0]:nth-child(4){-webkit-animation-delay:1.3s;animation-delay:1.3s}.load3 uni-view[data-v-14cbf4b0]:nth-child(4){-webkit-animation-delay:1.43s;animation-delay:1.43s}@-webkit-keyframes load-data-v-14cbf4b0{0%{opacity:1}to{opacity:.2}}",""])}}]);