# Delivery
The Api program is written using Symfony 5 framework.

git clone https://github.com/raymdau/Delivery.git

cd Delivery

cd docker

docker-compose up

### Unit Test
```
Directory: src/tests/OrdersTest.php
```
To run unit test, run docker-compose exec php-fpm ./bin/phpunit

## Database initiation
If database is not initaited properly, run docker exec -i docker_database_1 mysql -uroot -p12345678 delivery < database/dump/dump.sql

## API controller
```
Directory: src/src/Controller/OrdersController.php
```
