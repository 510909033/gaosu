<?php
namespace app\order\controller;

use think\Controller;
use think\Log;
use app\common\model\SysOrder;

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


    /**
     * 异步接收订单返回信息，订单成功付款后，处理订单状态并批量生成用户的二维码
     * @param int $id 订单编号
     */
    public function notify($id = 0)
    {
		require PAY_PATH . '/example/notify.php';

        $notify = new \PayNotifyCallBack();
        $notify->handle(true);

        //找到匹配签名的订单
        $order = SysOrder::get($id);
        if (!isset($order)) {
            \Log::write('未找到订单，id= ' . $id);
        }
        $succeed = ($notify->getReturnCode() == 'SUCCESS') ? true : false;
        if ($succeed) {

            \Log::write('订单' . $order->id . '生成二维码成功');

            $order->save(['flag' => '2'], ['id' => $order->id]);
            \Log::write('订单' . $order->id . '状态更新成功');
        } else {
            \Log::write('订单' . $id . '支付失败');
        }
    }  
    


    /**
     * 使用微信支付SDK生成支付用的二维码
     * @param $id
     */
    public function wxpayQRCode($id)
    {
        $order = Order::get($id);
        if (!isset($order)) $this->error('查询不到正确的订单信息');

        //判断是否已经存在订单 url，如果已经存在且未超过2小时就使用旧的，否则生成新的
        $interval = date_diff(new \DateTime($order->update_time), new \DateTime());
        $h = $interval->format('%h');

        if (isset($order->pay_url) && $order->pay_url != '' && $h < 2) {
            $url = $order->pay_url;
        } else {
            $order->money = 0.01;
            $notify = new \NativePay();
            $input = new \WxPayUnifiedOrder();
            $input->setBody("支付 0.01 元");
            $input->setAttach("test");
            $input->setOutTradeNo(\WxPayConfig::MCHID . date("YmdHis"));
            $input->setTotalFee($order->money);
            $input->setTimeStart(date("YmdHis"));
            $input->setTimeExpire(date("YmdHis", time() + 600));
            $input->setGoodsTag("QRCode");
            $input->setNotifyUrl("http://localhost/index/index/notify/id/" . $order->id);
            $input->setTradeType("NATIVE");
            $input->setProductId($id);
            $result = $notify->getPayUrl($input);
            $url = $result["code_url"];

            //保存订单标识
            $order->save();
        }
        //生成二维码
        return $this->getUrlQRCode($url);
    }

}

