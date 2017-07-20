<?php
namespace app\order\model;

use think\Model;
use think\Db;

class WayUserBindCar extends Model
{
 	protected $table 	= 'way_user_bind_car';
 	protected $pk 		= "id";

 	/**
	 * 
	 *获取用户车辆信息
	 * @param  (int)$userid 用户id
	 * @return array();用户绑定的车辆信息
	 */
 	public function getUserCar($userid=0)
 	{
 		$data = array();
 		if (empty($userid)||!is_numeric($userid)) return $data;

 		$sql = "select id,car_type_id,user_id from way_user_bind_car where user_id=? and status=1 and verify_time=1 limit 1";
 		$res = Db::query($sql,[$userid]);
 		if (!empty($res))
 			$data = $res[0];
 			
 		return $data;
 	}
}