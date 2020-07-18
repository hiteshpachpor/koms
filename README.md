# Kitchen Order Management System

This project is intended to be used as a backend for kitchens to manage their inventory and orders.

## About

1. This project uses Laravel. Read the official [Laravel installation guide](https://laravel.com/docs/7.x/installation) to learn more about the setup instructions.
2. [MySQL](https://www.mysql.com/) is required as a datastore.
3. APIs are exposed to interact with inventory and order data.

## Installation

1. Clone this repository:

```bash
git clone https://github.com/hiteshpachpor/koms.git
```

2. Install all package dependencies:

```bash
composer install
```

3. This project uses Node.js version `10.9.0`. To switch to it (& install it):

```bash
nvm use
```

4. Set up prettier for code formatting & husky for commit lint:

```bash
npm i
```

5. This project requires a MySQL database (5.7 or above). Set it up using:

```bash

```

6. Copy `.env.example` to `.env`, modify the below database config:

```yml
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

7. Run all database migrations & seed the database with sample data:

```bash

```

## Running

### Locally

```bash
php artisan serve
```

### On Docker

## Documentation

## APIs

### 1. Create an ingredient

### 2. List ingredients

### 3. Create a recipe

### 4. List recipes

### 5. Create a box for a user

### 6. View the ingredients required to be ordered by the company

## Database Structure

### ingredient

-   id
-   name
-   description
-   in_stock
-   stock_qty
-   measure
-   supplier_id
-   created_at
-   updated_at

### supplier

-   id
-   name
-   created_at
-   updated_at

### recipe

-   id
-   name
-   description
-   created_at
-   updated_at

### recipe_ingredient

-   id
-   recipe_id
-   ingredient_id
-   amount
-   created_at
-   updated_at

### box_order

-   id
-   user_id
-   user_address_id
-   delivery_date
-   delivery_slot
-   delivery_notes
-   created_at
-   updated_at

### box_order_recipe

-   id
-   box_order_id
-   recipe_id
-   created_at
-   updated_at

### users

-   id
-   name
-   phone
-   email
-   email_verified_at
-   password
-   remember_token
-   created_at
-   updated_at

### user_address

-   id
-   user_id
-   name
-   phone
-   flat
-   building
-   street
-   city
-   state
-   country
-   zipcode
-   created_at
-   updated_at
