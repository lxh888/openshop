<?php
require_once 'AipSpeech.php';


// 你的 APPID AK SK
const APP_ID = '15709852';
const API_KEY = '5b2WCra90E174cnejiU7UKS4';
const SECRET_KEY = 'xKVvTWoZyLjObQTSDVaKxPGyNvtnBhNT';
$client = new AipSpeech(APP_ID, API_KEY, SECRET_KEY);
$result = $client->synthesis('商家收款到账，100000.67元', 'zh', 1, array(
	//'cuid' => '',//用户唯一标识，用来区分用户，填写机器 MAC 地址或 IMEI 码，长度为60以内
	'spd' => 6,//语速，取值0-9，默认为5中语速
	'pit' => 6,//音调，取值0-9，默认为5中语调
    'vol' => 15,//音量，取值0-15，默认为5中音量
    'per' => 0,//发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女
));

// 识别正确返回语音二进制 错误则返回json 参照下面错误码
if(!is_array($result)){
	echo $result;
    //file_put_contents('audio.mp3', $result);
}


?>