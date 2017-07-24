<?php

namespace app\Way\controller;

use think\Controller;
use think\Request;
use app\way\controller\func\UserBindCarFuncController;
use app\common\model\WayUserBindCar;
use think\Validate;

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
    
    
}
