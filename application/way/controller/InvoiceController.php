<?php

namespace app\Way\controller;

use think\Controller;
use think\Request;
use app\way\controller\func\UserBindCarFuncController;
use app\common\model\Invoice;
use app\way\controller\NeedLoginController;
use app\common\tool\UserTool;
use think\Exception;
use app\common\model\SysUser;
use app\common\tool\ConfigTool;
use app\common\model\SysLogTmp;
use app\common\tool\TmpTool;
use think\helper\Time;
use app\common\model\WayCarType;
use app\common\model\SysConfig;
use think\Session;
use think\View;
use youwen\exwechat\ErrorCode;
use think\File;
use think\Validate;
use vendor\SMS\SmsSingleSender;
use app\way\validate\WayUserBindCarValidate;

use think\Paginator;
use think\Db;
use app\admin\model\User;//引入模型层

use app\common\tool\Verifier;
use app\common\tool\AjaxTool;
use app\common\model\WayInvoice;
use app\way\model\Records;


class InvoiceController extends \app\common\controller\NeedLoginController
{
    public function indexAction()
    {
         return $this->fetch('invoice');
    }

    public function addAction(){
    	$model = new WayInvoice();


    	$model->title=\request()->post('title');
    	$model->duty_paragraph = \request()->post('duty_paragraph');
    	$model->user_id = UserTool::getUser_id();

    	$model->save();


    	return $this->success('提交成功','index/index/index');

    }
}
