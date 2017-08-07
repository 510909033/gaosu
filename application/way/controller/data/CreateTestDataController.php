<?php
namespace app\way\controller\data;

use think\Controller;
use app\way\controller\AdminController;
use extend\crypt\CryptExtend;
use crypt\driver\Rsa;
use app\common\model\SysUser;
use app\common\model\WayUserBindCar;
use app\way\controller\func\UserBindCarFuncController;

class CreateTestDataController extends AdminController
{
    
    /**
     * @var \Redis
     */
    private $redis;
    private $config_id_key = 'data_userid';
    
    protected function _initialize(){
        parent::_initialize();
        $this->redis = new \Redis();
        $this->redis->pconnect('127.0.0.1',6379);
    }
    
  

    public function initAction($step=0,$count=10){

        
        if (1 == $step){
            $maxid = SysUser::max('id') + 1;
            $this->redis->set($this->config_id_key,$maxid);
            
            return 'success';
        }else if (2 == $step){
            for ($i=0;$i<$count;$i++){
                $this->createSysUser();
            }
        }else{
            exit('step error');
        }
    }
    
    
    private function createSysUser()
    {
        

        $id = $this->redis->incr($this->config_id_key   );
        $solt = rand(10000,99999);
        $regtime = time() + ( (rand(1,1440)-720)*86400 );
        $data = array(
                'uni_account' => 'otmgKwBaD_I0cw.'.rand(0,9).$id,
                'password' => sha1($id.$solt),
                'solt' => $solt,
                'regtime' => $regtime,
                'type' => '1',
                'create_time' => $regtime + rand(1,1000),
                'update_time' => '0',
                'phone' => '0',
                'mobile' => '',
                'email' => '',
                'sex' => '0',
                'subscribe' => '0',
                'nickname' => '',
                'city' => '',
                'country' => '',
                'province' => '',
                'language' => '',
                'headimgurl' => '',
                'subscribe_time' => '0',
                'unionid' => '',
                'remark' => '',
                'groupid' => '',
                'tabid_list' => '',
                'user_type' => '1',
                'qrcode_path' => '',
                'scene' => '0'
        );
        
        $user = SysUser::regApi($data);
     
        if (0 === $user['err']){
            $this->createWayUserBindCar($user['user_model']);
        }else{
           
        }
    }

    public function createWayUserBindCar(Sysuser $sysUser)
    {
        $time = strtotime($sysUser->create_time);
        $way_user_bind_car = array(
            'user_id' => $sysUser->id,
            'openid' => 'on8fG0yXIh9fbtLhLz'.$sysUser->id,
            'status' => rand(0,1),
            'verify' => rand(0,3),
            'qrcode_version' => rand(0,10000),
            'create_time' => $time,
            'update_time' => '0',
            'car_number' => '吉'.strtoupper(str_pad( base_convert ($sysUser->id ,10,36) , 6,'A')),
            'car_color' => '0',
            'username' => '刘'.$sysUser->id,
            'identity_card' => str_pad($sysUser->id, 18,'0'),
            'phone' => str_pad($sysUser->id, 11,'0'),
            'car_type_id' => rand(1,1000),
            'engine' => '112244',
            'brand' => '路虎·朗逸',
            'reg_time' => $time - rand(0,8640000),
            'chassis_number' => '2222',
            'car_qrcode_path' => ''
        );
        $wayUserBindCar =WayUserBindCar::create($way_user_bind_car);
        $func = new UserBindCarFuncController();
        $wayUserBindCar->car_qrcode_path = $func->createQrcode($wayUserBindCar);
        $wayUserBindCar->save();
    }
}
