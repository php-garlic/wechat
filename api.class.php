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
			require('./api.Model.class.php');
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

			//回复单文本及单图文消息
			if (strtolower($postObj->MsgType) == 'text' and trim($postObj->Content) == '图文') {

				//查询数据库（查询多图文）
				$sql = "SELECT * FROM infolist";
				$arr = $this->PdoDb($sql);

				$APiModel->reponseMg($postObj, $arr);
			
			} else {
				//查询单文本
				if ( strtolower( $postObj->MsgType) == "text") {
                    
                    
                    $Content = trim($postObj->Content);

					$sql = "SELECT  keywords,reply FROM info WHERE keywords = '".$Content."'";

					$Content = $this->PdoDb($sql);


					if (!empty($Content)) {
						
						$APiModel->reponseText($postObj, $Content[0]['reply']);
						

					} else {

						//查询天气
                        $Content = trim($postObj->Content);
                        $res = $APiModel->weather($Content);
                        if ($res) {

								//查询天气
                            $APiModel->reponseText($postObj, $res);
                            
                        } else {
                            //如果以上都不存在
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


		
		/**
		 * $url  接口url string
		 * $type 请求类型 string
		 * $res 返回数据类型 string
		 * $arr post请求参数 string
		 */
		public function http_curl($url, $type='get', $res = 'json', $arr = '')
		{	
			
			//1.初始化curl
			$ch = curl_init();
			// $url = 'http://www.baidu.com';

			//2.设置curl的参数
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			if ($type == 'post') {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
			}

			//3.采集
			$output = curl_exec($ch);
			var_dump($output);
			//4.关闭
			curl_close($ch);
			if ($res == 'json') {
				if ( curl_errno($ch)) {
					//请求失败
					return curl_error($ch);
				} else {
					//请求成功
					return json_decode($output, true);
				}
				
			}
		}

		

		//获取access_token
		public function Access_ToKen () 
		{
			session_start();
			if ($_SESSION['access_token'] && $_SESSION['expire_time'] > time()) {
				//如果access_token在session没有过期,则直接返回
				return $_SESSION['access_token'];

			} else {
				//如果access_token不存在或者已过期，重新获取access_token

				$appid = 'wxe07339320208362c';

				$appsecret = '79a4a901a0f2deb9aeece8e11877927d';

				$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;

				$res = $this->http_curl($url, 'get', 'json');
				
				$access_token = $res['access_token'];
				// var_dump($access_token);exit;
				//存入session
				$_SESSION['access_token'] = $access_token;

				$_SESSION['expire_time']  = time() + 7000;
				// var_dump($_SESSION['access_token']);
				
				return $access_token;
			}
		}


		public function definedIte() 
		{

			//创建微信菜单
			//目前微信接口的调用的方法都是通过 post / get
			echo $access_token = $this->Access_ToKen();
			echo '<hr>';
			//调用接口
			$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
			//定义微信菜单
			$postArr=array(
            'button'=>array(
                array(
                    'name'=>urlencode('图文'),
                    'type'=>'click',
                    'key'=>'item1',
                ),
                array(
                    'name'=>urlencode('音乐'),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode('歌曲'),
                            'type'=>'click',
                            'key'=>'songs'
                        ),//第一个二级菜单
                        array(
                            'name'=>urlencode('百度一下'),
                            'type'=>'view',
                            'url'=>'http://www.baidu.com'
                        ),//第二个二级菜单
                    )
                ),

                array(
                    'name'=>urlencode('blog'),
                    'type'=>'view',
                    'url'=>'http://www.php-garlic.cn',
                ),//第三个一级菜单

        ));

			$postJson = urldecode( json_encode( $postArr ) );
			// echo '<hr>';
			$res = $this->http_curl($url, 'post', 'json', $postJson);
			
			
		}

		//群发接口
		public function sendMsgAll () 
		{

			//1.获取全局access_token
			echo $access_token = $this->Access_ToKen();
			$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$access_token;
			//2.组装群发接口数据 array
			

			$array = array(

				    "touser" => "onVNYxMgqHZpBzYA3lpEugVHpSGA",
				    "text"=> array("content"=>"this is a test"),            
					"msgtype"=>"text"

				); 
			echo '<hr>';
			//将数组转为json格式
			$postJson = json_encode($array);
			var_dump($postJson);
			echo '<hr>';
			//调用curl
			$res = $this->http_curl($url, 'post', 'json', $postJson);
			var_dump($res);

		}

	}


