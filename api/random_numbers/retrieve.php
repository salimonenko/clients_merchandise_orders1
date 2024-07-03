<?php

// Для защиты от постороннего доступа (не из того модуля)
if(!defined('main_php') || (main_php !== 'access')){
    die('Ошибка доступа.');
}

try{

    $public_request = false;
    // Извлекаем запись
    $value = $num->retrieve_clients($num->n, $num->flag_DO_item, $num->dates, $num->sum, $num->delivered, $num->variant);


ob_start();
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
ob_get_contents();

    if($value){
        $public_request = true;
    }
// Анализируем результат выполнения последнего публичного запроса и сообщаем пользователю
    server_messenger($public_request);


}catch (Exception $e){
    http_response_code(400);
    echo $e->getMessage();
    exeption_getFile('Исключение. Запрос клиента - неверный.');
}

