# AaxisTest

This Symfony app is being developed on Symfony 6.0.2
The toker authentication is done with the bundle https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html

Disclaimer:
The evaluation criteria "Customer Information Display: Evaluate your ability to present information clearly and
efficiently in the user interface, ensuring a smooth user experience" was dismissed on the development of this API, after consulting with the evaluation team. No frontend functionalities should be evaluated on this application.

## System requirements

PHP Version: 8.0.28 | Download link https://www.php.net/releases/
Composer Version: 2.5.8 | Download link https://getcomposer.org/download/
Postgres Version: 16.1 | Download link https://www.enterprisedb.com/downloads/postgres-postgresql-downloads
OpenSSL | Download link https://slproweb.com/products/Win32OpenSSL.html

## PHP configuratoin

Make sure the following extensions are installed and enabled in the php.ini file

- pdo_pgsql
- pgsql
- PDO

## Database 

The database and the user used for the DB connection can be changed from the .env file. 
The default configuration uses a DB "axis". The user is "postgres" with password "admin". Make sure to update the .env file with an existing user on your Postgres DB.

# Instructions
After cloning the project and configuring the Database, execute the next commands to get the app running. 

Install the application vendors
- composer install

Create the DB
- php bin/console doctrine:database:create

Run the migration
- php bin/console doctrine:migrations:migrate

Run the following command to create an admin user
- php bin/console new:admin:user admin@admin.com admin

Generate the SSL keys (must have openSSL on server)
- php bin/console lexik:jwt:generate-keypair

Start the local server with one of the following commands
- php bin/console server:start
- symfony server:start (requires having Symfony CLI installed)

# Routes to test
All the API routes (except the login) are protected by token authentication. You'll need to send the token in the header on each request.
The easiest way to test the routes would be using Postman or a similar application

Login to get the token (POST request)
- http://127.0.0.1:8000/api/login_check
- JSON example: 
[
    {
        "sku": "sku1",
        "product_name": "name1",
        "description": "description1"
    },
    {
        "sku": "sku2",
        "product_name": "name2",
        "description": "description2"
    }
]

Create a new product (POST request):
- http://127.0.0.1:8000/api/product/create
- JSON example:
{
    "sku": "sku1",
    "product_name":"name1",
    "description": "description"
}

Get a list of all products (GET request):
- http://127.0.0.1:8000/api/product/list

Update products (POST request):
- http://127.0.0.1:8000/api/product/update
- JSON example:
[
   {"sku": "sku1",
    "product_name": "new name1",
    "description": "new description1"
   },
    {"sku": "sku2",
    "product_name": "new name2",
    "description": "new description2"
   }
]
