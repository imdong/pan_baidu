<?php
/* 百度网盘免分享代理 核心部分 */
require("contents.php");
require("lib.pan_baidu.php");

class Pan_Baidu {
	var $Web_Host;	// 网站地址
	var $Web_Path;	// 网站目录
	var $Web_HostPath;	// 网站完整路径
	var $Web_SSLHost;	// SSL服务器完整路径
	var $User_ID;	// 用户ID
    var $User_cookies;	// 用户Cookies;

    function _init($Host, $Path, $SSLHost, $User) {
    	$this->Web_Host = $Host;
    	$this->Web_Path = $Path;
    	$this->Web_HostPath = $Host . $Path;
    	$this->Web_SSLHost = $SSLHost;
    	$this->User_ID = $User;
    	$this->User_cookies = "BDUSS=FZQdGxUQXFEa0NnS2U2UmZzeGFNSkVuSUVISWgxNkpMY052aGkwY2lNSUFJbmxWQUFBQUFBJCQAAAAAAAAAAAEAAACYU94HaW0yNzc4NgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACVUVUAlVFVb;";
    	return $this->User_cookies;
    }

    function Get_List($path){
		$_Url = "http://pan.baidu.com/api/list?page=1&dir=%2FPublic_Share" . $path;
		$_Str = Get_Web_Contents($_Url, "GET", "", $this->User_cookies);
		$Json = json_decode($_Str["Body"]);
		if($Json->errno != 0){
			die("真不幸，竟然遇到了错误...");
		}
		$Json = $Json->list;
		foreach ($Json as $key => $value){
			$File_List[$key]["isdir"] = $value->isdir;
			$File_List[$key]["filename"] = $value->server_filename;
			$File_List[$key]["mtime"] = $value->server_mtime;
			if($value->isdir){
				$File_List[$key]["size"] = "<dir>";
				$File_List[$key]["path"] = $this->Web_HostPath . $this->User_ID . "/p" . substr($value->path,13);
			}else{
				$File_List[$key]["down"] = $this->Web_SSLHost . $this->User_ID . "/d/" . number_format($value->fs_id,0,'','') . "/" . $value->server_filename;
				$File_List[$key]["fs_id"] = number_format($value->fs_id,0,'','');
				$File_List[$key]["path"] = $this->Web_HostPath . $this->User_ID . "/v" . substr($value->path,13);
				$File_List[$key]["size"] = $value->size;
			}
		}
		// print_r($File_List);
		return $File_List;
	}

	function Get_Down($id){
		$_Url = "http://pan.baidu.com/disk/home";
		$_Str = Get_Web_Contents($_Url, "GET", "", $this->User_cookies);

		// if(!preg_match("#yunData\.sign1[^']+'(?<sign1>\w+)'.*?yunData\.sign3[^']+'(?<sign3>\w+)'.*?yunData.timestamp[^']+'(?<timestamp>\d+)'#is", $_Str["Body"], $sign)){
		if(!preg_match('#yunData\.setData\((?<yunData>.*?)\);\s+yunData\.setData#is', $_Str["Body"], $sign)){
			die("关键时候掉链子了..");
		}
		$sign = json_decode($sign['yunData'], true);

		$sign_key =  sign2($sign["sign3"], $sign["sign1"]);
		$timestamp = $sign["timestamp"];
		$sign = urlencode($sign_key);
		$fidlist = "%5B" . $id . "%5D";
		$_Url = "http://pan.baidu.com/api/download?sign=" . $sign . "&timestamp=" . $timestamp . "&fidlist=" . $fidlist;
		$_Str = Get_Web_Contents($_Url, "GET", "", $this->User_cookies);
		$Json = json_decode($_Str["Body"]);
		$Url = $Json->dlink[0]->dlink;
		$_Str = Get_Web_Contents($Url, "GET", "", $this->User_cookies);
		$Url = $_Str["Info"]["redirect_url"];
		return $Url;
	}
}
?>