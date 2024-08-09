 # fefu-yii2-test-task
***
>Тестовое задание на знание фреймфорка Yii2 в ДВФУ.
***
## Постановка задачи
В компании заказчика очень любят проводить собрания. В течение одного дня может быть проведено несколько десятков собраний.
Каждое собрание имеет время начала, с точностью до минуты, и планируемое время окончания. Собрания всегда проводятся в пределах одного рабочего дня.
В каждом собрании принимает участие произвольный набор сотрудников компании.
Интервалы времени проведения собраний могут пересекаться, и так происходит часто. В один момент времени сотрудник может присутствовать только на одном собрании.
## Задача
Создать сервис для построения такого расписания собраний заданного сотрудника, при котором он сможет посетить максимальное количество собраний на указанную дату.
Сервис должен быть реализован, как REST API с операциями CRUD для собраний и сотрудников. Плюс, метод для построения расписания.

>Задача со звёздочкой: построить оптимальный алгоритм построения расписания собраний.
***
## Стэк
- PHP = ^8.3.7
- MySQL = ^8.3.0
- Yii2 = ^2.0.45
***
## Установка
Установить PHP, Composer и MySQL.
Склонировить репозиторий:
```bash
git clone https://github.com/ihopes0/fefu-yii2-test-task.git
```
Установить все зависимости:
```bash
cd fefu-yii2-test-task; \
composer install
```
Поднять БД и добавить подключение в common/config/main-local.php.
```php
return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=YOUR_DB_NAME',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        // ...
    ],
    // ...
];
```
Провести миграции, сгенерируются демо данные пользователей и встреч:
```bash
php yii migrate/up
```
Поднять встроенный PHP сервер:
```bash
php -S localhost:8888 -t frontend/web
```
***
## API
### User
<span class="method get">GET</span> /user - получить первые 20 пользователей

<span class="method get">GET</span> /user?page=NUM - получить 20 пользователей на странице NUM

<span class="method get">GET</span> /user?expand=meetups - получить 20 пользователей с их расписанием

<span class="method get">GET</span> /user/{id} - получить одного пользователя по id

<span class="method post">POST</span> /user - добавить нового пользователя

<span class="method post">POST</span> /user/create-schedule?id=IDS&day=DAY - составить расписание для пользователя или пользователей, где IDS - это строка с id пользователей через запятую ("1,2,3,4"), для всех - all; DAY - дата в формате y-m-d (24-10-01)

<span class="method update">PATCH, PUT</span> /user/{id} - обновить данные пользователя по id

<span class="method delete">DELETE</span> /user/{id} - удалить пользователя с id

### Meetings
<span class="method get">GET</span> /meetup - получить первые 20 встреч

<span class="method get">GET</span> /meetup?page=NUM - получить 20 встреч на странице NUM

<span class="method get">GET</span> /meetup?expand=users - получить 20 встреч с их 
 участниками

<span class="method get">GET</span> /meetup/{id} - получить одну встречу по id

<span class="method post">POST</span> /meetup - добавить новую встречу

<span class="method update">PATCH, PUT</span> /meetup/{id} - обновить данные встречи по id

<span class="method delete">DELETE</span> /meetup/{id} - удалить встречу с id
***
## Создание расписания через CLI
```bash
php yii schedule/create-for USERS_ID DATE
```
USERS_ID - id пользователей через запятую, all для всех
DATE - дата формата y-m-d (24-10-01)
<style>
    .method {
        border-radius: 10px; 
        padding: 5px 5px;
    }

    .delete {
        background-color: #F0000E;
    }

    .post {
        background-color: #FFA500;
    }

    .update {
        background-color: #B3DCFD;
    }

    .get {
        background-color: #CD6;
    }
</style>