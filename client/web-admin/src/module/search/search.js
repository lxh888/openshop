av({

	id: 'module-search',
	include: ['src/module/citypicker/citypicker.js'],
	selector: '[module="search"]',
	'export': {
		template: "src/module/search/search.html",
	},
	'import': function (e) {
		this.template(e.template);
	},

	event: {

		renderStart: function () {
			console.log('加载search');
			//console.log('module-search  设置data');
			var _router = av.router();
			var search = (function () { try { return _router.anchor.query.search; } catch (e) { return false; } }());
			if (search) {
				search = av.decodeURL(search);
				this.data.data = JSON.parse(search);

				for (var i in this.data.show) {
					if (typeof this.data.data[this.data.show[i]] == 'undefined') {
						this.data.data[this.data.show[i]] = '';
					}

				}
			} else {
				this.data.data = {};
			}

			
		},
		renderEnd: function () {
			//调用layer的选择时间插件
			//开始时间
			laydate.render({
				elem: '[name="date_start"]'
				, type: 'date'
				, theme: '#337ab7'
			});
			//结束时间
			laydate.render({
				elem: '[name="date_end"]'
				, type: 'date'
				, theme: '#337ab7'
			});

			//var _this = this;
			var defaultLabel = ['四川省', '绵阳市', '游仙区'];
			if( typeof this.data.data.district != 'undefined'){
				
				defaultLabel = [	this.data.data.province , this.data.data.city , 	this.data.data.district];
				console.log(11111, defaultLabel);
			}

			av('module-citypicker').data.defaultLabel(defaultLabel);
		
			this.data.initSelectedScope();
			av('module-citypicker').render("refresh");//渲染 城市选择器
		},
		loadEnd: function () {
			//this.render("refresh");//重新渲染

			//判断一下 selectedScope
			
			
		}
		/*renderEnd: function(){
			var _this = this;
			//按回车键时提交
		$(document).unbind("keyup").on('keyup', function(e){
			if(e.keyCode === 13){
				if( $("textarea").is(":focus") ){  
							return false;
					}
							_this.data.submit();
			}
		});
		}*/


	},

	data: {

		//模板
		template: '',
		//显示列表
		showList: [],
		showData: {},

		//数据
		data: {},

		loadStart: false,
		selectedScope: 3,
		//选中的范围
		eventSelectedScope: function (ele, e) {
			this.selectedScope = $(ele).val();
			this.initSelectedScope();
			av('module-citypicker').render();//渲染 城市选择器
			console.log('eventSelectedScope', $(ele).val());
		},

		/**
		 * 初始化
		 */
		initSelectedScope: function () {

			if (this.selectedScope == 1) {
				console.log(121);
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = false;
				av('module-citypicker').data.areaShow = false;
			}

			if (this.selectedScope == 2) {
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = true;
				av('module-citypicker').data.areaShow = false;
			}

			if (this.selectedScope == 3) {
				av('module-citypicker').data.provinceShow = true;
				av('module-citypicker').data.cityShow = true;
				av('module-citypicker').data.areaShow = true;
			}
		},

		agentRangeSelectState:true,
		agentRangeState:false,
		isAgentRangeState: function(){
			this.agentRangeState = true;
			console.log('isAgentRangeState', this.agentRangeState);
		},

		eventClickAgentRangeSelect:function(ele){
			console.log($(ele).context.checked);
			if( $(ele).context.checked ){
				this.agentRangeSelectState = true;
			}
			else{
				this.agentRangeSelectState = false;
			}

			console.log(123456, this.agentRangeSelectState);
		},

		//提交数据
		submit: function () {
			console.log('module-search submit');
			var _router = av.router();
			if (!_router.anchor.query) _router.anchor.query = {};

			var search = {};
			for (var i in this.showList) {
				var v = $.trim($('[module="search"]').find('[name="' + this.showList[i] + '"]').val());
				if (v == "") {
					continue;
				}
				search[this.showList[i]] = v;
			}
			if ( this.agentRangeSelectState && this.agentRangeState ) {
				search.province = av('module-citypicker').data.provinceLabel;
				search.city = av('module-citypicker').data.cityLabel;
				search.district = av('module-citypicker').data.areaLabel;
			}
			//console.log( search );

			search = JSON.stringify(search);
			if (search == "{}") {
				if (_router.anchor.query.search) delete _router.anchor.query.search;
			} else {
				_router.anchor.query.search = av.encodeURL(search);
			}

			//console.log( av.router(_router) );

			av.router(_router).request();
			layer.closeAll();
		},

		//清理数据
		clear: function () {
			for (var i in this.showList) {
				$('[module="search"]').find('[name="' + this.showList[i] + '"]').val('');
			}
			this.data = {};
			this.submit();
		}

	}


});