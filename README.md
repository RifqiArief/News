## Cara run project 

1. Buat database mysql dengan nama 'news' 

2. buka project lalu masukan command berikut secara berurutan pada terminal
- composer install 
- composer update
- composer require laravel/passport
- composer require intervention/image
- php artisan key:generate
- php artisan migrate
- php artisan passport:install
- php artisan storage:link

3. lalu jalankan service dengan command berikut
- php artisan serve
