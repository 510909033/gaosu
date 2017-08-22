<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\tool\UserTool;
use app\admin\controller\func\LoginFunc;
use app\common\model\SysConfig;
use app\common\tool\ConfigTool;

class LoginController extends Controller 
{
    
    public function __construct(Request $request){
        parent::__construct($request);
        if (UserTool::getIs_login()){
            $this->redirect('admin/user/index');
        }
    }
    
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function indexAction()
    {
        $vars = [];
        
        $vars['form']['submit']['url'] = url('admin/login/login');
        
        return \view('index' , $vars );
    }

    public function loginAction(){
        $json=[];
        try {
            $data = $this->request->post();
            $data['type'] = SysConfig::REG_TYPE_ADMIN;
            $res = LoginFunc::login($data);
            if (true === $res ){
                $json['errcode'] = 0;
                $json['html'] = '登录成功';
                $json['location']['href'] = url('admin/user/index');
            }else{
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = $res;
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = $e->getMessage();
        }
        return \json($json);
    }
    
}
