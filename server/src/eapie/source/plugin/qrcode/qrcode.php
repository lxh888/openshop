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



namespace eapie\source\plugin\qrcode;

// 生成二维码
// 只需要使用phpqrcode.php就可以生成二维码了，当然您的PHP环境必须开启支持GD2。
// phpqrcode.php提供了一个关键的png()方法，其中参数$text表示生成二位的的信息文本；
// 参数$outfile表示是否输出二维码图片 文件，默认否；
// 参数$level表示容错率，也就是有被覆盖的区域还能识别，分别是 L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）； 
// 参数$size表示生成图片大小，默认是3；
// 参数$margin表示二维码周围边框空白区域间距值；
// 参数$saveandprint表示是否保存二维码并 显示。
class qrcode
{

    /**
     * 二维码保存临时路径
     * @var string
     */
    const TEMP_DIR = ''; 

    /**
     * 二维码类型
     * @var array
     */
    public $type = array(
        'user_recommend' => '用户推荐',
        'merchant_money_plus' => '商家收款'
    );

    public function  __construct()
    {
        require_once __DIR__.'/phpqrcode/phpqrcode.php';
    }

    // ==========================================

    /**
     * 创建一个二维码
     * 
     * $config = {
     *  data    [mix] [可选] [二维码内容]
     *  path    [str] [可选] [二维码文件保存路径，默认不保存]
     *  level   [str] [可选] [级别,也是容错率，默认L，(L,M,Q,H)不区分大小写]
     *  size    [int] [可选] [二维码大小，默认3(不是图片像素)]
     *  padding [int] [可选] [二维码内边距，默认0(不是图片像素)]
     * }
     * 
     * @param [arr] $config [参数配置]
     * @exit  image
     */
    public function output($config = array())
    {    
        //二维码内容
        $data = '';
        if (isset($config['data'])) {
            $data = $config['data'];
            if (is_array($data))
                $data = json_encode($data);
        }

        //二维码文件名，false为 不保存
        $path = false;
        if (isset($config['path']) && is_string($config['path']))
            $path = $config['path'];

        //级别,也是容错率
        $level = 'L';
        if (isset($config['level']) && in_array($config['level'], array('l','L','m','M','q','Q','h','H')))
            $level = $config['level'];

        //二维码大小
        $size = 3;
        if (isset($config['size']) && is_numeric($config['size']))
            $size = $config['size'];

        //二维码内边距
        $padding = 0;
        if (isset($config['padding']) && is_numeric($config['padding']))
            $padding = $config['padding'];

        \QRcode::png($data, $path, $level, $size, $padding);

        // 是否保存到文件
        if (!$path) {
            exit();
        }
    } 

}