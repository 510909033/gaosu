<?php

namespace app\way\controller;

use think\Controller;
use app\common\tool\UserTool;
/**
 * 微信授权执行类
 * @author Administrator
 *
 */
class AuthController extends Controller
{
    /**
     * 第一步 授权
     */
    public function authIndexAction(){
    
        $auth = new \weixin\auth\AuthExtend();
        $redirect_uri = url('way/auth/return_url','','',true);//一般不变

        if (!input('state')){
            $state = urlencode(url('way/user/bindindex','','',true));
        }else{
            $state = urlencode(input('state'));//解析code最后跳转的url 包含http
        }
        $is_unit = false;
        
        
        $redirect_uri = urlencode($redirect_uri);
        $auth->redirect($redirect_uri, $state, $is_unit);
    
    }
    
    /**
     * 第二步 解析code
     */
    public function return_urlAction(){
        $auth = new \weixin\auth\AuthExtend();
        $user = $auth->getResultByCode();
        if ($user && $user->id){
            UserTool::init($user);
            
            $url = urldecode(input('state'));
            
            header('location:'.$url);
            exit;
        }else{
            dump($auth->getError());
            return 'fail'; 
        }
    }
    
    
    
}
