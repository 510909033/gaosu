<?php
namespace app\order\controller;



class Index
{
    public function index()
    {
		require PAY_PATH . '/lib/WxPay.Api.php';
		require PAY_PATH . '/example/WxPay.JsApiPay.php';
		require PAY_PATH . '/example/log.php';

/*		$log_path = PAY_PATH ."/logs/".date('Y-m-d').'.log';
    	$logHandler= new \CLogFileHandler($log_path);
    	var_dump($logHandler);*/


		$tools = new \JsApiPay();
		$openId = $tools->GetOpenid();

		//②、统一下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = \WxPayApi::unifiedOrder($input);
		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		printf_info($order);
		$jsApiParameters = $tools->GetJsApiParameters($order);

		//获取共享收货地址js函数参数
		$editAddress = $tools->GetEditAddressParameters();

		return view('index', [
		    'jsApiParameters'  => $jsApiParameters,
		    'editAddress' => $editAddress,
		    'editAddress' => $editAddress,
		]);
    }



	function printf_info($data)
	{
	    foreach($data as $key=>$value){
	        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
	    }
	}
}

