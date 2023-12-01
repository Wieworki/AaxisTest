# SymfonyApi

This Symfony app is being developed on Symfony 6.0.2

## System requirements

PHP Version: 8.0.28 | Download link https://www.php.net/releases/
Composer Version: 2.5.8 | Download link https://getcomposer.org/download/
Postgres Version: 16.1 | Download link https://www.enterprisedb.com/downloads/postgres-postgresql-downloads

# PHP configuratoin

Make sure the following extensions are installed and enabled in the php.ini file

- pdo_pgsql
- pgsql
- PDO

## Database configuration

The database and the user used for the DB connection can be changed from the .env file. 
The default configuration uses a DB "axis". The user is "postgres" with password "admin". Make sure to update the .env file with an existing user on your Postgres DB.

### Database creation

To set up the DB run the following commands taking the project folder as root:

Create the DB
- php bin/console doctrine:database:create

Run the migration
- php bin/console doctrine:migrations:migrate

Run the following command to create an admin user
- php bin/console new:admin:user admin@admin.com admin