# REST API для библиотеки

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

Таблицы и тестовые данные создаються за счет миграции.

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
- book -(one to one)-> edition



