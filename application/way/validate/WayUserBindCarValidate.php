<?php
namespace app\way\validate;
use think\Validate;

class WayUserBindCarValidate extends Validate{
    
//     protected $scene = [
//         'save'  =>  [
//             'car_number' =>  'require|length:7',
//             'user_id'  =>  'require|gt:0|number',
//             'openid' =>  'require',
//             'status' =>  'require|number|in:0,1,2',
//             'verify' =>  'require|number|in:0,1,2,3',
//             'create_time' =>  'require|number|gt:0',
//             'car_color' =>  'require',
//             'username' =>  'require',
//             'identity_card' =>  'require',
//             'phone' =>  'require|number|length:11',
//             'car_type_id' =>  'require|number|gt:0',
//             'engine' =>  'require',
//             'brand' =>  'require',
//             'chassis_number' =>  'require',
//             '_agree'=>'require|accepted',
            
//         ],
//     ];
    
    protected $rule = [
        'car_number' =>  'require|length:7|regex:/^[\x{4e00}-\x{9fa5}]{1}[a-z0-9]{6}$/ui|unique:way_user_bind_car',
        'user_id'  =>  'require|gt:0|number|unique:way_user_bind_car',
        'openid' =>  'require',
        'status' =>  'require|number|in:0,1,2',
        'verify' =>  'require|number|in:0,1,2,3',
        'create_time' =>  'require|number|gt:0',
        'car_color' =>  'require|number|gt:0',
        'username' =>  'require|regex:/^[\x{4e00}-\x{9fa5}]+$/u',
        'identity_card' =>  'require|min:15|max:18',
        'phone' =>  'require|number|length:11',
        'car_type_id' =>  'require|number|gt:0',
        'engine' =>  'require',
        'brand' =>  'require',
        'chassis_number' =>  'require',
        '_agree'=>'require|accepted',
        'reg_time' =>  'require',//1501084800
    ];
    
    protected $message = [
        'car_number'=>'车牌号选项错误',
        'car_number.require'=>'请填写车牌号',
        'car_number.length'=>'车牌号只能为7位字符',
        'car_number.regex'=>'车牌号格式为一位汉字+6位字母数字组合' ,
        'car_number.unique'=>'车牌号已存在',  
        'user_id.unique'=>'您已绑定过车辆',
        'opendid'=>'读取用户信息失败',
        'car_color'=>'请填写车辆颜色',
        'username.require'=>'请填写车主姓名',
        'username.regex'=>'车主姓名只能为中文',
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