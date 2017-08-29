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
    
    protected  function getCarNumberAttr($value)
    {
        return strtoupper($value);
    }

    

    private static function setValidateRule(WayUserBindCarValidate $validate){
        $validate->rule('reg_time','require|number|gt:0|lt:'.time());
        $validate->message('reg_time.lt' , '车辆注册时间不能大于当前时间');
    }
    
    /**
     * 新绑定车辆唯一入口
     * @param unknown $data
     * @return \app\common\model\WayUserBindCar|false
     * @throws \Exception
     */
    public function addOne($data){
        $model = new static;
        /**
         * @var WayUserBindCarValidate $validate
         */
        $validate = Loader::validate('WayUserBindCarValidate');
        self::setValidateRule($validate);
        
        if($validate->batch(false)->check($data)){
            if ( $model->allowField(ConfigTool::$TABLE_WAY_USER_BIND_CAR__ADD_CAR_ALLOW_FIELD)->save($data) ){
                return self::get($model->id);
            }else{
                exception('添加数据到表失败,error='.$model->getError(),ConfigTool::$ERRCODE__MODEL);
            }
        }else{
            $this->error = $validate->getError();
            return false;
        }
    }
    
    /**
     * 新绑定车辆唯一入口
     * @param unknown $data
     * @return \app\common\model\WayUserBindCar|false
     * @throws \Exception
     */
    public function saveOne($data,WayUserBindCar $hasBind){
    
        /**
         * @var WayUserBindCarValidate $validate
         */
        $validate = Loader::validate('WayUserBindCarValidate');
        self::setValidateRule($validate);
//         $validate->rule('car_number' , 'require|eq:'.$hasBind->car_number);
        
        if ($hasBind->id != $data['id']){ 
            exception('已绑定车辆表id和待修改的id不同，分别为：',$hasBind->id.'-'.$data['id'] , ConfigTool::$ERRCODE__COMMON  );
        }

        if($validate->batch(false)->check($data)){
            $res = $hasBind->allowField(ConfigTool::$TABLE_WAY_USER_BIND_CAR__SAVE_CAR_ALLOW_FIELD)->save($data);
            if (false !== $res){//有数据更改 1 ， 没有数据更改0 ，都算成功
                return self::get($hasBind->id);
            }
            exception('sql失败,error='.$hasBind->getError(),ConfigTool::$ERRCODE__MODEL);
        }else{
            $this->error = $validate->getError();
            return false;
        }
    }
    
    /**
     * 获取用户绑定的唯一车辆，只根据user_id条件查找
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
