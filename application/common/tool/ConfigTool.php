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
    const ADMIN_ID=1;
    /**
     * 默认后台登录成功后跳转地址
     * @var string
     */
    const ADMIN_LOGIN_SUCCESS_URL = 'admin/role/index';
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
    
    public static $ERRORSTR_COMMON = '系统错误';
    
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
     * 常规错误
     * @var integer
     */
    public static $ERRCODE__COMMON = 2;
    /**
     * 程序不应该执行到此处
     * @var integer
     */
    public static $ERRCODE__SHOULD_NOT_BE_DONE_HERE = 3;
    /**
     * 模型类的方法执行后，执行失败，比如添加、更新、修改、删除数据失败等，
     * @var integer
     */
    public static $ERRCODE__MODEL = 4;
    
    /**
     * 绑定车辆表，添加数据时，允许添加的字段
     * @var array
     */
    public static  $TABLE_WAY_USER_BIND_CAR__ADD_CAR_ALLOW_FIELD = 
    ['username','identity_card','phone','car_number','car_type_id','engine','car_color','brand','user_id','openid','reg_time','chassis_number','car_qrcode_path','status','verify','create_time'
       ,'identity_image0','identity_image1','driving_license_image0', 'driving_license_image1', 
    ];
    
    /**
     * 绑定车辆表，修改数据时，允许修改的字段
     * @var array
     */
    public static  $TABLE_WAY_USER_BIND_CAR__SAVE_CAR_ALLOW_FIELD = 
    ['username','identity_card','phone','car_number','car_type_id','engine','car_color','brand','user_id','openid','reg_time','chassis_number','status','verify','create_time'
        ,'identity_image0','identity_image1','driving_license_image0', 'driving_license_image1',
    ];
    
    
    /**
     * 绑定车辆表，只读字段
     * @var array
     */
    public static  $TABLE_WAY_USER_BIND_CAR__READONLY = ['user_id','car_number','openid','create_time'];
    
    /**
     * 是否上传身份证正反面图片
     * @var string
     */
    public static $IS_UPLOAD_IDENTITY_IMAGE = TRUE;
    public static $UPLOAD_VALIDATE_IDENTITY_IMAGE_CONFIG = ['size'=>1024000,'ext'=>'jpg,png,gif'];
    
    
    /**
     * 车辆二维码生成后有效期，秒
     * @var integer
     */
    public static $WAY_USER_BIND_CAR_QRCODE_EXPIRE = 600;
    /**
     * 更新车辆时是否判断验证码
     * @var string
     */
    public static  $WAY_USER_BIND_CAR__CHECK_YZM = true;
    /**
     * 绑定车辆图片mine值
     * @var string
     */
//     public static $WAY_USER_BIND_CAR__IMAGE_MINE = 'image/png,image/jpg,image/jpeg,image/gif';
    public static $WAY_USER_BIND_CAR__IMAGE_MINE = 'image/*';
    
    
    public static $RSA_PUBLIC_KEY=<<<EEE
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDZENYDeswh/AyX3Bp1S2o/bhIM
FEQXQBb+EsgB/7+tTWyacrZ5vG/vLM6MH75bi8vDleV2GPqJvIhtdtsLYOtY+2At
MbRPAPCVWJz2LQT5FfFomMEHfTy4Fpxk3QbZTkd6pbettjFQKL7xVXM0OX3e0oEm
TKJ/2OHLAXjN+ZVwzQIDAQAB
-----END PUBLIC KEY-----
EEE;
    
    public static $RSA_PRIVATE_KEY=<<<EEE
-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBANkQ1gN6zCH8DJfc
GnVLaj9uEgwURBdAFv4SyAH/v61NbJpytnm8b+8szowfvluLy8OV5XYY+om8iG12
2wtg61j7YC0xtE8A8JVYnPYtBPkV8WiYwQd9PLgWnGTdBtlOR3qlt622MVAovvFV
czQ5fd7SgSZMon/Y4csBeM35lXDNAgMBAAECgYBlbGZzTcdgAcNomaGLOOe2J5or
JrUCICIeKWxm8rb/JPQf3oXAr/mlScxg0NxDLjjRdJK48cbu4LbmXujOkkSkfL1/
7adMn9kXnONInrDHPbhJxFSKIqh9xhu3J0xB27iSXjAWfhWcik0sv0qOtYzgPuuJ
ERgm+RxVqi0U+oYTQQJBAPK3GADOJEbLhzSZ3tk5ZQnCrmuwkNkQ5fncQCnDxiMm
66rsJjFQ93jFP0iP3mU24yZjTbtmQq09kpiMMhyzHfkCQQDk8lgg1m5Zls5SEXUt
S0lJzLgH3BetqbO6H57qPghRPsVUfnpXnUtDEErM6AiZPg5qyxlJAuwVqRRS5dPc
hi51AkA9gANHAPTUM3IY8/wxkod1h6zmgKDTP6LmQtbHPmIQOiZw8tFioZ9zLJey
bTHu949rBLKHj4vJldZ1bOCtwP0RAkBKHMU4hBGewLmzSWF0Mx3bXQDp0m570iaT
Bq9rxn7sxfQdAQBbolh9siV0pVw7NyJ1oZ9iyiZgcKZam3l0tp8NAkEAvnfd5gx1
a8frhy/TEABYwX7JuMlgcbTRQOHQZdMzMxGQY/Xs6PXpfHc3klmbuhitdx6dnW3U
XlOVhMEoiZxysA==
-----END PRIVATE KEY-----
EEE;
    
    public static function getRootUrl(){
        static $url=null;
        if (is_null($url)){
            $url = \request()->root();
            if (strpos($url, '.')){
                $url = dirname($url).'/';
            }
        }
        return $url;
    }
    

}
