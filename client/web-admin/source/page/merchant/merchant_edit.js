WangAho({
	
	
	id : "merchant/merchant_edit",
	
		
	hashchange : function(){
		var _project = WangAho(this.id);
		WangAho("index").scroll_constant(function(){
			_project.main();
		});
	},
	
	data : null,
	merchant_id : null,
	main : function(){
		var _project = WangAho(this.id);
		var config = {search:{}};
		var _http = http();
		config.merchant_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
		if(!config.merchant_id){
			http("#/merchant/merchant_list").request();
			return false;
		}
		this.merchant_id = config.merchant_id;
		
		var action = (function(){try{ return _http.anchor.query.action;}catch(e){return false;}}());
		if( !action ){
			action = "basics";
		}
		
		//数据请求
		var request = {application_config:["APPLICATIONCONFIG"],get:["MERCHANTADMINGET", [config]]};
		var template_data = WangAho().template("page/merchant/merchant_edit.html", "#content");
		if( action == "license" ){
			template_data = WangAho().template("page/merchant/merchant_edit.html", "#license-content");
		}else
		if( action == "image" ){
			var config = {search:{merchant_id: _project.merchant_id}};
			
			//排序
			if( (function(){try{ return _http.anchor.query.sort;}catch(e){return false;}}()) ){
				config.sort = [_http.anchor.query.sort];
			}else{
				if(!_http.anchor.query){
					_http.anchor.query = {};
				}
				WangAho().history_remove();//删除本页的记录
				_http.anchor.query.sort = "update_time_desc";
				http(_http).request();
				return false;
			}
			
			//分页
			if( (function(){try{ return _http.anchor.query.page;}catch(e){return false;}}()) ){
				config.page = _http.anchor.query.page;
			}
			request.list = ["MERCHANTADMINIMAGELIST",[config]];
			template_data = WangAho().template("page/merchant/merchant_edit.html", "#image-content");
		}else	
		if( action == "config" ){
			template_data = WangAho().template("page/merchant/merchant_edit.html", "#config-content");
		}else	
		if( action == "type" ){
			request.merchant_type_option = ["APPLICATIONADMINTYPEOPTION",[{sort:["sort_asc"],module:"merchant_type"}]];
			template_data = WangAho().template("page/merchant/merchant_edit.html", "#type-content");
		}
		
		//数据请求
		WangAho("index").data({
			request : request,
			success : function(data){
				if( !data ){
					return false;
				}
				
				//判断权限
				if( (function(){try{ return data.responseAll.data.get.errno;}catch(e){return false;}}()) ){
					layer.msg(data.responseAll.data.get.error, {icon: 5, time: 2000});
				}
				
				//判断数据是否存在
				if( !(function(){try{ return data.response.get.merchant_id;}catch(e){return false;}}()) ){
					setTimeout(function(){
						
						if( WangAho().history_previous_exist() ){
							WangAho().history_previous();//存在上一页则返回上一页
						}else{
							WangAho().history_remove();//删除本页的记录
							http("#/merchant/merchant_list").request();//返回列表页
						}
						
					}, 2000);
					return false;
				}
				
				data.action = action;
				//检查应用是否是创联众宜
				data.applicationCheckMuYingShangCheng=function(){
					var objectRequestAPI = new requestAPI();
					if( objectRequestAPI.application() == 'muyingshop_test' || objectRequestAPI.application() == 'muyingshop' ){
						return true;
					}else{
						return false;
					}
				}
				//获得配置数据
				data.config = WangAho().data("config.json");
				_project.data = data;
				WangAho("index").view(template_data, data, {
					
					"type in_array" : function(type_id, type){
						if(typeof type != 'object' || !type.length){
							return false;
						}
						
						var exist = false;
						for(var i in type){
							if( type[i].type_id == type_id){
								exist = true;
								break;
							}
						}
						return exist;
					},
					
					"action-button" : function(){
						return template( WangAho().template("page/merchant/merchant_edit.html", "#action-button"), function(fn){
							this.helper("action-query-href", function(action){
								var _h = http();
								if(!_h.anchor.query){
									_h.anchor.query = {};
								}
								if( !action){
									delete _h.anchor.query.action;
								}else{
									_h.anchor.query.action = action;
								}
								
								if( _h.anchor.query.page ){
									delete _h.anchor.query.page;//删除分页
								}
								
								return http(_h).href;
							});
							
							return fn(data);
						});
					},
				});
				
				if( action == "image" ){
					_project.merchant_image_event();
				}else
				if( action == "license" ){
					_project.license_submit();
					_project.event_license_image_upload_submit();
				}else
				if( action == "config" ){
					_project.config_submit();
				}else
				if( "type" == action ){
					_project.type_submit();
				}else{
					_project.submit();
					_project.event_logo_upload_submit();
				}
				
				
			}
		});
		
		
		
	},
	
	
	
	keyup : function(){
		//按回车键时提交
		$(document).unbind("keyup").on('keyup', function(e){
			if(e.keyCode === 13){
			    if( $("textarea").is(":focus") ){  
			        return false;
			    }
		        $('[name="submit"]').first().trigger("click");
		        $('[name="license-submit"]').first().trigger("click");
		        $('[name="config-submit"]').first().trigger("click");
		        $('[name="image-submit"]').first().trigger("click");
		        $('[name="type-submit"]').first().trigger("click");
		        
			}
		});
	},
	
	
	
	submit : function(){
		//调用 Chosen
		$('select[name="merchant_state"]').chosen({
			width: '100%',
			//placeholder_text_single: '-', //默认值
			earch_contains:true, 
			no_results_text: "没有匹配结果",
			case_sensitive_search: false //搜索大小写敏感。此处设为不敏感
        	//group_search: false //选项组是否可搜。此处搜索不可搜
		});
		
		
		//按回车键时提交
		this.keyup();
		
		$('[name="submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			//检查应用是否是创联众宜
			var applicationCheckMuYingShangCheng=function(){
				var objectRequestAPI = new requestAPI();
				if( objectRequestAPI.application() == 'muyingshop_test' || objectRequestAPI.application() == 'muyingshop' ){
					return true;
				}else{
					return false;
				}
			}
			form_input.merchant_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.merchant_name = $.trim($('[name="merchant_name"]').val());
			form_input.merchant_info = $.trim($('[name="merchant_info"]').val());
			
			form_input.merchant_phone = $.trim($('[name="merchant_phone"]').val());
			if(!applicationCheckMuYingShangCheng){
					form_input.merchant_tel = $.trim($('[name="merchant_tel"]').val());
					form_input.merchant_email = $.trim($('[name="merchant_email"]').val());
			}
			
			form_input.merchant_province = $.trim($('[name="merchant_province"]').val());
			form_input.merchant_city = $.trim($('[name="merchant_city"]').val());
			form_input.merchant_district = $.trim($('[name="merchant_district"]').val());
			form_input.merchant_address = $.trim($('[name="merchant_address"]').val());
			form_input.merchant_longitude = $.trim($('[name="merchant_longitude"]').val());
			form_input.merchant_latitude = $.trim($('[name="merchant_latitude"]').val());
			form_input.merchant_state = $.trim($('[name="merchant_state"]').val());
			
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				if(form_input.merchant_name == '') throw "商家名称不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["MERCHANTADMINEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
		
		
		
	},
	
	

	license_submit : function(){
		//按回车键时提交
		this.keyup();
		
		$('[name="license-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.merchant_license_name = $.trim($('[name="merchant_license_name"]').val());
			form_input.merchant_license_number = $.trim($('[name="merchant_license_number"]').val());
			form_input.merchant_license_address = $.trim($('[name="merchant_license_address"]').val());
			form_input.merchant_license_operator = $.trim($('[name="merchant_license_operator"]').val());
			
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				if(form_input.merchant_name == '') throw "商家名称不能为空";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["MERCHANTADMINEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
	},
	
	
	
	type_submit : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		
		$('[name="type-checkbox"]').unbind("background").bind("background", function(){
			$('[name="type-checkbox"]').each(function(){
				$(this).parent().removeClass("checkbox-checked");
			});
			
			$('[name="type-checkbox"]:checked').each(function(){
				$(this).parent().addClass("checkbox-checked");
			});
		});
		$('[name="type-checkbox"]').first().trigger("background");
		$('[name="type-checkbox"]').unbind("click").click(function(){
			$('[name="type-checkbox"]').first().trigger("background");
		});
		
		$('[name="type-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.type_id = [];
			$('[name="type-checkbox"]:checked').each(function(){
				form_input.type_id.push($(this).val());
			});
			
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["MERCHANTADMINTYPEEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
		
		
		
	},
	
	
	
	
	
	config_submit : function(){
		var _project = WangAho(this.id);
		//按回车键时提交
		this.keyup();
		var _application_config_credit = (function(){try{ return _project.data.response.application_config.credit;}catch(e){return false;}}());
		
		$('[name="config-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			form_input.config_withdraw_alipay = {
				account :$.trim($('[name="config_withdraw_alipay.account"]').val()),
				realname :$.trim($('[name="config_withdraw_alipay.realname"]').val())
			};
			form_input.config_withdraw_weixinpay = {
				openid :$.trim($('[name="config_withdraw_weixinpay.openid"]').val()),
				trade_type :$.trim($('[name="config_withdraw_weixinpay.trade_type"]').val())
			};
			form_input.config_rmb_consume_user_credit = {
				ratio_credit :$.trim($('[name="config_rmb_consume_user_credit.ratio_credit"]').val()),
				ratio_rmb :$.trim($('[name="config_rmb_consume_user_credit.ratio_rmb"]').val()),
				algorithm :$.trim($('[name="config_rmb_consume_user_credit.algorithm"]').val()),
				state : $('[name="config_rmb_consume_user_credit.state"]').is(':checked')? 1 : 0
			};
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				
				if( !_application_config_credit ){
					throw "积分配置异常";
				}
				
				if( form_input.config_rmb_consume_user_credit.ratio_credit != ''){
					var money_format_1 = /^[0-9\.]+$/;
					var money_format_2 = /\./;
				
					if( money_format_1.test(form_input.config_rmb_consume_user_credit.ratio_credit) ){
						form_input.config_rmb_consume_user_credit.ratio_credit = parseFloat(form_input.config_rmb_consume_user_credit.ratio_credit) * _application_config_credit.scale;
					}else{
						form_input.config_rmb_consume_user_credit.ratio_credit = null;
					}
					
					if( !form_input.config_rmb_consume_user_credit.ratio_credit || 
						money_format_2.test(form_input.config_rmb_consume_user_credit.ratio_credit) ){
						if( parseInt(_application_config_credit.precision) ){
							throw "积分比值的格式输入有误，格式必须是大于0的整数或者"+_application_config_credit.precision+"位小数";
						}else{
							throw "积分比值的格式输入有误，格式必须是大于0的整数";
						}
					}
				}
				
				if( form_input.config_rmb_consume_user_credit.ratio_rmb != "" ){
					var money_format = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
					
					if( money_format.test(form_input.config_rmb_consume_user_credit.ratio_rmb) ){
						form_input.config_rmb_consume_user_credit.ratio_rmb = parseInt((parseFloat(form_input.config_rmb_consume_user_credit.ratio_rmb).toFixed(2))*100);//元转为分
					}else{
						throw "人民币比值的格式输入有误，格式必须是整数或者是两位小数";
					}
				}
				
				
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			
			//提交数据
			WangAho("index").submit({
				method:"submit",
				request:["MERCHANTADMINEDIT", [form_input]],
				error:function(){
					$btn.removeClass('disabled');
				},
				success:function(data){
					//刷新页面
					WangAho().rerun();
				}
			});
			
			
		});
	},
	
	
	logo_file : null,
	event_logo_upload_submit : function(){
		var _project = WangAho(this.id);
		//选择上传图片
		$('[name="image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="image-files"]').get(0));
		});
		
		//图片发生改变时执行
		$('[name="image-files"]').unbind("change").change(function(){
			if( $('[name="image-files"]')[0].files.length ){
				_project.logo_file = $('[name="image-files"]')[0].files[0];
				_project.logo_file.src = new eonfox().file_url( _project.logo_file );
				$('[name="image-show"]').attr("src", _project.logo_file.src).attr("data-src", _project.logo_file.src);
				$('[name="image-show"]').show();
			}
		});
		
		
		$('[name="logo-image-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				if( !_project.logo_file ) throw "没有需要上传的图片";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			eonfox().submit({
				request : JSON.stringify({
					s : ["MERCHANTADMINLOGOQINIUUPLOAD", [form_input]],
					}),
				data : {file: _project.logo_file},	
				progress : function(loaded, total, percent){
					//console.log(loaded, total, percent);
					if(percent == 100){
		        		//layer.msg('上传成功', {icon: 1, time: 1000});
		        	}else{
		        		layer.msg( Math.floor(percent)+"%" );
		        	}
				},
				callback : function(r){
					layer.closeAll('loading');//关闭加载
					if( !r ){
						layer.msg("未知错误", {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					if( (function(){try{ return r.data.s.errno;}catch(e){return false;}}()) ){
						layer.msg(r.data.s.error, {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					
					layer.msg("操作成功!", {icon: 1, time: 1000});
					setTimeout(function(){
						//刷新页面
						WangAho("index").scroll_constant(function(){
							_project.main();
						});
					}, 1000);
				}
			});
			
			
		});
		
		
		
		
	},
	
	
	
		
	license_image_file : null,
	event_license_image_upload_submit : function(){
		var _project = WangAho(this.id);
		//选择上传图片
		$('[name="license-image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="license-image-files"]').get(0));
		});
		
		//图片发生改变时执行
		$('[name="license-image-files"]').unbind("change").change(function(){
			if( $('[name="license-image-files"]')[0].files.length ){
				_project.license_image_file = $('[name="license-image-files"]')[0].files[0];
				_project.license_image_file.src = new eonfox().file_url( _project.license_image_file );
				$('[name="license-image-show"]').attr("src", _project.license_image_file.src).attr("data-src", _project.license_image_file.src);
				$('[name="license-image-show"]').show();
			}
		});
		
		
		$('[name="license-image-submit"]').unbind("click").click(function(){
			var $btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			
			
			var form_input = {};
			var _http = http();
			form_input.merchant_id = (function(){try{ return _http.anchor.query.id;}catch(e){return false;}}());
			try {
				if(form_input.merchant_id == '') throw "商家ID异常";
				if( !_project.license_image_file ) throw "没有需要上传的图片";
			}
			catch(err) {
		        layer.msg(err, {icon: 5, time: 2000});
		        $btn.removeClass('disabled');
		        return false;
		    }
			
			eonfox().submit({
				request : JSON.stringify({
					s : ["MERCHANTADMINLICENSEIMAGEQINIUUPLOAD", [form_input]],
					}),
				data : {file: _project.license_image_file},	
				progress : function(loaded, total, percent){
					//console.log(loaded, total, percent);
					if(percent == 100){
		        		//layer.msg('上传成功', {icon: 1, time: 1000});
		        	}else{
		        		layer.msg( Math.floor(percent)+"%" );
		        	}
				},
				callback : function(r){
					layer.closeAll('loading');//关闭加载
					if( !r ){
						layer.msg("未知错误", {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					if( (function(){try{ return r.data.s.errno;}catch(e){return false;}}()) ){
						layer.msg(r.data.s.error, {icon: 5, time: 3000});
						$btn.removeClass('disabled');
						return;
					}
					
					layer.msg("操作成功!", {icon: 1, time: 1000});
					setTimeout(function(){
						//刷新页面
						WangAho("index").scroll_constant(function(){
							_project.main();
						});
					}, 1000);
				}
			});
			
			
		});
		
		
		
		
	},
	
	
	
		
	image_upload_list : {},
	image_upload_mime_limit : ['image/jpeg','image/pjpeg','image/png', 'image/x-png', 'image/gif', 'image/bmp'],
	//是否需要上传
	image_upload_button : false,
	merchant_image_template : function(){
		var _project = WangAho(this.id);
		
		var _merchant_id = this.merchant_id;
		_project.image_upload_button = false;//是否需要上传
		var list = [];
		if(typeof _project.image_upload_list[_merchant_id] == "object"){
			for(var i = 0; i < _project.image_upload_list[_merchant_id].length; i ++){
				list[i] = {};
				list[i].src = new eonfox().file_url(_project.image_upload_list[_merchant_id][i]);
				list[i].name = _project.image_upload_list[_merchant_id][i].name;
				list[i].size = _project.image_upload_list[_merchant_id][i].size;
				list[i].type = _project.image_upload_list[_merchant_id][i].type;
				list[i].title = _project.image_upload_list[_merchant_id][i].title;
				list[i].upload = _project.image_upload_list[_merchant_id][i].upload? true : false;
				list[i].error = _project.image_upload_list[_merchant_id][i].error? true : false;
				if(!list[i].upload && !list[i].error){
					_project.image_upload_button = true;//有需要上传的
				}
			}
		}
		
		var html = template( WangAho().template("page/merchant/merchant_edit.html", "#image-add-content"), function(fn){
			return fn({list:list, merchant_id:_merchant_id});
			});
			
		$('[name="image-upload-list"]').html(html);
		if( !_project.image_upload_button ){
			//没有需要上传的
			$('[name="image-submit"]').addClass("disabled");
		}else{
			$('[name="image-submit"]').removeClass("disabled");
		}
		_project.merchant_image_event();//更新事件
	},
	
	
		
	//上传文件事件
	merchant_image_event : function(){
		var _project = WangAho(this.id);
		var _merchant_id = this.merchant_id;
		
		//查看图片
		WangAho("index").image_look_event();
		//回车
		this.keyup();
		
		$('[action-button]').unbind("click").click(function(){
			var ids = WangAho("index").action_table_checked();
			var attr = $(this).attr("action-button");
			
			//排序
			if("sort" == attr){
				_project.merchant_image_edit_sort();
				return true;
			}
			//修改名称
			if("name" == attr){
				_project.merchant_image_edit_name();
				return true;
			}
			
			if(ids.length < 1){
				layer.msg("请选择要操作的数据", {icon: 5, time: 2000});
				return false;
			}
			
			//删除
			if("remove" == attr){
				layer.msg('你确定要删除么？('+ids.length+'条数据)', {time: 0 //不自动关闭
					,btn: ['确定', '取消'],yes: function(index){
												    layer.close(index);
												    _project.merchant_image_remove(ids);
												  }
				});
			}
			
		});
		
		
		//打开
		$('[name="image-add-input"]').unbind("click").click(function(){
			layer.closeAll();
			//页面层
			layer.open({
				title : "<span class=\"glyphicon glyphicon-plus\"></span> 添加图片",
			  	type: 1,
			  	offset: '0',
			  	area: [$(window).width()+"px", '100%'], //宽高
			  	content: template( WangAho().template("page/merchant/merchant_edit.html", "#image-add-button"), function(fn){
							return fn(_project.data);
							})
			});
			
			$('[name="image-files"]').first().focus();//失去焦点
			_project.merchant_image_template();//显示模板
			
		});
		
		//开始上传图片
		$('[name="image-submit"]').unbind("click").click(function(){
			$btn = $(this);
			if( $btn.hasClass("disabled") ){
				return false;
			}else{
				$btn.addClass("disabled");
			}
			_project.merchant_image_upload();
		});
		
		
		//选择上传图片
		$('[name="image-select"]').unbind("click").click(function(){
			new eonfox().trigger_click($('[name="image-files"]').get(0));
		});
		
		
		//图片发生改变时执行
		$('[name="image-files"]').unbind("change").change(function() {
			var files = $('[name="image-files"]')[0].files;
			for(var i = 0; i < files.length; i++){
				var exist = false;
				if(typeof _project.image_upload_list[_merchant_id] == "object" &&
				_project.image_upload_list[_merchant_id].length > 0){
					for(var n = 0; n < _project.image_upload_list[_merchant_id].length; n ++){
						if(_project.image_upload_list[_merchant_id][n].lastModified == files[i].lastModified &&
						_project.image_upload_list[_merchant_id][n].name == files[i].name &&
						_project.image_upload_list[_merchant_id][n].size == files[i].size &&
						_project.image_upload_list[_merchant_id][n].type == files[i].type ){
							exist = true;//该文件存在
							break;
						}
					}
				}
				
				
				//判断图片类型是否合法
				var legal = false;
				for(var l in _project.image_upload_mime_limit){
					if(_project.image_upload_mime_limit[l] == files[i].type){
						legal = true;
						break;
					}
				}
				
				if(!legal){
					layer.msg("“"+files[i].name+"” 文件格式不合法，只能上传png、jpg、gif图片文件", {icon: 5, time: 3000});
					continue;
				}
				
				
				if(!exist){
					//去掉后缀名称
					files[i].title = files[i].name.replace(/\.[\w]{1,}$/, ""); 
					
					if(typeof _project.image_upload_list[_merchant_id] != "object"){
						_project.image_upload_list[_merchant_id] = [];
					}
					
					_project.image_upload_list[_merchant_id].push(files[i]);
				}
			}
			
			_project.merchant_image_template();//显示模板
		});
		
		
		//修改图片名称
		$('[name="image-upload-list"] .image-name-input').unbind("input propertychange").bind("input propertychange", function(event){
			var id = parseInt($(this).attr("data-id"));
			var merchant_id = $(this).attr("data-merchant-id");
			if(typeof _project.image_upload_list[merchant_id] == 'object' && 
			typeof _project.image_upload_list[merchant_id][id] == 'object'){
				_project.image_upload_list[merchant_id][id].title = $(this).val();
			}
		});
		
		//清理已经上传的图片
		$('[name="image-clear"]').unbind("click").click(function(){
			if(typeof _project.image_upload_list[_merchant_id] != "object" || 
			_project.image_upload_list[_merchant_id].length < 1){
				return false;
			}
			
			//这里不能使用splice方法 
			var image_upload_list = [];
			for(var i = 0; i < _project.image_upload_list[_merchant_id].length; i ++){
				if( !_project.image_upload_list[_merchant_id][i].upload ){
					image_upload_list.push(_project.image_upload_list[_merchant_id][i]);
				}
			}
			_project.image_upload_list[_merchant_id] = image_upload_list;
			
			_project.merchant_image_template();//显示模板
		});
		
		
		//删除
		$('[name="image-delete"]').unbind("click").click(function(){
			if($(this).hasClass("disabled")){
				return false;
			}
			
			var id = parseInt($(this).attr("data-id"));
			var merchant_id = $(this).attr("data-merchant-id");
			if(typeof _project.image_upload_list[merchant_id] != "object" ||
			_project.image_upload_list[merchant_id].length < 1){
				return false;
			}
			
			//删除这个标识的文件
			_project.image_upload_list[merchant_id].splice(id, 1);
			_project.merchant_image_template();//显示模板
		});
		
		
		
		
	},
	
	
	//图片上传
	merchant_image_upload : function(merchant_id){
		var _project = WangAho(this.id);
		var _merchant_id = merchant_id? merchant_id : this.merchant_id;
		
		if(typeof _project.image_upload_list[_merchant_id] != "object" || 
		_project.image_upload_list[_merchant_id].length < 1){
			layer.msg("没有上传的图片", {icon: 5, time: 2000});
			return false;
		}
		
		var _http = http();
		var file_upload_obj;
		var file_upload_id;
		for(var i in _project.image_upload_list[_merchant_id]){
			//没有上传并且没有错误
			if(!_project.image_upload_list[_merchant_id][i].upload && !_project.image_upload_list[_merchant_id][i].error){
				file_upload_obj = _project.image_upload_list[_merchant_id][i];
				file_upload_id = i;
				break;
			}
		}
		//如果没有上传对象，则刷新页面
		if(!file_upload_obj){
			console.log("全部上传完成");
			_project.merchant_image_template();//显示模板
			WangAho("index").scroll_constant(function(){
				_project.main();
			});
			return;
		}
		
		
		//获取七牛云uptoken
		var auth = null;
		//加载层-风格3
		layer.load(2);
		auth_config = {
			merchant_id : _merchant_id,
			image_format : file_upload_obj.name.substring(file_upload_obj.name.lastIndexOf(".")+1, file_upload_obj.name.length),
			image_name : file_upload_obj.title,
			image_type : file_upload_obj.type,
			image_size : file_upload_obj.size
		};
		eonfox().submit({
			request : JSON.stringify({
				qiniu : ["MERCHANTADMINIMAGEQINIUUPTOKEN", [auth_config]],
				}),
			async:false,
			callback : function(r){
				layer.closeAll('loading');//关闭加载
				if( !r ){
					layer.msg("获取uptoken失败，未知错误", {icon: 5, time: 3000});
					return;
				}
				if( (function(){try{ return r.data.qiniu.errno;}catch(e){return false;}}()) ){
					layer.msg(r.data.qiniu.error, {icon: 5, time: 3000});
					
					_project.image_upload_list[_merchant_id][file_upload_id].error = true;
					_project.merchant_image_upload(_merchant_id);//继续上传
					return;
				}
				
				auth = (function(){try{ return r.data.qiniu.data;}catch(e){return false;}}());
				
			}
		});
		
		if( !auth ){
			_project.merchant_image_template();//显示模板
			return;
		}
		
		//已经上传了
	    _project.image_upload_list[_merchant_id][file_upload_id].upload = true;
		
		var putExtra = {
			//文件原文件名
	    	fname: "",
	    	//用来放置自定义变量
	    	params: {},
	    	//用来限制上传文件类型
	    	mimeType: _project.image_upload_mime_limit
	    };
		var config = {};
		//文件资源名
		var observable = qiniu.upload(file_upload_obj, auth.image_id, auth.qiniu_uptoken, putExtra, config);
		// 上传开始
		var subscription = observable.subscribe({
	    	//接收上传进度信息
	    	next:function(res){
	        	//console.log("observer.next", res);
	        	var $_progress = $('[name="image-progress"][data-id="'+file_upload_id+'"][data-merchant-id="'+_merchant_id+'"]');
	        	if(res.total.percent == 100){
	        		$('[name="image-delete"][data-id="'+file_upload_id+'"][data-merchant-id="'+_merchant_id+'"]').removeClass("disabled");
	        		$_progress.removeClass("progress-bar-warning progress-bar-striped active").addClass("progress-bar-success");
	        		$_progress.html('<span class="glyphicon glyphicon-ok"></span> 上传完成');
	        	}else{
	        		$('[name="image-delete"][data-id="'+file_upload_id+'"][data-merchant-id="'+_merchant_id+'"]').addClass("disabled");
	        		$_progress.removeClass("progress-bar-success").addClass("progress-bar-warning progress-bar-striped active");
	        		$_progress.attr("aria-valuenow", Math.floor(res.total.percent));
	        		$_progress.css("width", Math.floor(res.total.percent)+"%");
	        		$_progress.html(Math.floor(res.total.percent)+"%");
	        	}
	        	
	    	},
	    	//上传错误后触发
	    	error:function(err){
	    		layer.msg(err.message, {icon: 5, time: 3000});
	    		_project.image_upload_list[_merchant_id][file_upload_id].error = true;
	    		_project.merchant_image_upload(_merchant_id);//继续上传
	    		return;
	    		//console.log("observer.error", err);
	    	}, 
	    	//接收上传完成后的后端返回信息
	    	complete:function(res){
	    		//更改删除按钮
				$('[name="image-delete"][data-id="'+file_upload_id+'"][data-merchant-id="'+_merchant_id+'"]').html('<span class="glyphicon glyphicon-repeat"></span> 清理');
	    		$('[name="image-delete"][data-id="'+file_upload_id+'"][data-merchant-id="'+_merchant_id+'"]').removeClass("disabled");
	    		
	        	//console.log("observer.complete", res);
	        	//这里上传成功，将信息发送给后台，将图片状态设为1
	        	var form_input = {
	        		merchant_image_id : auth.merchant_image_id,
	        		image_id : auth.image_id,
	        		image_format : res.format,
	        		image_width : res.width,
	        		image_height : res.height,
	        		image_hash : res.hash,
	        		image_path : res.bucket
	        	};
				//提交数据
				eonfox().submit({
					request : JSON.stringify({
						state:["MERCHANTADMINIMAGEQINIUSTATE", [form_input]],
						}),
					recursion: true,
					callback : function(r){
						if( !r ){
							layer.msg("更新上传状态失败，未知错误", {icon: 5, time: 3000});
							return;
						}
						if( (function(){try{ return r.data.state.errno;}catch(e){return false;}}()) ){
							layer.msg(r.data.state.error, {icon: 5, time: 3000});
							return;
						}
						
						_project.merchant_image_upload(_merchant_id);//继续上传
						return;
						}
				});
	      	}
	    }); 
		
		
		
	},
	
	
	
	
		
	//删除图片
	merchant_image_remove : function(ids){
		if(!ids || !ids.length){
			return false;
		}
		
		var _project = WangAho(this.id);
		
		var request_array = [];
		for(var i in ids){
			request_array.push(["MERCHANTADMINIMAGEQINIUREMOVE", [{merchant_image_id:ids[i]}]]);
		}
		
		//提交数据
		WangAho("index").submit({
			method:"remove",
			request:request_array,
			success:function(bool){
				if(bool){
					//刷新页面
					WangAho("index").scroll_constant(function(){
						_project.main();
					});
				}
			}
		});
		
	},
	
	
	
		
	//排序
	merchant_image_edit_sort : function(){
		var obj = WangAho("index").action_table("sort");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["MERCHANTADMINIMAGEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["MERCHANTADMINIMAGEEDIT", [{merchant_image_id:obj[i].id, image_sort:obj[i].value}]]);
		}
		
		var _project = WangAho(this.id);
		
		//提交数据
		WangAho("index").submit({
			method:"edit",
			request:request_array,
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
		});
		
	},
	
	
	
	//修改名称
	merchant_image_edit_name : function(){
		var obj = WangAho("index").action_table("name");
		if( !obj || !obj.length ){
			return false;
		}
		
		var request_array = [];
		request_array.push(["MERCHANTADMINIMAGEEDITCHECK"]);//第一个是判断是否有编辑权限
		for(var i in obj){
			request_array.push(["MERCHANTADMINIMAGEEDIT", [{merchant_image_id:obj[i].id, image_name:obj[i].value}]]);
		}
		
		var _project = WangAho(this.id);
		
		//提交数据
		WangAho("index").submit({
			method:"edit",
			request:request_array,
			success:function(data){
				//刷新页面
				WangAho("index").scroll_constant(function(){
					_project.main();
				});
			}
		});
		
		
	},
	
	

	
	
	
	
});