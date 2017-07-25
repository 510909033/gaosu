<?php

namespace app\way\controller;

use think\Controller;
use app\common\tool\UserTool;

class AuthController extends Controller
{
    /**
     * 第一步 授权
     */
    public function authIndexAction(){
    
        $auth = new \weixin\auth\AuthController();
        $redirect_uri = url('way/auth/return_url','','',true);
        $state = urlencode(input('state'));
        $is_unit = false;
        
        $redirect_uri = urlencode($redirect_uri);
        
        $auth->redirect($redirect_uri, $state, $is_unit);
    
    }
    
    /**
     * 第二步 解析code
     */
    public function return_urlAction(){
        $auth = new \weixin\auth\AuthController();
        $user = $auth->getResultByCode();
        if ($user && $user->id){
            UserTool::init($user);
            
            $url = urldecode(input('state'));
            
            header('location:'.$url);
            exit;
        }else{
            return 'fail'; 
        }
    }
    
    
    
}
