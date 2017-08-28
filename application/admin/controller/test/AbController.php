<?php

namespace app\admin\controller\test;

use think\Controller;
use youwen\exwechat\api\http;
use think\Db;

class AbController extends Controller
{
    
    public function menuAction(){
        $url = url('admin/menu/save' ,'',false,true);
        $rand = uniqid().rand(1,10000);
        
        $data = array (
            'name' => '菜单unit',
            'fid' => '0',
            'status' => '1',
            'module' => 'admin'.$rand,
            'controller' => 'menu'.$rand,
            'action'=>'save'.$rand,
            'left_menu'=>1,
        );
        $res = http::curl_post($url, $data);
        
        $res = json_decode($res[1],true);
//         dump($res);
        echo $res['errcode'];
        
    }
    
    public function noAction(){
        return '';
        
    }
    
    public function bAction(){
        set_time_limit(1200);
        $count = 1;
        for ($i=0;$i<$count;$i++){
            $sql='update `a_portal_record` a,sys_config b ,sys_user c,way_user_bind_car d set 
                b.value= a.login_user_id,
    a.login_user_id= UUID() 
    where a.id=1 and b.id=9 and c.id=0 and d.id=0';
            
            $res = Db::execute($sql );
            if ($res !== 2){
                return $count;
            }
        }
        
        $res= Db::query("select a.*,b.* from  a_portal_record a,sys_config b  where a.id=1 and b.id=9 ");
        var_dump($res[0]['login_user_id'] === $res[0]['value']);
        
        return 'success';
    }
    public function cAction(){
        set_time_limit(1200);
        $count = 1000;
        for ($i=0;$i<$count;$i++){
            $sql='update `a_portal_record` set login_user_id= UUID() where id=1 ';
            $res = Db::execute($sql );
            $sql='update sys_config  set value= UUID() where id=9';
            $res = Db::execute($sql );
        }
    
        return 'success';
    }
    
}
