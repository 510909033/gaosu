<?php
namespace app\index\controller;

use think\Controller;
use app\common\tool\UserTool;
use app\admin\controller\PublicController;

class IndexController extends PublicController
{
    public function indexAction()
    {



    UserTool::getUser_id();




    	return view('index');
    }
}
