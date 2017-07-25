<?php

namespace app\way\controller;

use think\Controller;
use think\Request;
use app\common\model\SysUser;
use app\common\model\WayUserBindCar;
use think\db\Query;
use app\way\validate\InValidate;
use app\common\model\WayLogIn;

class InController 
{
    /**
     * 程序错误原因
     * @var array
     */
    public $reason = [];
    
    public function demo_data(){
        $a = [];
        $a['car_number']='吉AT999C';
        $a['car_color']='红色';
        $a['car_type']='海康威视的车型字符串或数字';
        $a['time'] = '2017-07-18 12:22:33';
        $a['secret_key']='tf_hk';
        return  json_encode($a,JSON_UNESCAPED_UNICODE);
    }
    
 
    /**
     * 记录入口数据
     */
    public function inAction(){
        $res = [];
        try {
            if (!\request()->isPost()){
                exception('不是POST提交');
            }
            $secret_key = input('post.secret_key');
                   
            $hard_car_number = input('post.car_number');
            $hard_car_color= input('post.car_color');
            $hard_car_type= input('post.car_type');
            $hard_pos = input('post.uniqu_device_identifier');
            if ($secret_key != 'tf_hk'){
                exception('密钥错误');
            }
            $res = $this->checkData($hard_car_number, $hard_car_color, $hard_car_type,$hard_pos);
            if ($res){
                $way_log=[];
                $way_log = $res;
                $way_log['in_time'] = $way_log['time'];
                $way_log['in_pos_id'] = $way_log['pos_id'];
                if ($this->save($way_log)){
                    //成功写入
                    $res = true;
                }else{
                    $res[] = '数据效验成功，但是将数据写入in表失败';
                }
            }else{
                $res = $this->reason;
            }
            
            
        } catch (\Exception $e) {
            $res[] = '出现了异常：'.$e->getMessage();
        }
    
        if (true === $res){
            return 'success';
        }else{
            
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * 通知抬杆
     */
    public function notify_up(){
        
        
        
        
    }
    
    /**
     * 记录入口数据
     */
    public function debug(){
        $hard_car_number = time();
        $hard_car_color= time();
        $hard_car_type= time();
        $hard_pos = 'zzzz';
        
        $hard_car_number = '1500356780';
        
        $res = [];
        try {
            $res = $this->_in($hard_car_number, $hard_car_color, $hard_car_type,$hard_pos);
        } catch (\Exception $e) {
            $res[] = '出现了异常：'.$e->getMessage();
        }
        
        if (true === $res){
            dump('成功');
        }else{
            dump('失败');
            dump($res);
        }
    }
    
    /**
     * 根据车牌，车颜色，车型匹配用户，并保存到入口表
     * @param unknown $car_number
     * @param unknown $car_color
     * @param unknown $car_type
     * @return false|array
     */
    public  function   checkData($hard_car_number,$hard_car_color,$hard_car_type,$hard_pos){ 
        $hard_car_number = strtolower($hard_car_number);
        $closure = function(Query $query) use($hard_car_number){
            $query->where('car_number' , $hard_car_number);
            $query->field('id,user_id,status,verify,car_type_id,car_color');
        };
        
        //根据硬件解析的结果
        $user_car_color = null;
        $user_car_type = null;
        
        $pos_id = $this->getPosIdBy_hard($hard_pos);//拍照硬件所在的高速站点id
        
        $reason = [];//错误原因
        $validateData = [];
        
        $userBindCar = WayUserBindCar::get($closure);
        if ($userBindCar && $userBindCar->id){
            //判断车辆状态
            $validateData['status'] = $userBindCar->status;
            $validateData['verify'] = $userBindCar->verify;
            //判断颜色和车型
            $user_car_color = $this->getCarColorBy_hard($hard_car_color);
            $user_car_type = $this->getCarTypeIdBy_hard($hard_car_type);
            $validateData['car_color'] = $user_car_color;
            $validateData['car_type_id'] = $user_car_type;
            
            
            $inValidate = new InValidate();
            //添加验证规则
            $inValidate->rule('car_color' , 'eq:'.$userBindCar->car_color);
            $inValidate->rule('car_type_id' , 'eq:'.$userBindCar->car_type_id);
            
//             $inValidate->message('car_type_id' , '车类型不符');
            //验证
            if ( !$inValidate->batch()->check($validateData) ){
                $reason = $inValidate->getError();
            }
            
            
        }else{
            //未绑定车辆
            $reason[] = '车牌号'.$hard_car_number.'未绑定车辆';
        }
        
        if (!$reason){
            //写入入口表
            $way_log = [];
            $way_log['user_id'] = $userBindCar->user_id;
            $way_log['user_bind_car_id'] = $userBindCar->id;
            $way_log['time'] = time();
            $way_log['pos_id'] = $pos_id;
            $way_log['create_time'] = time();
            
            return $way_log;
        }
        $this->reason = $reason;
        
        return false;
    }
    
    /**
     * 保存数据到入口表
     * @return boolean
     */
    private function save($way_log_in){
        
        $field = [
          'user_id',
          'user_bind_car_id',
            'create_time',
            'in_time',
            'in_pos_id',
        ];
        $wayLogIn = WayLogIn::create($way_log_in,$field);
        
        return $wayLogIn && $wayLogIn->id;
    }
    
    
    
    /**
     * 扫描用户二维码，将信息发送给程序
     */
    private function qrcode(){
        try {
            $userkey = input('post.userkey');
            
            $hard_car_number = time();
            $hard_car_color= time();
            $hard_car_type= time();
            $hard_pos = 'zzzz';
            
            
            $res = $this->checkData($hard_car_number, $hard_car_color, $hard_car_type, $hard_pos);
            if ($res){
                $way_log=[];
                $way_log = $res;
                $way_log['in_time'] = $way_log['time'];
                $way_log['in_pos_id'] = $way_log['pos_id'];
                if ($this->save($way_log)){
                    $res = true;
                }else{
                    $res[] = '写入数据库失败';
                }
            }else{
                $res = $this->reason;
            }
        } catch (\Exception $e) {
            $res[] = '出现异常：'.$e->getMessage();            
        }
        
        if (true === $res){
            return 'success';
        }
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }
    
    
    

    /**
     * 根据硬件提供的车型，获取车型表车类型uni_id
     * @param unknown $car_type
     * @return int | false
     */
    private function getCarTypeIdBy_hard($car_type){
    
        return 2222;
    }
        
    /**
     * 根据硬件提供的颜色，获取配置表的颜色对应主键
     * @param unknown $car_type
     * @return int | false
     */
    private function getCarColorBy_hard($car_color){
    
        return 0;
    }
    
    /**
     * 根据硬件编号获取高速站点位置
     * @param unknown $hard_pos
     * @return number
     */
    private function getPosIdBy_hard($hard_pos){
        return 0;
    }
    
    
}
