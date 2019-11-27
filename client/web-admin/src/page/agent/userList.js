av({

	id: 'page-agent-userList',
	include: ["src/common/content.js", "src/page/agent/userList/agentRegionOption.js","src/page/shop/orderDetails/checkArea.js"],
	extend: ["common-content"],
	'export': {
		template: "src/page/agent/userList.html"
	},
	'import': function(e) {
		this.template(e.template);
	},
	main: function() {
		var _this = this;
		var config = {
			search: {}
		};
		
		//搜索
		this.data.routerAnchorQuery('search', function(data) {
			data = av.decodeURL(data);
			config.search = JSON.parse(data);
		});

		//排序
		this.data.routerAnchorQuery('sort', function(data) {
			config.sort = [data];
		});

		//分页
		this.data.routerAnchorQuery('page', function(data) {
			config.page = data;
		});

		//状态
		this.data.routerAnchorQuery('state', function(data) {
			_this.data.state = data;
			config.search.state = data; //状态
		}, function() {
			_this.data.state = undefined;
		});

		this.data.request.list = ['AGENTADMINUSERLIST', [config]];
		this.data.request.agentRegionOption = ['AGENTADMINREGIONOPTION'];
		
		// 优利 区域列表
		if( this.data.applicationCheckYouli() ){
			this.data.request.areaList=['SHOPORDERSELFREGIONIDLIST'];
		}
		
	},
	event: {

		error: function(error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},

	},
	data: {
		request: {},
		state: undefined,
		list: {
			data: [],
		},
		search: [
			'user_id',
			'user_nickname',
			'user_phone',
			'agent_region_id',
			'agent_region_province',
			'agent_region_city',
			'agent_region_district',
		],

		//删除
		eventRemove: function(ele) {
			var ids = this.checkboxData('data-id');
			var _this = this;
			this.actionRemoveIds(ids, function() {

				var request_array = [];
				for(var i in ids) {
					request_array.push(["AGENTADMINUSERREMOVE", [{
						agent_user_id: ids[i]
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
		//优利 -- 拒绝 代理
		eventrefuse:function(agent_user_id){
			//提交数据
			   var _this=this
				_this.submit({
					method: "submit",
					request: ["AGENTADMINUSEREDIT", [{
						agent_user_id: agent_user_id,
						agent_user_state: 0,
					}]],
					error: function() {},
					success: function(bool) {
						
						//刷新页面
						av().compiler("reload").render().run();
							
					}
				});
		},
		//更新库存
		eventUpdateStock: function(ele, e, agent_user_id, agent_user_scoket) {
			var _this = this;
			layer.prompt({
				title: '更新库存',
				formType: 0, //0 单行文本框
				value: agent_user_scoket, //文本框输入值
				area: ['100px', '50px'] //自定义文本域宽高
			}, function(value, index) {
				try {
					if(!parseInt(value)) throw '库存必须是整数!';
				} catch(error) {
					layer.msg(error, {
						icon: 5,
						time: 2000
					});
					return;
				}
				//提交数据
				_this.submit({
					method: "submit",
					request: ["ADMINAGENTUSERREPLACESCOKET", [{
						agent_user_id: agent_user_id,
						scoket: parseInt(value)
					}]],
					error: function() {},
					success: function() {
						layer.close(index);
						av().run();
					}
				});
			});
		},

		/**
		 * 更新状态-审核失败
		 * 
		 * @param {Object} bool
		 */
		eventStateDefeated: function(ele, e) {
			var ids = this.checkboxData('data-id');
			var _this = this;

			this.actionPromptIds(ids, '请输入审核失败的原因', function(fail) {

				var request_array = [];
				for(var i in ids) {
					request_array.push(["AGENTADMINUSEREDIT", [{
						agent_user_id: ids[i],
						agent_user_state: 0,
						agent_user_fail: fail
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

		/**
		 * 更新状态-审核成功
		 * 
		 * @param {Object} void
		 */
		eventStateSucceed: function(ele, e) {
			var ids = this.checkboxData('data-id');
			var _this = this;

			this.actionStateIds(ids, function() {

				var request_array = [];
				for(var i in ids) {
					request_array.push(["AGENTADMINUSEREDIT", [{
						agent_user_id: ids[i],
						agent_user_state: 1
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

		projectAgentRegionOption: {},

		/**
		 * 易淘 申请代理审核成功
		 * 
		 * @param {Object} void
		 */
		eventYitaoStateSucceed: function(ele, e, userid, agentid) {
			var _this = this;

			layer.open({
				type: 1,
				shadeClose: true,
				shade: false,
				title: '通过代理申请',
				area: ['300px', '200px'], //自定义文本域宽高
				//content: $('#agentRegionOption') ,
				content: '<div id="agentRegionOption"></div>',
				btn: ["确定", "取消"],
				success: function() {
					//如果工程已存在，不再重复创建
					if(!_this.projectAgentRegionOption[userid]) {
						_this.projectAgentRegionOption[userid] = av('userList::agentRegionOption').new();
					}

					_this.projectAgentRegionOption[userid].clone({
						yitaoShenheAgentid: agentid,
						agentRegionOption: _this.agentRegionOption
					}).render('reload');

				},
				yes: function(index) {
					var form_input = {};
					form_input.agent_user_id = userid;
					form_input.agent_user_state = 1;
					form_input.agent_region_id = _this.projectAgentRegionOption[userid].data.yitaoShenheAgentid;
					console.log(form_input);
					try {} catch(error) {
						layer.msg(error, {
							icon: 5,
							time: 2000
						});
						return;
					}
					//提交数据
					_this.submit({

						method: "submit",
						request: ["AGENTADMINAGENTUSERAUDIT", [form_input]],
						success: function(bool) {
							if(bool) {
								layer.close(index);
								//刷新页面
								av().run();
							}
						}
					});
				},
				btn2: function(index) {
					layer.close(index);
				}

			});
		},
		/**
		 * 易淘 申请代理驳回
		 * 
		 * @param {Object} void
		 */
		eventYitaoStateFailed: function(ele, e, userid) {
			var _this = this;
			layer.prompt({
				title: '驳回申请原因',
				formType: 2, //0 单行文本框
				value: '', //文本框输入值
				area: ['300px', '150px'] //自定义文本域宽高
			}, function(value, index) {
				try {
					var tvalue = $.trim(value);
					if(tvalue == '') throw '必须填写驳回理由!';
				} catch(error) {
					layer.msg(error, {
						icon: 5,
						time: 2000
					});
					return;
				}
				//提交数据
				_this.submit({
					method: "submit",
					request: ["AGENTADMINAGENTUSERAUDIT", [{
						agent_user_id: userid,
						agent_user_state: 0,
						agent_user_fail: tvalue,
					}]],
					error: function() {},
					success: function() {
						layer.close(index);
						av().run();
					}
				});
			});
		},
		/*
	 优利 选择区域
	 * */
	projectArea: {},
	eventArea: function(agent_user_id) {
			var _this = this;
			console.log('agent_user_id',agent_user_id)
			layer.open({
				type: 1,
				shadeClose: true,
				shade: false,
				title: '选择代理地区',
				area: ['500px', '200px'], //自定义文本域宽高
				//content: $('#agentRegionOption') ,
				content: '<div id="checkArea"></div>',
				btn: ["确定", "取消"],
				success: function() {
					//如果工程已存在，不再重复创建
					if(!_this.projectArea[agent_user_id]) {
						_this.projectArea[agent_user_id] = av('orderDetails::checkArea').new();
					}
					_this.projectArea[agent_user_id].clone({
						areaList: _this.areaList,
					}).render('reload');
					console.log('_this.areaList',_this.areaList)
					av('orderDetails::checkArea')
				},
				yes: function(index) {
					var form_input = {};
					form_input.agent_region_id =  _this.projectArea[agent_user_id].data.shop_order_area;
					form_input.agent_user_id = agent_user_id;
					//agent_user_state  => 0(拒绝) 1（通过）
					form_input.agent_user_state = 1
					console.log(form_input);
					try {
						if(form_input.agent_region_id==null || form_input.agent_region_id==-1){throw '请选择区域!';}
					} catch(error) {
						layer.msg(error, {
							icon: 5,
							time: 2000
						});
						return;
					}
					//提交数据
					_this.submit({
						method: "submit",
						request: ["AGENTADMINUSEREDIT", [form_input]],
						success: function(bool) {
							if(bool) {
								layer.close(index);
								//刷新页面
								av().run();
							}
						}
					});
				},
				btn2: function(index) {
					layer.close(index);
				}

			});
		}

	}

});