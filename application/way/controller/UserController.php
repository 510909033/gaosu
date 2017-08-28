<?php

namespace app\Way\controller;

use think\Controller;
use think\Request;
use app\way\controller\func\UserBindCarFuncController;
use app\common\model\WayUserBindCar;
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

class UserController extends \app\common\controller\NeedLoginController
{
    private $yzm_key = 'yzm_user_bind_car';
    
    public function testAction(){
        $vars=[];
        $auth = new \weixin\auth\AuthExtend();
        $appId = $auth->getAppkey();
        $appSecret = $auth->getAppsecret();
        $jssdk = new \weixin\jssdk\Jssdk($appId, $appSecret);
        
        
        
        $vars['signPackage'] = $jssdk->getSignPackage();
      
        return \view('demo_jssdk' , $vars);
    }
 
    public function indexAction(){
        
        
        $wayUserBindCar = WayUserBindCar::get( array('user_id'=>UserTool::getUser_id()));
        
        
        if ($wayUserBindCar){
            $this->redirect('way/user/read',['id'=>$wayUserBindCar->id] );
        }else{
            $this->redirect('way/user/create');
        }
    }
    
    public function createAction(){
        if (WayUserBindCar::getOne(UserTool::getUser_id())){
            exception('您已绑定了车辆');
        }
        
        View::share('form_url' , url('way/user/save'));
        View::share('form_method','post');
        
        
        return $this->read(0);
    }
    
    public function readAction($id){

        if (!WayUserBindCar::getOne(UserTool::getUser_id())){
            exception('您尚未绑定车辆');
        }
        
     
        $vars = [
            'id'=>$id,
        ];
        View::share('form_url' , url('way/user/update' , $vars));
        View::share('form_method','put');
        
        return $this->read($id);
    }
    
    
    public function saveAction(){
        return $this->userBindCar(true,false);
    }
    
    public function updateAction(){
        return $this->userBindCar(false,true);
    }
    
