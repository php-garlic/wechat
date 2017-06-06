<?php  
	
	class WeChatAPi
	{	
		// protected $pdo;

		// public function __construct()
		// {	
		// 	$dsn = "mysql:host=localhost;dbname=wechat;charset=utf8";
		// 	$this->pdo = new PDO($dsn, 'root', 'dasuan9464');
		// 	// $sql = "SELECT  keywords FROM info WHERE keywords = '大蒜'";
		// 	// $stmtObj = $this->pdo->query($sql);
		// 	// $Content = $stmtObj->fetch(2); 
		// 	// echo '<pre>';
		// 	// var_dump($Content['key']);
		// }
		public function reponseEvent ($postObj) 
		{
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$time     = time();
			$MsgType  = "text";
			$Content  = "欢迎关注程序蒜微信公众号，回复城市可查询天气如：惠州，回复自己的姓名获取矫情的话";

			$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>	
						<MsgType><![CDATA[%s]]></MsgType>							
						<Content><![CDATA[%s]]></Content>
						</xml>";

			$info = sprintf($template, $toUser, $fromUser, $time, $MsgType, $Content);
			echo $info;
		}



		//回复多图文
		public function reponseMg($postObj, $arr) 
		{

			$toUser = $postObj->FromUserName;
			$FromUser = $postObj->ToUserName;
			
			$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>".count($arr)."</ArticleCount>
						<Articles>";

			foreach ($arr as $v) {
				$template .= "<item>
							<Title><![CDATA[".$v['title']."]]></Title> 
							<Description><![CDATA[".$v['description']."]]></Description>
							<PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
							<Url><![CDATA[".$v['url']."]]></Url>
							</item>";
			}

			$template .= "</Articles></xml>";

			echo sprintf($template, $toUser, $FromUser, time(), 'news');

		}




		//回复单文本
		public function reponseText($postObj, $Content) 
		{

			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$time     = time();
			$MsgType  = "text";
			// $Content  = $postObj->Content;
			$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						</xml>";

			$info = sprintf($template, $toUser, $fromUser, $time, $MsgType, $Content);
			echo $info;

		}




		public function weather ($Content)
		{
			
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://apis.haoservice.com/weather?cityname=".$Content."&key=6ed94dcacf3f4d0f8a4049661128b2b4",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $res = json_decode($response, true);
            curl_close($curl);
            if ($res['result'] != null) {

	            if ($err) {

	                return false;
	            } else {

	                // $res = json_decode($response, true);
	                //var_dump($res['result']['today']['city']);

	                $Content = '城市：'.$res['result']['today']['city'] ."\n日期：".$res['result']['today']['date_y'].'-'.$res['result']['today']['week'] ."\n今日温度：".$res['result']['today']['temperature'] ."\n今日天气：".$res['result']['today']['weather'] ."\n穿衣指数：".$res['result']['today']['dressing_advice'];

	                return $Content;
	            }

        	}

           
		} 


	}//class


?>

