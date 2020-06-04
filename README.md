## Installation

``` bash
# clone the repo
$ git clone https://github.com/masadi/toos-cbt.git toos-cbt

# go into app's directory
$ cd  toos-cbt

# install app's dependencies
$ composer install

```

## PostgreSQL

1. Install PostgreSQL

2. Create user
``` bash
$ sudo -u postgres createuser --interactive
enter name of role to add: laravel
shall the new role be a superuser (y/n) n
shall the new role be allowed to create database (y/n) n
shall the new role be allowed to create more new roles (y/n) n
```
3. Set user password
``` bash
$ sudo -u postgres psql
postgres= ALTER USER laravel WITH ENCRYPTED PASSWORD 'password';
postgres= \q
```
4. Create database
``` bash
$ sudo -u postgres createdb laravel
```
5. Copy file ".env.example", and change its name to ".env".
Then in file ".env" replace this database configuration:

* DB_CONNECTION=mysql
* DB_HOST=127.0.0.1
* DB_PORT=3306
* DB_DATABASE=laravel
* DB_USERNAME=root
* DB_PASSWORD=

To this:

* DB_CONNECTION=pgsql
* DB_HOST=127.0.0.1
* DB_PORT=5432
* DB_DATABASE=laravel
* DB_USERNAME=laravel
* DB_PASSWORD=password

### Set APP_URL

> If your project url looks like: example.com/sub-folder 
Then go to `toos-cbt/.env`
And modify this line:

* APP_URL = ;

To make it look like this:

* APP_URL = http://example.com/sub-folder;


### Next step

``` bash
# in your app directory

# generate laravel APP_KEY
$ php artisan key:generate

# Generate config file
$ php artisan config:cache

# run database migration and seed
$ php artisan migrate:refresh --seed

# symlink storage folder
$ php artisan storage:link
```

## Usage

``` bash
# start local server
$ php artisan serve
