## Project Title

GROCERY MANAGEMENT

## Project Description

As its name suggests, it’s a Grocery Website where users can buy groceries online.
There will be three types of users(Admin, Partners, and Customers)
Admin can add/update/delete partners.
Partners can add a product, price, expiration date, etc.
Customers can add the products to the wishlist and cart.

## Install and Run the Project

Install laravel 10 project
command -> composer create-project laravel/laravel Grocery-management

## Run project

php artisan serve

## seeder for create admin

php artisan db:seed --class=UserSeeder

## make Migration model And Controller

php artisan make:model User -mc

## migrate Table

php artisan migrate

## developed Feature

## postman collection

https://documenter.getpostman.com/view/25052881/2s93K1nyod
