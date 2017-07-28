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
use app\common\tool\TmpTool;
use think\helper\Time;
use app\common\model\WayCarType;
use app\common\model\SysConfig;

class UserController extends NeedLoginController
{
 
    /**
     * 车辆绑定显示页
     * @param number $id
     * @return \think\response\View
     */
    public function bindIndexAction($id=0){
        
        
        $vars = [];
        $form = [];
        try {
            if ($id){
                $wayUserBindCar = WayUserBindCar::get(array('id'=>$id,'user_id'=>UserTool::getUser_id()));
                if ($wayUserBindCar){
                   $form = $wayUserBindCar; 
                }
            }else{
                $where = [
                    'user_id'=>UserTool::getUser_id()
                ];
               
                $wayUserBindCar = WayUserBindCar::where($where)->find();
            
            
                if ($wayUserBindCar){
                    $form = $wayUserBindCar;
                }
            }
            $vars['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
        } catch (\Exception $e) {
            $vars['errcode'] = ConfigTool::$ERRCODE__EXCEPTION ;
            exception('系统错误');
        }
        
        if ($form){
            $form->reg_time = date('Y-m-d' ,$form->reg_time);
            $vars['form'] = $form->toJson();
        }else{
            $vars['form'] = '[]';
        }
        
        return \view('',$vars);
    }


    private function debugLogUserBindCarAction($array){
        TmpTool::arrayToArrayFile($array);
    }

    /**
     * 用户绑定车辆
     * 
     */
    public function userBindCarAction(){
        
        $json = [];
        try {
            if (!$this->request->isAjax()){
                exception('不是ajax提交');
            }
            
//             WayUserBindCar::destroy(['user_id'=>UserTool::getUser_id()]);
            
            
            $wayUserBindCar = new WayUserBindCar();
            
            $data = $this->request->post();
            
            $data['user_id'] = UserTool::getUser_id();
            $data['openid'] = UserTool::getUni_account();
            
            $data['car_qrcode_path'] = '';
            
            
            $data['status'] = 0;
            $data['verify'] = 0;
            $data['create_time'] = time();
            
            $data['reg_time'] = strtotime($data['reg_time']);
            
            //log debug
            $this->debugLogUserBindCarAction($data);
            
            $res = $wayUserBindCar->bindCar($data);
            if (!$res){
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = implode('<br />', (array)$wayUserBindCar->getError());
         
                $json['error'] = ($wayUserBindCar->getError());
                $json['debug']['res'] = $res;
            }else{
                if(!$res->car_qrcode_path){
                    WayUserBindCar::save_car_qrcode_path($res);
                }
                
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '绑定车辆成功';
                $json['view_url'] = url('way/user/bindindex',['id'=>$res->id]);
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = $e->getMessage();
            if (ConfigTool::IS_LOG_TMP){
                SysLogTmp::log('绑定车辆出现异常', ($e->getMessage()) , 0 ,__METHOD__);
            }
            TmpTool::arrayToArrayFile($e);
        }
   
        $json['method'] = $this->request->method();
        
        TmpTool::arrayToArrayFile($json,__FILE__);
        return json($json);
        
    }
    
    
    public function getCarTypeJsonAction(){
        $all = WayCarType::all();
        
        return json(array('records'=>$all));
    }


    public function getCarColorJsonAction(){
        
        $all = SysConfig::getListByType(SysConfig::TYPE_GS_COLOR_CONFIG);
        
        return json(array('records'=>$all));
    }
    
}
