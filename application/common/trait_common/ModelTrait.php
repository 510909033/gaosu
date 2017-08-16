<?php
namespace app\common\trait_common;
use app\common\model\SysLogTmp;
use think\Validate;
use app\common\tool\ConfigTool;
use phpDocumentor\Reflection\Types\Static_;
use think\Model;

trait ModelTrait {
    /**
     * 添加角色
     * 1：disData处理数据
     * 2：validate进行验证
     * 3：验证成功写入数据
     * 或
     *      验证失败返回验证器错误信息 
     *      —————— 截止 ——————
     * 4：写入数据成功，返回模型
     *  或
     *  写入失败，记录日志，返回系统错误字符串
     *      ————截止————
     * 5:   ————截止———— 
     * @param unknown $data
     * @return SysRole|string   string错误信息，可以直接显示给用户
     * 
     */
    public static function addData($data , Validate $validate , $allowField){
        $data = self::disAddData($data);
        if ($validate->scene('add')->check($data)){
            $res = self::before_create($data);
            if (true !== $res){
                return $res;
            }
            $model = self::create($data , $allowField );
            if ($model && $model->id){
                return $model;
            }
            self::log('insert失败：'.$model->getError(), var_export($model,true) , 0, __FILE__.',line='.__LINE__);
            return '系统错误';
        }else{
            return $validate->getError();
        }
    }
    
    /**
     * 简单处理数据，例如去除两边空格等
     * @param array $data
     * @return array    处理后的数据
     */
    protected static function disAddData($data){
        return $data;
    }
    /**
     * 写入数据前的逻辑判断
     * @param array $data
     * @return true|string  string为用户可见的错误信息
     */
    protected static function before_create($data){
        return true;
    }
    
    
    
    public static function updateData($id,$data, Validate $validate , $allowField){
        
        $data = self::disUpdateData($data);
        if ($validate->scene('update')->check($data)){
            $res = self::before_update($data);
            if (true !== $res){
                return $res;
            }
            $model = self::get($id);
            if (!$model){
                return lang('要更新的数据不存在');
            }
            $save_res = $model->allowField($allowField)->save($data);
            
            if (false !== $save_res){
                return $model;
            }
            self::log('insert失败：'.$model->getError(), var_export($model,true) , 0, __FILE__.',line='.__LINE__);
            return lang(ConfigTool::$ERRORSTR_COMMON);
        }else{
            return $validate->getError();
        }
    }
    
    /**
     * 
     * @param unknown $id
     * @return true|string  true 成功，string失败原因
     * @throws \Exception
     */
    public static function deleteOne($id){
        if (!Validate::is($id,'integer')){
            return lang(ConfigTool::$ERRORSTR_COMMON);
        }
        $model = self::get($id);
        $res = self::before_delete($model);
        if (true !== $res){
            return lang($res);
        }
        if (1 === $model->delete()){
            return true;
        }else{
            return '删除失败';
        }
      
    }
    
    /**
     * 简单处理数据，例如去除两边空格等
     * @param array $data
     * @return array    处理后的数据
     */
    protected static function disUpdateData($data){
        return $data;
    }
    /**
     * 写入数据前的逻辑判断
     * @param array $data
     * @return true|string  string为用户可见的错误信息
     */
    protected static function before_update($data){
        return true;
    }
    
    protected Static function before_delete(Model $model){
        return true;
    }
    
    
    
    protected  static function log($title, $content, $user_id, $pos){
        SysLogTmp::log($title, $content, $user_id, $pos);
    }
}