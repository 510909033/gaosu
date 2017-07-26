<?php
namespace app\way\validate;
use think\Validate;

class WayUserBindCarValidate extends Validate{
    
    protected $scene = [
        'save'  =>  [
            'car_number' =>  'require|length:7',
            'user_id'  =>  'require|gt:0|number',
            'openid' =>  'require',
            'status' =>  'require|number|in:0,1,2',
            'verify' =>  'require|number|in:0,1,2,3',
            'create_time' =>  'require|number|gt:0',
            
            'car_color' =>  'require',
            'username' =>  'require',
            'identity_card' =>  'require',
            'phone' =>  'require|number|length:11',
            'car_type_id' =>  'require',
            'engine' =>  'require',
            'brand' =>  'require',
            'reg_time' =>  'require',
            'chassis_number' =>  'require',
            
        ],
    ];
    
    protected $rule = [
        'car_number' =>  'require|length:7|unique:way_user_bind_car',
        'user_id'  =>  'require|gt:0|number|unique:way_user_bind_car',
        'openid' =>  'require',
        'status' =>  'require|number|in:0,1,2',
        'verify' =>  'require|number|in:0,1,2,3',
        'create_time' =>  'require|number|gt:0',
        
        'car_color' =>  'require',
        'username' =>  'require',
        'identity_card' =>  'require',
        'phone' =>  'require|number|length:11',
        'car_type_id' =>  'require',
        'engine' =>  'require',
        'brand' =>  'require',
        'reg_time' =>  'require',
        'chassis_number' =>  'require',
//         'car_qrcode_path' =>  'require',
        
    ];
    
    protected $message = [
        'car_number.unique'=>'车牌号已存在',  
        'user_id.unique'=>'您已绑定过车辆',
    ];
    
}