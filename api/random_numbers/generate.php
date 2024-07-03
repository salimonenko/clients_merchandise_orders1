<?php

// Для защиты от постороннего доступа (не из того модуля)
if(!defined('main_php') || (main_php !== 'access')){
    die('Ошибка доступа.');
}


try{
// Читаем файл с данными о клиентах и товарах
$lines_Arr1 = @$read->getFile() or exeption_getFile('Исключение. Отсутствует файл '. $file);
$mode = $read->mode;

    $public_request = false;

// Если данные непустые
if($lines_Arr1 !== '' && $lines_Arr1 != array()){

        for($i = 1; $i < sizeof($lines_Arr1); $i++){
        // Устанавливаем значение для последующей записи его в базе данных

            $line = explode(';', $lines_Arr1[$i]);

            $num->id_item = $line[0];
            $num->id = $line[1];
            $num->comm = $line[2];

            if($mode == 'false'){
                $num->date1 = date(date_create_from_format("d.m.Y", $line[3])->format("Y-m-d"));
                $num->name_item = $line[4];
                $num->name = $line[5];
                $num->status = $line[6];
            }else{ /* true */ // Для дополнительно добавляемых данных, взятых из другого файла
                $num->date1 = date('Y-m-d'); // Текущая дата
                $num->status = 'new';
            }

        // Создаем записи в таблицах
        $public_request = $num->generate_clients() or exeption_getFile('Исключение. Не выполнился запрос  generate_clients');
        $public_request = $num->generate_merchandise() or exeption_getFile('Исключение. Не выполнился запрос  generate_merchandise');
        $public_request = $num->generate_orders() or exeption_getFile('Исключение. Не выполнился запрос  generate_orders');
    }
    // Анализируем результат выполнения последнего публичного запроса и сообщаем пользователю
    server_messenger($public_request);

} else { // Сообщаем пользователю о проблеме
    // ответ - 400 bad request
    http_response_code(400);
    echo "Невозможно записать случайное число на сервер.";
}




// Для наглядности, выводим прочитанный текстовый файл с данными в браузер
foreach ($lines_Arr1 as $line_num => $line){
    echo $line .'<br/>';
}


}catch (Exception $e){
    http_response_code(400);
    echo $e->getMessage();
}

