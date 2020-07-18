# Kitchen Order Management System

This project is intended to be used as a backend for kitchens to manage their inventory and orders.

## About

1. This project uses Laravel. Read the official [Laravel installation guide](https://laravel.com/docs/7.x/installation) to learn more about its dependencies.
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

5. This project requires a MySQL database (5.7 or above). For development purposes, you may install it locally or via Docker. Copy `.env.example` to `.env`, modify the below database config accordingly:

```yml
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

6. Run all database migrations & seed the database with sample data:

```bash
php artisan migrate:fresh --seed
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

> This table contains the list of all the ingredients required for recipes.

| field       | type                             | nullable | default        |
| ----------- | -------------------------------- | -------- | -------------- |
| id          | bigint(20) unsigned              | no       | auto_increment |
| name        | varchar(255)                     | no       |                |
| description | text                             | no       |                |
| in_stock    | blob                             | no       |                |
| stock_qty   | int(11)                          | no       |                |
| measure     | enum('g','kg','ml','l','pieces') | no       |                |
| supplier_id | int(11)                          | no       |                |
| created_at  | timestamp                        | yes      |                |
| updated_at  | timestamp                        | yes      |                |

### supplier

> This table contains the list of suppliers for the ingredients.

| field      | type                | nullable | default        |
| ---------- | ------------------- | -------- | -------------- |
| id         | bigint(20) unsigned | no       | auto_increment |
| name       | varchar(255)        | no       |                |
| created_at | timestamp           | yes      |                |
| updated_at | timestamp           | yes      |                |

### recipe

> This table contains the list of all the recipes.

| field       | type                | nullable | default        |
| ----------- | ------------------- | -------- | -------------- |
| id          | bigint(20) unsigned | no       | auto_increment |
| name        | varchar(255)        | no       |                |
| description | text                | no       |                |
| created_at  | timestamp           | yes      |                |
| updated_at  | timestamp           | yes      |                |

### recipe_ingredient

> This table contains the list of all the ingredients that go into a recipe.

| field         | type                | nullable | default        |
| ------------- | ------------------- | -------- | -------------- |
| id            | bigint(20) unsigned | no       | auto_increment |
| recipe_id     | int(11)             | no       |                |
| ingredient_id | int(11)             | no       |                |
| amount        | int(11)             | no       |                |
| created_at    | timestamp           | yes      |                |
| updated_at    | timestamp           | yes      |                |

### box_order

> This table contains the list of all the boxes created by users.

| field           | type                                  | nullable | default        |
| --------------- | ------------------------------------- | -------- | -------------- |
| id              | bigint(20) unsigned                   | no       | auto_increment |
| user_id         | int(11)                               | no       |                |
| user_address_id | int(11)                               | no       |                |
| delivery_date   | date                                  | no       |                |
| delivery_slot   | enum('Morning','Afternoon','Evening') | no       |                |
| delivery_notes  | varchar(255)                          | no       |                |
| created_at      | timestamp                             | yes      |                |
| updated_at      | timestamp                             | yes      |                |

### box_order_recipe

> This table contains the list of recipes that go into a box.

| field        | type                | nullable | default        |
| ------------ | ------------------- | -------- | -------------- |
| id           | bigint(20) unsigned | no       | auto_increment |
| box_order_id | int(11)             | no       |                |
| recipe_id    | int(11)             | no       |                |
| created_at   | timestamp           | yes      |                |
| updated_at   | timestamp           | yes      |                |

### users

> This table contains the list of all the users.

| field             | type                | nullable | default        |
| ----------------- | ------------------- | -------- | -------------- |
| id                | bigint(20) unsigned | no       | auto_increment |
| name              | varchar(255)        | no       |                |
| email             | varchar(255)        | no       |                |
| email_verified_at | timestamp           | yes      |                |
| password          | varchar(255)        | no       |                |
| remember_token    | varchar(100)        | yes      |                |
| created_at        | timestamp           | yes      |                |
| updated_at        | timestamp           | yes      |                |
| phone             | varchar(16)         | no       |                |

### user_address

> This table contains the list of all the addresses saved by a user.

| field      | type                | nullable | default        |
| ---------- | ------------------- | -------- | -------------- |
| id         | bigint(20) unsigned | no       | auto_increment |
| user_id    | int(11)             | no       |                |
| name       | varchar(255)        | no       |                |
| phone      | varchar(16)         | no       |                |
| flat       | varchar(64)         | no       |                |
| building   | varchar(128)        | no       |                |
| street     | varchar(255)        | no       |                |
| city       | varchar(64)         | no       |                |
| state      | varchar(64)         | no       |                |
| country    | varchar(64)         | no       |                |
| zipcode    | varchar(10)         | no       |                |
| created_at | timestamp           | yes      |                |
| updated_at | timestamp           | yes      |                |

---

**&copy; Hitesh Pachpor**
