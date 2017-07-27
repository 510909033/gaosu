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
        'reg_time' =>  'require',//1501084800
        'chassis_number' =>  'require',
//         'car_qrcode_path' =>  'require',
        '_agree'=>'require|accepted',
    ];
    
    protected $message = [
        'car_number.unique'=>'车牌号已存在',  
        'user_id.unique'=>'您已绑定过车辆',
        'opendid'=>'读取用户信息失败',
        'car_color'=>'请填写车辆颜色',
        'username'=>'请填写车主姓名',
        'identity_card' =>  '请填写身份证号码',
        'phone' =>  '请填写手机号码',
        'car_type_id' =>  '请选择车型',
        'engine' =>  '请填写发动机号',
        'brand' =>  '请填写车辆品牌',
        'reg_time' =>  '请填写车辆注册时间',
        'chassis_number' =>  '请填写车架号',
        '_agree'=>'请同意吉林省高速缴费平台用户缴费协议',
    ];
    
}