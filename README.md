# Сервис приема и обработки заказов

Небольшой сервис на PHP 8.2+, Laravel 11, позволяющий операторам принимать и обрабатывать заказы.

## Требования

- PHP >= 8.2
- Composer
- SQLite

## Установка и запуск

1.  **Клонировать репозиторий:**
    ```sh
    git clone https://github.com/Vitaliy1991777/order-management-system.git
    cd order-management-system
    ```

2.  **Установить зависимости:**
    ```sh
    composer install
    ```

3.  **Создать файл окружения:**
    Скопируйте `.env.example` в `.env`.
    ```sh
    cp .env.example .env
    ```

4.  **Сгенерировать ключ приложения:**
    ```sh
    php artisan key:generate
    ```

5.  **Создать файл базы данных SQLite:**
    ```sh
    touch database/database.sqlite
    ```

6.  **Выполнить миграции базы данных:**
    Эта команда создаст все необходимые таблицы.
    ```sh
    php artisan migrate
    ```

7.  **Заполнить базу данных начальными данными:**
    Эта команда создаст двух пользователей: оператора и руководителя.
    ```sh
    php artisan db:seed
    ```

8.  **Запустить локальный сервер:**
    ```sh
    php artisan serve
    ```
    Сервис будет доступен по адресу `http://127.0.0.1:8000`.

## Данные для входа

-   **Руководитель:**
    -   **Логин:** `manager@example.com`
    -   **Пароль:** `password`

-   **Оператор:**
    -   **Логин:** `operator@example.com`
    -   **Пароль:** `password`

## Описание API


Все эндпоинты доступны без авторизации.

-   **Поиск клиента**
    -   `GET /api/customers`
    -   Обязательный параметр: `phone`.
    -   *Пример:* `/api/customers?phone=89292522270`

-   **Список заказов**
    -   `GET /api/orders`
    -   Необязательные параметры для фильтрации: `date` (ГГГГ-ММ-ДД), `status`, `search`.
    -   *Пример:* `/api/orders?status=новый&search=Петров`

-   **Создание нового заказа**
    -   `POST /api/orders`
    -   Тело запроса должно быть в формате JSON и содержать данные о клиенте и товарах.

-   **Статистика заказов**
    -   `GET /api/orders/stats`
    -   Возвращает количество заказов по каждому статусу.


## Архитектурные решения

Для избежания дублирования кода между веб-контроллером (`OrderController`) и API-контроллером (`Api\OrderController`), вся бизнес-логика по созданию заказа была вынесена в отдельный сервисный класс `App\Services\OrderService`.

Это решение соответствует принципам DRY (Don't Repeat Yourself) и SRP (Single Responsibility Principle), делая контроллеры "тонкими", а код — более чистым, тестируемым и легким для поддержки.

![Руководитель](https://github.com/user-attachments/assets4060b95d-8845-46ad-98dc-932513796532)
![Оператор](https://github.com/user-attachments/assets/bccb6c6b-63e4-474d-bd4e-c071fc57cc90)
![Статистика](https://github.com/user-attachments/assets/cc33bc49-2afb-412a-bfeb-71634cac73b7)
![Авторизация](https://github.com/user-attachments/assets/3e0960bf-65c1-48fc-a043-c108bb4f9588)
