av({
	id: 'page-application-couponAdd',
	include: ["src/common/content.js"],
	extend: ["common-content"],
	'export': { template: "src/page/application/couponAdd.html" },
	'import': function (e) {
		this.template(e.template);
	},
	main: function () {
		this.data.request.moduleOption = ["APPLICATIONADMINCOUPONMODULEOPTION"];
		this.data.request.typeOption = ["APPLICATIONADMINCOUPONTYPEOPTION"];
	},
	event: {
		error: function (error) {
			console.log('error 跳转', error);
			return av.router(av.router().url, '#/application-couponList/').request();
		},
		renderEnd: function () {
			//调用layer的选择时间插件
			//开始时间
			laydate.render({
				elem: '[name="coupon_start_time"]'
				, type: 'datetime'
				, theme: '#337ab7'
			});
			//结束时间
			laydate.render({
				elem: '[name="coupon_end_time"]'
				, type: 'datetime'
				, theme: '#337ab7'
			});
		},
		loadEnd: function () {
			this.render("refresh");
		}
	},
	data: {
		request: {},
		state: undefined,
		list: {
			data: [],
		},
		coupon_property_unit: '元',
		coupon_discount_unit: '元',
		eventChangeUnit: function (ele, e) {
			if ($(ele).val() == 1) {
				this.coupon_property_unit = '积分';
				if ($.trim($('[name="coupon_type"]').val()) != 4)
					this.coupon_discount_unit = '积分';
			}
			else if ($(ele).val() == 0) {
				this.coupon_property_unit = '元';
				if ($.trim($('[name="coupon_type"]').val()) != 4)
					this.coupon_discount_unit = '元';
			}
		},
		eventChangeType: function (ele, e) {
			//折扣
			if ($(ele).val() == 4) this.coupon_discount_unit = '折';
			else if ($.trim($('[name="coupon_property"]').val()) == 0) this.coupon_discount_unit = '元';
			else if ($.trim($('[name="coupon_property"]').val()) == 1) this.coupon_discount_unit = '积分';
		},
		//按回车键时提交
		keyupFunction: function () {
			this.eventSubmit();
		},
		submitLock: false,
		eventSubmit: function () {
			var _this = this;
			if (_this.submitLock) {
				return false;
			} else {
				_this.submitLock = true;
			}
			var form_input = {};
			//获取数据
			form_input.coupon_name = $.trim($('[name="coupon_name"]').val());
			form_input.coupon_info = $.trim($('[name="coupon_info"]').val());
			form_input.coupon_label = $.trim($('[name="coupon_label"]').val());
			form_input.coupon_comment = $.trim($('[name="coupon_comment"]').val());
			form_input.coupon_type = $.trim($('[name="coupon_type"]').val());//优惠券类型
			form_input.coupon_module = $.trim($('[name="coupon_module"]').val());//所属模块
			form_input.coupon_property = $.trim($('[name="coupon_property"]').val());//所属资金类型
			form_input.coupon_limit_min = $.trim($('[name="coupon_limit_min"]').val());//最小限制
			form_input.coupon_limit_max = $.trim($('[name="coupon_limit_max"]').val());//最大限制
			form_input.coupon_discount = $.trim($('[name="coupon_discount"]').val());//折扣 
			form_input.coupon_start_time = $.trim($('[name="coupon_start_time"]').val());//开始时间
			form_input.coupon_end_time = $.trim($('[name="coupon_end_time"]').val());//结束时间
			form_input.coupon_state = $('[name="coupon_state"]').is(':checked') ? 0 : 1;
			if(_this.applicationCheckYouli()){
				form_input.coupon_get_type =$('[name="coupon_get_type"]').is(':checked')? 1 : 0; //是否发放优惠券
				form_input.coupon_get_num = $.trim($('[name="coupon_get_num"]').val());//可领取次数
			}
			//读取积分配置
			var creditsConfig = this.applicationCreditConfig();
			//检查数据
			try {
				/*****************************开始结束时间**************************/
				if (form_input.coupon_start_time != '' && form_input.coupon_end_time != '' && new Date(form_input.coupon_start_time.replace("-", "/").replace("-", "/")) > new Date(form_input.coupon_end_time.replace("-", "/").replace("-", "/")))
					throw "开始时间不能晚于结束时间!";
				if (form_input.coupon_start_time == '') {
					delete form_input.coupon_start_time;
				}
				if (form_input.coupon_end_time == '') {
					delete form_input.coupon_end_time;
				}
				if (form_input.coupon_name == '') throw "优惠券名称不能为空!";
				if (form_input.coupon_type == '') throw "优惠券类型不能为空!";
				/*****************************最大最小限制**************************/
				//资金类型为人民币，且最小或者最大限制经过转换后有仍有小数点
				var dotMin = form_input.coupon_limit_min.indexOf(".");
				var dotCntMin = form_input.coupon_limit_min.substring(dotMin + 1, form_input.coupon_limit_min.length);
				var dotMax = form_input.coupon_limit_max.indexOf(".");
				var dotCntMax = form_input.coupon_limit_max.substring(dotMax + 1, form_input.coupon_limit_max.length);
				if (form_input.coupon_property == 0 && (
					(dotMin > -1 && dotCntMin.length > 2)
					|| (dotMax > -1 && dotCntMax.length > 2))) {
					throw '人民币最大或最小限制小数点位数不能超过两位!';
				}
				if (form_input.coupon_property == 1 && (
					(dotMin > -1 && dotCntMin.length > creditsConfig.precision)
					|| (dotMax > -1 && dotCntMax.length > creditsConfig.precision))) {
					throw '积分最大或最小限制小数点位数不能超过' + creditsConfig.precision + '位!';
				}
				if (form_input.coupon_limit_min != '' && form_input.coupon_limit_max != '' && parseFloat(form_input.coupon_limit_min) > parseFloat(form_input.coupon_limit_max))
					throw '最小限制不能超过最大限制';
				/**********************************折扣**************************/
				var dot = form_input.coupon_discount.indexOf(".");
				var dotCnt = form_input.coupon_discount.substring(dot + 1, form_input.coupon_discount.length);
				//非折扣&&人民币&&有小数点
				if (form_input.coupon_type != 4 && form_input.coupon_property == 0 && dot > -1 && dotCnt.length > 2)
					throw "人民币折扣的小数位不能超过2位！";
				//非折扣&&积分&&有小数点
				else if (form_input.coupon_type != 4 && form_input.coupon_property == 1 && dot > -1 && dotCnt.length > creditsConfig.precision)
					throw "积分折扣的小数位不能超过" + creditsConfig.precision + "位！";
				//折扣&&有小数点
				else if (form_input.coupon_type == 4 && dot > -1 && dotCnt.length > 1)
					throw "折扣券的小数位不能超过1位！";

				if(_this.applicationCheckYouli()&& (form_input.coupon_get_num.indexOf('.')>-1||form_input.coupon_get_num<=0))
				throw "领取次数必须为正整数！";
			}
			catch (err) {
				//console.log(err);
				layer.msg(err, { icon: 5, time: 2000 });
				return _this.submitLock = false;
			}
			//处理后去小数尾数传给后端
			if (form_input.coupon_type != 4 && form_input.coupon_property == 0) {
				form_input.coupon_discount = (form_input.coupon_discount * 100).toFixed(0);

			}
			else if (form_input.coupon_type != 4 && form_input.coupon_property == 1) {
				form_input.coupon_discount = (form_input.coupon_discount * creditsConfig.scale).toFixed(0);

			}
			else if (form_input.coupon_type == 4) {
				form_input.coupon_discount = (form_input.coupon_discount * 10).toFixed(0);
			}
			//处理后去小数尾数传给后端
			if (form_input.coupon_property == 0) {
				form_input.coupon_limit_min = (form_input.coupon_limit_min * 100).toFixed(0);
				form_input.coupon_limit_max = (form_input.coupon_limit_max * 100).toFixed(0);
			}
			else if (form_input.coupon_property == 1) {
				form_input.coupon_limit_min = (form_input.coupon_limit_min * creditsConfig.scale).toFixed(0);
				form_input.coupon_limit_max = (form_input.coupon_limit_max * creditsConfig.scale).toFixed(0);
			}
			console.log(form_input);
			//return false;
			//提交数据
			this.submit({
				method: "submit",
				request: ["APPLICATIONADMINCOUPONADD", [form_input]],
				error: function () {
					_this.submitLock = false;
				},
				success: function () {
					_this.submitLock = false;
					//刷新页面
					// av().render("refresh").run();
					av.router(av.router().url, '#/application-couponList/').request();
				}
			});
		}
	}
});