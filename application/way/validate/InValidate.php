<?php
namespace app\way\validate;
use think\Validate;
class InValidate extends Validate{
    
    protected $rule = [
        'status'  =>  'eq:1',
        'verify' =>  'eq:1',
    ];
}