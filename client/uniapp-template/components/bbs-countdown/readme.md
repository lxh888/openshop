### CountDown 倒计时

倒计时组件，组件名：``bbs-countdown``。

**使用方式：**

在 ``script`` 中引用组件

```javascript
import bbsCountdown from "@/components/bbs-countdown/bbs-countdown.vue"

export default {
    components: {bbsCountdown}
}
```

**bbsCountDown 示例：**

```html
	<view id="countDown">
		<!--
		:auto-start="false"
		绑定为false倒计时不会自动执行 需要方法触发 默认值为true
		不传auto-start 倒计时加载后会自动执行 请根据业务需求设置
		-->
		<bbs-countdown
			
			:time="timestamp"
			
			:auto-start="false"
			
			:now="now"
			@start="countDownStart"
			@abort="countDownAbort"
			@end="countDownEnd"
			@progress="progress"
			
			ref="countdown"
		></bbs-countdown>
		
		<button @click="start">倒计时开始</button>
		<button @click="abort">倒计时暂停</button>
	
	</view>

<script>

import bbsCountdown from "@/wxcomponents/bbs-countdown/bbs-countdown.vue"
	
export default {
	components: {
		bbsCountdown
	},
	data() {
		return {
			timestamp: 1000000,
		}
	},
	methods: {
  			// 现在的时间戳--必须传入
  			now() {
  				return Date.now()
  			},
  			// 点击按钮触发倒计时开始
  			start() {
  				this.$refs.countdown.start();
  			},
  			// 点击按钮触发倒计时暂停
  			abort() {
  				this.$refs.countdown.abort();
  			},
  			// 倒计时开始
  			countDownStart() {
  				uni.showModal({
  					title: '倒计时开始',
  					content: '倒计时开始',
  					showCancel: false
  				})
  			},
  			// 倒计时暂停
  			countDownAbort() {
  				uni.showModal({
  					title: '倒计时暂停',
  					content: '倒计时暂停',
  					showCancel: false
  				})
  			},
  			// 倒计时结束
  			countDownEnd() {
  				uni.showModal({
  					title: '倒计时结束',
  					content: '倒计时结束',
  					showCancel: false
  				})
  			},
  			// 倒计时进行中
  			progress(time) {
  				console.log(time);
  			},
  			
  			
  		}
}
</script>



```

**bbsCountDown 属性说明：**

## Props

### auto-start

- Type: `Boolean`
- Default: `true`

是否自动开始倒计时
Starts the countdown automatically when initialized.



### emit-events

- Type: `Boolean`
- Default: `true`

Emits the countdown events.

### interval

- Type: `Number`
- Default: `1000`

The interval time (in milliseconds) of the countdown progress.

**Note:** The value should not be less than `0`.

### now

- Type: `Function`
- Default: `() => Date.now()`

必传函数否则会报错
Generates the current time (in milliseconds) in a specific time zone.


### time

- Type: `Number`
- Default: `0`

倒数的时间(以毫秒为单位)。
The time (in milliseconds) to count down from.

**Note:** The value should not be less than `0`.





## bbsCountDown 事件说明

### start

倒计时开始
Starts the countdown.

### abort

终止倒计时
Aborts the countdown.

### end

监听倒计时结束
Ends the countdown.





#
## 特别说明

- time值只需要传入计算好的时间戳即可
- now必须传入一个函数return Date.now() 即可,不传入会报错
- 该组件没有样式,自己自由发挥!
- 组件基于@chenfengyuan/vue-countdown组件修改,部分属性,方法已删除,详情请看最下方原作者源码进行对比



## 鸣谢
	倒计时组件基于@chenfengyuan/vue-countdown组件修改
	[MIT](https://opensource.org/licenses/MIT) © [Chen Fengyuan](https://chenfengyuan.com)
	


  - [Website](https://fengyuanchen.github.io/vue-countdown)
  + 原作dome地址
  
  
  - [github](https://github.com/fengyuanchen/vue-countdown)
  + 原作者github地址
