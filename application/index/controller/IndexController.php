<?php
namespace app\index\controller;

use think\Controller;
use app\common\tool\UserTool;

class IndexController extends controller
{
    public function indexAction()
    {



    UserTool::getUser_id();




    	return view('index');
    }
}
