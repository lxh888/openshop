av({

	id: "page-application-config",
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': { template: "src/page/application/config.html" },
	'import': function (e) {
		this.template(e.template);
	},
	main: function () {
		//请求全部配置信息
		this.data.request.data = ["APPLICATIONADMINCONFIGDATA"];
	},
	event: {
		error: function (error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/').request();
		},
		loadEnd: function () {
			//初始化一下
			if (this.data.checkedIndex && this.data.data) {
				this.data.circleConfigObject(this.data.checkedIndex);
			}
		}
	},
	data: {

		request: {},
		data: null,
		appCreditsConfig: {},
		//选中的分类数据
		checkedData: {},
		//选中的数据的id
		checkedIndex: '',
		//按回车键时提交
		keyupFunction: function () {
			if (this.checkedIndex) {
				this.eventSubmit(null, null, this.checkedIndex);
			}

		},
		//循环分类对象返回给data里的checkedData
		circleConfigObject: function (config_id) {
			for (var i in this.data) {
				if (this.data[i].config_id == config_id) {
					this.checkedData = this.data[i];
					break;
				}
			}
		}
		,
		//当选择类型时 参数1节点对象，参数2事件对象，参数3自定义 此处为分类对象的id
		eventSelectConfigType: function (ele, e, config_id) {
			//将分类数据赋给选中对象
			this.checkedIndex = config_id;
			this.circleConfigObject(config_id);
			//this.checkedData = this.data[ind];
		},
		submitLock: false,
		eventSubmit: function (ele, e, config_id) {
			console.log('config_id',config_id);
			if (this['submit_' + config_id]) {
				this['submit_' + config_id]();
			}
		},
		//安卓app提交
		submit_app_android_version: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = {};
			form_input.name = $.trim($('[name="name"]').val());
			form_input.info = $.trim($('[name="info"]').val());
			form_input.number = $.trim($('[name="number"]').val());
			form_input.download = $.trim($('[name="download"]').val());
			form_input.required = $('[name="required"]').is(':checked') ? 1 : 0;

			try {
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ app_android_version: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
		},
		//微信H5、公众号提交
		submit_weixin_mp_access: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = {};
			form_input.id = $.trim($('[name="id"]').val());
			form_input.secret = $.trim($('[name="secret"]').val());

			try {
				// if(form_input.id =='') throw '请填写appid！';
				// if(form_input.secret =='') throw '请填写appsecret！';
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ weixin_mp_access: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
		},
		//微信小程序提交
		submit_weixin_applet_access: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = {};
			form_input.id = $.trim($('[name="id"]').val());
			form_input.secret = $.trim($('[name="secret"]').val());

			try {
				// if(form_input.id =='') throw '请填写appid！';
				// if(form_input.secret =='') throw '请填写appsecret！';
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ weixin_applet_access: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
		},
		//微信app提交
		submit_weixin_app_access: function () {
			var _this = this;
			if (_this.submitLock) {
			} else {
				_this.submitLock = true;
			}

			var form_input = {};
			form_input.id = $.trim($('[name="id"]').val());
			form_input.secret = $.trim($('[name="secret"]').val());

			try {
				// if(form_input.id =='') throw '请填写appid！';
				// if(form_input.secret =='') throw '请填写appsecret！';
			}
			catch (err) {
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ weixin_app_access: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
		},
		//e麦商城特殊配置
		submit_emshop_config: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			//读取积分配置
			var creditsConfig = this.applicationCreditConfig();
			var form_input = { daily_attendance: {}, register_credit: {}, register_coupon: {} };
			form_input.daily_attendance.user = $.trim($('[name="daily_attendance_user"]').val()) ;
			form_input.daily_attendance.member = $.trim($('[name="daily_attendance_member"]').val()) ;
			form_input.register_credit.user = $.trim($('[name="register_credit_user"]').val()) ;
			form_input.register_credit.member = $.trim($('[name="register_credit_member"]').val()) ;
			form_input.register_coupon.state = $('[name="register_coupon_state"]').is(':checked') ? 1 : 0;
			form_input.register_coupon.keyword = $.trim($('[name="register_coupon_keyword"]').val());
			try {
				var dot = form_input.daily_attendance.user.toString().indexOf(".");
				// console.log(dot);
				if (!this.checkDecimalDoint(form_input.daily_attendance.user, creditsConfig.precision)) {
					throw "用户签到奖励积分小数位不能超过" + creditsConfig.precision + "位！";
				}
				if (!this.checkDecimalDoint(form_input.daily_attendance.member, creditsConfig.precision)) {
					throw "用会员签到奖励积分小数位不能超过" + creditsConfig.precision + "位！";
				}

				if (!this.checkDecimalDoint(form_input.register_credit.user, creditsConfig.precision)) {
					throw "用户推荐奖励积分小数位不能超过" + creditsConfig.precision + "位！";
				}
				if (!this.checkDecimalDoint(form_input.register_credit.member, creditsConfig.precision)) {
					throw "会员推荐奖励积分小数位不能超过" + creditsConfig.precision + "位！";
				}

			}
			catch (err) {
				console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			form_input.daily_attendance.user=(form_input.daily_attendance.user*creditsConfig.scale).toFixed(0);
			form_input.daily_attendance.member=(form_input.daily_attendance.member*creditsConfig.scale).toFixed(0);
			form_input.register_credit.user=(form_input.register_credit.user*creditsConfig.scale).toFixed(0);
			form_input.register_credit.member=(form_input.register_credit.member*creditsConfig.scale).toFixed(0);
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ emshop_config: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
		},
		//分销配置
		submit_shop_distribution_reward: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = { member: { rmb_award: [] }, shop_manager: { rmb_award: [] }, chief_inspector: { rmb_award: [] }, area_agent: { rmb_award: [] } };
			var precisionError = {};
			//循环各用户角色的奖金发放等级，将第一个不合法的数据信息放入到精度错误中去
			$('[name="member_rmb_award"]').each(
				function () {
					if(!_this.checkDecimalDoint($.trim($(this).val()),2) && JSON.stringify(precisionError) == '{}'){
						precisionError.name = '会员';
						precisionError.index = '第' + (parseInt(form_input.member.rmb_award.length)+1) + '级';
					}
					form_input.member.rmb_award.push(($.trim($(this).val()) * 100).toFixed(0));
				}
			);
			$('[name="shop_manager_rmb_award"]').each(
				function () {
					if(!_this.checkDecimalDoint($.trim($(this).val()),2) && JSON.stringify(precisionError) == '{}'){
						precisionError.name = '店长';
						precisionError.index = '第' + (parseInt(form_input.shop_manager.rmb_award.length)+1) + '级';
					}
					form_input.shop_manager.rmb_award.push(($.trim($(this).val()) * 100).toFixed(0));
				
				}
			);
			$('[name="chief_inspector_rmb_award"]').each(
				function () {
					if(!_this.checkDecimalDoint($.trim($(this).val()),2) && JSON.stringify(precisionError) == '{}'){
						precisionError.name = '总监';
						precisionError.index = '第' + (parseInt(form_input.chief_inspector.rmb_award.length)+1) + '级';
					}
					form_input.chief_inspector.rmb_award.push(($.trim($(this).val()) * 100).toFixed(0));

				
				}
			);
			$('[name="area_agent_rmb_award"]').each(
				function () {
					if(!_this.checkDecimalDoint($.trim($(this).val()),2) && JSON.stringify(precisionError) == '{}'){
						precisionError.name = '区域代理';
						precisionError.index = '第' + (parseInt(form_input.area_agent.rmb_award.length)+1) + '级';
					}
					form_input.area_agent.rmb_award.push(($.trim($(this).val()) * 100).toFixed(0));

				
				}
			);
			form_input.member.name = this.checkedData.config_value.member.name;
			form_input.member.admin_id = this.checkedData.config_value.member.admin_id;
			form_input.shop_manager.name = this.checkedData.config_value.shop_manager.name;
			form_input.shop_manager.admin_id = this.checkedData.config_value.shop_manager.admin_id;
			form_input.chief_inspector.name = this.checkedData.config_value.chief_inspector.name;
			form_input.chief_inspector.admin_id = this.checkedData.config_value.chief_inspector.admin_id;
			form_input.area_agent.name = this.checkedData.config_value.area_agent.name;
			form_input.area_agent.admin_id = this.checkedData.config_value.area_agent.admin_id;

			form_input.area_agent.additional_rewards = $.trim($('[name="additional_rewards"]').val());

			form_input.is_open = $('[name="is_open"]').is(':checked') ? 1 : 0;

			try {
				if (precisionError.name && precisionError != '{}')
					throw '赏金小数位不能超过两位！请调整' + precisionError.name + precisionError.index + '赏金的小数位数!';
				if (!_this.checkDecimalDoint(form_input.area_agent.additional_rewards,2))
					throw '额外奖金小数位不能超过2位！'
			}
			catch (err) {
				//console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			form_input.area_agent.additional_rewards = ($.trim($('[name="additional_rewards"]').val()) * 100).toFixed(0);

			//console.log(form_input);
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ shop_distribution_reward: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});
		},
		checkDecimalDoint: function (str, point) {
			var dot = str.toString().indexOf(".");
			var dotCnt = str.toString().substring(dot + 1, str.length);
			if (dot > -1 && dotCnt.length > point)
				return false;
			else
				return true;
		},
		//推荐商品奖励发放 
		submit_shop_recommend_reward: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}

			var form_input = { shop_manager: {}, chief_inspector: {}, area_agent: {} };
			form_input.shop_manager.royalty = $.trim($('[name="shop_manager_royalty"]').val());
			form_input.chief_inspector.royalty = $.trim($('[name="chief_inspector_royalty"]').val());
			form_input.area_agent.royalty = $.trim($('[name="area_agent_royalty"]').val());
			//转数字
			form_input.method = $.trim($('[name="method"]').val());
			form_input.max_royalty_random = $.trim($('[name="max_royalty_random"]').val());
			form_input.min_royalty_random = $.trim($('[name="min_royalty_random"]').val());
			form_input.quota_recommend_money = $.trim($('[name="quota_recommend_money"]').val()) ;
			form_input.is_open = $('[name="is_open"]').is(':checked') ? 1 : 0;
			form_input.shop_manager.name = this.checkedData.config_value.shop_manager.name;
			form_input.shop_manager.admin_id = this.checkedData.config_value.shop_manager.admin_id;
			form_input.chief_inspector.name = this.checkedData.config_value.chief_inspector.name;
			form_input.chief_inspector.admin_id = this.checkedData.config_value.chief_inspector.admin_id;
			form_input.area_agent.name = this.checkedData.config_value.area_agent.name;
			form_input.area_agent.admin_id = this.checkedData.config_value.area_agent.admin_id;
			try {
				if (form_input.shop_manager.royalty.toString().indexOf(".") > -1)
					throw '奖励比例不能有小数！';
				if (form_input.chief_inspector.royalty.toString().indexOf(".") > -1)
					throw '奖励比例不能有小数！';
				if (form_input.area_agent.royalty.toString().indexOf(".") > -1)
					throw '奖励比例不能有小数！';
				if (form_input.shop_manager.royalty > 100 || form_input.shop_manager.royalty > 100 || form_input.area_agent.royalty > 100 ||
					form_input.shop_manager.royalty < 0 || form_input.shop_manager.royalty < 0 || form_input.area_agent.royalty < 0)
					throw '奖励比例不能大于100或者小于0!';

				if (form_input.max_royalty_random > 100 || form_input.min_royalty_random > 100 || form_input.max_royalty_random < 0 || form_input.min_royalty_random < 0)
					throw '随机赏金比例不能大于100或者小于0!';
				if (form_input.max_royalty_random != '' && form_input.min_royalty_random != "" && parseInt(form_input.max_royalty_random) < parseInt(form_input.min_royalty_random))
					throw '最小随机赏金比例不能大于最大随机赏金比例!';
				
					if (!this.checkDecimalDoint(form_input.quota_recommend_money, 2)) {
						throw '定额赏金发放小数位不能超过两位！';
					}

			}
			catch (err) {
				//console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			form_input.quota_recommend_money=(form_input.quota_recommend_money*100).toFixed(0);
			console.log(form_input);
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ shop_recommend_reward: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});

		},
		//易淘商品购买门槛提成设置
		submit_shop_distribution_yitao: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = { shop_manager_reward: {} };

			form_input.shop_manager_reward.one_level_royal = $.trim($('[name="one_level_royal"]').val());
			form_input.shop_manager_reward.two_level_royal = $.trim($('[name="two_level_royal"]').val());
			form_input.shop_manager_reward.region_money = $.trim($('[name="region_money"]').val());
			form_input.is_open = $('[name="is_open"]').is(':checked') ? 1 : 0;
			try {
				if (form_input.shop_manager_reward.one_level_royal == '' || form_input.shop_manager_reward.two_level_royal == '') { throw '门槛商品提成不能为空'; }
				if (form_input.shop_manager_reward.region_money == '') { throw '区域管理费不能为空'; }
				if (!this.checkDecimalDoint(form_input.shop_manager_reward.one_level_royal, 2) ||
					!this.checkDecimalDoint(form_input.shop_manager_reward.two_level_royal, 2)) { throw '门槛商品提成小数点不能超过两位！'; }
				if (!this.checkDecimalDoint(form_input.shop_manager_reward.region_money, 2)) {
					throw '区域管理费小数点不能超过两位！';
				}

			}
			catch (err) {
				//console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			form_input.shop_manager_reward.one_level_royal = (form_input.shop_manager_reward.one_level_royal * 100).toFixed(0);
			form_input.shop_manager_reward.two_level_royal = (form_input.shop_manager_reward.two_level_royal * 100).toFixed(0);
			form_input.shop_manager_reward.region_money = (form_input.shop_manager_reward.region_money * 100).toFixed(0);

			console.log(form_input);
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ shop_distribution_yitao: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});

		},
		//易淘商品再次购买门槛商品提成设置
		submit_shop_distributions_yitao: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = { shop_manager_reward: {} };

			form_input.shop_manager_reward.one_level_royal = $.trim($('[name="one_level_royal"]').val());
			form_input.shop_manager_reward.two_level_royal = $.trim($('[name="two_level_royal"]').val());
			form_input.shop_manager_reward.region_money = $.trim($('[name="region_money"]').val());
			form_input.is_open = $('[name="is_open"]').is(':checked') ? 1 : 0;
			try {
				if (form_input.shop_manager_reward.one_level_royal == '' || form_input.shop_manager_reward.two_level_royal == '') { throw '门槛商品提成不能为空'; }
				if (form_input.shop_manager_reward.region_money == '') { throw '区域管理费不能为空'; }
				if (!this.checkDecimalDoint(form_input.shop_manager_reward.one_level_royal, 2) ||
					!this.checkDecimalDoint(form_input.shop_manager_reward.two_level_royal, 2)) { throw '门槛商品提成小数点不能超过两位！'; }
				if (!this.checkDecimalDoint(form_input.shop_manager_reward.region_money, 2)) {
					throw '区域管理费小数点不能超过两位！';
				}

			}
			catch (err) {
				//console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			form_input.shop_manager_reward.one_level_royal = (form_input.shop_manager_reward.one_level_royal * 100).toFixed(0);
			form_input.shop_manager_reward.two_level_royal = (form_input.shop_manager_reward.two_level_royal * 100).toFixed(0);
			form_input.shop_manager_reward.region_money = (form_input.shop_manager_reward.region_money * 100).toFixed(0);

			console.log(form_input);
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ shop_distributions_yitao: form_input }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});

		},
		//审核配置
		submit_display: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = { };

			form_input.config_value = $('[name="display"]').is(':checked') ? 0 : 1;
			try {
			}
			catch (err) {
				//console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			console.log(form_input);
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCONFIGEDIT", [{ display: form_input.config_value }]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					av().render("refresh").run();
				}
			});

		}
	}
})