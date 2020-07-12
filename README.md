# REST API для библиотеки

## Как запустить

Входная точка для сервера: /web/index.php

В файле config.php указаны настройки для бд, под них сделан initial_db.sql.
Если нужно чистый проект, нужно указать здесь настройки и создать таблицы, такие как в initial_db.sql.

В initial_db.sql создается все нужные настройки бд:
- база данных library
- пользователь api_library со всеми правами только для бд library
- таблицы
- тестовые данные

## API

/api/book - uri для доступа к API книг
Действия:
* view
* create
* update
* delete

## Структура приложения

Запрос приходит в соответствующий контроллер, где обрабатывается действием.

Для каждой таблицы в бд существует модель с её свойствами.

## База данных

В приложении создается только одно соединение с бд. Оно же используется всеми моделями.

## Сущности базы данных

book:
- id - int, primary key
- name - char(255), unique
- edition_id - int

author:
- id - int, primary key
- name - char(255), unique

edition:
- id - int, primary key
- name - char(255), unique

book_author:
- book_id - int
- author_id - int

Связи:
- book -(many to many)-> author
- book -(many to one)-> edition

## Не сделано (не знаю нужно ли было избегать/делать это)

Если издание или автор были добавлены к объекту книги, а потом удалены, то при сохранении книги, в книге они останутся.

Класс построения запросов к бд и отдельные унаследованные методы для запросов.

