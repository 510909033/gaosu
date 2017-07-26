<?php

namespace app\common\model;

use think\Model;
use think\Validate;
use think\Loader;
use app\way\validate\WayUserBindCarValidate;
use app\common\tool\ConfigTool;
use app\common\tool\UserTool;

class WayUserBindCar extends Model
{
//     protected $readonly = ConfigTool::$TABLE_WAY_USER_BIND_CAR__READONLY;
//     protected $readonly = null;
    
    public function __construct(){
        $this->readonly = ConfigTool::$TABLE_WAY_USER_BIND_CAR__READONLY;
        parent::__construct();
    }
    
    public function bindCar($data){
        $hasBind = self::getOne(UserTool::getUser_id());
        if (!$hasBind){
            return $this->addOne($data);
        }else{  
            return $this->saveOne($data,$hasBind);
        }
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
     * @return \app\common\model\WayUserBindCar|boolean
     */
    private function saveOne($data,WayUserBindCar $hasBind){
    
        /**
         * @var WayUserBindCarValidate $validate
         */
        $validate = Loader::validate('WayUserBindCarValidate');
       
//         $validate->rule('car_number' , 'require|eq:'.$hasBind->car_number);
        
        
        
        if ($hasBind->id != $data['id']){ 
            $this->error = '系统错误';
            return false;
        }
      
        if($validate->scene('save')->batch()->check($data)){
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
    
}
