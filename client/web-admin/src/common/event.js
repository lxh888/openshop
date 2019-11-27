av({
	
	id: 'common-event',
	event: {
		
		//只加载一次的事件
		ready: function(){
			console.log('ready common-event');
			var _this = this;
			//按回车键时提交
			$(document).unbind("keyup").on('keyup', function(e){
				if(e.keyCode === 13){
					if( $("textarea").is(":focus") ){  
				        return false;
				    }
			       	
			       	for(var i in _this.data.keyupFunctions){
			       		if( typeof _this.data.keyupFunctions[i] == 'function'){
			       			_this.data.keyupFunctions[i]();
			       		}
			       	}
				}
			});
		}
		
		
	},
	data: {
		
		keyupFunctions:{},
		
		/**
		 * 用户退出
		 */
		userLogout: function(){
			//提交数据
			new requestAPI().submit({
				request : {
					s:["USERLOGOUT"],
					},
				callback:function(r){
					layer.msg("退出成功", {icon: 1, time: 2000});
					setTimeout(function(){
						av.router(av.router().url, '#/userLogin').request();
					}, 2000);
				},
			});
		},
		
		
		
	}
	
});
