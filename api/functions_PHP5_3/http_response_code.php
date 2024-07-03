<?php

function http_response_code($code){ // В РНР5.3 (Denwer) нет функции http_response_code, поэтому эмулируем ее
//return;
    $httpStatusCode = $code;
    $phpSapiName = substr(php_sapi_name(), 0, 3);
        if($phpSapiName == 'cgi' || $phpSapiName == 'fpm'){
            header('Status: '.$httpStatusCode.' ');
        } else {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol.' '.$httpStatusCode.' ');
        }

    header('Content-Type: text/html; charset=utf-8');
}
