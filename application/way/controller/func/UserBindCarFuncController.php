<?php

namespace app\way\controller\func;

use think\Controller;
use app\common\model\WayUserBindCar;

class UserBindCarFuncController 
{
    private $qrcode_root = ROOT_PATH.'public'.DS;
    private $db_path = 'user_bind_car'.DS.'qrcode'.DS;
    
    
    public function __construct(){
    }

    /**
     * 目录分级
     * @param unknown $id
     * @return string   44/44/
     */
    private function getSplitDir($id){
        
        return ($id%1000).DS.($id%1000).DS.($id%1000).DS;
    }
    
    
    
    /**
     * 创建用户车辆二维码
     * 保存图片到本地
     * @param boolean $is_replace  true的话强制生成图片，false如果图片存在直接返回 
     * @return string|false  图片路径，相对于public，起始位置没有/
     */
    public function createQrcode(WayUserBindCar $wayUserBindCar,$size=30,$margin=4,$is_replace=true ){
        $filename = VENDOR_PATH.'phpqrcode/phpqrcode.php';
        if (!is_file($filename)){
            exception('文件不存在：'.$filename);
        }
        require_once $filename;
        
        $text = $this->encrypt_qrcode_text($wayUserBindCar);
        
        
        
        $dir = $this->qrcode_root.$this->db_path.$this->getSplitDir($wayUserBindCar->id);
        
        $onlyfilename = $wayUserBindCar->id.'.png';
        $outfile = $dir.$onlyfilename;
        
        $db_file = $this->db_path.$this->getSplitDir($wayUserBindCar->id).$onlyfilename;
        if (!is_dir($dir)){
            if ( !mkdir($dir,755,true) ){
                exception('目录创建失败：'.$dir);   
            }
        }
        if ($is_replace){            
            if (is_file($outfile)){
                @unlink($outfile);
            }
        }else{
            if (is_file($outfile)){
                return $db_file;
            }
        }
        \QRcode::png(time() ,$outfile , \QR_ECLEVEL_L , $size=30,$margin=4);
        if (is_file($outfile)){
            return $db_file;
        }
        return false;
    }
    
    /**
     * 车辆二维码文字生成规则，此规则确定后不可改变
     * @param WayUserBindCar $wayUserBindCar
     * @return string 二维码text
     * @todo
     */
    private function encrypt_qrcode_text(WayUserBindCar $wayUserBindCar){
        
        return $wayUserBindCar->id;
    }
    
    /**
     * 解密车辆二维码
     * @param string $encrypt_qrcode_text
     * @return WayUserBindCar $wayUserBindCar   车辆信息模型
     * @todo
     */
    private function decrypt_qrcode_text(string $encrypt_qrcode_text){
        
    }
    
    
    
    
    
    
    
    
    
}
