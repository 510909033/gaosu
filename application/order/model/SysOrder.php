<?php
namespace app\order\model;

use think\Model;
use think\Db;

class SysOrder extends Model
{
	protected $table 	= 'sys_order';
 	protected $pk 		= "id";



 	 /**
	 * 
	 *插入
	 * @param  (array())$data 待插入的数据
	 * @return (int);插入生成的主键
	 */
 	public function add($data){
 		return Db::name($this->table)->insertGetId($data);
 	}
}