<?php

namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Cookie;
use app\common\controller\NeedLoginController;
/**
 * 退出类
 * @author "baotian0506<510909033@qq.com>"
 *
 */
class LogoutController extends NeedLoginController
{
    
    public function logoutAction(){
        Session::destroy();
        Cookie::clear();
        $this->redirect('admin/logout/index');
    }
}
