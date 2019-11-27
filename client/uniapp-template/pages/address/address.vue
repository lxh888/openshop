<template>
	<view class="content b-t">
		<view class="list b-b" v-for="(item, index) in AddressList" :key="index" @click="checkAddress(item.id)">
			<view class="wrapper">
				<view class="address-box">
					<text v-if="item.default==1" class="tag">默认</text>
					<text class="address">{{item.province}} {{item.city}} {{item.district}} {{item.details}}</text>
				</view>
				<view class="u-box">
					<text class="name">{{item.consignee}}</text>
					<text class="mobile">{{item.phone}}</text>
				</view>
			</view>
			<text  class="yticon  icon-iconfontshanchu1" @click.stop="del(item.id)" style="color: #555555;font-size: 34upx;"></text>
			<text class="yticon icon-bianji" @click.stop="addAddress('edit', item.id)"></text>
		</view>
		<!-- <text style="display:block;padding: 16upx 30upx 10upx;lihe-height: 1.6;color: #fa436a;font-size: 24upx;">
			重要：添加和修改地址回调仅增加了一条数据做演示，实际开发中将回调改为请求后端接口刷新一下列表即可
		</text> -->
		
		<button class="add-btn" @click="addAddress('add')">新增地址</button>
	</view>
</template>

<script>
	import eonfox from '@/components/eonfox/eonfox.js';
	import fns from '@/components/eonfox/fns.js';
	var ef = new eonfox();
	export default {
		data() {
			return {
				source: 0,
				AddressList:[],
				addressList: [
					{
						name: '刘晓晓',
						mobile: '18666666666',
						addressName: '贵族皇仕牛排(东城店)',
						address: '北京市东城区',
						area: 'B区',
						default: true
					},{
						name: '刘大大',
						mobile: '18667766666',
						addressName: '龙回1区12号楼',
						address: '山东省济南市历城区',
						area: '西单元302',
						default: false,
					}
				]
			}
		},
		onLoad(option){
			console.log(option.source);
			var source = option.source;
			var _this=this;
			if(option.source){
				_this.source=option.source
			}
			// uni.setStorage({
			//     key: 'source_key',
			//     data: source,
			//     success: function () {
			//         console.log('success');
			//     }
			// });
		},
		onShow() {
			this.load()
		},
		methods: {
			load(){
				var that = this
				ef.submit({
					request: {
						addressList: ['USERADDRESSSELFLIST']
					},
					callback: function(data) {
						var dataList = fns.checkError(data, ['addressList'], function(errno, error) {
							uni.showToast({
								title: error,
								icon: 'none'
							});
						});
						console.log(dataList);
						if (dataList.addressList) {
							that.AddressList = dataList.addressList;
						}
					},
					error: function(err) {
						uni.showToast({
							title: '出错啦',
							icon: 'none'
						});
					}
				});
			},
			//选择地址
			checkAddress(item){
				var _this=this
				// uni.getStorage({
				//     key: 'source_key',
				//     success: function (res) {
				// 		_this.source = res.data;
				//         console.log(res.data);
				//     }
				// });
				if(_this.source == 1){
					//this.$api.prePage()获取上一页实例，在App.vue定义
					// this.$api.prePage().addressData = item;
					// uni.navigateBack()
					console.log(item)
					uni.redirectTo({
						url:'../order/createOrder?addressId='+item
					})
				}
				else if(_this.source == 2){
					console.log('13123',item);
					uni.setStorage({
						key:'nearByBusinessAddressID',
						data:item,
						success() {
							console.log('跳转')
							uni.switchTab({
							   url:'../nearbyBusiness/nearbyBusiness'
							})
						}
					})
				}
				else if(_this.source == 3){
					uni.setStorage({
						key:'nearByBusinessAddressID',
						data:item,
						success() {
							console.log('跳转')
							uni.navigateBack();
						}
					})
				}
			},
			del(id){
				var that = this
				uni.showModal({
				    title: '提示',
				    content: '是否删除地址信息',
				    success: function (res) {
				        if (res.confirm) {
				          ef.submit({
				          	request: {
				          		address: ['USERADDRESSSELFREMOVE',[{id:id}]]
				          	},
				          	callback: function(data) {
				          		var dataList = fns.checkError(data, ['address'], function(errno, error) {
				          			uni.showToast({
				          				title: error,
				          				icon: 'none'
				          			});
				          		});
				          		console.log(dataList);
				          		if(dataList.address){
				          			uni.showToast({
				          				title:'删除成功',
				          				success() {
				          					setTimeout(function(){
				          						that.load()
				          					},1000)
				          					
				          				}
				          			})
				          		}
				          	},
				          	error: function(err) {
				          		uni.showToast({
				          			title: '出错啦',
				          			icon: 'none'
				          		});
				          	}
				          });
				        } else if (res.cancel) {
				            console.log('用户点击取消');
				        }
				    }
				});
				
			},
			addAddress(type, item){
				uni.navigateTo({
					url: `/pages/address/addressManage?type=${type}&data=${JSON.stringify(item)}`
				})
			},
			//添加或修改成功之后回调
			refreshList(data, type){
				//添加或修改后事件，这里直接在最前面添加了一条数据，实际应用中直接刷新地址列表即可
				this.addressList.unshift(data);
				
				console.log(data, type);
			}
		}
	}
</script>

<style lang='scss'>
	page{
		padding-bottom: 120upx;
	}
	.content{
		position: relative;
	}
	.list{
		display: flex;
		align-items: center;
		padding: 20upx 30upx;;
		background: #fff;
		position: relative;
	}
	.wrapper{
		display: flex;
		flex-direction: column;
		flex: 1;
	}
	.address-box{
		display: flex;
		align-items: center;
		.tag{
			font-size: 24upx;
			color: $base-color;
			margin-right: 10upx;
			background: #fffafb;
			border: 1px solid #ffb4c7;
			border-radius: 4upx;
			padding: 4upx 10upx;
			line-height: 1;
		}
		.address{
			font-size: 30upx;
			color: $font-color-dark;
		}
	}
	.u-box{
		font-size: 28upx;
		color: $font-color-light;
		margin-top: 16upx;
		.name{
			margin-right: 30upx;
		}
	}
	.icon-bianji{
		display: flex;
		align-items: center;
		height: 80upx;
		font-size: 40upx;
		color: $font-color-light;
		padding-left: 30upx;
	}
	
	.add-btn{
		position: fixed;
		left: 30upx;
		right: 30upx;
		bottom: 16upx;
		z-index: 95;
		display: flex;
		align-items: center;
		justify-content: center;
		width: 690upx;
		height: 80upx;
		font-size: 32upx;
		color: #fff;
		background-color: $base-color;
		border-radius: 10upx;
		box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);		
	}
</style>
