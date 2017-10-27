<?php
namespace app\index\controller;

use think\Controller;
use app\common\tool\UserTool;
use app\common\controller\NeedLoginController;

class IndexController extends NeedloginController
{
    public function indexAction()
    {

    	return view('index');
    }
}
