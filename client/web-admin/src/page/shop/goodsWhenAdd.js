av({
    id:'page-shop-goodsWhenAdd',//工程ID
    include : ["src/common/content.js"],//获取js文件
    extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
    'export' : {
		template: "src/page/shop/goodsWhenAdd.html",
		},//引入模版，可以同时引入多个
    'import' : function(e){
    	// this.data.templateTest = e.template2;//绑定模版
        this.template(e.template);//绑定模版
    },
    main: function(){
        
    },
    event: {
	renderEnd: function(){
			//调用layer的选择时间插件
			laydate.render({
				elem: '[name="shop_goods_when_time"]'
				,type: 'datetime'
				,theme: '#337ab7'
				,range: '~'
			});
		},
	
    },
    data:{
    	request: {},
		state: undefined,
		list: {
			data : [],
		},
		submitLock:false,
		//按回车键时提交
		keyupFunction: function(){
			this.eventSubmit();
		},
		//添加商品
		eventSubmit: function(){
			
			var _this=this
			_this.submitLock=true;
			var  time= $.trim($('[name="shop_goods_when_time"]').val());
			var  arr=[]
			arr=time.split(" ~ ");
			console.log('time',time)
			
			var form_input = {};
			form_input.shop_goods_id = $.trim($('[name="shop_goods_id"]').val());
			form_input.shop_goods_when_start_time = arr[0];
			form_input.shop_goods_when_end_time = arr[1];
			form_input.shop_goods_when_name = $.trim($('[name="shop_goods_when_name"]').val());
			form_input.shop_goods_when_info = $.trim($('[name="shop_goods_when_info"]').val());
			form_input.shop_goods_when_sort = $.trim($('[name="shop_goods_when_sort"]').val());
			
			try {
				if(form_input.shop_goods_id == '') throw "商品id不能为空";
				if(form_input.shop_type_sort == ""){
					delete form_input.shop_type_sort;
				}
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        _this.submitLock=false;
		        return false;
		    }
			
			//提交数据
			this.submit({
				method:"submit",
				request:["SHOPADMINGOODSWHENADD", [form_input]],
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					_this.submitLock=false;
					av().run();
				}
			});
		}
    }
});