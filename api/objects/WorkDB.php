<?php

class WorkDB{

    // для соединения с базой данных и имена таблиц
    private $conn;
    private $table_name; // = "clients";
    private $table_items; // = "merchandise";
    private $table_order;


    // свойства объекта
    public $id;
    public $name;
    public $item_id;
    public $id_item;
    public $date;
    public $name_item;
    public $comm;
    public $status;
    public $date1;
    public $dates;
    public $flag_DO_item;
    public $n;
    public $sum;
    public $delivered;
    public $variant;

    // конструктор с $db как соединение с базой данных
    public function __construct($db){
        $this->conn = $db;
    }

// Передать данные в класс
    public function setClientTableName( $value ){
        $this->table_name = $value;
    }

    // Создаем запись в базе данных
    public function generate_clients(){
// Таблица данных о клиентах (clients)
        // Делаем запрос для вставки данных, полученных из файла
        $query = "INSERT INTO $this->table_name  SET name=:name, id=:id";
//        $query = "INSERT INTO $this->table_name(name) VALUES (:name)";  // Или так - тоже можно

        // Подготовляем запрос
        $stmt = $this->conn->prepare($query);
        // Преобразуем опасные символы в безопасные последовательности
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":id", $this->id);


        try{
        // execute query
            if($stmt->execute()){
                return true;
            }
        }catch (Exception $e){
            exeption_getFile('Ошибка при добавлении в БД дополнительных данных. Возможно, они уже были добавлены.');
        }

        return false;
    }

    public function setMerchandiseTableName( $value ){
        $this->table_items = $value;
    }


    // Создаем запись в базе данных
    public function generate_merchandise(){
// Таблица данных о товарах (items)
        // Делаем запрос для вставки данных, полученных из файла
        $query = "INSERT INTO $this->table_items  SET name=:name_item, id=:id_item";

        // Подготовляем запрос
        $stmt = $this->conn->prepare($query);
        // Преобразуем опасные символы в безопасные последовательности
        $this->name_item = htmlspecialchars(strip_tags($this->name_item));
        $this->id_item = htmlspecialchars(strip_tags($this->id_item));

        // bind values
        $stmt->bindParam(":name_item", $this->name_item);
        $stmt->bindParam(":id_item", $this->id_item);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    public function setOrdersTableName( $value ){
        $this->table_order = $value;
    }

    // Создаем запись в базе данных
    public function generate_orders(){
// Таблица данных о товарах (items)
        // Делаем запрос для вставки данных, полученных из файла
        $query = "INSERT INTO $this->table_order  SET  customer_id=:id_item, item_id=:id, comment=:comm, order_date=:date1, status=:status";

        // Подготовляем запрос
        $stmt = $this->conn->prepare($query);
        // Преобразуем опасные символы в безопасные последовательности
        $this->name_item = htmlspecialchars(strip_tags($this->name_item));
        $this->id_item = htmlspecialchars(strip_tags($this->id_item));

        // bind values
        $stmt->bindParam(":id_item", $this->id);
        $stmt->bindParam(":id", $this->id_item);
        $stmt->bindParam(":comm", $this->comm);
        $stmt->bindParam(":date1", $this->date1);
        $stmt->bindParam(":status", $this->status);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }


    public function setRetrieveData( $value ){
        $this->table_order = $value;
    }

    // Делаем выборку по клиентам из базы данных
    public function retrieve_clients($n, $flag_DO_item, $dates, $sum, $delivered, $variant){

        if($flag_DO_item == ''){ // Делали заказы или нет
            $znak = '<';
        }else{
            $znak = '>';
        }

        // Получаем начальную и конечную даты
        if ($dates != '' && $dates !== 'all'){
            $dates_Arr = explode('-', $dates);

            $date_min = date(date_create_from_format("d.m.Y", $dates_Arr[0])->format("Y-m-d"));
            $date_max = date(date_create_from_format("d.m.Y", $dates_Arr[1])->format("Y-m-d"));

            $date1 = new DateTime($date_min);
            $date2 = new DateTime($date_max);
            $interval = $date1->diff($date2)->d; // Интервал дней между датами, заданными в $dates

        }else{
            $interval = '';
        }

        switch ($this->variant) {
            case 'a':
                // a. Выбрать имена (name) всех клиентов, которые не делали заказы в последние 7 дней (Источник: https://pastebin.com/xXjJXsGT , но там - ошибка)
                $query = "SELECT DISTINCT c.name    
                  FROM $this->table_name AS c, $this->table_order AS o
                  WHERE c.id = o.customer_id AND
                  o.order_date ". $znak ." CURDATE() - INTERVAL ". $interval ." DAY;";
                break;

            case 'b':
                // b. Выбрать имена (name) 5 клиентов, которые сделали больше всего заказов в магазине (без сортировки).
                $query = "SELECT c.name
                  FROM $this->table_name AS c, $this->table_order AS o
                  WHERE c.id = o.customer_id
                  GROUP BY c.id
                  ORDER BY COUNT(o.customer_id) DESC
                  LIMIT 5;";
                break;

            case 'c':
                // c. Выбрать имена (name) 10 клиентов, которые сделали заказы на наибольшую сумму.
                /*  Суммы заказов из БД неизвестны, поэтому выполнить невозможно */
                break;

            case 'd':
                // d. Выбрать имена (name) всех товаров, по которым не было доставленных заказов (со статусом complete).
                $query = "SELECT m.name
                          FROM merchandise AS m
                          WHERE m.id NOT IN
                         (SELECT item_id FROM orders WHERE status = 'complete')";
                break;

            default:
                http_response_code(400);
                die("Ошибка: выбран неверный запрос на сервер");
        }


/********************************************************************************************************************/

        // Подготовляем запрос
        $stmt = $this->conn->prepare($query);
/*      // execute query (выводит код SQL-запроса)
            if($stmt->execute()){
                $value = $stmt->fetch(PDO::FETCH_LAZY);
            }
*/
            // execute query
            if($stmt->execute()){
                $value = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            }

        if(empty($value)){
            $value = false;
        }

        return $value;
    }

}
