# Laravel Blog Testing
This repository is a clone of the following project: https://github.com/JeffreyWay/Laravel-From-Scratch-Blog-Project
### *All the adjustments of the code are done for testing purposes as a part of a faculty course.*
---------------------------------------------------------------------------------------------------
## Laravel From Scratch Blog Post Demo Project

http://laravelfromscratch.com

## Installation of the Original Project

First clone this repository, install the dependencies, and setup your .env file.

```
git clone git@github.com:JeffreyWay/Laravel-From-Scratch-Blog-Project.git blog
composer install
cp .env.example .env
```

Then create the necessary database.

```
php artisan db
create database blog
```

And run the initial migrations and seeders.

```
php artisan migrate --seed
```
