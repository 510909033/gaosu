<?php
namespace app\order\controller;

use app\common\model\SysOrder;
use think\Config;
use think\Controller;
use weixin\auth\AuthExtend;
use youwen\exwechat\api\accessToken;
use youwen\exwechat\api\message\template;

class OrderController extends Controller 
{

	function IndexAction()
	{
		$y = 0;
		for ($i=1; $i <2 ; $i++) { 
			$userIds = [2,22,100];
			$userId = $userIds[$y];
			$carId 	= rand(1,7);
			$bindId = model("WayUserBindCar")->getUserCar($userId);
			$inlist[] = ['user_id'=>$userId,'user_bind_car_id'=>$bindId['id'],'create_time'=>date('Y-m-d H:i:s',time()+1+$i),'in_time'=>date('Y-m-d H:i:s',time()+1+$i),'in_pos_id'=>1,'open_id'=>$userId]; 
			$outlist[] = ['user_id'=>$userId,'user_bind_car_id'=>$bindId['id'],'create_time'=>date('Y-m-d H:i:s',time()+2+$i),'out_time'=>date('Y-m-d H:i:s',time()+2+$i),'out_pos_id'=>2,'open_id'=>$userId];
			$y++;
			if ($y==3) {
				$y=0;
			}
			echo $i;
		}
		$in = model('WayLogIn');
		$in->saveAll($inlist);
		$out = model('WayLogOut');
		$out->saveAll($outlist);
	}
 	/**
	 * 
	 *合并出入口信息并下单
	 */

/*	public function MergelogAction()
	{
		$userLogData = [];

		$allData = model('WayLog')->getAllLog();
		$allData = $this->filterArray($allData);

		foreach ($allData as $key => $value) {
			switch ($value['type']) {
				case 'in':
					if ($allData[$key+1]['type']==$value['type']) 
						$this->addWayLog($allData[$key],array());
					else
						$this->addWayLog($allData[$key],$allData[$key+1]);
					break;
				case 'out':
					if ($key!=0) {
						if ($allData[$key-1]['type']==$value['type']) 
							$this->addWayLog(array(),$allData[$key]);
					}else{
						$this->addWayLog(array(),$allData[$key]);
					}
					break;
			}
		}
	}*/

	/**
	 * 
	 *过滤数组,去除重复录入的数据
	 * @param  (array)$arr 需要处理的数组
	 * @return array();
	 */
/*	public function filterArray($arr=[])
	{
		if (empty($arr)) return array();

		for ($i=0; $i <count($arr)-1 ; $i++) { 
			if ($arr[$i]['type']==$arr[$i+1]['type']){	
				if (strtotime($arr[$i+1]['in_time'])-strtotime($arr[$i]['in_time'])<10*60){
					model('WayLog'.ucfirst($arr[$i]['type']))->changeStatus($arr[$i]['id'],3);
					unset($arr[$i]);
				}
			}
		}
		return array_values($arr); 
	}*/

	/**
	 * 
	 *插入数据到总表
	 * @param  (array)$inarr 入口数据
	 * @param  (array)$outarr 出口数据
	 * @return bool;
	 */
/*	public function addWayLog($inarr,$outarr)
	{
		$data = array();

		$user_id 	= isset($inarr['user_id'])&&!empty($inarr['user_id']) ? $inarr['user_id'] : (isset($outarr['user_id'])&&!empty($outarr['user_id']) ? $outarr['user_id'] : 0 );
		$bindCat	= model('WayUserBindCar')->getUserCar($user_id);
		$in_pos_id 	= isset($inarr['in_pos_id']) ? $inarr['in_pos_id'] : 0; 
		$out_pos_id = isset($outarr['in_pos_id']) ? $outarr['in_pos_id'] : 0; 

		$data['user_id'] 			= $user_id; 
		$data['user_bind_car_id'] 	= isset($bindCat['id']) ? $bindCat['id'] : 0;
		$data['create_time'] 		= time();
		$data['in_time'] 			= isset($inarr['in_time']) ? strtotime($inarr['in_time']) : 0; 
		$data['out_time'] 			= isset($outarr['in_time']) ? strtotime($outarr['in_time']) : 0; 
		$data['in_pos_id'] 			= $in_pos_id;
		$data['out_pos_id'] 		= $out_pos_id;
		$data['pay_total_fee'] 		= $this->getPayNum($bindCat['car_type_id'],$in_pos_id,$out_pos_id);
		
		Db::startTrans();

		try {
			$res = model('WayLog')->add($data);
			if ($res) {
				if (!empty($inarr))
					model('WayLogIn')->changeStatus($inarr['id'],1);
				if (!empty($outarr)) {
					model('WayLogOut')->changeStatus($outarr['id'],1);
				}
			}			
			
			$order =  $this->addOrder($data);
			if (!$res||!$order) 
				return false;
			Db::commit();
		} catch (\Exception $e) {
			echo model()->_sql();
			Db::rollback();
		}
	}*/
	/**
	 * 
	 *计算费用
	 * @param  (int)$cartype 车型
	 * @param  (int)$in_pos_id 入口站点编号
	 * @param  (int)$out_pos_id 出口站点编号
	 * @return (int) 金额：单位分;
	 */

/*	public function getPayNum($carType,$in_pos_id,$out_pos_id)
	{
		//收费配置
		$charge 	= Config::get('charge_charge');
		$rate 		= $charge[$carType]; 
		$mileage 	= abs($out_pos_id-$in_pos_id);

		return $rate*$mileage*1000;
	}*/
	/**
	 * 
	 *添加订单
	 * @param  (array())$data log原始数据
	 * @return (int) 添加生成的id;
	 */


/*	public function addOrder($data)
	{	

		$orderData = array();

		$orderData['user_id'] = $data['user_id'];
		$orderData['create_time'] = time();
		$orderData['create_cache'] = json_encode($data);
		$orderData['total_fee'] = $data['pay_total_fee'];
		$orderData['out_trade_no'] = create_order_num();
		$orderData['body'] = "高速费用";

		$id = model('SysOrder')->add($orderData);

		return $id;
	}*/

	/**
	 * 查询所有未支付的订单进行推送消息
	 */
	
	public function createPushMessageAction()
	{
	    $notPayOrder = SysOrder::all(['trade_state'=>'']);
	   if (!empty($notPayOrder))
	   {
    	    foreach ($notPayOrder as $key => $value)
    	    {
    	        $data = [
    	            'touser'=>$value['openid'],
    	            'template_id'=>'GE7wCNP-i61975MhBMaV1XOW7ExbzGV-fQqLh5iiW0w',
    	            'url'=>'http://gs.jltengfang.com/order/Wxpay/index?id='.$value['out_trade_no'],
    	            'topcolor'=>'#FF0000',
    	            'data'=>[
    	                'first'=>['value'=>'高速支付订单'],
    	                'orderID'=>['value'=>$value['out_trade_no']],
    	                'orderMoneySum'=>['value'=>$value['total_fee']],
    	                'backupFieldName'=>['value'=>'高速收费'],
    	                'backupFieldData'=>['value'=>'111'],
    	                'remark'=>['value'=>'如有问题请联系110']
    	            ],
    	        ];
    	        var_dump($data);
                $auth = new AuthExtend();
                $accessToken = $auth->getAccessToken(false);
    	        $message = new template($accessToken);
    	        $res = $message->send($data);
    	        var_dump($res);
    	    }
	   	}else 
	   	{
	       echo "没有未支付的订单";	    
	   	}
	}
}