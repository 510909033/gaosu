<?php

namespace app\way\controller;

use think\Controller;
use app\common\tool\UserTool;
use think\Request;

class AuthController extends Controller
{
    /**
     * 第一步 授权
     */
    public function authIndexAction(){
    
        $request = \request();
        $auth = new \weixin\auth\AuthController();
        $redirect_uri = url('way/auth/return_url','','',true);
        $state = urlencode($request->get('state'));
        $is_unit = false;
        $auth->redirect($redirect_uri, $state, $is_unit);
    
    }
    
    /**
     * 第二步 解析code
     */
    public function return_urlAction(){
        $request = \request();
        $auth = new \weixin\auth\AuthController();
        $user = $auth->getResultByCode();
        if ($user && $user->id){
            UserTool::init($user);
            
            $url = urldecode($request->get('state'));
            
            header('location:'.$url);
            exit;
        }else{
            return 'fail'; 
        }
    }
    
    
    
}
