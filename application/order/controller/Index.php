<?php
namespace app\order\controller;

use think\Controller;
use think\Log;

class Index extends Controller
{
    public function index()
    {
		require PAY_PATH . '/lib/WxPay.Api.php';
		require PAY_PATH . '/example/WxPay.JsApiPay.php';
		require PAY_PATH . '/example/log.php';

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

		$jsApiParameters = $tools->GetJsApiParameters($order);

		//获取共享收货地址js函数参数
		$editAddress = $tools->GetEditAddressParameters();

       	$this->assign('order', $order);
       	$this->assign('jsApiParameters', $jsApiParameters);
      	return $this->fetch('jsapi');
    }
}

