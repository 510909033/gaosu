<?php
namespace app\common\trait_common;
use app\common\model\SysLogTmp;
use think\Validate;
use app\common\tool\ConfigTool;
use think\Request;
use think\Model;

trait RestTrait {
    protected function _before_save(){
       
    }
    protected function _before_update(){
         
    }
    protected function _before_delete(){
         
    }
    public  function getAddConfig(){
        return $this->_before_save(); 
    }
    
    /**
     * @return Model    string 模型对应的类名
     */
    public function getModelname(){
        $config = $this->_before_save();
        return $config['modelname'];
    }
    
    
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @var Model $modelname
     * @return \think\Response
     * 
     */
    public function saveAction(Request $request)
    {
        $json = [];
        try {
            $arr = $this->_before_save();
            
            $validate = $arr['validate'];
            $allowField = $arr['allowField'];
          
            $modelname = $arr['modelname'];
         
   
            $data = $this->request->post();
            $res = $modelname::addData($data, $validate , $allowField);
            if (is_object($res)){
                
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '添加成功';
                $json['id'] = $res->id;
            }else{
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = $res;
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = ConfigTool::$ERRORSTR_COMMON;
            $json['debug']['e'] = $e->getMessage();
        }
        if (ConfigTool::IS_LOG_TMP){
            SysLogTmp::log(get_class($this).' ，errcode='.$json['errcode'], var_export($json,true), 0, __FILE__.',line='.__LINE__ );
        }
        return \json($json);
    }
    
    
    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function updateAction(Request $request, $id)
    {
        $json = [];
        try {
            $arr = $this->_before_update();
        
            $validate = $arr['validate'];
            $allowField = $arr['allowField'];
            $modelname = $arr['modelname'];
        
             
            $data = $this->request->put();
        
            $res = $modelname::updateData($id,$data, $validate , $allowField);
            if (is_object($res)){
        
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '编辑成功';
                $json['id'] = $res->id;
            }else{
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = $res;
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = ConfigTool::$ERRORSTR_COMMON;
            $json['debug']['e'] = $e->getMessage();
        }
        if (ConfigTool::IS_LOG_TMP){
            SysLogTmp::log(get_class($this).' ，errcode='.$json['errcode'], var_export($json,true), 0, __FILE__.',line='.__LINE__ );
        }
        return \json($json);
    }
    
    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function deleteAction($id)
    {
        $json = [];
        $json['id'] = $id;
        try {
            $arr = $this->_before_delete();
        
            $modelname = $arr['modelname'];
        
            $res = $modelname::deleteOne($id);
            if ( true === $res ){
        
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '操作成功';
                $json['id'] = $id;
            }else{
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = $res;
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['html'] = ConfigTool::$ERRORSTR_COMMON;
            $json['debug']['e'] = $e->getMessage();
        }
        if (ConfigTool::IS_LOG_TMP){
            SysLogTmp::log(get_class($this).' ，errcode='.$json['errcode'].',html='.$json['html'], var_export($json,true), 0, __FILE__.',line='.__LINE__ );
        }
        return \json($json);
    }
    
    
}