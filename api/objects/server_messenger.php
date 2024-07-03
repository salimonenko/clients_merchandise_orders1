<?php

// Анализируем результат выполнения публичного запроса и сообщаем пользователю
function server_messenger($public_request){
    if($public_request === true){
        // выдаем код ответа - 201 created
        http_response_code(201);
        echo "Операция выполнена успешно.";

    } else { // Если невозможно создать запись, сообщаем пользователю
        // Устанавливаем код ответа - 503 service unavailable
        http_response_code(503);
        echo "Произошла ошибка сервера при выполнении операции.";
    }
}


// Перехват исключения ошибочного доступа к файлу
function exeption_getFile($mess){
    throw new Exception($mess);
}
