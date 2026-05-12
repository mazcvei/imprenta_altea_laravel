### Pasos para el despliegue

1-  git clone <URL>
2-  cp .env.example .env
2-  crear base de datos en phpmyAdmin y setear la variable DB_DATABASE en .env
3-  composer install
4-  php artisan migrate
5-  php artisan db:seed
6-  php artisan storage:link
7-  php artisan jwt:generate
8-  php artisan serve