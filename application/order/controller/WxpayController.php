<?php
namespace app\order\controller;

use think\Config;
use think\Controller;
use app\common\model\SysOrder;

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
        
        
        
/*         $orderid = $_GET['id'];
        if (!isset($orderid) || empty($orderid) || !is_numeric($orderid))
            $this->error('查询不到正确的订单信息'); */
        $orderid = '201707191700278553924516';
            
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
        
        var_dump($order);die();


        $jsApiParameters = $tools->GetJsApiParameters($order);
        
        $this->assign('order', $order);
        $this->assign('jsApiParameters', $jsApiParameters);
        return $this->fetch('jsapi');

    }

    /**
     * 异步接收订单返回信息，订单成功付款后     
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


}
