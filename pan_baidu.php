<?php
/*
 * 文件名: pan_baidu.php
 * 作  者: 青石
 * 
 */
// 应用程序初始化配置
$Pan_CFG_Host = "https://imdong.duapp.com";	// 只包含域名信息，如果有端口，也写进去，但是后面不要写 / 
$Pan_CFG_Path = "/pan_baidu/";		// 文件目录信息 前面以 / 开头 后面不要带符号
$Pan_CFG_HostPath_SSL = "https://imdong.duapp.com/pan_baidu/";	// 用来过防盗链下载的 https 地址，可以设置为上面的一样（就不能过防盗链了）	

include "class.pan_baidu.php";
header("Content-type: text/html; charset=utf-8");

if(!preg_match("#".$Pan_CFG_Path."/?\??(?<User>[^/]+)?(/?(?<Cmd>[^/])?)(?<Msg>/[^<^>^?^\*^|]*)?#i", $_SERVER['REQUEST_URI'], $_get_par)){
	die("你输入的URL地址不正确...");
}
// print_r($_get_par);
if(!isset($_get_par['User'])||$_get_par['User']==""){
	include "view.index.php";
	exit;
}
$_get_cfg['User'] = $_get_par['User'];

if(!isset($_get_par['Cmd'])||$_get_par['Cmd']==""){
	$_get_cfg['Cmd'] = "p";
}else{
	$_get_cfg['Cmd'] = $_get_par['Cmd'];
}

if($_get_cfg['Cmd'] == "p"){
	if(!isset($_get_par['Msg'])||$_get_par['Msg']==""){
		$_get_cfg["Path"] = "/";
	}else{
		$_get_cfg["Path"] = $_get_par['Msg'];
	}
}elseif($_get_cfg['Cmd'] == "v"){
	if(!isset($_get_par['Msg'])||$_get_par['Msg']==""){
		die("你输入的URL地址不正确...");
	}else{
		if(!preg_match("#^(?<Path>.+)?(/(?<FileName>[^/]+))#i",$_get_par['Msg'], $_get_par['ids'])){
			die("你输入的URL地址不正确...");
		}
		$_get_cfg['Path'] = $_get_par['ids']['Path'];
		$_get_cfg['FileName'] = $_get_par['ids']['FileName'];
	}
}elseif($_get_cfg['Cmd'] == "d"){
	if(!isset($_get_par['Msg'])){$_get_par['Msg']="";}
	if(!preg_match("#/(?<id>\d+)#i",$_get_par['Msg'], $_get_par['ids'])){
		die("你输入的URL地址不正确...");
	}
	$_get_cfg['id'] = $_get_par['ids']['id'];
}
unset($_get_par);

$Pan_Baidu = new Pan_Baidu;
$Pan_Baidu->_init($Pan_CFG_Host, $Pan_CFG_Path, $Pan_CFG_HostPath_SSL, $_get_cfg['User']);	// 初始化

if($_get_cfg['Cmd'] == "p"){
	$File_List = $Pan_Baidu->Get_List($_get_cfg["Path"]);
	include "view.userindex.php";
	exit;
}elseif($_get_cfg['Cmd'] == "d"){
	$File_Url = $Pan_Baidu->Get_Down($_get_cfg["id"]);
	header("Location: " . $File_Url);
	exit;
}elseif($_get_cfg['Cmd'] == "v"){
	$File_List = $Pan_Baidu->Get_List($_get_cfg["Path"]);
	$fileID = '';
	foreach ($File_List as $key => $value) {
		if($value['filename'] == $_get_cfg['FileName'] && $value['size'] != '<dir>'){
			$fileID = $value;
			continue;
		}
	}
	include "view.down.php";
	exit;
}else{
	die("未知的错误...");
}
?>