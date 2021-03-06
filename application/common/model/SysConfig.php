<?php

namespace app\common\model;

use think\Model;
use think\db\Query;
use app\common\tool\TmpTool;
use app\common\tool\ConfigTool;
use app\common\tool\UserTool;

class SysConfig extends Model
{
    
    /**
     * 配置表type值
     * 上传文件当前表后缀
     * @var string
     */
    const TYPE_UPLOAD_SUBFIX_NUM = '上传文件配置';
    const TYPE_WEIXIN_CONFIG = '微信配置';
    const TYPE_GS_COLOR_CONFIG = '高速配置-高速绑定车辆颜色配置';
    
    /**
     * user表type字段的值
     * 微信注册方式
     * @var integer
     */
    const REG_TYPE_WEIXIN = 1;
    const REG_TYPE_PHONE = 2;
    const REG_TYPE_EMAIL = 3;
    const REG_TYPE_USERNAME = 4;
    /**
     * 后台添加
     * @var integer
     */
    const REG_TYPE_ADMIN = 5;
    /**
     * 用户类型 ，user_type 个人
     * @var integer
     */
    const REG_USER_TYPE__PERSONAL = 1;
    /**
     * 用户类型 ，user_type 商家
     * @var integer
     */
    const REG_USER_TYPE__MERCHANT = 2;
    
    
    
    
//     /**
//      * 添加一条配置
//      * @param array $data
//      * @param array $vars
//      * @return number|\think\false
//      */
//     public function addOne(array $data ){
//         $model = null;
//         $model = $this->validateFailException()->validate('\\app\\base\\validate\\ConfigValidate')->save($data);
//         return $model;
//     }

    /**
     * 根据type获取配置列表
     * @param unknown $type
     * @return \think\static[]|\think\false
     */
    public static function getListByType($type){
        return self::all( ['type'=>$type]);
    }
    
    /**
     * 根据type和key获取唯一的一行数据
     * @param unknown $type
     * @param unknown $key
     * @return string|null null表示未获取到
     */
    public static function getValueBy($type,$key){
        $model = self::get(function(Query $query) use ($type,$key){
            $where = [
                'type'=>$type,
                'key'=>$key,
            ];
            $query->where($where);
        });
        return $model&&$model->id?$model->value:null;
    }
    

    
    public function init_table_data(){
        $config['微信配置'] = [
            'APPID' => 'wx43cccee49cb479ff',
            'MCHID' => '1482887882',
            'KEY' => '56ac2c50a487c623b207df90f3682985',
            'APPSECRET' => '8483dc9da54927c64c4071d6db413be0',
            'NOTIFY_URL' => 'gs.jltengfang.com/order/index/notify',
            'SSLCERT_PATH' => '../cert/apiclient_cert.pem',
            'SSLKEY_PATH' => '../cert/apiclient_key.pem',
            'REPORT_LEVENL' => 1,
            'access_token'=>'',
            'access_token_expire'=>0,
        ];
        
        $config[self::TYPE_GS_COLOR_CONFIG] = [
            'blue' => '蓝色',
            'black' => '黑色',
            'yellow' => '黄色',
            'white' => '白色',
        ];
        
        
        
        foreach ($config as $type=>$arr){
            foreach ($arr as $key=>$value){
                $key = strtolower($key);
                $where = [
                    'type'=>$type,
                    'key'=>$key
                ];
                $line = self::get($where);
               if ($line && $line['id']){
                   continue;
               }else{
                   $line = new static;
               }
                $data = [
                  'type'=>$type,
                  'key'=>$key,
                   'value'=>$value
                ];
                if (ConfigTool::IS_LOG_TMP){
                    SysLogTmp::log('增加配置表数据,type=【'.$type.'】', $type.'|'.$key.'|'.$value, UserTool::getUser_id(), __FILE__);
                }
                $line->save($data);
            }
            
        }
        
        return true;
    }
    
    
    
    
    
    
    
    
    
}
