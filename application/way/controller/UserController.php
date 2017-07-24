<?php

namespace app\Way\controller;

use think\Controller;
use think\Request;
use app\way\controller\func\UserBindCarFuncController;
use app\common\model\WayUserBindCar;
use think\Validate;
use weixin\auth\AuthController;
use app\common\model\SysConfig;

class UserController extends Controller
{
 
    /**
     * 用户绑定车辆
     */
    public function userBindCarAction(){
   
        
        $func = new UserBindCarFuncController();
        
        $wayUserBindCar = WayUserBindCar::get(1);
        $res = $func->createQrcode($wayUserBindCar);
        
        
        
        dump($res);
        
    }
    
    public function initAconfigAction(){
        $config = new SysConfig();
        $config->init_table_data();
        
    }
    
    public function auth(){
        
        $auth = new AuthController();
        $redirect_uri = url('way/user/return_url');
        $state = 'web';
        $is_unit = false;
        $auth->redirect($redirect_uri, $state, $is_unit);
        
    }
    
    public function return_url(){
        
        $auth = new AuthController();
        $user = $auth->getResultByCode();
        
        dump($user);
        
        
    }
    
    
}
