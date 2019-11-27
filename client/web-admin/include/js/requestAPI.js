var requestAPI = function () { };
requestAPI.prototype = {

	constructor: requestAPI,

	//是否开启调试模式
	debug: false,

	//文件服务器URL
	fileServerUrl: 'http://img.eonfox.cc/',


	apiServerUrl: function () {
		var router = av.router();
		if (router.query && router.query.server == "developer") {
			return "http://server.test.eapie.com/"; //测试版链接
		} else {
			return "http://server.test.eapie.com/"; //正式版链接
		}
	},

	application: function () {
		var router = av.router();
		if (router.query && router.query.application) {
			return router.query.application;
		} else
			if (router.query && router.query.app) {
				return router.query.app;
			} else {
				return '';
			}
	},

	//会话名称
	sessionName: 'Open_Source-Eonfox_API_Engine_Session',

	//提交的有效等待时间
	submitSleepExpireTime: 30,



	/* 中断请求任务 */
	abort: function () {
		//清理等待的请求
		requestAPI.prototype.submitQueue = [];
		//中断请求任务
		if (requestAPI.prototype.submitTask.abort) {
			requestAPI.prototype.submitTask.abort();
		}
	},




	/* 提交请求 
	 * 暂时只支持 POST
	 * {
	 * 	url : this.apiServerUrl,默认接口地址
	 * 	data : {},
	 *  callback : 回调函数 第一个是单个 data，第二个是 全部的返回数据
	 *  error : 错误回调函数
	 * }
	 * 
	 */
	submit: function (config) {
		if (requestAPI.prototype.debug) {
			console.log("submit()传入参数:", config);
		}

		//回调函数
		if (!config.callback || config.callback.constructor != Function) {
			config.callback = function () { };
		}
		if (!config.error || config.error.constructor != Function) {
			config.error = function () { };
		}

		//超时
		if(typeof config.timeout == 'undefined'){
			config.timeout = 30000;
		}
	

		//路由
		if (typeof config.url == 'undefined' || typeof config.url != 'string') {
			config.url = this.apiServerUrl();
		}

		config.right_data = new FormData();
		config.left_data = new FormData();
		//请求字符串
		if (config.request) {
			//如果是对象，则先转换为字符串
			if (typeof config.request == "object") {
				config.request = JSON.stringify(config.request)
			}
			if (typeof config.request == "string") {
				config.right_data.append("data", config.request);
				config.left_data.append("data", config.request);
			}
		}

		//用户传入的data数据
		if (config.data && typeof config.data == "object") {
			for (var i in config.data) {
				config.right_data.append(i, config.data[i]);
				config.left_data.append(i, config.data[i]);
			}
		}

		if (requestAPI.prototype.debug) {
			console.log("post()：right_data、left_data:", config.right_data, config.left_data);
		}

		/*//是否强制提交
		config.recursion = config.recursion? true : false;
		//大于0，说明存在队列
		if( this.submitQueue.length > 0 ){
			//判断是否强制提交
			if( config.recursion ){
				//去登记注册
				requestAPI.prototype.submitRegister(config);
			}else{
				//否则返回错误信息
				console.warn("应用接口提交队列个数：", this.submitQueue.length);
				return config.error("应用接口出现重复提交，前方正在提交队列个数：", this.submitQueue.length);
			}
		}else{
			//去登记注册
			requestAPI.prototype.submitRegister(config);
		} */

		//去登记注册
		requestAPI.prototype.submitRegister(config);
		//并且调用执行
		requestAPI.prototype.submitRun();
	},



	//请求任务
	submitTask: null,

	/**
	 * 提交队列
	 */
	submitQueue: [],

	/**
	 * 提交登记
	 */
	submitRegister: function (config) {
		config.time = ((new Date()).getTime() / 1000);//赋值是 时间戳 （秒）,用于有效时间
		requestAPI.prototype.submitQueue.push(config);
	},

	/**
	 * 运行提交
	 */
	submitRun: function () {
		if (requestAPI.prototype.submitQueue.length < 1) {
			return false;//没有执行的提交
		}
		if (requestAPI.prototype.submitQueue[0].runtime) {
			return false;//正在执行
		}

		//检查是否已经失效
		if ((requestAPI.prototype.submitQueue[0].time + requestAPI.prototype.submitSleepExpireTime) < ((new Date()).getTime() / 1000)) {
			//已经过了有效期
			//删除第一个元素
			requestAPI.prototype.submitQueue.shift();
			//再次提交
			return requestAPI.prototype.submitRun();
		}

		requestAPI.prototype.submitQueue[0].runtime = true;
		var config = requestAPI.prototype.submitQueue[0];

		//从本地缓存中同步获取指定 key 对应的内容。
		var token = requestAPI.prototype.token(function (e) {
			config.error(e);
		});

		if (!(function () { try { return token['session_right_token']; } catch (e) { return false; } }())) {
			config.right_data.append("session", "start");
			config.right_data.append("application", this.application());
		} else {
			config.right_data.append("token", token['session_right_token']);

			config.left_data.append("token", token['session_left_token']);
			config.left_data.append("session", "start");
			config.left_data.append("application", this.application());
		}

		var ajax = {
			url: config.url,
			method: "POST",
			type: "json",
			timeout: config.timeout,//超时设置
			response: function () {
				//当请求完成之后调用这个函数，无论成功或失败。执行时间比success晚
				//删除第一个元素
				requestAPI.prototype.submitQueue.shift();
				//再次提交
				requestAPI.prototype.submitRun();
			},
			success: function () { },
			fail: function (err) {
				config.error(err);
			}
		}


		if (config.progress && config.progress.constructor == Function) {
			ajax.request = function (XMLHttpRequest) {
				if (XMLHttpRequest.upload) { // 检查上传属性是否存在
					XMLHttpRequest.upload.onprogress = function (e) {
						if (e.lengthComputable) {
							var loaded = e.loaded;                    //已经上传大小情况 
							var total = e.total;                        //附件总大小 
							var percent = Math.floor(100 * loaded / total);     //已经上传的百分比  
							config.progress(loaded, total, percent);
							//console.log("已经上传了："+percent+"%"); 
						}
					};

				}
			};
		}


		//右令牌
		var rightTokenPost = function () {
			ajax.data = config.right_data;
			ajax.success = function (success_data) {
				if (typeof success_data != 'object') {
					success_data = (function () { try { return JSON.parse(success_data); } catch (e) { return success_data; } }());
				}
				if (typeof success_data != 'object') {
					console.warn("应用接口响应异常");
					return config.callback(false, success_data);
				}

				//如果存在请求令牌，直接返回数据
				if ((function () { try { return success_data['token']; } catch (e) { return false; } }())) {
					//储存令牌
					requestAPI.prototype.storageToken(success_data);
					//返回到回调函数
					return config.callback(success_data);
				} else {
					//否则说明没有这个会话，再进行左令牌查询
					return leftTokenPost();
				}
			};

			if (requestAPI.prototype.debug) {
				console.log("post()：右令牌提交:", ajax);
			}

			requestAPI.prototype.submitTask = av.ajax(ajax);
			//requestAPI.prototype.submitTask = uni.request(ajax);
		};


		//左令牌
		var leftTokenPost = function () {
			ajax.data = config.left_data;
			ajax.success = function (success_data) {
				if (typeof success_data != 'object') {
					success_data = (function () { try { return JSON.parse(success_data); } catch (e) { return success_data; } }());
				}
				if (typeof success_data != 'object') {
					console.warn("应用接口响应异常");
					return config.callback(false, success_data);
				}

				//如果没有报错
				if ((function () { try { return success_data['token']; } catch (e) { return false; } }())) {
					//储存令牌
					requestAPI.prototype.storageToken(success_data);
				}

				//返回到回调函数
				return config.callback(success_data);
			};

			if (requestAPI.prototype.debug) {
				console.log("post()：左令牌提交:", ajax);
			}

			requestAPI.prototype.submitTask = av.ajax(ajax);
			//requestAPI.prototype.submitTask = uni.request(request);
		};


		return rightTokenPost();
	},




	/**
	 * 储存token
	 * 
	 * @param {Object} data
	 */
	storageToken: function (data) {
		if (!data) {
			return false;
		}

		var token_data = null;
		var exist_right_token = false;
		var exist_left_token = false;

		exist_right_token = (function () { try { return data['token']['session_right_token']; } catch (e) { return false; } }());
		exist_left_token = (function () { try { return data['token']['session_left_token']; } catch (e) { return false; } }());
		if (exist_right_token && exist_left_token) {
			token_data = data['token'];
		} else {
			//有可能是顶级关联对象
			exist_right_token = (function () { try { return data['session_right_token']; } catch (e) { return false; } }());
			exist_left_token = (function () { try { return data['session_left_token']; } catch (e) { return false; } }());
			if (exist_right_token && exist_left_token) {
				token_data = data;
			}
		}

		if (!token_data) {
			return false;
		}

		//从本地缓存中同步获取指定 key 对应的内容。
		var storageToken = requestAPI.prototype.token();
		if ((function () { try { return storageToken['session_right_token']; } catch (e) { return false; } }()) &&
			(function () { try { return storageToken['session_left_token']; } catch (e) { return false; } }())) {

			if (storageToken['session_right_token'] == token_data['session_right_token'] ||
				storageToken['session_left_token'] == token_data['session_left_token']) {
				if (requestAPI.prototype.debug) {
					console.log("需要对比旧token中的当前时间戳,为true则不需要更新token", storageToken['session_now_time'], token_data['session_now_time'], parseInt(storageToken['session_now_time']) > parseInt(token_data['session_now_time']));
				}
				if (parseInt(storageToken['session_now_time']) > parseInt(token_data['session_now_time'])) {
					if (requestAPI.prototype.debug) {
						console.log("并发异步，不需要更新token");
					}
					return false;
				}

			}
		}

		//console.log( uni.setStorageSync );

		try {
			localStorage.setItem(this.sessionName + ":" + this.application(), JSON.stringify(token_data));
		} catch (e) {
			console.warn(e);
			return false;
		}

		return true;
	},



	/**
	 * 获取token
	 * 
	 * @param	{Function}	error_function
	 */
	token: function (error_function) {
		//异步可能存在覆盖的问题，所以对比已存在的token,如果右左有一个相同则比较当前时间，即最大的当前时间是最新的。
		var storageToken = false;
		try {
			storageToken = localStorage.getItem(this.sessionName + ":" + this.application());
			if (storageToken) {
				storageToken = (function () { try { return JSON.parse(storageToken); } catch (e) { return false; } }());
			}
		} catch (e) {
			console.warn(e);
			if (error_function) {
				error_function(e);
			}
			return false;
		}

		return storageToken;
	},


	/**
	 * 获取左token
	 * 如果没用传入回调函数，那么则直接返回当前左令牌，但是有可能会出现左令牌失效
	 * 正常操作是，传入一个回调函数，左令牌始终是保持最新的。
	 * 
	 * @param	{Function}	#fn
	 */
	leftToken: function (fn) {
		if (typeof fn != "function") {
			var storageToken = requestAPI.prototype.token();
			if ((function () { try { return storageToken['session_left_token']; } catch (e) { return false; } }())) {
				return storageToken['session_left_token'];
			} else {
				return '';
			}
		} else {
			requestAPI.prototype.submit({
				callback: function () {
					//从本地缓存中同步获取指定 key 对应的内容。
					var leftToken = "";
					var storageToken = requestAPI.prototype.token();
					if ((function () { try { return storageToken['session_left_token']; } catch (e) { return false; } }())) {
						leftToken = storageToken['session_left_token'];
					}
					fn(leftToken);
				}
			});
			return true;
		}
	},


	/**
	 * 获取 websocketToken
	 * 
	 * @param	{Function}	#fn
	 */
	websocketToken: function (fn) {
		if (typeof fn != "function") {
			var storageToken = requestAPI.prototype.token();
			if ((function () { try { return storageToken['session_websocket_token']; } catch (e) { return false; } }())) {
				return storageToken['session_websocket_token'];
			} else {
				return '';
			}
		} else {
			requestAPI.prototype.submit({
				callback: function () {
					//从本地缓存中同步获取指定 key 对应的内容。
					var websocketToken = "";
					var storageToken = requestAPI.prototype.token();
					if ((function () { try { return storageToken['session_websocket_token']; } catch (e) { return false; } }())) {
						websocketToken = storageToken['session_websocket_token'];
					}
					fn(websocketToken);
				}
			});
			return true;
		}
	}









};