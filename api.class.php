<?php  
	
	

	class APi
	{

		public function PdoDb ($sql) 
		{

			$dsn = "mysql:host=localhost;dbname=wechat;";
			$this->pdo = new PDO($dsn, 'root', 'dasuan9464');
			$stmtObj = $this->pdo->query($sql);
			$Content = $stmtObj->fetchAll(2); 
			
			return $Content;
		}
		
		//接收事件推送并回复
		public function reponseMsg() 
		{	
			//实例化类
			$APiModel = new WeChatAPi;

			//获取到微信推送过来的post数据(xml)
			
			// $postArr = $GLOBAlS['HTTP_RAW_POST_DATA'];
			//获取到微信推送过来的post数据(xml)
			$postArr = file_get_contents('php://input');
			
			//处理消息类型
			$postObj = simplexml_load_string($postArr);
			

			//判断该数据包是否是订阅的事件推送
			//转为小写
			if ( strtolower( $postObj->MsgType) == "event") {
				// echo 'aaa';
				//如果是关注事件subscribe
				if ( strtolower($postObj->Event == "subscribe") ) {
					//回复用户消息
					
					$APiModel->reponseEvent($postObj);

				}
			}


			// //回复文本消息			
			// if ( strtolower( $postObj->MsgType) == "text") {

			// 	$str = trim($postObj->Content);

			// 	$sql = "SELECT  keywords FROM info WHERE keywords = '".$str."'";

			// 	$stmtObj = $this->pdo->query($sql);
			// 	$str = $stmtObj->fetch(2); 

			// 	$APiModel = new WeChatAPi;

			// 	$APiModel->reponseText($postObj, $str['keywords']);

			// } 


			//回复单文本及单图文消息
			if (strtolower($postObj->MsgType) == 'text' and trim($postObj->Content) == '图文') {
				$sql = "SELECT * FROM infolist";
				$arr = $this->PdoDb($sql);

				$APiModel->reponseMg($postObj, $arr);
			
			} else {
				
				if ( strtolower( $postObj->MsgType) == "text") {
                    
                    
                        $Content = trim($postObj->Content);

						$sql = "SELECT  keywords,reply FROM info WHERE keywords = '".$Content."'";

						$Content = $this->PdoDb($sql);

						if (!empty($Content)) {
							
							$APiModel->reponseText($postObj, $Content[0]['reply']);
							

						} else {

                            $Content = trim($postObj->Content);
                            $res = $APiModel->weather($Content);
                            if ($res) {
 									
                                $APiModel->reponseText($postObj, $res);
                                
                            } else {
                                
                                $Content = 'sorry,该信息数据库正在创建中.....';

                                $APiModel->reponseText($postObj, $Content);
                                
                                exit;
                                
                            }
                                                 

						}
			

				}
			}



		}




		//获取微信 access_token
		public function Wx_Access_Token () 
		{
			
			//1.q请求url地址
			$appid = 'wxe07339320208362c';
			$appSecre = '79a4a901a0f2deb9aeece8e11877927d';
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appSecre;

			//2.初始化
			$ch = curl_init();
			//3.设置参数
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			//4调用接口
			$res = curl_exec($ch);
			//5.关闭curl
			curl_close($ch);
			if (curl_errno($ch)) {
				var_dump(curl_errno($ch));
			}

			$arr = json_decode($res, true);
			var_dump($arr);

		}

		//获取微信服务器IP地址
		public function getWxServerIp()
		{
			$access_token = 'suL683hdpJO9TPETE5rxh0IRIGZ7P4oc-88swmXkA7Sfu7jgao8Shn0MCzFX2d54g4FM-dadY4uQHUYYJ5nth93ABdbntS1Tj_hoLuKM6VPZnAMUoZNQUPx6NQyrhgidAGGiAGADJJ';

			$url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($ch);

			curl_close($ch);
			if (curl_errno($ch)) {
				var_dump(curl_error($ch));
			}

			$arr = json_decode($res, true);
			echo '<pre>';
			var_dump($arr);

		} 


		

		//天气api

		




		

	}
