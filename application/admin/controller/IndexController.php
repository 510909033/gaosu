<?php

namespace app\admin\controller;

use think\Controller;
use app\common\tool\ConfigTool;
use app\common\model\WayRecord;

class IndexController extends PublicController //implements IPriviCheckInterf
{
    protected function _initialize(){
        parent::_initialize();
        
    }
    
    public function sqlAction(){
        
        $wayRecord = new WayRecord();
    }
    
    public function indexAction(){
        
        $data = 'UD50P/sHS2UjdfFgPAwjqJHcZRrwk3fL4827E7khahnkDrYMp1us7Im9omkttOvEQ7kaLo7BVj++Oe5qJd/YcPTWF/0DOW2i7vfUlGxFgkY9rbaNs0qiz5+tcN0M3g+7Luqdnc6OCvXK8dYhUSOcodM0NMJ3jW7I8t4gzNlrBKI=';
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
