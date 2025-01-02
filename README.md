### How to setup a working environment

This project is a simple Laravel 11 application, powered by Laravel Sail.

To help you with the initial setup, we’ve already added some basic code:
 - Routes, controllers and views
 - Migrations to create all required tables
 - Factories to create all Models
 - Database Seeders to have enough information to display Users (`DatabaseSeeder`)

#### Docker

We prepared a sample `.env.example` with essential environment variables, 
a basic `Dockerfile` to create our app image, and a `docker-compose.yml` file with required services. 
Feel free to edit these if needed.

Here are the steps you need to run the project.
Note that you need Docker and Docker Compose installed in order for these commands to work.

```sh
# Copy the example .env file
cp .env.example .env

# Install composer dependencies
composer install --ignore-platform-reqs
# or if you don't have composer or the latest PHP installed locally
docker run --rm -v $(pwd):/app composer install --ignore-platform-reqs

# Start Docker containers
./vendor/bin/sail up -d

# Generate application key
docker compose exec app php artisan key:generate

# Run all migrations and seed the DB
docker compose exec app php artisan migrate:fresh --seed --seeder=DatabaseSeeder

# Install front end dependencies
docker compose exec app npm install --no-update-notifier --no-fund

# Run Vite server (CSS, JS) within the container
npm run dev

# For login Run the seeders
php artisan db:seed
```
If everything works well, the project should be accessible on [http://localhost:8080](http://localhost:8080).

