<?php
namespace weixin\auth;

use app\home\model\User;
use \app\common\model\WeixinUser;
use app\base\controller\interf\AuthInterf;
use app\common\model\SysConfig;
use app\common\model\SysUser;
/**
 * 微信静默授权类
 * @author Administrator
 *
 */
class AuthController 
{
    private $codeArr = [];
    private  $openid= false;
    /**
     * @var \app\common\struct\AuthAccount
     */
    private $account;
    
    private $error;
    
    private $user;
    private $weixinUser;
    private $field_access_token='access_token';
    private $field_access_token_expire = 'access_token_expire';
    
    /**
     * 是否是测试模式
     * @var string
     */
    public static $is_can_unit = true;
    
    /**
     * 获取access_token，攻程序调用
     * @param string $is_force
     * @return string access_token
     * @throws  \Exception
     */
    public function getAccessToken($is_force = false){
        SysConfig::startTrans();
        $sysConfig = new SysConfig();
        $where = [
            'type'=>SysConfig::TYPE_WEIXIN_CONFIG,
            'key'=>$this->field_access_token,
        ];
        $lineAccessToken = $sysConfig->where($where)->lock(true)->find();
        
        $sysConfig = new SysConfig();
        $where = [
            'type'=>SysConfig::TYPE_WEIXIN_CONFIG,
            'key'=>$this->field_access_token_expire,
        ];
        $lineExpire = $sysConfig->where($where)->find();
        
        if (  ($lineExpire->value  - 60) < time() || $is_force ){
            //更新
            $lineAccessToken->value = $this->apiGetToken();
            if (!$lineAccessToken->value){
                exception('获取access_token失败');
            }
            $lineAccessToken->save();
            $lineExpire->value = time() + 7200;
            $lineExpire->save();
            return $lineAccessToken->value;
        }else{
            return $lineAccessToken->value;
        }
        SysConfig::commit();
    }
    
    /**
     * 通过微信api
     * @return mixed|boolean
     */
    private function apiGetToken(){

        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->getAppkey().'&secret='.$this->getAppsecret();
        $content = file_get_contents($url);
        $arr = json_decode($content,true);
        

        if (isset($arr['access_token']) && isset($arr['expires_in'])){
            return $arr['access_token'];
        }
        return false;
    }
    
    
    private function getAppkey(){
        return SysConfig::getValueBy(SysConfig::TYPE_WEIXIN_CONFIG, 'appid');
    }
    
    private function getAppsecret(){
        return SysConfig::getValueBy(SysConfig::TYPE_WEIXIN_CONFIG, 'appsecret');
    } 


    /**
     * 跳转到微信服务器获取code
     * @param unknown $redirect_uri 微信跳回地址http开始的
     * @param unknown $state    额外参数，跳回时会get返回
     * @param unknown $is_unit  是否是测试，测试不会跳转到微信，直接跳回到$redirect_uri 
     */
    public function redirect($redirect_uri,$state,$is_unit){
        
        if (!$redirect_uri){
            exception('redirect_uri is need!');
        }
        $appid = $this->getAppkey();
        if (self::$is_can_unit && $is_unit){
            $url = $redirect_uri.'?is_unit=1&state='.$state;
        }else{
            $scope = 'snsapi_base';
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
        }
        
        header('location:'.$url);
        exit;
    }
    
    /**
     * 根据code获取用户openid，保存session信息，user_id为键，
     * @return false|\app\home\model\User
     * @throws \think\Exception
     * @desc    
     * 获取openid后，如果user表存在 uni_account + type ，则认为存在，直接设置session
     * 如果不存在，则写入 weixin_user表及user表，然后设置session，
     * 最终返回true或抛出异常
     */
    public function getResultByCode(){
        $openid = $this->getOpenid();
        $arr =[
            'openid'=>$openid,
        ] ;
        
  
        if ($arr['openid']){
            if ($this->bindUser()  ){
                return $this->user;
            }
            return false;
        }else{
            $this->error = 'openid不存在';
            return false;
        }
    }


    /**
     * {@inheritDoc}
     * @see \app\base\controller\interf\AuthInterf::getOpenid()
     */
    public function getOpenid()
    {
        $code = \request()->get('code') ;
        
        $is_unit = \request()->get('is_unit');
        
        $appid = $this->getAppkey();
        $secret = $this->getAppsecret();
        if ( self::$is_can_unit && $is_unit  ){
            $arr['openid'] = rand(1,5) > 3?uniqid():'openid_nochange';
        }else{
            $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
            $con = file_get_contents($url);
            $arr = json_decode($con,true);
        }
        $this->codeArr = $arr;
        if (!$this->codeArr['openid']){
            $this->codeArr['openid'] = false;
        }
        $this->openid = $this->codeArr['openid'] ;
        
        $this->account->setAccount($this->openid);
        $this->account->setAuth_type(SysConfig::REG_TYPE_WEIXIN);
        
        return $this->openid;
    }

    /**
     * 存在直接返回user对象，不存在添加返回 <br>
     * {@inheritDoc}
     * @see \app\base\controller\interf\AuthInterf::bindUser()
     */
    public function bindUser()
    {
        if (!$this->openid){
            $this->error = 'openid为空';
            return false;
        }
        $user = new SysUser();
        
        $where = [
            'uni_account'=>$this->openid,
            'type'=>SysConfig::REG_TYPE_WEIXIN,
        ];
        $line = $user->where($where)->find();
        if ($line){
            return $line;
        }else{
            $regData = [
                'uni_account'=>$this->openid,
                'password'=>sha1($this->openid),
                'solt'=>12345,
                'regtime'=>time(),
                'type'=>SysConfig::REG_TYPE_WEIXIN,
                'subscribe'=> 0 ,
            ];
            $regResult = $user->regApi($regData);
            if ( 0 === $regResult['err'] ){
                return $regResult['user_model'];
            }else{
                $this->error = $regResult;
            }
        }
        return false;
    }



    /**
     * {@inheritDoc}
     * @see \app\base\controller\interf\AuthInterf::getError()
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * {@inheritDoc}
     * @see \app\common\interf\AuthInterf::getAccount()
     */
    public function getAccount()
    {
        return $this->account;
    }


    
}
