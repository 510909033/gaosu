<?php

namespace app\way\controller;

use think\Controller;
use think\Request;
use app\common\model\WayLogOut;

class OutController extends Controller
{
    
    public function out(){
        $secret_key = input('post.secret_key');
    
        $hard_car_number = input('post.car_number');
        $hard_car_color= input('post.car_color');
        $hard_car_type= input('post.car_type');
        $hard_pos = input('post.uniqu_device_identifier');
    
        //         $hard_car_number = '1500356780';
    
        $res = [];
        try {
            if ($secret_key != 'tf_hk'){
                exception('密钥错误');
            }
            $inController = \controller('In');
            $res = $inController->checkData($hard_car_number, $hard_car_color, $hard_car_type,$hard_pos);
            if ($res){
                $way_log = [];
                $way_log = $res; 
                $way_log['out_time'] = $way_log['time'];
                $way_log['out_pos_id'] = $way_log['pos_id'];
                if ($this->save($way_log)){
                    //成功写入
                    $res = true;
                }else{
                    $res[] = '数据效验成功，但是将数据写入way_log_out表失败';
                }
            }else{
                $res = $inController->reason;
            }
    
    
        } catch (\Exception $e) {
            $res[] = '出现了异常：'.$e->getMessage();
        }
    
        if (true === $res){
            return 'success';
        }else{
            return json_encode($res);
        }
    }
    
    /**
     * 保存数据到出口表
     * @return boolean
     */
    private function save($way_log_in){
        $wayLogOut = WayLogOut::create($way_log_in);
        return $wayLogOut && $wayLogOut->id;
    }
    
}
