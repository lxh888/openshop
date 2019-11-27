<?php
/** ---- eapie ----
 * 优狐积木框架，让开发就像组装积木一样简单！
 * 解放千千万万程序员！这只是1.0版本，后续版本势如破竹！
 * 
 * QQ群：523668865
 * 开源地址 https://gitee.com/lxh888/openshop
 * 官网 http://eonfox.com/
 * 后端框架文档 http://cao.php.eonfox.com
 * 
 * 作者：绵阳市优狐网络科技有限公司
 * 电话/微信：18981181942
 * QQ：294520544
 */




namespace eapie\source\request\merchant;

use eapie\main;
use eapie\error;

/**
 * 导入
 */
class import extends \eapie\source\request\merchant
{

    // 接口秘钥
    private $API_KEY = 'qq23094';
    private $API_SECRET = '';

    private $merchant_id = '';
    private $error = array();

    /**
     * api: MERCHANTIMPORT
     * @author green
     *
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function api_imoport($input)
    {
        // 检查请求参数
        if (empty($input['key']) || !is_string($input['key'])) {
            throw new error('缺少API KEY');
        }
        if (empty($input['seller_nick']) || !is_string($input['seller_nick'])) {
            throw new error('缺少商家昵称');
        }

        ignore_user_abort(true);//客户端断开连接时不中断脚本的执行 
        set_time_limit(0);//设置最大执行时间
        ini_set('memory_limit', '-1');//内存限制
        $this->API_KEY = $input['key'];
        $merchant_id = $this->import_list($input['seller_nick']);
        if ($merchant_id) {
            $this->_import_detail($merchant_id);
        }

        return $this->error;
    }

    /**
     * api: MERCHANTIMPORTONE
     * @author green
     *
     * @return [type] [description]
     */
    public function api_import_one($input)
    {
        // 检查请求参数
        if (empty($input['key']) || !is_string($input['key'])) {
            throw new error('缺少API KEY');
        }
        if (empty($input['shop_goods_id']) || !is_string($input['shop_goods_id'])) {
            throw new error('缺少商品ID');
        }

        $this->API_KEY = $input['key'];
        $this->_import_detail(null, $input['shop_goods_id']);

        return $this->error;
    }

    /**
     * 发送请求
     * @author green
     * @param  string $url   [网址]
     * @param  array  $param [请求参数]
     * @return array
     */
    public function _request($url, $param)
    {
        // 初始化
        $ch = curl_init();
        $url .= '?' . http_build_query($param);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 执行
        $response = curl_exec($ch);

        // 关闭
        curl_close($ch);

        return json_decode($response, true) ?: $response;
    }


    //===========================================
    // 商品列表
    //===========================================


    /**
     * 导入商品列表
     * @author green
     *
     * @param  string $url [网站]
     * @return bool
     */
    private function import_list($seller_nick)
    {
        // 请求参数
        $url = 'http://api.onebound.cn/taobao/api_call.php';
        $param = array(
            'api_name' => 'item_search_shop',
            'key' => $this->API_KEY,
            'seller_nick' => $seller_nick,
            'page' => 1,
        );

        // 发送请求
        $list = array();
        $response = $this->_request($url, $param);
        if (!empty($response['error'])) {
            throw new error($response['error']);
        }
        if (empty($response['items']['item'])) {
            throw new error('该商家没有商品');
        } else {
            $list = $response['items']['item'];
            $this->_recursion_shop_goods($url, $param, $list);
        }

        $merchant_id = $this->_add_merchant($response['user']);
        $this->_add_shop_goods($merchant_id, $list);
        return $merchant_id;
    }

    /**
     * 递归商家全部商品
     * @author green
     * @param  string   $url    [网址]
     * @param  integer  $param  [请求参数]
     * @param  array    $list   [商品列表]
     * @return array
     */
    private function _recursion_shop_goods($url, $param, &$list)
    {
        $param['page'] += 1;
        $response = $this->_request($url, $param);
        if (!empty($response['items']['item'])) {
            $list = array_merge($list, $response['items']['item']);
            $this->_recursion_shop_goods($url, $param, $list);
        }
    }

