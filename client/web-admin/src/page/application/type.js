av({
    id:'page-application-type',//工程ID
    // selector:'view',//筛选器，指定渲染到哪个父节点。默认是全局那个  BUG
    include : [
    	"src/common/content.js", 
			'src/page/application/type/add.js',
			'src/page/application/type/edit.js'
    	],
    extend : ["common-content"],//继承该js，只获取不继承无法获取该对象的属性
    'export' : {
		template: "src/page/application/type.html",
		},//引入模版，可以同时引入多个
    'import' : function(e){
    	// this.data.templateTest = e.template2;//绑定模版
        this.template(e.template);//绑定模版
    },
 
    //主函数
    main: function(){
    
			
    	var _this = this;
		var config = { search:{} };
    	//排序
		this.data.routerAnchorQuery('sort', function(data){
			config.sort = [data];
		});
    	
    	//搜索
		this.data.routerAnchorQuery('search', function(data){
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});
		
    	//分页
		this.data.routerAnchorQuery('page', function(data){
			config.page = data;
		});
    	
    	
		this.data.request.type_module_option = ["APPLICATIONADMINTYPEMODULEOPTION"];
		//分类将 商家ID 传进去   商家ID  注意前面要执行获取商家ID的方法
		this.data.request.type_option = ["APPLICATIONADMINTYPEOPTION", [{sort:["sort_asc"]}]];
		
		
		this.data.request.list = ["APPLICATIONADMINTYPELIST", [config]];
	if( config.search.type_parent_id ){
		this.data.request.type_parent_get = ["APPLICATIONADMINTYPEGET", [{type_id : config.search.type_parent_id}]];
	}else{
		this.data.request.type_parent_get = undefined;
		this.data.type_parent_get = undefined;
	}
		
	
// 	var request = {
// 		type_module_option:["APPLICATIONADMINTYPEMODULEOPTION"],
// 		type_option:["APPLICATIONADMINTYPEOPTION", [{sort:["sort_asc"]}]],
// 		list:["APPLICATIONADMINTYPELIST", [config]]
// 	};
// if( config.search.type_parent_id ){
// 	request.type_parent_get = ["APPLICATIONADMINTYPEGET", [{type_id : config.search.type_parent_id}]];
// }
    	
    },
    event: {
	
    },
    //数据对象
    data:{
		request: {},
		state: undefined,
		list: {
			data : [],
		},
		
		getModuleName: function(module){
			if( this.type_module_option ){
				var type_module_option = this.type_module_option;
				for(var i in type_module_option){
					if(module == i){
						return type_module_option[i];
					}
				}
			}else{
				return "未知";
			}
		},
		
		//排序
		eventClickSort: function(){
			var obj = this.inputData("sort");
			var request_array = [];
			request_array.push(["APPLICATIONADMINTYPEEDITCHECK"]);//第一个是判断是否有编辑权限
			for(var i in obj){
				request_array.push(["APPLICATIONADMINTYPEEDIT", [{type_id:obj[i].id, type_sort:obj[i].value}]]);
			}
			
			//提交数据
			this.submit({
				method:"edit",
				request:request_array,
				success:function(data){
					//刷新页面
					av().run();
				}
			});
			
		},
		
		
		//设置状态
		eventClickState: function(state){
			var ids = this.checkboxData('data-id');
			var _this = this;			
			this.actionStateIds(ids, function(){				
				var request_array = [];
				for(var i in ids){
					request_array.push(["APPLICATIONADMINTYPEEDIT", [{type_id:ids[i], type_state:state}]]);
				}				
				//提交数据
				_this.submit({					
					method:"list",
					request:request_array,
					success:function(bool){
						if(bool){
							//刷新页面
							av().compiler("reload").render().run();
						}
					}					
				});						
			});
		},
		//批量删除
		eventClickRemove: function(){
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionRemoveIds(ids, function(){
				var request_array = [];
				for(var i in ids){
					request_array.push(["APPLICATIONADMINTYPEREMOVE", [{type_id:ids[i]}]]);
				}
				//提交数据
				_this.submit({
					method:"list",
					request:request_array,
					success:function(bool){
						if(bool){
							//刷新页面
							av().compiler("reload").render().run();
						}
					}
				});
			});
		},


		



		//添加
		eventClickAdd:function(){
			var _this = this;
			var parent_data = null;
			//判断是否存在父级
			if(_this.type_parent_get && _this.type_parent_get.type_id){
				var parent_id = _this.type_parent_get.type_id;
				
				if( parent_id && _this.list && _this.list.data ){
					var list = _this.list.data;
					for(var i in list){
						if( list[i].type_id == parent_id ){
							parent_data = list[i];
							break;
						}
					}
					
					if( !parent_data && _this.type_parent_get &&
						parent_id == _this.type_parent_get.type_id){
						parent_data = _this.type_parent_get;
					}
				}
			}
			
			_this.actionAdd(parent_data);
		},
		//添加操作
		actionAdd: function(parent_data){
			var project = av('merchant-setting-type::add');
			//拷贝 将数据复制到新模版
			project.clone({
				merchant_id: this.merchant_id,
				type_option: this.type_option,
				parent_data: parent_data,
				type_module_option:this.type_module_option,
			});
			
			//备份原始的提交按钮函数
			var keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				project.data.eventSubmit();
			};
			layer.closeAll();
			var selector = 'add' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-plus\"></span> 添加分类",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 500 ? 500 : $(window).width()) + "px", ($(window).height() - 50) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function() {
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = keyupFunctions;
				},
				success: function(){
					//渲染这个插件
					project.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="merchant-setting-type::add"]').focus(); //获得焦点
				}
			});
			//成功的时候 回调
			project.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
			}
		},




		//添加子级
		eventClickAddSon: function(typeid,typemodule){
			var project = av('merchant-setting-type::add');
			//拷贝 将数据复制到新模版
			project.clone({
				merchant_id: this.merchant_id,
				type_option: this.type_option,
				parent_data: {type_id:typeid,type_module:typemodule},
				type_module_option:this.type_module_option,
			});
			
			//备份原始的提交按钮函数
			var keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				project.data.eventSubmit();
			};
			
			layer.closeAll();
			var selector = 'add' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-plus\"></span> 添加分类",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 500 ? 500 : $(window).width()) + "px", ($(window).height() - 50) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function() {
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = keyupFunctions;
				},
				success: function(){
					//渲染这个插件
					project.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="merchant-setting-type::add"]').focus(); //获得焦点
				}
			});
			//成功的时候 回调
			project.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
			}
		},
		//编辑
		ClickEdit:function(typeid){
			var project = av('merchant-setting-type::edit');
			var typeData=false;
			if(typeid=='parent'){
				typeData=this.type_parent_get;
			}else{
				for(var i in this.list.data){
					if(this.list.data[i].type_id==typeid){
						typeData=this.list.data[i];
						break;
					}
				}
			}
			
			if(!typeData){
				layer.msg('类型id异常，请刷新重试！')
				return
			}
			
			console.log('编辑子组件',typeData);
			//拷贝 将数据复制到新模版
			project.clone({
				merchant_id: this.merchant_id,
				type_option: this.type_option,
				typeData:typeData,
				type_module_option:this.type_module_option,
			});
			
			//备份原始的提交按钮函数
			var keyupFunctions = av('common-event').data.keyupFunctions['common-content'];
			//替换为引入工程的提交按钮函数
			av('common-event').data.keyupFunctions['common-content'] = function() {
				project.data.eventSubmit();
			};
			
			layer.closeAll();
			var selector = 'add' + Date.parse(new Date());
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-edit\"></span> 编辑分类",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 500 ? 500 : $(window).width()) + "px", ($(window).height() - 50) + 'px'], //宽高
				content: '<div id="' + selector + '"></div>',
				end: function() {
					//销毁提交按钮提交函数，还原备份
					av('common-event').data.keyupFunctions['common-content'] = keyupFunctions;
				},
				success: function(){
					//渲染这个插件
					project.compiler("reload").selector('#' + selector).render("refresh");
					$('[input-focus="merchant-setting-type::edit"]').focus(); //获得焦点
				}
			});
			//成功的时候 回调
			project.data.successSubmitCallback = function(){
				av().run();
				layer.closeAll();
			}
		}
		
	}
});