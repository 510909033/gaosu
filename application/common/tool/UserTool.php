<?php
namespace app\common\tool;
use app\common\model\SysUser;
use think\Session;

class UserTool {
    private static $is_login = null;
    private static $user_id=null;
    private static $uni_account = null;
    
    /**
     * @return the $is_login
     */
    public static function getIs_login()
    {
        if (is_null(self::$is_login)){
            self::$is_login = session('is_login');
        }
        return UserTool::$is_login;
    }

    /**
     * @return the $user_id
     */
    public static function getUser_id()
    {
        if (is_null(self::$user_id)){
            self::$user_id = session('user_id');
        }
        return UserTool::$user_id?UserTool::$user_id:0;
    }

    /**
     * @return the $uni_account
     */
    public static function getUni_account()
    {
        if (is_null(self::$uni_account)){
            self::$uni_account = session('uni_account');
        }
        return UserTool::$uni_account?UserTool::$uni_account:'';
    }

    public static function init(SysUser $sysUser){
        Session::boot();
        
        \session('user_id',$sysUser->id);
        \session('uni_account' , $sysUser->uni_account);
        \session('is_login',true);
        
        self::$user_id = $sysUser->id;
        self::$uni_account = $sysUser->uni_account;
        self::$is_login = true;
    }
    
    
    
}