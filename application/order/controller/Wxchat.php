<?php
namespace app\order\controller;

use youwen\exwechat\exLog;
use youwen\exwechat\exWechat;
use youwen\exwechat\exRequest;
use youwen\exwechat\api\message\template;
use think\Config;
use weixin\auth\AuthController;

/**
 * 微信交互控制器
 * @author baiyouwen <youwen21@yeah.net>
 */
class Wxchat
{
    // 微信消息对象
    private $exRequest;
    // 数组消息体 － 微信消息对象的局部信息
    private $_msg;

    /**
     * 微信消息入口
     * 
     * @author baiyouwen
     */
    public function indexAction()
    {      

        $token = Config::get('wxchat.Token'); 
        exLog::log($_GET, 'get');
        exLog::log(file_get_contents("php://input"), 'post');
        // 微信验证控制器
        $exwechat = new exWechat($token);
        // 接口配置 和 签名验证
        $ret = $exwechat->authentication();
        if(is_bool($ret)){
            if(!$ret){
                exit('签名验证失败');
            }
        }else{ //接口配置  开发者模式接入
            $data = [];
            $auth = new AuthController();
            $accessToken = $auth->getAccessToken(false);
            $message = new template($accessToken);
            $res = $message->send($data);

            var_dump($res);
        }
    }


    public function wxPay($orderdate){
//        require PAY_PATH . '/lib/WxPay.Api.php';
//        require PAY_PATH . '/example/WxPay.JsApiPay.php';
//        require PAY_PATH . '/example/log.php';

        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();


        $input = new \WxPayUnifiedOrder();
        $input->SetBody($orderdate['body']);
        $input->SetAttach("speed");
        $input->SetOut_trade_no($orderdate['out_trade_no']);
        $input->SetTotal_fee($orderdate['total_fee']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        //$input->SetGoods_tag("test");
        $input->SetNotify_url(Config::get('wxpay.NOTIFY_URL'));
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
