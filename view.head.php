<html>
<head>
	<title><?php echo $Pan_Baidu->User_ID; ?> - Root<?php echo urldecode($_get_cfg["Path"]); ?></title>
	<style>
		html{font-size:14px;font-family:arial;}
		a{text-decoration:underline; color:black;}
		a:hover{text-decoration:none;color:#ff0000}
		a:visited{text-decoration:none; color:blue;}
		h1{font-size:20px;}
	</style>
</head>
<body>
<h1><?php
$View_cfg_WebPath = $Pan_Baidu->Web_HostPath.$Pan_Baidu->User_ID;
echo '<a href="' . $View_cfg_WebPath . '/">' . $Pan_Baidu->User_ID . "</a>";
?> - <?php
echo '<a href="' . $View_cfg_WebPath . '/p/">Root</a>/';

$File_Path = explode("/", substr($_get_cfg["Path"], 1));
$File_Path_Web = $View_cfg_WebPath . "/p";
foreach ($File_Path as $key=>$value){
	if($value == ""){break;}
	$File_Path_Web .= "/" . $value;
	echo '<a href="' . $File_Path_Web . '">' . urldecode($value) . '</a>/';
}
?></h1><hr>