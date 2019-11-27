# 检查资金订单类的交易类型是否已经添加
`table/application/order.php::get_type()`
```
/**
 * 获取交易类型
 * 交易类型。充值、转账、红包、购货、退款，还有管理员后台的操作：人工收入、人工支出
 * 
 * @param   void
 * @return  array
 */
public function get_type(){
    return array(
        parent::TRANSACTION_TYPE_ADMIN_PLUS =>"人工收入",
        parent::TRANSACTION_TYPE_ADMIN_MINUS =>"人工支出",
        parent::TRANSACTION_TYPE_RECHARGE =>"充值",//第三方平台支付的，称之为充值
        parent::TRANSACTION_TYPE_TRANSFER =>"转账",//自家平台的支付，称之为转账
        parent::TRANSACTION_TYPE_CONSUME =>"消费",
        parent::TRANSACTION_TYPE_RECOMMEND_CREDIT =>"推荐积分奖励",
        parent::TRANSACTION_TYPE_RECOMMEND_MONEY =>"推荐钱包奖励",
        parent::TRANSACTION_TYPE_AWARD_MONEY =>"钱包奖励",
        parent::TRANSACTION_TYPE_HOUSE_PRODUCT_AWARD_MONEY =>"楼盘产品发布钱包奖
        //......
        //注意，一定要将 交易类型 添加到这里
    );
}
```
## 交易类型如何定义呢？
到初始化公共常量类文件中定义：`initialize/transaction_type.php`

## 示例代码
```
//将充值积分 提交给 用户
$user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
    "user_id" => $data['order_plus_account_id'],
    "user_credit_plus" => $data['daily_attendance_credit_value'],
    "user_credit_type" => parent::TRANSACTION_TYPE_RECHARGE  //注意，一定要将 交易类型 添加到资金订单类的交易类型列表中，这样才能合法验证
));
```
# 批量对同一个用户插入资金，如积分，处理办法
对同一个用户进行批量插入资金数据，这种需求特别的少，特别注意的地方就是，再批量插入的时候不要设置时间，如下代码就会返回false：
```
//将充值积分 提交给 用户
foreach($arrTest as $arrValue){
    $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
        "user_id" => $_SESSION['user_id'],
        "user_credit_plus" => $arrValue['credit_value'],
        "user_credit_type" => parent::TRANSACTION_TYPE_RECHARGE,
        "user_credit_time" => time()
    ));
}
```
正确的写法如下，去掉 `"user_credit_time" => time()`：
```
//将充值积分 提交给 用户
foreach($arrTest as $arrValue){
    $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
        "user_id" => $_SESSION['user_id'],
        "user_credit_plus" => $arrValue['credit_value'],
        "user_credit_type" => parent::TRANSACTION_TYPE_RECHARGE,
    ));
}
```