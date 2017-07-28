<?php

namespace app\common\model;

use think\Model;
use think\Validate;
use think\Loader;
use app\way\validate\WayUserBindCarValidate;
use app\common\tool\ConfigTool;
use app\common\tool\UserTool;
use app\way\controller\func\UserBindCarFuncController;

class WayUserBindCar extends Model
{

    

    
    public function __construct($data=[]){
        $this->readonly = ConfigTool::$TABLE_WAY_USER_BIND_CAR__READONLY;
        parent::__construct($data);
    }
    
    protected  function setIdentityCardAttr($value)
    {
        return strtolower($value);
    }
    
    protected  function setCarNumberAttr($value)
    {
        return strtolower($value);
    }
    
    /**
     * @param unknown $data
     * @return \app\common\model\WayUserBindCar|boolean
     */
    public function bindCar($data){
        $hasBind = self::getOne(UserTool::getUser_id());
        if (!$hasBind){
            //如果没有绑定 
            unset($data['id']);
            $res =  $this->addOne($data);
        }else{  
            $res =  $this->saveOne($data,$hasBind);
        }
        
        
        if ($res){
            $res = self::get($res->id);
        }
        
        
        return $res;
    }
    
    private function setValidateRule(WayUserBindCarValidate $validate){
        $validate->rule('reg_time','require|number|gt:0|lt:'.time());
        $validate->message('reg_time.lt' , '车辆注册时间不能大于当前时间');
    }
    
    /**
     * 新绑定车辆唯一入口
     * @param unknown $data
     * @return \app\common\model\WayUserBindCar|boolean
     */
    private function addOne($data){
        
        /**
         * @var WayUserBindCarValidate $validate
         */
        $validate = Loader::validate('WayUserBindCarValidate');
        $this->setValidateRule($validate);
        
        if($validate->batch()->check($data)){
            return $this->create($data , ConfigTool::$TABLE_WAY_USER_BIND_CAR__ADD_CAR_ALLOW_FIELD);
        }else{
            $this->error = $validate->getError();
            return false;
        }
    }
    
    /**
     * 新绑定车辆唯一入口
     * @param unknown $data
     * @return \app\common\model\WayUserBindCar|false
     */
    private function saveOne($data,WayUserBindCar $hasBind){
    
        /**
         * @var WayUserBindCarValidate $validate
         */
        $validate = Loader::validate('WayUserBindCarValidate');
        $this->setValidateRule($validate);
//         $validate->rule('car_number' , 'require|eq:'.$hasBind->car_number);
        
        if ($hasBind->id != $data['id']){ 
            $this->error = '系统错误';
            return false;
        }

        //scene('save')->
        if($validate->batch()->check($data)){
            $res = $hasBind->allowField(ConfigTool::$TABLE_WAY_USER_BIND_CAR__SAVE_CAR_ALLOW_FIELD)->save($data);
            if (false !== $res){
                return $hasBind;
            }
           
            $this->error = '编辑车辆失败';
            return false;
        }else{
            $this->error = $validate->getError();
            return false;
        }
    }
    
    /**
     * 获取用户绑定的唯一车辆
     * @param unknown $user_id
     */
    public static function getOne($user_id){
        $where = [
            'user_id'=>$user_id
        ];
        
        return WayUserBindCar::where($where)->find();
    }
    
    /**
     * 更新绑定车辆的二维码
     * @param WayUserBindCar $wayUserBindCar
     * @return false|1 or 0 ，false表示失败，1或0表示成功
     */
    public static function save_car_qrcode_path(WayUserBindCar $wayUserBindCar){
        $func = new UserBindCarFuncController();
        $car_qrcode_path = $func->createQrcode($wayUserBindCar);
        if ($car_qrcode_path){
            $wayUserBindCar->car_qrcode_path = $car_qrcode_path;
            return $wayUserBindCar->allowField('car_qrcode_path')->save();
        }
        return false;
    }
    
}
