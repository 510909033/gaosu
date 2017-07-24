<?php

namespace app\common\model;

use think\Model;

class SysUser extends Model
{
    
    /**
     * 注册用户
     * @param array $data   注册信息
     * @return array err=0表示成功，通过user_model获取注册对象，err!=0注册错误，具体看数组信息
     */
public static function regApi($data){
        try {
    
            $vars = [
                'err'=>0,
            ];
    
            $model = new static;
    
            $res = $model->validate('\\app\\common\\validate\\RegValidate','',true)->save($data);
            if (false === $res){
                $vars['err'] = $model->getError();
                $vars['reason'] = 'validate';
            }else if ( 0 === $res){
                $vars['reason'] = '用户名已存在';
                $vars['err'] = $data['uni_account'];
            }else if (1 === $res){
                $vars['reason'] = 'success';
                $vars['err'] = 0;
                $vars['user_model'] = $model;//
            }else{
                $vars['reason'] = '其他情况res='.$res;
                $vars['err'] = 'error';
            }
        }catch (\Exception $e){
            if ( stripos($e->getMessage(), 'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry') !== false ){
                $vars['reason'] = '用户名已存在';
                $vars['err'] = $data['uni_account'];
                $vars['exception'] = $e->getMessage();
            }else{
                $vars['reason'] = 'exception';
                $vars['err'] = '系统错误';
                $vars['exception'] = $e->getMessage();
            }
        }
        return $vars;
    }
}
