<?php

namespace app\way\controller;

use think\Controller;
use think\Session;
use app\common\tool\UserTool;
use app\common\model\SysUser;
use app\common\tool\ConfigTool;
use think\Env;
use app\common\controller\NeedLoginController;
mb_internal_encoding("UTF-8");
/**
 * 此类应该已经停止使用，转移到了common下
 * @deprecated
 * @author Administrator
 * @see \app\common\controller\NeedLoginController
 */
class NeedLoginController extends Controller
{
    
    
    /**
     * 如果传递了数字，则会默认此账号已登录,调试模式使用
     * @var false|int
     * 
     */
    protected static $debug_user_id = null;
    
    public function __construct(){
        self::$debug_user_id = Env::get('debug.user_id');
        parent::__construct();
        $this->check()  ;
    }
    
    protected function check(){
       
        Session::boot();
        if (self::$debug_user_id){
            $this->debugTrace('debug_user_id模式,debug_user_id='.self::$debug_user_id);
            $user = SysUser::get(self::$debug_user_id);
            if (!$user){
                exception('调试模式下，用户不存在，user_id='.self::$debug_user_id);
            }
            UserTool::init($user);

        }
        
        if ( !UserTool::getIs_login() ){
            
            if ($this->request->isAjax()){
                $json = [
                    'status' => 0 ,
                    'reason'=>ConfigTool::$JSON_REASON_NEED_LOGIN,
                    'html'=>'尚未登录请登录',
                ];
                json($json)->send();
                
            }
            
            $this->redirect('way/auth/authindex',['state'=> urlencode(urlencode(\request()->url(true))) ]);
        }
    }
    
    protected function debugTrace($log,$level='log'){
        trace($log,$level);
    }
    
    
}
