<?php  
	
	


	class WeChatAPi
	{	
		
		//用户关注事件
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



		//查询天气
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

	               $Content = '城市：'.$res['result']['today']['city']."\n";
	               $Content .= "日期：".$res['result']['today']['date_y'].'-'.$res['result']['today']['week']."\n"; 
	               $Content .= "今日温度：".$res['result']['today']['temperature']."\n"; 
	               $Content .= "今日天气：".$res['result']['today']['weather']."\n";
	               $Content .= "穿衣指数：".$res['result']['today']['dressing_advice']."\n";

	                return $Content;
	            }

        	}

		} 


	}


?>