    /**
     * 车辆绑定显示页
     * @param number $id
     * @return \think\response\View
     */
    private function read($id){
        $vars = [];
        $form = [];
        try {
            if ($id){
//                 $wayUserBindCar = WayUserBindCar::get( array('id'=>$id,'user_id'=>UserTool::getUser_id()));
                $wayUserBindCar = WayUserBindCar::get( array('user_id'=>UserTool::getUser_id()));
                if ($wayUserBindCar){
                   $form = $wayUserBindCar; 
                }
            }else{
                $where = [
                    'user_id'=>UserTool::getUser_id()
                ];
               
                
                $wayUserBindCar = WayUserBindCar::where($where)->find();
                
            
                if ($wayUserBindCar){
                    $form = $wayUserBindCar;
                }
            }
            $vars['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
        } catch (\Exception $e) {
            $vars['errcode'] = ConfigTool::$ERRCODE__EXCEPTION ;
            exception('系统错误');
        }
        
        $image = new \stdClass();
     
        if ($form){
            $image->identity_image0 = $form->identity_image0?$form->identity_image0:'/way2/images/shenfenz_03.jpg';
            $image->identity_image1 = $form->identity_image1?$form->identity_image1:'/way2/images/shenfenz_03.jpg';
            $image->driving_license_image0 = $form->driving_license_image0?$form->driving_license_image0:'/way2/images/shenfenz_03.jpg';
            $image->driving_license_image1 = $form->driving_license_image1?$form->driving_license_image1:'/way2/images/shenfenz_03.jpg';
            
            
            $form->reg_time = date('Y-m-d' ,$form->getData('reg_time'));
            $vars['form'] = $form->toJson();
            $vars['form_array'] = (array)$image;
            $vars['reg_time'] = $form->reg_time;
        }else{
            $vars['form'] = '[]';
            $image->identity_image0 = '/way2/images/shenfenz_03.jpg';
            $image->identity_image1 ='/way2/images/shenfenz_03.jpg';
            $image->driving_license_image0 = '/way2/images/shenfenz_03.jpg';
            $image->driving_license_image1 = '/way2/images/shenfenz_03.jpg';
            
            $vars['form_array'] = (array)$image;
            $vars['reg_time'] = date('Y-m-d' ,time());
        }
 
       
        $vars['rsa_public_key'] = ConfigTool::$RSA_PUBLIC_KEY;
        
        
        
        
        return \view('bindindex_version2',$vars);
    }
    
    /**
     * phone
     * @return \think\response\Json
     */
    public function sendAction(){
        try {
            
            $max_hours = 10;
            $cache_key = 'cache_'.date('ymdH').UserTool::getUser_id();
            $value = (int)cache($cache_key);
            if ($value >= $max_hours){
                exception('发送验证码数量达到了规则上限');
            }
            
            cache($cache_key , $value+1);
            
            
            $json=[];
            //var_dump(VENDOR_PATH . 'SMS\SmsSender.php');die();
            require_once VENDOR_PATH . 'SMS\SmsSender.php';
            require_once VENDOR_PATH . 'SMS\SmsVoiceSender.php';
            
            $appid = 1400023627;
            $appkey = "091dbec841263da9db9b68b6bddc8098";
            $templId = 9117;
            
            $phoneNumber = input('phone');
            
            $key = 'yzm_user_bind_car';
            $yzm = session($key);
            if (!$yzm){
                $yzm = rand(100000,999999);
                session($key,$yzm);
            }
            
            
            $singleSender = new SmsSingleSender($appid, $appkey);
            
            // 假设模板内容为：测试短信，{1}，{2}，{3}，上学。`
            $params = array($yzm,600);
            $result = $singleSender->sendWithParam("86", $phoneNumber, $templId, $params, "", "", "");
            //{"result":0,"errmsg":"OK","ext":"","sid":"8:8gVBbgkZ25Gqz5rDpac20170828","fee":1}
            $rsp = json_decode($result,true);
            if (strtoupper($rsp['errmsg']) == 'OK'){
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '验证码发送成功';
                $json['debug']['data'] = $rsp;
            }else{
                exception('api返回了错误结果');
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = '验证码发送失败';
            $json['debug']['e'] = $e->getMessage();
        }
        
        return \json($json);
    }
    private function deleteYzmSession(){
        $key = 'yzm_user_bind_car';
        session($key,null);
    }


    private function debugLogUserBindCarAction($array){
        TmpTool::arrayToArrayFile($array);
    }
    
    private function uploadImage($name){
        if (!ConfigTool::$IS_UPLOAD_IDENTITY_IMAGE){
            return '';
        }
        $file=  $this->request->file($name);
//         dump($this->request->file());
        if (null === $file){
            return '';
        }
        $path = dirname($_SERVER['SCRIPT_FILENAME']).DS.'static'.DS.'upload_way'.DS;
        $rule = ['size'=>2024000,'ext'=>'jpg,png,gif'];
        $info  = $file->validate($rule)->move($path);
        
        
        if ( $info ){
            return 'upload_way'.DS.$info->getSaveName();
        }
        $msg = is_array($file->getError())?implode('<br />', $file->getError()):$file->getError();
        exception($msg , ConfigTool::$ERRCODE__COMMON);
    }
    
    /**
     * @param unknown $data
     * @return true|array   true表示通过验证， array为数据验证失败
     */
    private function imageValidate_add($data){
        if (input('unit')){
            return true;
        }
        
        $validate = new Validate();
        $validate->rule('identity_image0' , 'require');
        $validate->rule('identity_image1' , 'require');
        $validate->rule('driving_license_image0' , 'require');
        $validate->rule('driving_license_image1' , 'require');
        
        
        $validate->message('identity_image0' , '身份证正面图片必传');
        $validate->message('identity_image1' , '身份证反面图片必传');
        $validate->message('driving_license_image0' , '行驶证正面图片必传');
        $validate->message('driving_license_image1' , '行驶证反面图片必传');
        
        if ( ! $validate->check($data)){
            return $validate->getError();
        }
        return true;
    }
    

    /**
     * 用户绑定车辆
     * errcode
     * 
     */
    private function userBindCar($is_add,$is_update){
        //SELECT id,status,verify,qrcode_version,car_number FROM `way_user_bind_car` order by id desc limit 100
        //return response('',500);
        
        $json =[];
        try {
            usleep(2000);
            
            $wayUserBindCar = new WayUserBindCar();
            
            //获取post数据
            if ($this->request->isPut()){
                $data = $this->request->put();
            }else{
                $data = $this->request->post();
            }
            
            foreach ($data as $k=>$v){
                if ($k == '_method'){
                    continue;
                }
                $decrypted='';
                $de_res=openssl_private_decrypt(base64_decode($v), $decrypted, ConfigTool::$RSA_PRIVATE_KEY);
                if (!$de_res){
                    exception('系统错误,field='.$k.',值为：'.$v.',line='.__LINE__.',method='.__METHOD__,ConfigTool::$ERRCODE__SHOULD_NOT_BE_DONE_HERE);
                }
                $data[$k] = $decrypted;
                
            }
            if (ConfigTool::$WAY_USER_BIND_CAR__CHECK_YZM){
                $session_yzm = session($this->yzm_key);
                if (!$session_yzm){
                    exception('验证码超时，请重新获取验证码');
                }
                if ($data['yzm'] != $session_yzm){
                    exception('验证码错误');
                }
            }
            
            
          
            $data['user_id'] = UserTool::getUser_id();
            $data['openid'] = UserTool::getUni_account();
            $data['car_qrcode_path'] = '';
            $data['status'] = 0;
            $data['verify'] = 0;
            $data['create_time'] = time();
            $data['reg_time'] = strtotime($data['reg_time']);
            
            $data['identity_image1'] = $this->uploadImage('identity_image1');
            $data['identity_image0'] = $this->uploadImage('identity_image0');
            $data['driving_license_image0'] = $this->uploadImage('driving_license_image0');
            $data['driving_license_image1'] = $this->uploadImage('driving_license_image1');
            
            
          
            if ($is_add && !$is_update){
//                 unset($data['id']);
                $data['id'] = 0;
                $res=null;
                $res_validate = $this->imageValidate_add($data);
                if (true === $res_validate){
                    $res = $wayUserBindCar->addOne($data);
                }else{
                    \exception( '图片上传验证失败：'.var_export($res,true) ,ConfigTool::$ERRCODE__COMMON);
                }
            }else if (!$is_add && $is_update){
                if (!$data['identity_image0']){
                    unset($data['identity_image0']);
                }
                if (!$data['identity_image1']){
                    unset($data['identity_image1']);
                }
                if (!$data['driving_license_image0']){
                    unset($data['driving_license_image0']);
                }
                if (!$data['driving_license_image1']){
                    unset($data['driving_license_image1']);
                }
                
                $hasBind = WayUserBindCar::getOne(UserTool::getUser_id());
       
                $res = $wayUserBindCar->saveOne($data,$hasBind);
            }else{
                \exception('错误的条件,file='.__FILE__.',line='.__LINE__ , ConfigTool::$ERRCODE__SHOULD_NOT_BE_DONE_HERE);
            }
            
            if (!$res){
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = implode('<br />', (array)$wayUserBindCar->getError());
                $json['unit']['error'] = $wayUserBindCar->getError();
          
            }else{
                if(!$res->car_qrcode_path){
                    WayUserBindCar::save_car_qrcode_path($res);
                }
                
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '绑定车辆成功';
                $json['view_url'] = url('way/user/read',['id'=>$res->id]);
              
                $json['data'] = $res;
                $json['data']->dis_create_time = date('Y-m-d' , $res->getData('create_time'));
                
                //删除验证码session
                $this->deleteYzmSession();
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['debug']['e'] = $e->getMessage();
            $json['html'] = '系统错误';
            if (ConfigTool::IS_LOG_TMP){
                $log_content = print_r($json,true) ;
                SysLogTmp::log('绑定车辆出现异常,json变量内容', $log_content , 0 ,__METHOD__);
                SysLogTmp::log('绑定车辆出现异常,e=', var_export($e,true), 0 ,__METHOD__);
            }
        }
   
        
        if (ConfigTool::IS_LOG_TMP){
            $log_content = print_r($json,true) ;
            SysLogTmp::log('绑定车辆结果,method='.$this->request->method().',id='.input('id'), print_r(array('json'=>$json,'data'=>$data ,
                'file'=>$this->request->file(),
            ),true) , 0 ,__METHOD__);
        }
        
        return json($json);
        
    }
    
    
    
    public function getCarTypeJsonAction(){
        $all = WayCarType::all();
        foreach ($all as $k=>$v){
            $all[$k] = $v->toArray();
            $all[$k]['name'] = $v->name.'('.$v->title.')';
        }
        
        return json(array('records'=>$all));
    }


    public function getCarColorJsonAction(){
        
        $all = SysConfig::getListByType(SysConfig::TYPE_GS_COLOR_CONFIG);
        
        return json(array('records'=>$all));
    }
    
}
