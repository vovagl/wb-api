WB API Laravel Service(сервис на Laravel для получения и хранения данных из внешнего API) Сервис загружает и хранит следующие сущности:

Orders
Sales,
Stocks,
Incomes,

Данные сохраняются в MySQL и доступны через REST API.

Стек:

PHP 8.1,
Laravel 12,
MySQL / MariaDB,
Laravel HTTP Client,
Artisan Commands,


Установка проекта:

git clone https://github.com/vovagl/wb-api.git,
cd wb-api,
composer install,
cp .env.example .env,
php artisan key:generate,


API Configuration:

API_BASE_URL=http://109.73.206.144:6969,
API_KEY=API_kEY,


Import data:

php artisan app:import-orders,
php artisan app:import-sales,
php artisan app:import-stocks,
php artisan app:import-incomes,



API Endpoints:

GET /api/orders,
GET /api/sales,
GET /api/stocks,
GET /api/incomes,
