<?php

namespace app\common\model;

use think\Model;
use think\Validate;
use think\Loader;
use app\way\validate\WayUserBindCarValidate;

class WayUserBindCar extends Model
{
    /**
     * 添加数据时，允许添加的字段
     * @var array
     */
    private $config_addOne_field = ['username','identity_card','phone','car_number','car_type_id','engine','car_color','brand','user_id','openid','reg_time','chassis_number','car_qrcode_path','status','verify','create_time']; 
    
    /**
     * 新绑定车辆唯一入口
     * @param unknown $data
     * @return \app\common\model\WayUserBindCar|boolean
     */
    public function addOne($data){
        
        /**
         * @var WayUserBindCarValidate $validate
         */
        $validate = Loader::validate('WayUserBindCarValidate');
        
        if($validate->batch()->check($data)){
            return $this->create($data , $this->config_addOne_field);
        }else{
            $this->error = $validate->getError();
            return false;
        }
    }
    
}
