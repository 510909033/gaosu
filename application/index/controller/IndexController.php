<?php
namespace app\index\controller;

use think\Controller;
use app\common\tool\UserTool;
use app\common\controller\NeedLoginController;

class IndexController extends NeedLoginController
{
    public function indexAction()
    {



    UserTool::getUser_id();




    	return view('index');
    }
}
