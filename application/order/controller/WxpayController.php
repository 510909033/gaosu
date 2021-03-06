<?php
namespace app\order\controller;

use think\Config;
use think\Controller;
use app\common\model\SysOrder;
use think\Log;

/**
 * 微信支付控制器
 * @author dwc
 */
class WxpayController extends Controller
{


    /**
     * 微信支付入口
     * 
     * @author baiyouwen
     */
    public function indexAction()
    {      
        require PAY_PATH . '/lib/WxPay.Api.php';
        require PAY_PATH . '/example/WxPay.JsApiPay.php';
        require PAY_PATH . '/example/log.php';
        
        $orderid = $_GET['ordernum'];
        if (!isset($orderid) || empty($orderid) || !is_numeric($orderid))
            $this->error('查询不到正确的订单信息');
            
        $orderdate = SysOrder::get(['out_trade_no'=>$orderid]);
       
        if (!$orderdate || empty($orderdate))
            $this->error('查询不到正确的订单信息');            
        
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($orderdate['body']);
        $input->SetAttach("speed");
        $input->SetOut_trade_no($orderdate['out_trade_no']);
        $input->SetTotal_fee($orderdate['total_fee']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url(Config::get('wxpay.NOTIFY_URL'));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        
        if (isset($order['err_code'])&&!empty($order['err_code']))
            $this->error($order['err_code_des']);

        $jsApiParameters = $tools->GetJsApiParameters($order);
        
        $this->assign('order', $order);
        $this->assign('jsApiParameters', $jsApiParameters);
        return $this->fetch('jsapi');

    }

    /**
     * 异步接收订单返回信息，订单成功付款后     
     * @param int $id 订单编号
     */
    public function notifyAction()
    {
        require PAY_PATH . '/example/notify.php';
        $notify = new \PayNotifyCallBack();
        $notify->handle(false);
    }  


}
