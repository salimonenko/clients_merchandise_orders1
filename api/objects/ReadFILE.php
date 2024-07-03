<?php

// Читает файл с данными о клиентах (ID товара;ID клиента;Комментарий к заказу;Дата)
class ReadFILE{

// Имя файла
    public $file;

    // конструктор с $file (передаем сюда имя файла с данными, которые нужно записать в базу данных)
    public function __construct($file, $flag_DO_item){
        $this->file_name = $file;
        $this->mode = $flag_DO_item;
    }

    public function getFile(){
        $lines_Arr = file($this->file_name, FILE_SKIP_EMPTY_LINES);
        return $lines_Arr;
    }

}