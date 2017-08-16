<?php

namespace app\admin\controller;

use think\Controller;
use app\common\tool\UserTool;
use app\common\controller\NeedLoginController;
use app\common\interf\IPriviCheckInterf;

class IndexController extends NeedLoginController //implements IPriviCheckInterf
{
    
    public function indexAction(){
//         $user_id = 11111;
//         $debug=[];
//         $res = UserTool::getAllPrivileges($user_id,$debug);
        
//         $res = UserTool::getAllPrivi($user_id);
        
//         dump($res);
//         dump($debug);
        
//         dump(UserTool::isPrivi());

        file_put_contents('c:/25.debug', var_export($this->request->post(),true) . var_export($this->request->cookie(),true)   ) ;
        
    }
    
    public function chartAction(){
        
        return \view();
    }
    
}
