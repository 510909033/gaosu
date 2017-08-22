<?php

namespace app\admin\controller;

use think\Controller;
use app\common\controller\NeedLoginController;

class PublicController extends NeedLoginController
{
    protected function _initialize(){
        //表示在后台模块
        defined('ADMIN_MODULE') ?'':define('ADMIN_MODULE', TRUE);
        
    }
}
