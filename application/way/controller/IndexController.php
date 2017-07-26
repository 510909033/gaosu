<?php

namespace app\way\controller;

use think\Controller;

class IndexController extends Controller
{
    public function indexAction(){
        
        echo '<meta name="viewport" content="width=device-width, initial-scale=1" />';
        dump($_SESSION);
        
        $arr = [];
        $arr['初始化config表数据'] = url('way/user/initconfig');
        $arr['测试创建用户车辆二维码'] = url('way/user/bindindex');
        $arr['获取access_token'] = url('way/index/testAccess_token');
    
    
        foreach ($arr as $text=>$link){
    
            echo "<a href='{$link}'>{$text}</a><br /><br />";
    
        }
    }
    
    
    public function testAccess_tokenAction(){
        $auth = new AuthExtend();
        dump($auth->getAccessToken());
    }
    
}
