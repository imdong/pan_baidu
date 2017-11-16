<?php
/**
 * 名称：cURL网页抓取
 * 版本：v0.3
 * 作者：吣碎De人(http://www.qs5.org)
 * 最后更新时间：2013年2月4日
 * 获取更新：http://www.qs5.org/
 * 
 */


//使用方法：
/*
$_Url = "http://www.baidu.com";
$_Data = "u=admin&p=123456";
$_Cookies = "0a63b_lastvisit=176%091359981539%09%2Flogin.php; 0a63b_winduser=BlEOUFpoCgUAAgAHWlVSDQZUCgMOUQcABwgAClFXUQFfCABTVlow; 0a63b_ck_info=%2F%09; 0a63b_lastvisit=deleted";
$Proxy = array("Proxy" => "124.160.133.2:80", "UserNmae" => "Root", "PassWord" => "Root");
$Head = array("User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)", "Accept-Language: en-us");

//						 地址  访问方式 Post数据 
$_Str = Get_Web_Contents($_Url, "GET", $_Data, $_Cookies, $Proxy, 30, $Head);
print_r($_Str);
*/


function Get_Web_Contents($_Get_Url, $_Method = "GET", $_Form_Data = "", $_Cookie = "", $_Proxy = array("Proxy" => ""), $_Time_Out = 30, $_Headers = array()){
	$ch = curl_init();	//创建cURL对象
	curl_setopt($ch, CURLOPT_URL, $_Get_Url);	//设置读取URL
	curl_setopt($ch, CURLOPT_HEADER, 1);	//是否输出头信息，0为不输出，非零则输出
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	//设置输出方式, 0为自动输出返回的内容, 1为返回输出的内容,但不自动输出.
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $_Time_Out);	// 设置超时 30秒
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
	// 设置代理
	if(isset($_Proxy["Proxy"])){
		curl_setopt($ch, CURLOPT_PROXY, $_Proxy["Proxy"]);	//设置代理地址
		if(isset($_Proxy["UserNmae"]) and isset($_Proxy["PassWord"])){
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_Proxy["UserNmae"].":".$_Proxy["PassWord"]);	// 设置代理用户名与密码
		}
	}
	// 设置 POST 数据
	if(strtoupper($_Method) == "POST"){
		curl_setopt($ch, CURLOPT_POST, 1);	//设置为 POST 提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $_Form_Data);	//设置POST数据
	}
	// 设置 Cookies 数据
	if(strlen($_Cookie)){
		curl_setopt($ch, CURLOPT_COOKIE, $_Cookie);	// 设置 Cookies
	}
	// 设置附加协议头
	if(isset($_Headers)){
		//设置 User-Agent
		if(isset($_Headers['User-Agent'])){
			curl_setopt($ch, CURLOPT_USERAGENT, $_Headers['User-Agent']);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $_Headers);	// 设置附加协议头
	}

	@$html = curl_exec($ch);  //执行
	if ($html === False) {	//获取错误,
		$ret["Error"] = curl_error($ch);
		return $ret;
	}
	$ret["Info"] = curl_getinfo($ch);	//获取详细信息
	curl_close($ch);//关闭对象
	// 区分头信息与正文
	$_wz = strpos($html,"\r\n\r\n");
	$ret["Header"] = substr($html,0,$_wz);	//截取头信息
	// 获取Cookies 信息
	if(preg_match_all("/set-cookie:\s?(.*?=.*?);/i", $ret["Header"], $cookie)){
		$cookie = $cookie[1];
	}
	$ret["Cookies"] = "";
	foreach ($cookie as $value){
		if(!is_array($value)){
			$ret["Cookies"].= $value."; ";
		}
	}
	$ret["Cookies"] = substr($ret["Cookies"],0,-1);

	$ret["Body"] = substr($html,$_wz+4);	//获取正文
	return $ret;
}
?>