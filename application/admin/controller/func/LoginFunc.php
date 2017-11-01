<?php

namespace app\admin\controller\func;

use think\Controller;
use app\common\model\SysUser;
use app\common\model\SysConfig;
use app\common\tool\UserTool;
use app\common\model\SysLogTmp;
/**
 * 登录类
 * @author "baotian0506<510909033@qq.com>"
 *
 */
class LoginFunc 
{
    /**
     * 登录方法
     * @param unknown $data uni_account,password,type
     * @return true|string  
     */
    public static function login($data){
        // uni_account , password ,type
        $json=[];
        try {
            
            $where=[
                'uni_account'=>$data['uni_account'],
                'type'=>$data['type'],
            ];
            $user = SysUser::get($where);
            if (!$user){
                exception($msg='用户名错误');
            }
            $password = self::password($data['password'], $user->solt);
            if ($user->password != $password){
                exception($msg='密码错误');
            }
            UserTool::init($user);
            return true;
        } catch (\Exception $e) {
             isset($msg)?SysLogTmp::log('登录异常', $e->getMessage(), 0, __METHOD__.',line='.__LINE__):'';
            return isset($msg)?$msg:'系统错误';
        }
    }
    
    private static function password($password,$solt){
        return sha1($password.$solt);
    }
    
}
