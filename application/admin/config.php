<?php
//配置文件
return [
	'charge_charge' => [
		'1'=>'1',
		'2'=>'2',
		'3'=>'3',
		'4'=>'4',
		'5'=>'5',
		'6'=>'6',
		'7'=>'7',
	],
	'wxpay' => [
		'APPID' => 'wx9e1d8fc5ee0c85a1',
        'MCHID' => '1485492832',
        'KEY' => '1f231dfsdfs548hg76754hghkghkh3jD',
        'APPSECRET' => '39ea8dc418b5ab3a03867a5937fe19fd',
        'NOTIFY_URL' => 'gs.jltengfang.com/order/wxpay/notify',
        'SSLCERT_PATH' => '../cert/apiclient_cert.pem',
        'SSLKEY_PATH' => '../cert/apiclient_key.pem',
        'REPORT_LEVENL' => 1
	],
	'wxchat'=>[
		'TOKEN'=>'E7vtcApgBl4TKJudfvd8',
	],

	'status' => [
		1 => '审核中',
		2 => '已审核',
	],
	//$status =  Config::get('status');
	//$status[$date['status']];
];