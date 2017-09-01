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
use app\way\validate\WayUserBindCarValidate;

class UserController extends \app\common\controller\NeedLoginController
{
    /**
     * 手机验证码session的key
     * @var string
     */
    private $yzm_key = 'yzm_user_bind_car';
    private $yzm_key_timeout = 'yzm_user_bind_car_timeout';
    private $yzm_key_phone = 'yzm_user_bind_car_phone';
    private $yzm_timeout = 600;
    
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
            $this->redirect('way/user/detail');
//             $this->redirect('way/user/read',['id'=>$wayUserBindCar->id] );
        }else{
            $this->redirect('way/user/create');
        }
    }
    
    public function createAction(){
        if (WayUserBindCar::getOne(UserTool::getUser_id())){
            $this->redirect('way/user/detail');
//             exception('您已绑定了车辆',ConfigTool::$ERRCODE__COMMON);
        }
        
        View::share('form_url' , url('way/user/save'));
        View::share('form_method','post');
        
        
        return $this->read(0);
    }
    
    
    public function detailAction(){
        $vars = [];
        $title = '';
        $user_id = UserTool::getUser_id();
        $en = WayUserBindCar::getOne($user_id);
        if (!$en){
            exception('您尚未绑定车辆',ConfigTool::$ERRCODE__COMMON);
        }
        //fmt.Printf(book1.title)
        //您的信息已提交成功，正在审核中请耐心等候
        if (0 == $en->status){
            $title = '绑定车辆已被禁用';
        }else if (0 == $en->verify){
            $title = '您的信息已提交成功，尚未审核，请耐心等候';
        }else if (1 == $en->verify){
            $title = '您的信息已审核通过';
        }else if (2 == $en->verify){
            $title = '您的信息已审核未通过';
        }else if (3 == $en->verify){
            $title = '您的信息正在审核中，请耐心等候';
        }
        
        $vars['entity'] = $en;
        
        
        

        $vars['html']['title'] = $title;
        return \view('detail2',$vars);    
    }
    
    public function qrcodeAction(){
        try {
            $json = [];
            $func = new UserBindCarFuncController();
            $user_id = UserTool::getUser_id();
            $wayUserBindCar = WayUserBindCar::getOne($user_id);
            
            if (!$wayUserBindCar){
                exception('尚未绑定车辆',ConfigTool::$ERRCODE__COMMON);
            }
            
            $res = WayUserBindCar::save_car_qrcode_path($wayUserBindCar);
            
            
            if (false === $res){
                exception('获取二维码失败',ConfigTool::$ERRCODE__COMMON);
            }
            $json['qrcode']['url'] = $wayUserBindCar->car_qrcode_path_url; 
            $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = $e->getCode() == ConfigTool::$ERRCODE__COMMON?$e->getMessage():'系统错误';
            if ($e->getCode() != ConfigTool::$ERRCODE__COMMON){
                SysLogTmp::log($e->getMessage(), $e->getMessage(), $user_id, __METHOD__.',line='.__LINE__);
            }
        }
//         echo $wayUserBindCar->car_qrcode_path;
        return \json($json);
    }
    
    public function readAction($id){

        if (!WayUserBindCar::getOne(UserTool::getUser_id())){
            exception('您尚未绑定车辆',ConfigTool::$ERRCODE__COMMON);
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
            $image->identity_image1 = $form->identity_image1?$form->identity_image1:'/way2/images/aaaaa1.jpg';
            $image->driving_license_image0 = $form->driving_license_image0?$form->driving_license_image0:'/way2/images/aaaaa2.jpg';
            $image->driving_license_image1 = $form->driving_license_image1?$form->driving_license_image1:'/way2/images/aaaaa3.jpg';
            
            
            $form->reg_time = date('Y-m-d' ,$form->getData('reg_time'));
            $vars['form'] = $form->toJson();
            $vars['form_array'] = (array)$image;
            $vars['reg_time'] = $form->reg_time;
        }else{
            $vars['form'] = '[]';
            $image->identity_image0 = '/way2/images/shenfenz_03.jpg';
            $image->identity_image1 ='/way2/images/aaaaa1.jpg';
            $image->driving_license_image0 = '/way2/images/aaaaa2.jpg';
            $image->driving_license_image1 = '/way2/images/aaaaa3.jpg';
            
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
            usleep(300000);
            $max_hours = 10;
            $cache_key = 'cache_'.date('ymdH').UserTool::getUser_id();
            $value = (int)cache($cache_key);
            if ($value >= $max_hours){
                exception('发送验证码数量达到了规则上限',ConfigTool::$ERRCODE__COMMON);
            }
            
            cache($cache_key , $value+1);
            
            
            $json=[];
            //var_dump(VENDOR_PATH . 'SMS\SmsSender.php');die();
            require_once VENDOR_PATH . 'SMS/SmsSender.php';
            require_once VENDOR_PATH . 'SMS/SmsVoiceSender.php';
            
            $appid = 1400023627;
            $appkey = "091dbec841263da9db9b68b6bddc8098";
            $templId = 9117;
            
            $phoneNumber = input('phone');
            $validate = new Validate();
            $validate->rule('phone' , 'require|number|length:11');
            if (!$validate->check(['phone'=>$phoneNumber])){
                exception('手机号格式不对',ConfigTool::$ERRCODE__COMMON);
            }
            
            session($this->yzm_key_phone,$phoneNumber);
            
            $key = $this->yzm_key;
            $yzm = \session($key);
            
            session($this->yzm_key_timeout , time() + $this->yzm_timeout );
            if (!$yzm){
                $yzm = rand(100000,999999);
                \session($key,$yzm);
            }
            $rsp=[];
            if(ConfigTool::$WAY_USER_BIND_CAR__CHECK_YZM){
                $singleSender = new SmsSingleSender($appid, $appkey);
                // 假设模板内容为：测试短信，{1}，{2}，{3}，上学。`
                $params = array($yzm,$this->yzm_timeout);
                $result = $singleSender->sendWithParam("86", $phoneNumber, $templId, $params, "", "", "");
                //{"result":0,"errmsg":"OK","ext":"","sid":"8:8gVBbgkZ25Gqz5rDpac20170828","fee":1}
                $rsp = json_decode($result,true);
            }else{
                $rsp['errmsg'] = 'OK';
            }
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
            if ($e->getCode() == ConfigTool::$ERRCODE__COMMON){
                $json['html'] = $e->getMessage();
            }
        }
        
        return \json($json);
    }
    
    /**
     * yzm 
     * phone
     * @param unknown $id
     */
    public function updatePhoneAction($id){
        try {
            
            
            $json=[];
            $yzm = input('yzm');
            $phone = input('phone');
            
            $this->checkYzm($yzm,$phone);
            
            
            
            
            $model = WayUserBindCar::get($id);
            $validate = new WayUserBindCarValidate();
//             $validate->
            if ($model && $model->user_id == UserTool::getUser_id()){
                $model->phone = $phone;
                $model->save();
            }else{
                exception('绑定车辆数据不存在');
            }
            $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
            $json['html'] = '修改成功';
            $json['url']['next_page'] = url('way/user/detail');
            
            $this->deleteYzmSession();
            
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = $e->getMessage();
        }
        
        return \json($json);
    }
    
    private function deleteYzmSession(){
        session($this->yzm_key,null);
        session($this->yzm_key_timeout,null);
        session($this->yzm_key_phone,null);
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
        $rule = ConfigTool::$UPLOAD_VALIDATE_IDENTITY_IMAGE_CONFIG;
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
     * 
     * @param unknown $yzm
     * @return void
     * @throws \Exception
     */
    private function checkYzm($yzm,$phone){
        if (ConfigTool::$WAY_USER_BIND_CAR__CHECK_YZM){
            
            $expire = \session($this->yzm_key_timeout);
            if (is_null($expire)){
                exception($syserrmsg='请点击发送手机验证码',ConfigTool::$ERRCODE__COMMON);
            }
            
            if ($expire < time()){
                exception($syserrmsg='验证码超时，请重新获取验证码',ConfigTool::$ERRCODE__COMMON);
            }
            
            $session_yzm = session($this->yzm_key);
            if (!$session_yzm){
                exception($syserrmsg='验证码超时，请重新获取验证码',ConfigTool::$ERRCODE__COMMON);
            }
            if ( $yzm != $session_yzm){
                exception($syserrmsg='验证码错误',ConfigTool::$ERRCODE__COMMON);
            }
            
            if ($phone != session($this->yzm_key_phone)){
                exception($syserrmsg='手机号和接收验证码的手机号不同，请填写同一个手机号码',ConfigTool::$ERRCODE__COMMON);
            }
            
        }
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
        $syserrmsg='';
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
//             if (ConfigTool::$WAY_USER_BIND_CAR__CHECK_YZM){
//                 $session_yzm = session($this->yzm_key);
//                 if (!$session_yzm){
//                     exception($syserrmsg='验证码超时，请重新获取验证码',ConfigTool::$ERRCODE__COMMON);
//                 }
//                 if ($data['yzm'] != $session_yzm){
//                     exception($syserrmsg='验证码错误',ConfigTool::$ERRCODE__COMMON);
//                 }
//             }
            
            $this->checkYzm($data['yzm'] , $data['phone']);
            
            
            
            $data['user_id'] = UserTool::getUser_id();
            $data['openid'] = UserTool::getUni_account();
            $data['car_qrcode_path'] = '';
            $data['status'] = 1;
            $data['verify'] = 3;
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
                    \exception( $syserrmsg=$res_validate ,ConfigTool::$ERRCODE__COMMON);
//                     \exception( '图片上传验证失败：'.var_export($res,true) ,ConfigTool::$ERRCODE__COMMON);
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
//                 $json['view_url'] = url('way/user/read',['id'=>$res->id]);
                $json['view_url'] = url('way/user/detail');
              
                $json['data'] = $res;
                $json['data']->dis_create_time = date('Y-m-d' , $res->getData('create_time'));
                
                //删除验证码session
                $this->deleteYzmSession();
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['debug']['e'] = $e->getMessage();
            
            if (!$syserrmsg && $e->getCode() == ConfigTool::$ERRCODE__COMMON){
                $syserrmsg = $e->getMessage();
            }
            
            $json['html'] = $syserrmsg?$syserrmsg:'系统错误';
            
            
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
        
        $json['debug']['post数据'] = $data;
        
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