    /**
     * 添加商家
     * @author green
     *
     */
    private function _add_merchant($input)
    {
        // 格式化数据
        $data = array();
        $data['merchant_id'] = "taobao_{$input['shopid']}";
        $data['merchant_name'] = $input['title'];
        $data['merchant_insert_time'] = time();
        $data['merchant_update_time'] = time();

        // 是否已存在
        $row = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
        if ($row) {
            return $data['merchant_id'];
        }

        // 插入数据，记录日志
        if (object(parent::TABLE_MERCHANT)->insert($data)) {
            return $data['merchant_id'];
        } else {
            throw new error('添加商家失败');
        }
    }

    /**
     * 添加商品
     * @author green
     *
     * @param  string $merchant_id [商家主键ID]
     * @param  array  $list        [商品列表]
     * @return void
     */
    private function _add_shop_goods($merchant_id, $list)
    {
        // 查询商家商品
        $select = array('shop_goods_id');
        $where = array(array('shop_id = [+]', $merchant_id), array('[and] shop_goods_id like "taobao_%"'));
        $shop_goods = object(parent::TABLE_SHOP_GOODS)->select(array('select' => $select, 'where' => $where));
        $shop_goods_ids = array();
        foreach ($shop_goods as $i) {
            $shop_goods_ids[] = $i['shop_goods_id'];
        }

        // 格式化数据
        $data = array();
        $timestamp = time();
        foreach ($list as $i) {
            $item = array(
                'shop_goods_id' => "taobao_{$i['num_iid']}",
                'shop_id' => $merchant_id,
                'shop_goods_name' => $i['title'],
                'shop_goods_img' => $i['pic_url'],
                'shop_goods_sales' => $i['sales'],
                'shop_goods_insert_time' => $timestamp,
                'shop_goods_update_time' => $timestamp,
            );
            // 是否已添加
            if (in_array($item['shop_goods_id'], $shop_goods_ids)) {
                continue;
            }

            $data[] = $item;
        }
        if (!$data) {
            $this->error[] = '没有新增商品';
            return;
        }

        // 插入
        $res = object(parent::TABLE_SHOP_GOODS)->insert_batch($data);
        if (!$res) {
            throw new error('添加商品失败');
        }
    }


    //===========================================
    // 商品详情
    //===========================================


