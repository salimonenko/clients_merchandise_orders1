<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try{
// соединение с базой данных
require_once 'config/database.php';
// Подключаем объект WorkDB
require_once 'objects/WorkDB.php';
// Подключаем функцию вывода пользователю результата выполнения запроса
require_once 'objects/server_messenger.php';
// Подключаем функцию чтения текстового файла с данными о клиентах
require_once 'objects/ReadFILE.php';




if(!function_exists('http_response_code')){
    require_once 'functions_PHP5_3/http_response_code.php';
}


$database = new Database();
$db = $database->getConnection();

if(!$db){
    die('Не получилось установить соединение с базой данных.');
}

$num = new WorkDB($db);

$num->setClientTableName($database_table_clients);
$num->setMerchandiseTableName($database_table_merchandise);
$num->setOrdersTableName($database_table_orders);



// Принимаем присланные данные (еще д.б. проверка данных на валидность, но в тестовых целях - не делаем)
// Имена переменных задаем здесь, не доверяя их именам из запроса от клиента/ Доверять можно, если только к этому скрипту будет авторизованный или локальный (без запросов извне) доступ
// $data_reqv_Arr = array('what', 'to_do', 'n', 'flag_DO_item', 'dates', 'sum');
// Можно читать переменные из запроса в цикле, задавая их имена автоматически в виде $data_reqv_Arr[i], но это будет не наглядно. Поэтому задаем их вручную
$what = isset($_REQUEST['what']) ? $_REQUEST['what'] : '';
$to_do = isset($_REQUEST['to_do']) ? $_REQUEST['to_do'] : '';
$n = isset($_REQUEST['n']) ? $_REQUEST['n'] : '';
$flag_DO_item = isset($_REQUEST['flag_DO_item']) ? $_REQUEST['flag_DO_item'] : '';
$dates = isset($_REQUEST['dates']) ? $_REQUEST['dates'] : '';
$sum = isset($_REQUEST['sum']) ? $_REQUEST['sum'] : '';
$delivered = isset($_REQUEST['delivered']) ? $_REQUEST['delivered'] : '';
$variant = isset($_REQUEST['variant']) ? $_REQUEST['variant'] : '';

// Проверка на всякий случай, т.к. из браузера может прийти, что угодно
if(strlen($what) > 10 || strlen($to_do) > 10 || strlen($n) > 6 || strlen($flag_DO_item) > 10 || strlen($dates) > 25 || strlen($sum) > 6 || strlen($delivered) > 10){
    http_response_code(400);
    die("Ошибка браузера: похоже, на сервер переданы слишком длинные данные.");
}

$num->what = $what;
$num->n = $n;
$num->flag_DO_item = $flag_DO_item;
$num->dates = $dates;
$num->sum = $sum;
$num->delivered = $delivered;
$num->variant = $variant;



// 1. Логика получения на вход текстового файла с данными о заказах (разделитель “;”) вида: ID товара;ID клиента;Комментарий к заказу и загрузки его содержимого в структуру БД:
// Операция выполняется с использованием доверенного источника (т.к. файл находится на сервере), поэтому никаких проверок не делаем.
    switch ($flag_DO_item) {
        case 'false':
            $file = '../clients_data.txt';
            break;
    	case 'true':
		    $file = '../clients_data_toAdd.txt'; break;
        default:
            die('Ошибка. На сервер пришло неверное значение переменной flag_DO_item');
    }

$read = new ReadFILE($file, $flag_DO_item);


// 2. Логика тестового запроса a. : Выбрать имена (name) всех клиентов, которые не делали заказы в последние 7 дней.





    switch ($to_do) {
        case 'generate':
            include_once 'random_numbers/generate.php'; // Запрос для записи данных в таблицы базы данных
            break;
        case 'retrieve':
            include_once 'random_numbers/retrieve.php';
            break;
        default:
            http_response_code(400);
            die("Ошибка: выбран неверный запрос на сервер");
    }

}catch (Exception $e){
    http_response_code(400);
    echo 'Произошла ошибка. Перехвачено исключение (см. '. $_SERVER['PHP_SELF'] . ', стр. '. __LINE__ . ' )';
}