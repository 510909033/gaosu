<?php
namespace app\order\controller;

use youwen\exwechat\exLog;
use youwen\exwechat\exWechat;
use youwen\exwechat\exRequest;
use youwen\exwechat\api\message\template;

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
            $message = new template($token);
            $res = $message->send($data);

            var_dump($res);
        }
    }

}
