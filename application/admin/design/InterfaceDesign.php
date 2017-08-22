<?php
namespace app\design;
use phpDocumentor\Reflection\Types\Mixed_;

class InterfaceDesign {
    
}
/**
 * 
 * @author Administrator
 *
 */
interface IUser{
    public function addUser(AbsUserStruct $userStruct);
    public function updateUser(AbsUserStruct $userStruct);
    public function deleteUser(AbsUserStruct $userStruct);
    
}
interface IRole{
    
    public static function addRole(IRoleStruct $roleStruct);
    
    
    
    
    
}
interface IMenu{
    
}
interface IRoleMenu{
    
}
interface IUserRole{
    
}
interface IUserMenu{
    
}

interface IRoleStruct{
    /**
     * @param mixed $data
     * @return array
     */
    public function parseAdd( $data);
    
    public function parseUpdate($data);
    
    
}

abstract class AbsUserStruct{
    const ACTION_TYPE_ADD = 'ADD';
    const ACTION_TYPE_UPDATE = 'UPDATE';
    const ACTION_TYPE_DELETE = 'DELETE';
    private $field = [
        'id',
        'uni_account',
        'type',
    ];
    
    private $action_type;
    
    public function __construct($action_type){
        
    }
    
    
    abstract protected function  checkWhenAdd();
    abstract protected function  checkWhenUpdate();
    abstract protected function  checkWhenDelete();
    
    
}
