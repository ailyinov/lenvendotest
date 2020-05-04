# lenvendotest
* git clone git@github.com:ailyinov/lenvendotest.git
* cd lenvendotest
* composer install
* docker-compose build
* docker-compose up -d
* `docker exec -it lenvendotest_php_1 php bin/console ongr:es:index:create`
 - paste your working directory name in container name e.g. %dirname%_php_1. 
 - `docker ps` to check container name
* sudo chmod -R 777 var public/img
* http://localhost:81
