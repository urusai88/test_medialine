Для доступа к дереву используются рекурсивные запросы (pgsql, mysql 8.*)

Проект разворачивается в докере

Миграции на месте

Экшены: 
* ```category/list?offset={offset}```
* ```news/list/{category_id}?offset={offset}```


Заполнить базу данных можно с помощью ```yii hello/seed-database```