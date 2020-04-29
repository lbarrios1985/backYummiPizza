# The Yummi Pizza

Take Pizza orders for delivery.

## Getting Started

### Prerequisites

PHP v7.3.7

### Installing

Create the Database

```
mysql> CREATE DATABASE db_name;
```

Clone the repository

```
git clone https://github.com/lbarrios1985/backYummiPizza.git
```

Switch to the repo folder

```
cd backYummiPizza
```

Copy the example env file and make the required configuration changes in the .env file

```
cp .env.example .env
```

Install all the dependencies using composer

```
composer install
```

Generate a new application key

```
php artisan key:generate
```

Run the database migrations (Set the database connection in .env before migrating)

```
php artisan migrate:refresh --seed
```

In case of error with the command above, run

```
composer dump-autoload
```

Install Laravel Passport for API authentication tokens

```
php artisan passport:install
```

Start the local development server

```
php artisan serve
```

You can now access the server at http://localhost:8000
