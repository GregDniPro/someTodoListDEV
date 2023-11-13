Requirements:
- Docker & docker-compose should be installed.
- Add "todolist.local    127.0.0.1" string to your /etc/hosts.

Commands to init:
- cd to the project directory, where docker-compose.yml is located.
- Run "cp .env.example .env && cp ./src/.env.example ./src/.env" to copy env files.
- Run "docker-compose run php composer install -o" to install app dependencies.
- Run "docker-compose run php php artisan migrate" to apply db migrations.
- Run "docker-compose run php php artisan db:seed" to fill db with example data.
- TODO generate openAPI docs

Tests and codestyle:
* TODO "./vendor/bin/codecept run" - run tests. TODO
* "./vendor/bin/pint --config ./pint.json --test" - run PHP code style fixer (remove --test for autofix).

Development tools:
* "php artisan ide-helper:generate" - PHPDoc generation for Laravel Facades
* "php artisan ide-helper:models" - PHPDocs for models
* "php artisan ide-helper:meta" - PhpStorm Meta file
