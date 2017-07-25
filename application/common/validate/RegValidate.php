<?php
namespace app\common\validate;

use think\Validate;

class RegValidate extends Validate
{
    protected $rule = [
        'uni_account'  =>  'require',
        'password' =>  'require|length:40',
        'solt' =>  'require|length:5',
        'regtime' =>  'require|length:10',
        'type' =>  'require|max:20',
    ];

}