docker exec -it laravel_app php artisan migrate:rollback
docker exec -it laravel_app php artisan migrate
docker exec -it laravel_app php artisan db:seed
