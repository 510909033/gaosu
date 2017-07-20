<?php
namespace app\order\model;

use think\Model;
use think\Db;

class WayLogOut extends Model
{
 	protected $table 	= 'way_log_out';
 	protected $pk 		= "id";



 	/**
	 * 
	 *变更入口信息的状态
	 * @param  (int)$id 信息编号
	 * @param  (int)$status 状态编号
	 * @return (int);影响的行数
	 */
 	public function changeStatus($id,$status=0)
 	{	
		return Db::query("update {$this->table} set status=? where id=?",[$status,$id]);
 	}
}