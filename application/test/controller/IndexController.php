<?php
namespace app\test\controller;

use think\Controller;
class IndexController extends Controller {
    protected function _initialize()
    {

        
    }

	
	public function indexAction(){

			$this->responseMsgAction();

	}
	
	/*// 接收事件推送并回复
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

		//用户发送腾放关键字的时候，回复一个单图文
		if( strtolower($postObj->MsgType) == 'text' && trim($postObj->Content)=='腾放' ){
			$toUser = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$arr = array(
				array(
					'title'=>'微信',
					'description'=>"微信改变生活",
					'picUrl'=>'https://res.wx.qq.com/a/wx_fed/weixin_portal/res/static/img/1EqrNF5.png',
					'url'=>'https://weixin.qq.com/',
				),
				array(
					'title'=>'qq',
					'description'=>"每一天，乐在沟通",
					'picUrl'=>'http://android-artworks.25pp.com/fs08/2016/06/08/7/1_173210056691eb8d6f8398d53d320fac_con.png',
					'url'=>'http://www.qq.com',
				),
			);
			$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>".count($arr)."</ArticleCount>
						<Articles>";
			foreach($arr as $k=>$v){
				$template .="<item>
							<Title><![CDATA[".$v['title']."]]></Title> 
							<Description><![CDATA[".$v['description']."]]></Description>
							<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
							<Url><![CDATA[".$v['url']."]]></Url>
							</item>";
			}
			
			$template .="</Articles>
						</xml> ";
			echo sprintf($template, $toUser, $fromUser, time(), 'news');

			//注意：进行多图文发送时，子图文个数不能超过10个
		}else{
				$ch = curl_init();
				$url = 'http://wthrcdn.etouch.cn/weather_mini?city='.urlencode($postObj->Content);
				$str = file_get_contents($url);  //调用接口获得天气数据
    			$result= gzdecode($str);   //解压
   				echo  $result;
 				
				//执行HTTP请求
				
				$arr = json_decode($result,true);
				$content = $arr['data']['city'];


		}
	}*/


	 public function responseMsgAction()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$RX_TYPE = trim($postObj->MsgType);

				switch($RX_TYPE)
				{
					case "text":
						$resultStr = $this->handleTextAction($postObj);
						break;
					case "event":
						$resultStr = $this->handleEventAction($postObj);
						break;
					default:
						$resultStr = "Unknow msg type: ".$RX_TYPE;
						break;
				}
				echo $resultStr;
        }else {
        	echo "";
        	exit;
        }
    }

	public function handleTextAction($postObj)
	{
		$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$keyword = trim($postObj->Content);
		$time = time();
		$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";             
		if(!empty( $keyword ))
		{
			$msgType = "text";

			//天气
			$str = mb_substr($keyword,-2,2,"UTF-8");
			$str_key = mb_substr($keyword,0,-2,"UTF-8");
			if($str == '天气' && !empty($str_key)){
				$data = $this->weather($str_key);
				if(empty($data->weatherinfo)){
					$contentStr = "抱歉，没有查到\"".$str_key."\"的天气信息！";
				} else {
					$contentStr = "【".$data->weatherinfo->city."天气预报】\n".$data->weatherinfo->date_y." ".$data->weatherinfo->fchh."时发布"."\n\n实时天气\n".$data->weatherinfo->weather1." ".$data->weatherinfo->temp1." ".$data->weatherinfo->wind1."\n\n温馨提示：".$data->weatherinfo->index_d."\n\n明天\n".$data->weatherinfo->weather2." ".$data->weatherinfo->temp2." ".$data->weatherinfo->wind2."\n\n后天\n".$data->weatherinfo->weather3." ".$data->weatherinfo->temp3." ".$data->weatherinfo->wind3;
				}
			}
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			echo $resultStr;
		}else{
			echo "Input something...";
		}
	}

	public function handleEventAction($object)
	{
		$contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "感谢您关注【卓锦苏州】"."\n"."微信号：zhuojinsz"."\n"."卓越锦绣，名城苏州，我们为您提供苏州本地生活指南，苏州相关信息查询，做最好的苏州微信平台。"."\n"."目前平台功能如下："."\n"."【1】 查天气，如输入：苏州天气"."\n"."【2】 查公交，如输入：苏州公交178"."\n"."【3】 翻译，如输入：翻译I love you"."\n"."【4】 苏州信息查询，如输入：苏州观前街"."\n"."更多内容，敬请期待...";
                break;
			default :
				$contentStr = "Unknow Event: ".$object->Event;
				break;
        }
        $resultStr = $this->responseTextAction($object, $contentStr);
        return $resultStr;
    }
    
    public function responseTextAction($object, $content, $flag=0)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>%d</FuncFlag>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }

	private function weatherAction($n){
		include("weather_cityId.php");
		$c_name=$weather_cityId[$n];
		if(!empty($c_name)){
			$json=file_get_contents("http://m.weather.com.cn/data/".$c_name.".html");
			return json_decode($json);
		} else {
			return null;
		}
	}

	public function http_curl($url,$type='get',$res='json',$arr=''){	
		//1.初始化curl
		$ch = curl_init();
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
		//curl_close($ch);
		if($res == 'json'){
			if(curl_errno($ch)){
				//请求失败
				return curl_error($ch);
			}else{
				//请求成功
				return json_decode($output,true);
			}
		}
		
	}
		

	function getWxAccessTokenAction(){
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
		return $arr;
	}

//返回access_token
	/*public function getWxAccessTokenAction(){
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
	}*/

	public function definedItemAction(){
		//创建微信菜单
		//目前微信接口的调用方式都是通过curl post/get
		header('content-type:text/html;charset=utf-8');
		 $access_token = $this->getWxAccessTokenAction();

		 $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token['access_token'];
		 echo $url;
/*		 $auth = new AuthExtend();
         $accessToken = $auth->getAccessToken(false);*/
		 $postArr = array(
		 			'button'=>array(
		 					array(
		 						'name'=>urlencode('菜单一'),
		 						'type'=>'view',
		 						'key'=>'item1',
		 						'url'=>'http://gs.jltengfang.com/index.php/way/user/bindindex'
		 						),//第一个一级菜单
		 					array(
		 						'name'=>urlencode('菜单二'),
		 						'sub_button'=>array(
		 							array(
		 								'type'=>'view',
		 								'name'=>urlencode('扫码带提示'),
		 								'url'=>'http://gs.jltengfang.com/index.php/way/user/bindindex',
		 								),
		 							array(
		 								'type'=>'view',
		 								'name'=>urlencode('扫码带提示'),
		 								'url'=>'http://gs.jltengfang.com/index.php/way/user/bindindex',
		 								),
		 							),
		 						),//第二个一级菜单
		 					array(
		 						'name'=>urlencode('菜单3'),
		 						'type'=>'view',
		 						'key'=>'item3',
		 						'url'=>'http://gs.jltengfang.com/index.php/way/user/bindindex'
		 						)
		 					////第三个一级菜单
		 			)
		 		);
		 echo '<hr/>';
		 var_dump($postArr);
		 echo $postJson = urldecode(json_encode($postArr));

		 $res = $this->http_curl($url,'post','json',$postJson);

		 echo '<br/>';

		 var_dump($res);
	}

}//class end
