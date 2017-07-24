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
 
    
    public function indexAction(){
        $arr = [];
        $arr['初始化config表数据'] = url('way/user/initconfig');
        $arr['授权第一步'] = url('way/user/auth');
        $arr['测试创建用户车辆二维码'] = url('way/user/userbindcar');
        
        
        foreach ($arr as $text=>$link){
            
            echo "<a href='{$link}'>{$text}</a><br /><br />";
            
        }
        
    }
    
    /**
     * 用户绑定车辆
     */
    public function userBindCarAction(){
   
        
        $func = new UserBindCarFuncController();
        
        $wayUserBindCar = WayUserBindCar::get(1);
        $res = $func->createQrcode($wayUserBindCar);
        
        
        
        dump($res);
        
    }
    
    public function initConfigAction(){
        $config = new SysConfig();
        
        $auth = new AuthController();
        
        $auth->getAccessToken(false);
//         return ;
        
        
        $config->init_table_data();
        
    }
    
    /**
     * 第一步 授权
     */
    public function authAction(){
        
        $auth = new AuthController();
        $redirect_uri = url('way/user/return_url');
        $state = 'web';
        $is_unit = false;
        $auth->redirect($redirect_uri, $state, $is_unit);
        
    }
    
    /**
     * 第二步 解析code
     */
    public function return_urlAction(){
        
        $auth = new AuthController();
        $user = $auth->getResultByCode();
        
        dump($user);
        
        
    }
    
    
}
