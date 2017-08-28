<?php

namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Cookie;

class LogoutController extends PublicController
{
    
    public function logoutAction(){
        Session::destroy();
        Cookie::clear();
        $this->redirect('admin/logout/index');
    }
}
