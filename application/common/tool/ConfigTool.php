<?php
namespace app\common\tool;

/**
 * 配置类
 * @author Administrator
 *@example
 *  所有关于JSON的以JSON开头
 *      JSON_REASON是原因
 *  关于表配置的以TABLE_表名大写__具体配置
 *      TABLE_WAY_USER_BIND_CAR__ADD_CAR_ALLOW_FIELD
 *  ERRCODE__    错误代码
 */
class ConfigTool {
   /*
    * 常见日志
    *   获取access_token
    *   用户微信授权后，获取的用户信息
    *    
    */
    
    /**
     * 是否记录系统临时日志，即写入sys_log_tmp表
     * @var string
     */
    const IS_LOG_TMP = TRUE;
    
    /**
     * 需要登录的reason原因
     * @var string
     */
    public static  $JSON_REASON_NEED_LOGIN = 'need_login';
    
    /**
     * 无错误
     * @var integer
     */
    public static $ERRCODE__NO_ERROR = 0;
    /**
     * 异常产生的错误
     * @var integer
     */
    public static $ERRCODE__EXCEPTION = 1;
    /**
     * 未细分的错误
     * @var integer
     */
    public static $ERRCODE__COMMON = 2;
    
    
    /**
     * 绑定车辆表，添加数据时，允许添加的字段
     * @var array
     */
    public static  $TABLE_WAY_USER_BIND_CAR__ADD_CAR_ALLOW_FIELD = 
    ['username','identity_card','phone','car_number','car_type_id','engine','car_color','brand','user_id','openid','reg_time','chassis_number','car_qrcode_path','status','verify','create_time'];
    
    /**
     * 绑定车辆表，修改数据时，允许修改的字段
     * @var array
     */
    public static  $TABLE_WAY_USER_BIND_CAR__SAVE_CAR_ALLOW_FIELD = 
    ['username','identity_card','phone','car_number','car_type_id','engine','car_color','brand','user_id','openid','reg_time','chassis_number','status','verify','create_time'];
    
    
    /**
     * 绑定车辆表，只读字段
     * @var array
     */
    public static  $TABLE_WAY_USER_BIND_CAR__READONLY = ['user_id','car_number','openid','create_time'];
}
