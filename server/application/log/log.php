<?php 

function DebugLog($cont){
    $logfile = fopen("debug_log.txt", "a+");
    $stamp = date("Y-m-d")." ".date("h:i:s");
    fwrite($logfile, $stamp."   ".$cont."\n");
    fclose($logfile);
}
function array2str($array){
    $str="{";
    foreach($array as $key=> $val){
        $str.=" ".$key."=>".$val.",";
    } 
    $str.="}";
    return $str;
}
?>