<?php
//  其他参数区
function sign2($j, $r) {
	$a = array();
    $p = array();
    $o = "";
    $v = strlen($j);
    for ($q = 0; $q < 256; $q++) {
        $a[$q] = charCodeAt(substr($j, ($q % $v), 1), 0);
        $p[$q] = $q;
    }

    for ($u = $q = 0; $q < 256; $q++) {
        $u = ($u + $p[$q] + $a[$q]) % 256;
        $t = $p[$q];
        $p[$q] = $p[$u];
        $p[$u] = $t;
    }
    for ($i = $u = $q = 0; $q < strlen($r); $q++) {
        $i = ($i + 1) % 256;
        $u = ($u + $p[$i]) % 256;
        $t = $p[$i];
        $p[$i] = $p[$u];
        $p[$u] = $t;
        $k = $p[(($p[$i] + $p[$u]) % 256)];
        $o .= fromCharCode(charCodeAt($r, $q) ^ $k);
    }
    return base64_encode($o);
}

function charCodeAt($str, $index){
    $char = mb_substr($str, $index, 1, 'UTF-8');
    if (mb_check_encoding($char, 'UTF-8'))
    {
        $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
        return hexdec(bin2hex($ret));
    }
    else
    {
        return null;
    }
}
function fromCharCode($codes) {
   if (is_scalar($codes)) $codes= func_get_args();
   $str= '';
   foreach ($codes as $code) $str.= chr($code);
   return $str;
}
?>