[![CircleCI](https://circleci.com/gh/hiteshpachpor/koms.svg?style=svg&circle-token=bc5c52e2fd542cde5628dd9ad08e9526cc2ddd9e)](https://github.com/hiteshpachpor/koms)

# Kitchen Order Management System

This project is intended to be used as a backend for kitchens to manage their inventory and orders.

## About

1. This project uses Laravel. Read the official [Laravel installation guide](https://laravel.com/docs/7.x/installation) to learn more about its dependencies.
2. [MySQL](https://www.mysql.com/) is required as a datastore.
3. APIs are exposed to interact with inventory and order data.

## Run it on Docker

### System requirements:

1. Docker

### Steps:

1. Clone this repository:

```bash
$ git clone https://github.com/hiteshpachpor/koms.git
```

2. Copy the contents of `.env.example` to `.env`, and then modify the below database config:

```yml
DB_CONNECTION=mysql
DB_HOST=koms-db
DB_PORT=3306
DB_DATABASE=koms
DB_USERNAME=komsuser
DB_PASSWORD=password
```

3. Start all the containers:

```bash
$ docker-compose up -d
```

**This will start 3 containers:**

-   `koms-webserver`: Nginx web server which listens to port 8080 for incoming traffic.
-   `koms-app`: Laravel app under php-fpm.
-   `koms-db`: MySQL database.

4. Generate an api key:

```bash
$ docker exec -it koms-app php artisan key:generate
```

5. Cache settings:

```bash
$ docker exec -it koms-app php artisan config:cache
```

> After this, you will be able to access the Laravel app on [http://localhost:8080/](http://localhost:8080/)
>
> However, for the APIs to be functional, we need to first set up a MySQL user.

6. Create a new MySQL user for the Laravel app:

```bash
$ docker exec -it koms-db bash
root@<container>:/# mysql -u root -p
mysql> GRANT ALL ON koms.* TO 'komsuser'@'%' IDENTIFIED BY 'password';
mysql> FLUSH PRIVILEGES;
```

> Once this is set up, the app is ready to interact with the database.

7. Run all database migrations & seed the database with sample data:

```bash
$ docker exec -it koms-app php artisan migrate:fresh --seed
```

If you only want to run database migrations:

```bash
$ docker exec -it koms-app php artisan migrate
```

## Run it manually

### System requirements:

1.  PHP >= 7.2
2.  Composer
3.  MySQL (optional)
4.  Apache2/Nginx (optional)
5.  Node.js (10.9.0 or above)

### Steps:

1. Clone this repository:

```bash
$ git clone https://github.com/hiteshpachpor/koms.git
```

2. Install all package dependencies:

```bash
$ composer install
```

3. This project uses Node.js version `10.9.0`. To switch to it (& install it):

```bash
$ nvm use
```

4. Set up prettier for code formatting & husky for commit lint:

```bash
$ npm i
```

5. This project requires a MySQL database (5.7 or above). For development purposes, you may install it locally or via Docker. Copy `.env.example` to `.env`, modify the below database config accordingly:

```yml
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

6. Generate the api key:

```bash
$ php artisan key:generate
```

7. Run all database migrations & seed the database with sample data:

```bash
$ php artisan migrate:fresh --seed
```

If you only want to run database migrations:

```bash
$ php artisan migrate
```

8. Run it locally:

```bash
$ php artisan serve
```

## Testing

> Laravel will automatically pick up database config from `.env.testing` while running tests, where SQLite configuration is set up.
>
> This makes it easy to run tests on CircleCI and it also doesn't pollute the database being used for development.

1. To set up the local SQLite test database:

```bash
$ touch database/database.sqlite
$ php artisan migrate --env=testing
```

2. Copy `APP_KEY` from `.env` to `.env.testing`.

3. Run all the tests:

```bash
$ php artisan test
```

4. Run a single test (e.g. ingredient listing):

```bash
$ php artisan test --filter testIngredientListing
```

## Documentation

## APIs

### 1. Create an ingredient

**Request:**

```json
POST /api/ingredients HTTP/1.1
Accept: application/json
Content-Type: application/json

{
    "name": "Chili",
    "description": "Lorem ipsum.",
    "in_stock": true,
    "stock_qty": 50,
    "measure": "g",
    "supplier_id": 1
}
```

### 2. List ingredients

**Request:**

```json
GET /api/ingredients HTTP/1.1
Accept: application/json
Content-Type: application/json
```

> This API is paginated using `page` query parameter.

### 3. Create a recipe

**Request:**

```json
POST /api/recipes HTTP/1.1
Accept: application/json
Content-Type: application/json

{
    "name": "Gravy",
    "description": "Lorem bro.",
    "ingredients": [
        {
            "id": 7,
            "amount": 1
        },
        {
            "id": 8,
            "amount": 2
        },
        {
            "id": 9,
            "amount": 3
        }
    ]
}
```

### 4. List recipes

**Request:**

```json
GET /api/recipes HTTP/1.1
Accept: application/json
Content-Type: application/json
```

> This API is paginated using `page` query parameter.

### 5. Create a box for a user

**Request:**

```json
POST /api/box/create HTTP/1.1
Accept: application/json
Content-Type: application/json

{
    "user_id": 1,
    "user_address_id": 1,
    "delivery_date": "2020-07-25",
    "delivery_slot": "Morning",
    "delivery_notes": "Ring the bell.",
    "recipes": [
        1,
        2,
        3
    ]
}
```

### 6. View the ingredients required to be ordered by the company

**Request:**

```json
GET /api/purchase-order/{YYYY-MM-DD?} HTTP/1.1
Accept: application/json
Content-Type: application/json
```

> If no date is passed, purchase order list for today + next 7 days is returned.

## Database Structure

### ingredient

> This table contains the list of all the ingredients required for recipes.

| field       | type                             | nullable | default        |
| ----------- | -------------------------------- | -------- | -------------- |
| id          | bigint(20) unsigned              | no       | auto_increment |
| name        | varchar(255)                     | no       |                |
| description | text                             | yes      |                |
| in_stock    | tinyint(4)                       | no       | 1              |
| stock_qty   | int(11)                          | no       | 0              |
| measure     | enum('g','kg','ml','l','pieces') | no       |                |
| supplier_id | bigint(20) unsigned              | no       |                |
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
| description | text                | yes      |                |
| created_at  | timestamp           | yes      |                |
| updated_at  | timestamp           | yes      |                |

### recipe_ingredient

> This table contains the list of all the ingredients that go into a recipe.

| field         | type                | nullable | default        |
| ------------- | ------------------- | -------- | -------------- |
| id            | bigint(20) unsigned | no       | auto_increment |
| recipe_id     | bigint(20) unsigned | no       |                |
| ingredient_id | bigint(20) unsigned | no       |                |
| amount        | int(11)             | no       |                |
| created_at    | timestamp           | yes      |                |
| updated_at    | timestamp           | yes      |                |

### box_order

> This table contains the list of all the boxes created by users.

| field           | type                                  | nullable | default        |
| --------------- | ------------------------------------- | -------- | -------------- |
| id              | bigint(20) unsigned                   | no       | auto_increment |
| user_id         | bigint(20) unsigned                   | no       |                |
| user_address_id | bigint(20) unsigned                   | no       |                |
| delivery_date   | date                                  | no       |                |
| delivery_slot   | enum('Morning','Afternoon','Evening') | no       |                |
| delivery_notes  | varchar(255)                          | yes      |                |
| created_at      | timestamp                             | yes      |                |
| updated_at      | timestamp                             | yes      |                |

### box_order_recipe

> This table contains the list of ingredients and recipes that go into a box.
>
> Rather than storing only references for recipes and ingredients, their names have also been copied over.
> This is done to safeguard against any recipes or ingredients getting modified or deleted in the future.

| field              | type                             | nullable | default        |
| ------------------ | -------------------------------- | -------- | -------------- |
| id                 | bigint(20) unsigned              | no       | auto_increment |
| box_order_id       | bigint(20) unsigned              | no       |                |
| recipe_id          | bigint(20) unsigned              | no       |                |
| recipe_name        | varchar(255)                     | no       |                |
| ingredient_id      | bigint(20) unsigned              | no       |                |
| ingredient_name    | varchar(255)                     | no       |                |
| ingredient_amount  | int(11)                          | no       |                |
| ingredient_measure | enum('g','kg','ml','l','pieces') | no       |                |
| created_at         | timestamp                        | yes      |                |
| updated_at         | timestamp                        | yes      |                |

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
| phone             | varchar(16)         | yes      |                |

### user_address

> This table contains the list of all the addresses saved by a user.

| field      | type                | nullable | default        |
| ---------- | ------------------- | -------- | -------------- |
| id         | bigint(20) unsigned | no       | auto_increment |
| user_id    | bigint(20) unsigned | no       |                |
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
