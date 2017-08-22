<?php
namespace app\common\controller;
use think\Controller;
use think\Session;
use app\common\tool\UserTool;
use app\common\model\SysUser;
use app\common\tool\ConfigTool;
use think\Env;
use app\common\interf\IPriviCheckInterf;
use think\Db;
use app\common\model\SysLogTmp;
use think\Request;
class TopBaseController extends Controller
{
    public function __construct(Request $request=null){
        parent::__construct($request);
        if ( $this instanceof IPriviCheckInterf ){
            $this->checkPrivi();
        }
    }
    
    /**
     * 调用方法前请先登录
     */
    protected function checkPrivi(){
         if ( !UserTool::isPrivi() ){
             exception('没有权限');
//              $this->error('没有权限');
         }
    }
    
    protected function debugTrace($log,$level='log'){
        trace($log,$level);
    }
    
    
}