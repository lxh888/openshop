<template>
	<view class="content">
		<!-- 头部 -->
		<view class="title-box">
			<view class="search-box">
				<uni-icon type="search" size="30" class="search" @click="search" color= "#8e8e8e"></uni-icon>
				<input type="text"  v-model="searchData" @confirm="search" placeholder="输入关键词搜索商品" class="input"/>
			</view>
			<text class="cancel" @click="back">取消</text>
		</view>
		<!-- 内容 -->
		<view class="search-content">
			<view class="search-title">
				<text class="search-title-text">最近搜索</text>
				<uni-icon type="trash" size="30" class="trash" color="#8e8e8e" @click="clearHistory" v-if="SearchHistory[0]"></uni-icon>
				<view class="content-text" v-for="(item,historyIndex) in SearchHistory" :key="historyIndex">
					<text class="history" @click="toSearch(item)" v-if="item">{{item}}</text>
				</view>
			</view>
			<!-- <view class="search-title">
				<text class="search-title-text">搜索发现</text>
				<view class="content-text" v-for="(item,findIndex) in find" :key="findIndex">
					<text class="find" @click="toSearch(item)">{{item}}</text>
				</view>
			</view> -->
			<!-- <view class="search-title">
				<text class="search-title-text">常用分类</text>
				<view class="content-text classification-box" v-for="(item,classificationIndex) in classification" :key="classificationIndex">
					<text class="classification">{{item}}</text>
				</view>
			</view> -->
		</view>
	</view>
</template>

<script>
	import uniIcon from "@/components/uni-icon/uni-icon.vue"
	export default {
		data() {
			return {
				searchData:'',
				SearchHistory:[],
				find:[
						"人气热销榜",
						"脉动",
						"电动牙刷",
						"豆豆鞋",
						"尤妮佳",
						"沐浴露",
						"日东红茶",
						"榴莲",
						"雅诗兰黛",
					],
				classification:[
					"基础护肤",
					"面部清洁",
					"兰蔻",
					"雅诗兰黛",
					"资生堂",
					"悦诗风吟",
					"眼部护理"
				],
				content: []
			};
		},
		components:{
			uniIcon
		},
		methods:{
			clearHistory(){
				uni.setStorageSync('SearchHistory', []);
				this.SearchHistory=[];
			},
			search(){
				var data=this.searchData;
				uni.navigateTo({
					url: '../../pages/product/list?searchText='+data
				})
				//缓存
				let SearchHistory = uni.getStorageSync('SearchHistory')

					if (!SearchHistory)
						SearchHistory = []

					if (!SearchHistory.includes(data)) {
						SearchHistory.push(data)
					}

					uni.setStorageSync('SearchHistory', SearchHistory)
			},
			back() {
				uni.navigateBack({
					delta: 1
				});
			},
			toSearch(data){
				uni.navigateTo({
						url: '../../pages/product/list?searchText='+data
					
				})
			}
		},
		onShow:function() {
// 			uni.request({
// 				url: 'http://softstore.applinzi.com/?data={"api_list": ["API"]}',
// 				method: 'GET',
// 				data: {},
// 				success: res => {
// 					console.log(res.data.data.api_list.data)
// 					this.content = res.data.data.api_list.data
// 				},
// 				fail: () => {},
// 				complete: () => {}
// 			});
           this.SearchHistory= uni.getStorageSync('SearchHistory') || []
		}
	}
</script>

<style scoped lang="stylus" ref="stylesheet/stylus">
	.content 
		// padding 25upx 0
		margin 0 auto
		width 95%
		margin-top 10upx
		.title-box
			width 100%
			height 100upx
			display flex
			justify-content center   // 水平居中
			align-items center       // 垂直居中
			flex-direction row
			.search-box
				display flex
				align-items center       // 垂直居中
				flex-direction row
				width 85%
				border 1px solid #d3d3d3
				background #e6e6e6
				border-radius 10upx
				.search
					color #d3d3d3
				.input
					padding-left 30upx
					width 80%
					font-size 32upx
			.cancel
				font-size 35upx
				color #333
				width 15%
				text-align center
		.search-content
			width 100%
			.search-title
				width 100%
				position relative
				margin-bottom 20upx
				.search-title-text
					display block
					font-weight 600
				.trash
					font-size 20upx
					position absolute
					right 10upx
					top 0
				.content-text
					display inline-block
					color #636363
					font-size 28upx
					margin 20upx 10upx
					.history
						background #F5F5F5
						padding 5upx 20upx
						border-radius 50upx
					.find
						background #F5F5F5
						padding 5upx 20upx
						border-radius 50upx
				.classification-box
					background #d3d3d3
					text-align center
					// line-height 120upx
					width 120upx
					height 120upx
					border-radius 50%
					vertical-align top
					.classification
						margin-top 25upx 
						width 80upx
						display inline-block
						

</style>