<?php
include "view.head.php";
?><pre><?php
foreach ($File_List as $key => $value){
	if($value["isdir"] == 1){
		printf("        %s               &lt;dir&gt; <a href=\x22%s\x22>%s</a>\n", date('Y-m-d h:m:s', $value["mtime"]), $value["path"], $value["filename"]);
	}elseif($value['isdir'] == 0){
		// 使用更合理的单位显示
		$value["size_unit"] = 0;
		while ($value["size"] > 102400) {
			$value["size"] /= 1024;
			$value["size_unit"]++;
			if($value["size_unit"] >= 2){break;}
		}
		switch($value["size_unit"]){
			case 0: $value["size_unit"] = "B ";break;
			case 1: $value["size_unit"] = "KB";break;
			case 2: $value["size_unit"] = "MB";break;
		}
		$value["size"] = number_format($value["size"]);
		if(strlen($value["size"]) < 15){
			$value["size"] = substr("               " . $value["size"], -15);
		}
		printf("        %s  %s %s <a href=\x22%s\x22 rel=\x22noreferrer\x22 >%s</a>\n", date('Y-m-d h:m:s', $value["mtime"]), $value["size"],  $value["size_unit"], $value["path"], $value["filename"]);
	}
}
?></pre><?php
include "view.bottom.php";
?>