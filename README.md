## Installation

``` bash
# clone the repo
$ git clone https://github.com/masadi/toos-cbt.git toos-cbt

# go into app's directory
$ cd  toos-cbt

# install app's dependencies
$ composer install

# install app's dependencies
$ npm install

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
Then go to `my-project/.env`
And modify this line:

* APP_URL = ;

To make it look like this:

* APP_URL = http://example.com/sub-folder;


### Next step

``` bash
# in your app directory
# generate laravel APP_KEY
$ php artisan key:generate

# run database migration and seed
$ php artisan migrate:refresh --seed

# generate mixing
$ npm run dev

# and repeat generate mixing
$ npm run dev
```

## Usage

``` bash
# start local server
$ php artisan serve

# test
$ php vendor/bin/phpunit
```

## Copyright and license

copyright 2020 creativeLabs Łukasz Holeczek. Code released under [the MIT license](https://github.com/coreui/coreui-free-laravel-admin-template/blob/master/LICENSE).
There is only one limitation you can't can’t re-distribute the CoreUI as stock. You can’t do this if you modify the CoreUI. In past we faced some problems with persons who tried to sell CoreUI based templates.

## Support CoreUI Development

CoreUI is an MIT licensed open source project and completely free to use. However, the amount of effort needed to maintain and develop new features for the project is not sustainable without proper financial backing. You can support development by donating on [PayPal](https://www.paypal.me/holeczek), buying [CoreUI Pro Version](https://coreui.io/pro) or buying one of our [premium admin templates](https://genesisui.com/?support=1).

As of now I am exploring the possibility of working on CoreUI fulltime - if you are a business that is building core products using CoreUI, I am also open to conversations regarding custom sponsorship / consulting arrangements. Get in touch on [Twitter](https://twitter.com/lukaszholeczek).
