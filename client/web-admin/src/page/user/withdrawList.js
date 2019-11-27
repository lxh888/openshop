av({

	id: 'page-user-withdrawList',
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': {
		template: "src/page/user/withdrawList.html",
		mt:"src/page/user/withdrawList/message.html"
	},
	'import': function(e) {
		this.template(e.template);
		this.data.eventMessageTemplate = e.mt;
	},
	main: function() {
		var _this = this;
		var config = {
			search: {}
		};

		//排序
		this.data.routerAnchorQuery('sort', function(data) {
			config.sort = [data];
		});
		//状态
		this.data.routerAnchorQuery('state', function(data) {
			_this.data.state = data;
			config.search.state = data; //状态
		}, function() {
			_this.data.state = undefined;
		});
		//分页
		this.data.routerAnchorQuery('page', function(data) {
			config.page = data;
		});

		this.data.request.list = ['USERADMINWITHDRAWLIST', [config]];

	},
	event: {

		error: function(error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},

	},
	data: {
		request: {},
		list: {
			data: [],
		},
		state: undefined,

		//成功
		eventPass: function(ele, e) {
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionStateIds(ids, function() {

				var request_array = [];
				for(var i in ids) {
					request_array.push(["USERADMINWITHDRAWPASS", [{
						user_withdraw_id: ids[i]
					}]]);
				}

				//提交数据
				_this.submit({

					method: "list",
					request: request_array,
					success: function(bool) {
						if(bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}

				});

			});

		},
		//失败
		eventFail: function(ele, e) {
			var ids = this.checkboxData('data-id');
			var _this = this;

			this.actionPromptIds(ids, '请输入审核失败的原因', function(fail) {

				var request_array = [];
				for(var i in ids) {
					request_array.push(["USERADMINWITHDRAWFAIL", [{
						user_withdraw_id: ids[i],
						user_withdraw_fail_info: fail
					}]]);
				}

				//提交数据
				_this.submit({

					method: "list",
					request: request_array,
					success: function(bool) {
						if(bool) {
							//刷新页面
							av().compiler("reload").render().run();
						}
					}

				});

			});

		},
		
		eventMessageTemplate:'',
		eventMessage:function(ele, e, Key) {
			var _this = this
			var jjson = _this.list.data[Key].user_json
		    jjson = JSON.parse(jjson)
			console.log('jjson',jjson)
			
			layer.closeAll();
			var compiler = av.compiler(this.eventMessageTemplate);
			var content = compiler.render({data:jjson});
			
			console.log(content);
			
			//页面层
			var layerid = layer.open({
				title: "<span class=\"glyphicon glyphicon-edit\"></span> 查看提现信息",
				type: 1,
				//offset: '0',
				//area: ["500px", '566px'], //宽高
				area: [($(window).width() > 300 ? 500 : $(window).width()) + "px", ($(window).height() - 100) + 'px'], //宽高
				content: $(content).html(),
				success: function() {
				}
			});
		}

	}

});