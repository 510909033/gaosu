<?php

namespace app\Way\controller;

use think\Controller;
use think\Request;
use app\way\controller\func\UserBindCarFuncController;
use app\common\model\WayUserBindCar;
use think\Validate;
use weixin\auth\AuthController;
use app\common\model\SysConfig;
use app\common\model\SysArea;
use think\Db;
use app\common\model\SysUser;
use app\common\tool\UserTool;
use phpDocumentor\Reflection\Types\Parent_;
use think\Session;

class UserController extends Controller
{
    
    public function __construct(){
        parent::__construct();
//         Session::boot();
        if ( !UserTool::getIs_login() ){
            $this->redirect('way/auth/authindex',['state'=>urlencode(\request()->url())]);
        }
        
    }
 
    public function bindIndexAction($id){
        $vars = [];
        
        $wayUserBindCar = WayUserBindCar::get($id);
        
        $vars['form'] = $wayUserBindCar->toJson();
        
        return \view('',$vars);
    }
    
    public function indexAction(){
        $arr = [];
        $arr['初始化config表数据'] = url('way/user/initconfig');
        $arr['授权第一步'] = url('way/auth/auth');
        $arr['测试创建用户车辆二维码'] = url('way/user/userbindcar');
        
        
        foreach ($arr as $text=>$link){
            
            echo "<a href='{$link}'>{$text}</a><br /><br />";
            
        }
        
    }
    
    /**
     * 用户绑定车辆
     */
    public function userBindCarAction(){
        $json = [];
        try {
            UserTool::init(SysUser::get(11111));
            
            $wayUserBindCar = new WayUserBindCar();
            
            $data = \request()->post();
            
       
            $data['user_id'] = 11111;
            $data['openid'] = UserTool::getUni_account();
            $data['reg_time'] = '车辆注册时间';
            $data['chassis_number'] = '车架号';
            $data['car_qrcode_path'] = '尚未生成';
            
            
            $data['status'] = 0;
            $data['verify'] = 0;
            $data['create_time'] = time();
            
            
            $res = $wayUserBindCar->addOne($data);
            if (!$res){
                $json['status'] = 0;
                $json['type'] = 'msg';
                $json['html'] = implode('<br />', $wayUserBindCar->getError());
                $json['error'] = ($wayUserBindCar->getError());
            }else{
                $func = new UserBindCarFuncController();
                $car_qrcode_path = $func->createQrcode($res);
                if ($car_qrcode_path){
                    $res->car_qrcode_path = $car_qrcode_path;
                    $res->save();
                }
                
                $json['status'] = 1;
                $json['html'] = '绑定车辆成功';
                $json['view_url'] = url('way/user/bindindex',['id'=>$res->id]);
            }
        } catch (\Exception $e) {
            $json['status'] = 0;
            $json['error'] = $e->getMessage();
            $json['html'] = $e->getMessage();
        }
   
     
        return json($json);
        
//         dump($res);
//         return ;
        
//         $func = new UserBindCarFuncController();
        
//         $wayUserBindCar = WayUserBindCar::get(1);
//         $res = $func->createQrcode($wayUserBindCar);
//         dump($res);
        
    }
    
    public function initConfigAction(){
        $info = [];
        try {
            $config = new SysConfig();
            
//             $auth = new AuthController();
//             $auth->getAccessToken(false);
            
            $info['初始化配置表'] = $config->init_table_data();
            
            $res = Db::query("show tables");
            foreach ($res as $arr ){
                $info['表名'][] = current($arr);
//                 $info['表行数大小'][key($arr)] = Db::table(current($arr))->count('id');
            }
            
            if (SysArea::count('id') < 1){
                $info['初始化地区表'] = SysArea::execute(file_get_contents(EXTEND_PATH.'sys_area.sql'));
            }
        } catch (\Exception $e) {
            $info['异常'] = $e->getMessage();
        }
        

        
        dump($info);
    }
    

    
}
