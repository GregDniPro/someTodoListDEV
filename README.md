```markdown
# Todolist Project

## Requirements
- Docker & docker-compose should be installed.
- Add the following line to your `/etc/hosts` file: `todolist.local    127.0.0.1`

## Commands to Initialize
1. Navigate to the Todolist project directory, where `docker-compose.yml` is located.
2. Run the following commands to copy environment files:
   ```bash
   cp .env.example .env
   cp ./src/.env.example ./src/.env
   ```
3. Install dependencies:
   ```bash
   docker-compose run php composer install -o
   ```
4. Apply database migrations:
   ```bash
   docker-compose run php php artisan migrate
   ```
5. Fill the database with example data:
   ```bash
   docker-compose run php php artisan db:seed
   ```
6. Generate OpenAPI specification:
   ```bash
   php artisan openapi:generate
   ```
    - Alternatively, open 'todolist.local/openapi' in your browser.

## Tests and Codestyle
- Run tests:
  ```bash
  ./vendor/bin/codecept run
  ```
- Run PHP code style fixer (remove `--test` for autofix):
  ```bash
  ./vendor/bin/pint --config ./pint.json --test
  ```

## Development Tools
- Generate PHPDoc for Laravel Facades:
  ```bash
  php artisan ide-helper:generate
  ```
- Generate PHPDocs for models:
  ```bash
  php artisan ide-helper:models
  ```
- Generate PhpStorm Meta file:
  ```bash
  php artisan ide-helper:meta
  ```