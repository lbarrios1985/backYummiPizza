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

# API Routes

## User
Method: POST 		api/auth/signup
Method: POST 		api/auth/login
Method: GET 		api/user
Method: PATCH 		api/auth/edit_profile
Method: GET 		api/auth/logout

## Pizzas
Method: RESOURCE	api/pizza
Method: GET 		api/order/{pizza_id}

## Shopping Cart
Method: GET 		api/cart
Method: DELETE		api/cart/delete
Method: GET 		api/cart/increment/item/{item_pos}
Method: GET 		api/cart/decrement/item/{item_pos}
Method: GET 		api/cart/remove/item/{item_pos}
