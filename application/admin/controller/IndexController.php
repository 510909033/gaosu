<?php

namespace app\admin\controller;

use think\Controller;
use app\common\controller\NeedLoginController;
use app\common\tool\ConfigTool;
use app\common\model\WayRecord;

class IndexController extends NeedLoginController //implements IPriviCheckInterf
{
    
    public function sqlAction(){
        
        $wayRecord = new WayRecord();
    }
    
    public function indexAction(){
        
        $data = 'PHyLDGrpD+upNjP9D/RSazGD+1szvrlZDfOpeJ972ly1jvDBMVuj7Gog+cP92agfomoF6f+Rt66CbYbA6+5EXQZvYoLs7faoPKxe5AMEmQ9X589tJV4kPKnVVD3cTjE383YPiv5cGs5VXrXHnJmIR2SZitq3J3wivCbp09nr8TI=';
        $data = base64_decode($data);
        
        openssl_public_decrypt($data, $decrypted, ConfigTool::$RSA_PUBLIC_KEY);
        
        dump($decrypted);
        return ;
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
    
    public function rsaAction(){
        $data = str_repeat('我', 30);
        $crypted='';
        $key = ConfigTool::$RSA_PRIVATE_KEY;
        \openssl_private_encrypt($data, $crypted, $key);
        
//         echo (base64_encode($crypted));
        $filename = VENDOR_PATH.'phpqrcode/phpqrcode.php';
        if (!is_file($filename)){
            exception('文件不存在：'.$filename);
        }
        require_once $filename;
//         echo base64_encode($crypted);exit;
        \QRcode::png(base64_encode($crypted) , false , QR_ECLEVEL_L , 6);
        exit;
        
//         $en = Rsa::encrypt($data, 'user');
//         dump(Rsa::decrypt($en, 'user'));
        
    }
    
}
