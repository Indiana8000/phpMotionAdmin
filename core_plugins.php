<?php

$path = realpath($GLOBALS['CONFIG']['PLUGIN_PATH']);

$GLOBALS['PLUGINS'] = Array();
if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            if(strtolower(substr($file,strlen($file)-4)) == ".php") {
            	require_once($path . '/' . $file);
            }
        }
    }
    closedir($handle);
}

?>