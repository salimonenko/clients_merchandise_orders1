<?php

header('Content-Type: text/html; charset=utf-8');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Тестовые выборки из базы данных по товарам и клиентам</title>

<style>
    .reqv {display: inline-block; margin: 6px}
    .info{border: solid 1px; height: 20px; display: inline-block; min-width: 350px; margin: 10px; width: auto}

</style>

</head>

<body>

<div class="reqv">
    <button onclick="work('generate', 'clients', 'all', false, '', '', '', true, '')">Создать тестовую базу<br/> данных из первого файла</button>
</div>

<div class="reqv">
    <button onclick="work('generate', 'clients', 'all', true, '', '', '', false, '')">Загрузить из другого файла <br/> ID товара;ID клиента;Комментарий к заказу <br/>в структуру БД</button>
</div>


<div class="reqv">
    <button onclick="work('retrieve', 'clients', 'all', false, '26.06.2024-03.07.2024', '', '', false, 'a')">Выбрать имена (name) всех клиентов,<br/> которые не делали заказы в последние 7 дней</button>
</div>

<div class="reqv">
    <button onclick="work('retrieve', 'clients', 5, false, 'all', 'max', '', false, 'b')">Выбрать имена (name) 5 клиентов,<br/> которые сделали больше всего заказов в магазине</button>
</div>

<div class="reqv">
    <button onclick="work('retrieve', 'clients', 'all', true, 'all', '', 'complete', false, 'd')">Выбрать имена (name) всех товаров, <br/>по которым не было доставленных заказов (со статусом complete). </button>
</div>


<br/>
<div id="xhr_message" class="info"></div>

<script>

/*                                     ЧтоСделать:                               Кто/Что:  Число      Делали     диапазонДат       Сумма,       Доставляли?
                                                                                          клиен./тов. заказы?     или all, ''   или max,или ''

* a. Выбрать имена (name) всех клиентов, которые не делали заказы в последние   : clients - all         нет   -  диапазонДат         ''              ''
 7 дней.
 b. Выбрать имена (name) 5 клиентов, которые сделали больше всего заказов в     : clients - 5           да    -    all              'max'            ''
 магазине.
 c. Выбрать имена (name) 10 клиентов, которые сделали заказы на наибольшую      : clients - 10          да    -    all               max             ''
 сумму.
 d. Выбрать имена (name) всех товаров, по которым не было доставленных          : items   - all         нет   -    all               ''          complete
 заказов (со статусом “complete”).
*
* Переменные:                           to_do                                   : what       n      flag_DO_item    dates             sum        delivered
* */

// a.-d.
function work(to_do, what, n, flag_DO_item, dates, sum, delivered, flag_DROP_TABLE, variant) {
// Впоследствии д.б. рефакторинг этой функции по клиентам или товарам, а также по видам работы с БД: запись или извлечение данных, хотя бы.
/*
* to_do: retrieve || generate           // Выборка из БД (получаем или записываем данные)
* what: clients || items                // Клиенты или товары
* n: число || all                       // числоКлиентов/Товаров или all (все)
* flag_DO_item: true || false           // Делали заказ или НЕ делали
* dates:  диапазонДат || all || ''      // или '' (нет даты), или диапазонДат, или all (все даты)
* sum:   '' || число || max             // или '' (нет суммы), или диапазонСумм, или max (любая сумма)
* delivered: complete || new || ''      // Товары доставляли или нет (статус complete или new), или неУказано
* flag_DROP_TABLE: true || false        // Уничтожать таблицу БД или нет
* variant: a || b || c || d || ''       // Выбор варианта выборки из БД
* */

// Проверяем входные параметры на корректность (д.б. и другие проверки)
    var flag_work_error = false;

    if(!/^retrieve$|^generate$/.test(to_do)){
        flag_work_error = 'Неверно указан вид задачи для сервера';
    }

    if(!/^clients$|^items$/.test(what)){
        flag_work_error = 'Неверно указана категория, для которой делается выборка';
    }

    if(!/^\d+$|^all$/.test(n)){
        flag_work_error = 'Неверно указано число клиентов, для которых делается выборка';
    }

    if(!/false|true/.test(flag_DO_item)){
        flag_work_error = 'Неверно указано действие. Должно быть указано, клиенты делали заказы или НЕ делали';
    }

    if(!/^\d\d\.\d\d\.\d\d\d\d\-\d\d\.\d\d\.\d\d\d\d$|^all$|^$/.test(dates)){
        flag_work_error = 'Неверно указан диапазон дат, в течение которого делается выборка.';
    }

    if(!/^\d*$|^max$/.test(sum)){
        flag_work_error = 'Неверно указана сумма заказов для выборки';
    }

    if(!/complete|new|^$/.test(delivered)){
        flag_work_error = 'Неверно указан статус, доставлен ли товар или нет.';
    }

    if(!/^a$|^b$|^c$|^d$|^$/.test(variant)){
        flag_work_error = 'Неверно указан вариант выборки.';
    }


    // ...


     if(flag_work_error != 0){
         alert(flag_work_error);
         return -1;
     }

// Сообщение на сервер формируем вручную, т.к. автоматическое его формирование может внести уязвимости
    var x = 'to_do=' + to_do + '&what=' + what + '&n=' + n + '&flag_DO_item=' +
        flag_DO_item + '&dates=' + dates + '&sum=' + sum + '&delivered=' + delivered + '&flag_DROP_TABLE=' + flag_DROP_TABLE + '&variant=' + variant;
    x = encodeURI(x);

    sender(x);
}




    function sender(x) { // Кроссбраузерная (вместо некроссбраузерного fetch) функция отправляет сообщение на сервер  и ждет того или иного ответа, выводя потом его в alert
        var xhr = new XMLHttpRequest();
        // Готовим тело сообщения для отправки (м.б. дополнительная функциональность)
        var body = x;
        xhr.open("POST", 'api/create_database.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function xhr_state() {
            if (xhr.readyState != 4) return;
            if  (xhr.status == 201) {
                // После подтверждения получения сообщения сервером выдаем оповещение
                alert('Операция успешно выполнена сервером.');
            } else {
//                alert('xhr error '+xhr.statusText); // Сообщение об ошибке, например, на транспортном (IP/ТСР) уровне. Обычно вызвано проблемами  с доступом к сети или неправильной работой РНР на сервере, т.п.
            }

            document.getElementById('xhr_message').innerHTML = xhr.responseText;
        };
        xhr.send(body);
        return false;
    }





</script>

</body>
</html>