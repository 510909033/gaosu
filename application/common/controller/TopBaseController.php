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
    
    protected function _initialize(){
        parent::_initialize();
        if ( $this instanceof IPriviCheckInterf ){
            $this->checkPrivi();
        }
    }
    
    /**
     * 调用方法前请先登录
     */
    protected function checkPrivi(){
        trace(__METHOD__.','.__LINE__ .',class='.get_class($this) );
         if ( !UserTool::isPrivi() ){
             exception('没有权限');
//              $this->error('没有权限');
         }
    }
    
    protected function debugTrace($log,$level='log'){
        trace($log,$level);
    }
    
    
}
