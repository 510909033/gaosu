<?php

namespace app\way\controller;

use think\Controller;
use app\common\tool\UserTool;
use think\Request;
use weixin\auth\AuthController;

class AuthController extends Controller
{
    /**
     * 第一步 授权
     */
    public function authIndexAction(){
    
        $request = new Request();
        $auth = new AuthController();
        $redirect_uri = url('way/auth/return_url');
        $state = urlencode($request->get('state'));
        $is_unit = false;
        $auth->redirect($redirect_uri, $state, $is_unit);
    
    }
    
    /**
     * 第二步 解析code
     */
    public function return_urlAction(){
        $request = new Request();
        $auth = new AuthController();
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
