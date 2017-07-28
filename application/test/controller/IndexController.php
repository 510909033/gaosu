<?php
namespace app\test\controller;

use think\Controller;
class IndexController extends Controller {
    protected function _initialize()
    {

        
    }

	
	public function indexAction(){

			$this->reponseMsgAction();

	}
	
	// 接收事件推送并回复
	public function reponseMsgAction(){
		//1.获取到微信推送过来post数据（xml格式）
		$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
		$tmpstr  = $postArr;
		//2.处理消息类型，并设置回复类型和内容
		$postObj = simplexml_load_string( $postArr );
		//$postObj->ToUserName = '';
		//$postObj->FromUserName = '';
		//$postObj->CreateTime = '';
		//$postObj->MsgType = '';
		//$postObj->Event = '';
		// gh_e79a177814ed
		//判断该数据包是否是订阅的事件推送
		if( strtolower( $postObj->MsgType) == 'event'){
		    
			//如果是关注 subscribe 事件
			if( strtolower($postObj->Event == 'subscribe') ){
			   
				//回复用户消息(纯文本格式)	
				$toUser   = $postObj->FromUserName;
				$fromUser = $postObj->ToUserName;
				$time     = time();
				$msgType  = 'text';

				$content  = '欢迎关注智慧高速MPS'.PHP_EOL.'OpenID为：'.$postObj->FromUserName;

				$content  = '欢迎关注公众帐号'.$postObj->FromUserName.'-'.$postObj->ToUserName;

				$template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
				$info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
				echo $info;


			}
		}
	}

	public function http_curlAction($url,$type='get',$res='json'){	
		//1.初始化curl
		$ch = curl_init();
		$url = 'http://www.baidu.com';
		//2.设置curl的参数
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($type == 'post'){
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
		}
		//3.采集
		$output = curl_exec($ch);
		//4.关闭
		curl_close($ch);
		if($res == 'json'){
			return json_decode($output,true);
		}
	}
		

	/*function getWxAccessTokenAction(){
		//1.请求url地址
		$appid = 'wx9e1d8fc5ee0c85a1';
		$appsecret =  '39ea8dc418b5ab3a03867a5937fe19fd';
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
		//2初始化
		$ch = curl_init();
		//3.设置参数
		curl_setopt($ch , CURLOPT_URL, $url);
		curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
		//4.调用接口 
		$res = curl_exec($ch);
		//5.关闭curl
		
		if( curl_errno($ch) ){
			var_dump( curl_error($ch) );
		}
		$arr = json_decode($res, true);
		curl_close( $ch );
		var_dump( $arr );
	}*/

//返回access_token
	public function getWxAccessTokenAction(){
		//将access_token存在session/cookie中
		if($_SESSION['access_token'] && $_SESSION['expire_time']>time()){
			//如果access_token在session并没有过期
			return $_SESSION['access_token'];
		}else{
			//如果access_token不存在或已过期，重新取access_token
			$appid = 'wx9e1d8fc5ee0c85a1';
			$appsecret =  '39ea8dc418b5ab3a03867a5937fe19fd';
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
			$this->http_curlAction($url,'get','josn');
			$access_token = $res['access_token'];
			//将重新获取到的access_token存到session
			$_SESSION['access_token'] = $access_token;
			$_SESSION['expire_time'] = time()+7000;
			return $access_token;
		}
	}

	public function definedItemAction(){
		//创建微信菜单
		//目前微信接口的调用方式都是通过curl post/get
		echo $access_token = $this->getWxAccessTokenAction;
		 $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		 $postArr = array(
		 			'button'=>array(
		 					'name'=>'132'，
		 					'type'=>'click',
		 					'key'=>'item1',
		 					'url'=>'http://www.baidu.com'
		 				)//第一个一级菜单
		 		
		 	);
		 echo  $postJson = json_encode($postArr);
		 $res = $this->http_curl($url,'post','json',$postJson);
		 var_dump($res);
	}

}//class end
