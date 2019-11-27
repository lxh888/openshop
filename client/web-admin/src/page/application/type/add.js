av({
	
	id: 'merchant-setting-type::add',
	selector: false,
	include : ["src/common/content.js", "src/common/application.js"],
	extend : ["common-application","common-content"],
	'export' : {template : "src/page/application/type/add.html"},
    'import' : function(e){
        this.template(e.template);
    },
    event:{
    	
		},
	main:function(){

	},
	data:{
		
		parent_data: undefined,//父级数据
		type_module_option: [{}],
		submitLock:false,
		typeLogoUploadFileMimeLimit: ['.jpg', '.png', '.gif', '.jpeg'],//判断图片合法性
		typeLogoUploadFile: { file: null, url: '', uploadPercent: '' },//上传的用户头像资源
		//点击选择分类
		eventUpLoad: function () {
			av.triggerClick($('[name="type_logo_image_id"]')[0]);
		},
		//选择文件后
		eventFileChange: function (ele, e) {
			var files = ele.files;
			if (files[0]) {
				//判断图片类型是否合法
				//获取前缀
				var suffix = files[0].name.replace(files[0].name.replace(/\.[\w]{1,}$/, ""), "");
				var legal = false;
				for (var l in this.typeLogoUploadFileMimeLimit) {
					if (this.typeLogoUploadFileMimeLimit[l] == suffix) {
						legal = true;
						break;
					}
				}

				if( !legal ){
				 layer.msg("“"+files[0].name+"” 图片格式不合法，只能上传'.jpg','.png','.gif','.jpeg'图片", {icon: 5, time: 3000});
				 return false;
				}
				this.typeLogoUploadFile.file = files[0];
				//获取图片地址
				this.typeLogoUploadFile.url = av.getfileURL(files[0])
				console.log('123123',this.typeLogoUploadFile);
			}

		
		},
		//取消文件
		eventCancel:function(){
			this.typeLogoUploadFile={ file: null, url: '', uploadPercent: '' }
			// this.typeLogoUploadFile.file=null;
			// this.typeLogoUploadFile.url='';
			//$('[name="type_logo_image_id"]').attr("src", "");
		},
	

		eventSubmit:function(){
			var _this = this;
			if( _this.submitLock ){
				return false;
			}else{
				_this.submitLock = true;
			}
			
			var form_input = {};
			var form_data = {};
			console.log('id',this.merchant_id)
			form_input.type_name = $.trim($('[name="type_name"]').val());
			form_input.type_info = $.trim($('[name="type_info"]').val());
			form_input.type_parent_id = $.trim($('[name="type_parent_id"]').val());
			form_input.type_module = $.trim($('[name="type_module"]').val());//分类模块
			form_input.type_label = $.trim($('[name="type_label"]').val());
			form_input.type_comment = $.trim($('[name="type_comment"]').val());
			form_input.type_json = $.trim($('[name="type_json"]').val());
			form_input.type_sort = $.trim($('[name="type_sort"]').val());
			form_input.type_state=$('[name="type_state"]').is(':checked')? 0 : 1;
			//如果是母婴
			if(this.applicationCheckMuYing()){
				form_input.type_merchant_usable=$('[name="type_merchant_usable"]').is(':checked')? 1 : 0;
			}
			console.log('打印',form_input)
			try {
				if( !form_input.type_module) throw "请选择模块";
				if( !form_input.type_name) throw "分类名称不能为空";
				
			}
			catch(err) {
				console.log(err),
		        layer.msg(err, {icon: 5, time: 2000});
		        return _this.submitLock = false;
				}
			if( _this.typeLogoUploadFile.file ){
				form_data = {file: _this.typeLogoUploadFile.file};
			}
			//提交数据
			this.submit({
				method:"submit",
				request:["APPLICATIONADMINTYPEADD", [form_input]],
				data : form_data,	
				progress : function(loaded, total, percent){
					//console.log(loaded, total, percent);
					if(percent == 100){
		        		//layer.msg('上传成功', {icon: 1, time: 1000});
		        	}else{
		        		layer.msg( Math.floor(percent)+"%" );
		        	}
				},
				error:function(){
					_this.submitLock = false;
				},
				success:function(){
					
					_this.submitLock = false;

					//成功提交的回调
					if( typeof _this.successSubmitCallback == 'function'){
						_this.successSubmitCallback();
					}
				}
			});
		}
	}
});