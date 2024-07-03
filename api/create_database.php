<?php
// Для поддержки старых версий РНР
if(!function_exists('http_response_code')){
    require_once 'functions_PHP5_3/http_response_code.php';
}

define('main_php', 'access');

$servername = "localhost";
$database = "clients_merchandise_orders";
$username = "root";
$password = ""; // В учебных целях пароль не задан

// Имена таблиц БД
$database_table_clients = 'clients';
$database_table_merchandise = 'merchandise';
$database_table_orders = 'orders';


try{
// ********  Актуально при первом запуске, когда еще нет базы данных  **********************
// Создание соединения
$conn = new mysqli($servername, $username, $password);
// Проверка соединения
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

// Создание базы данных, если ее еще нет
$sql = "CREATE DATABASE IF NOT EXISTS $database";
    if ($conn->query($sql) !== TRUE) {
        die("Ошибка создания базы данных: " . $conn->error);
    }
$conn->close();


    $database_table_name_Arr = array( // Массив команд SQL
        $database_table_clients     => "(id INT(6) UNSIGNED  PRIMARY KEY, name VARCHAR(30) NOT NULL)",
        $database_table_merchandise => "(id INT(6) UNSIGNED  PRIMARY KEY, name VARCHAR(30) NOT NULL)",
        $database_table_orders      => "(id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, item_id INT(6) , customer_id INT(6) , comment VARCHAR(30) , status VARCHAR(30) , order_date DATE )"
    );





// Нужно ли уничтожать все таблицы БД ?
    $flag_DROP_TABLE = isset($_REQUEST['flag_DROP_TABLE']) ? $_REQUEST['flag_DROP_TABLE'] : 'not_isset';

    foreach ($database_table_name_Arr as $tableName => $SQLcommand) {
        // Создание нового соединения - для созданной базы данных с клиентами

        $conn_t = new mysqli($servername, $username, $password, $database);

        switch ($flag_DROP_TABLE) {
            case 'false': // Если нужно добавить записи в таблицу, то не удаляем ее
                break;
            case 'true':
                $sql = "DROP TABLE IF EXISTS ". $tableName; // Удаляем, чтобы потом создать заново
                $mes = '';
                if(!mysqli_query($conn_t, $sql)){
                    $mes = "ERROR: Не удалось выполнить $sql. " . mysqli_error($conn_t);
                    die($mes);
                };
                // Создание таблицы в базе данных
                $sql = "CREATE TABLE IF NOT EXISTS $tableName ". $SQLcommand;

                if(!mysqli_query($conn_t, $sql)){
                    $mes = "ERROR: Не удалось выполнить $sql. " . mysqli_error($conn_t);
                }
                // Закрыть подключение
                $conn_t->close();

                if($mes !== ''){
                    die($mes);
                }
                break;
            default:
                die('Ошибка. На сервер пришло неверное значение переменной flag_DO_item');
        }
    }





    include_once 'objects/main.php';


}catch (Exception $e){
    http_response_code(400);
    echo 'Произошла ошибка. Перехвачено исключение (см. '. $_SERVER['PHP_SELF'] . ', стр. '. __LINE__ . ' )';
}


