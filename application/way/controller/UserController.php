<?php

namespace app\Way\controller;

use think\Controller;
use think\Request;
use app\way\controller\func\UserBindCarFuncController;
use app\common\model\WayUserBindCar;
use app\way\controller\NeedLoginController;
use app\common\tool\UserTool;
use think\Exception;
use app\common\model\SysUser;
use app\common\tool\ConfigTool;
use app\common\model\SysLogTmp;

class UserController extends NeedLoginController
{
 
    /**
     * 车辆绑定显示页
     * @param number $id
     * @return \think\response\View
     */
    public function bindIndexAction($id=0){
        
        
        $vars = [];
        
        if ($id){
            $wayUserBindCar = WayUserBindCar::get($id);
            $vars['form'] = $wayUserBindCar->toJson();
        }else{
            $where = [
                'user_id'=>UserTool::getUser_id()  
            ];
            $wayUserBindCar = WayUserBindCar::where($where)->find();
            
            $start = microtime(true);
       
            
            if ($wayUserBindCar){
                $vars['form'] = $wayUserBindCar->toJson();
            }else{
                $vars['form'] ='[]';
            }
        }
        
        return \view('',$vars);
    }


    

    /**
     * 用户绑定车辆
     */
    public function userBindCarAction(){
        
        $json = [];
        try {
            
            $wayUserBindCar = new WayUserBindCar();
            
            $data = $this->request->post();
            
       
            $data['user_id'] = UserTool::getUser_id();
            $data['openid'] = UserTool::getUni_account();
            
            $data['car_qrcode_path'] = '尚未生成';
            
            
            $data['status'] = 0;
            $data['verify'] = 0;
            $data['create_time'] = time();
            
            $res = $wayUserBindCar->bindCar($data);
       
//             $res = $wayUserBindCar->addOne($data);
            if (!$res){
                $json['status'] = 0;
                $json['type'] = 'msg';
                $json['html'] = implode('<br />', (array)$wayUserBindCar->getError());
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
            
            if (ConfigTool::IS_LOG_TMP){
                SysLogTmp::log('绑定车辆出现异常', serialize($e) , 0 ,__METHOD__);
            }
        }
   
        $json['method'] = $this->request->method();
        
     
        return json($json);

        
    }
    


    
}