    /**
     * 导入商品详情
     * @author green
     * @param  string $merchant_id [商家ID]
     * @return void
     */
    private function _import_detail($merchant_id, $shop_goods_id = null)
    {
        $url = 'http://api.onebound.cn/taobao/api_call.php';
        $param = array(
            'api_name' => 'item_get',
            'key' => $this->API_KEY,
            'num_iid' => '',
        );

        // 是否单个商品
        if ($shop_goods_id) {
            $where = array(array('shop_goods_id = [+]', $shop_goods_id));
        } else {
            $where = array(array('shop_id = [+]', $merchant_id), array('[and] shop_goods_id like "taobao_%"'));
        }
        $select = array('shop_goods_id', 'shop_goods_sn', 'shop_goods_insert_time', 'shop_goods_update_time');
        $shop_goods = object(parent::TABLE_SHOP_GOODS)->select(array('select' => $select, 'where' => $where));

        // 循环商品
        foreach ($shop_goods as $i) {
            // 是否已导入
            if ($i['shop_goods_sn'] || $i['shop_goods_update_time'] > $i['shop_goods_insert_time']) {
                continue;
            }

            // 发送请求
            $shop_goods_id = $i['shop_goods_id'];
            $param['num_iid'] = substr($shop_goods_id, 7);
            $response = $this->_request($url, $param);
            if (!empty($response['error'])) {
                $this->error[] = "请求商品详情失败：商品ID{$shop_goods_id}，{$response['error']}";
                continue;
            }

            // 开启事务
            db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

            $res = $this->_edit_shop_goods($shop_goods_id, $response['item']['desc']);
            if (!$res) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                continue;
            }
            if (!empty($response['item']['props_list'])) {
                $res = $this->_add_shop_goods_spu($shop_goods_id, $response['item']['props_list'], $response['item']['props_img']);
                if (!$res) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    continue;
                }
            }
            if (!empty($response['item']['skus']['sku'])) {
                $res = $this->_add_shop_goods_sku($shop_goods_id, $response['item']['skus']['sku']);
                if (!$res) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    continue;
                }
            }
            if (!empty($response['item']['item_imgs'])) {
                $res = $this->_add_shop_goods_img($shop_goods_id, $response['item']['item_imgs']);
                if (!$res) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    continue;
                }
            }
            if (!empty($response['item']['video'])) {
                $res = $this->_add_shop_goods_video($shop_goods_id, $response['item']['video']);
                if (!$res) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    continue;
                }
            }

            // 提交事务
            db(parent::DB_APPLICATION_ID)->query('COMMIT');
        }
    }

    /**
     * 编辑商品信息
     * @author green
     * @param  string $shop_goods_id    [商品主键ID]
     * @param  string $desc             [商品详情]
     * @return bool
     */
    private function _edit_shop_goods($shop_goods_id, $desc)
    {
        $data = array();
        $data['shop_goods_details'] = $desc;
        $data['shop_goods_sn'] = '1';
        $data['shop_goods_update_time'] = time();
        $where = array(array('shop_goods_id = [+]', $shop_goods_id));

        $res = object(parent::TABLE_SHOP_GOODS)->update($where, $data);
        if (!$res) {
            $this->error[] = "编辑商品详情失败：商品ID{$shop_goods_id}";
            return false;
        }

        return true;
    }

    /**
     * 添加商品属性
     * @author green
     * @param  string $shop_goods_id    [商品主键ID]
     * @param  array  $list              [商品属性，索引数组]
     * @param  array  $list_img          [商品属性图片，索引数组]
     * @return void
     */
    private function _add_shop_goods_spu($shop_goods_id, $list, $list_img)
    {
        // 格式化数据
        $data = array();
        $spues = array();
        $timestamp = time();
        // 属性
        foreach ($list as $key => $value) {
            $spu = explode(':', $value);
            $spu = $spu[0];
            if (array_key_exists($spu, $spues)) {
                continue;
            }
            $item = array(
                'shop_goods_spu_id' => object(parent::TABLE_SHOP_GOODS_SPU)->get_unique_id(),
                'shop_goods_spu_parent_id' => '',
                'shop_goods_id' => $shop_goods_id,
                'shop_goods_spu_name' => $spu,
                'shop_goods_spu_info' => '',
                'image_url' => '',
                'shop_goods_spu_insert_time' => $timestamp,
                'shop_goods_spu_update_time' => $timestamp,
            );
            $data[] = $item;
            $spues[$spu] = $item['shop_goods_spu_id'];
        }
        // 属性子类
        foreach ($list as $key => $value) {
            $spu = explode(':', $value);
            $data[] = array(
                'shop_goods_spu_id' => object(parent::TABLE_SHOP_GOODS_SPU)->get_unique_id(),
                'shop_goods_spu_parent_id' => $spues[$spu[0]],
                'shop_goods_id' => $shop_goods_id,
                'shop_goods_spu_name' => $spu[1],
                'shop_goods_spu_info' => $key,
                'image_url' => isset($list_img[$key]) ? $list_img[$key] : '',
                'shop_goods_spu_insert_time' => $timestamp,
                'shop_goods_spu_update_time' => $timestamp,
            );
        }
        if (!$data) {
            $this->error[] = "商品属性为空，商品ID{$shop_goods_id}";
            return false;
        }

        // 插入
        $res = object(parent::TABLE_SHOP_GOODS_SPU)->insert_batch($data);
        if (!$res) {
            $this->error[] = "添加商品属性失败，商品ID{$shop_goods_id}";
            return false;
        }

        return true;
    }

    /**
     * 添加商品规格
     * @author green
     * @param  string $shop_goods_id    [商品主键ID]
     * @param  array  $list             [商品规格，索引数组]
     */
    private function _add_shop_goods_sku($shop_goods_id, $list)
    {
        // 查询商品属性
        $select = array('shop_goods_spu_id', 'shop_goods_spu_info');
        $where = array(array('shop_goods_id = [+]', $shop_goods_id), array('[and] shop_goods_spu_parent_id <> ""'));
        $shop_goods_spu = object(parent::TABLE_SHOP_GOODS_SPU)->select(array('select' => $select, 'where' => $where));
        $spues = array();
        foreach ($shop_goods_spu as $i) {
            $spues[$i['shop_goods_spu_info']] = $i['shop_goods_spu_id'];
        }

        // 格式化数据
        $data = array();
        $timestamp = time();
        foreach ($list as $i) {
            // 获取SPU ID
            $spu_id = ',';
            $spu = explode(';', $i['properties']);
            foreach ($spu as $j) {
                $spu_id .= "{$spues[$j]},";
            }

            $data[] = array(
                'shop_goods_sku_id' => "taobao_{$i['sku_id']}",
                'shop_goods_id' => $shop_goods_id,
                'shop_goods_spu_id' => $spu_id,
                'shop_goods_sku_stock' => $i['quantity'],
                'shop_goods_sku_price' => $i['price'],
                'shop_goods_sku_market_price' => $i['orginal_price'],
                'shop_goods_sku_insert_time' => $timestamp,
                'shop_goods_sku_update_time' => $timestamp,
            );
        }
        if (!$data) {
            $this->error[] = "商品规格为空，商品ID{$shop_goods_id}";
            return false;
        }

        // 插入
        $res = object(parent::TABLE_SHOP_GOODS_SKU)->insert_batch($data);
        if (!$res) {
            $this->error[] = "添加商品规格失败，商品ID{$shop_goods_id}";
            return false;
        }

        return true;
    }

    /**
     * 添加商品图片
     * @author green
     * @param  string $shop_goods_id    [商品主键ID]
     * @param  array  $list             [商品图片列表，索引数组]
     * @return void
     */
    private function _add_shop_goods_img($shop_goods_id, $list)
    {
        // 格式化数据
        $data = array();
        $timestamp = time();
        foreach ($list as $i) {
            $data[] = array(
                'shop_goods_image_id' => object(parent::TABLE_SHOP_GOODS_IMAGE)->get_unique_id(),
                'shop_goods_id' => $shop_goods_id,
                'image_url' => $i['url'],
                'shop_goods_image_time' => $timestamp,
            );
        }
        if (!$data) {
            return true;
        }

        // 插入
        $res = object(parent::TABLE_SHOP_GOODS_IMAGE)->insert_batch($data);
        if (!$res) {
            $this->error[] = "添加商品图片失败，商品ID{$shop_goods_id}";
            return false;
        }

        return true;
    }

    /**
     * 添加商品视频
     * @author green
     * @param  string $shop_goods_id    [商品主键ID]
     * @param  array  $list             [商品视频列表，索引数组]
     * @return void
     */
    private function _add_shop_goods_video($shop_goods_id, $list)
    {
        // 格式化数据
        $data = array();
        $timestamp = time();
        foreach ($list as $i) {
            // 保存图片到七牛云
            // $binary = file_get_contents("http:{$i['url']}");
            // $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload(array('binary' => $binary));
            // $image_id = isset($response['image_id']) ? $response['image_id'] : '';
            $image_id = '';

            $data[] = array(
                'shop_goods_file_id' => object(parent::TABLE_SHOP_GOODS_FILE)->get_unique_id(),
                'shop_goods_id' => $shop_goods_id,
                'file_url' => $i['url'],
                'image_id' => $image_id,
                'shop_goods_file_time' => $timestamp,
            );
        }
        if (!$data) {
            return true;
        }

        // 插入
        $res = object(parent::TABLE_SHOP_GOODS_FILE)->insert_batch($data);
        if (!$res) {
            $this->error[] = "添加商品图片失败，商品ID{$shop_goods_id}";
            return false;
        }

        return true;
    }

}