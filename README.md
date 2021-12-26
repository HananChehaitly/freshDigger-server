<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Features

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- ## Admin
- Register Buyers 
- Sets weekly buying limit for dollar buying businesses
- ## Buyer
- Gets table of weekly exchanges
- Gets remaining buying allowance
- Gets notified when asked for a rate
- Can reply with an offer for the exchange
- Once buying limit is exceeded, can see the date on which they will be visible to sellers again
- ## Seller
- Gets Map of available buyers 
- Can see on the map pin the name and remaining allowance of the buyer
- Can see road directions to the buyer with Google Maps API
- Can go to the profile of the buyer from the map
- Can see the time to get to the destination
- Can ping buyer for a dollar-LBP exchange rate
- Can see the offers sorted plus pending ones
- Can accept or decline offer
- Has to confirm amount in case they accept

## Tech

- Laravel: A web application framework with expressive, elegant syntax.
- JWT: For authentication using JSON Web Tokens
- Google Maps API: Used for road directions and estimation of time to destination
- Expo-notifications: Used for push notifications
-React Native: The frontend of the website.

## Installation
Install composer on your machine using the following link:
Composer download

Clone the repository:

-git clone https://github.com/HaidarAliN/E-learning-Hub-server.git

In the command line, run:

-cd freshDigger-server

-composer update

Copy the example env file and make the required configuration changes in the .env file

-cp .env.example .env

Generate a new application key

-php artisan key:generate

Generate a new JWT authentication secret key

-php artisan jwt:generate

Run the database migrations (Set the database connection in .env before migrating)

-php artisan migrate

Start the local development server

-php artisan serve

## Database seeding

Populate the database with seed data with relationships which includes users, courses, uploaded materials, quizzes, questions, and submissions. This can help you to quickly start testing the API or couple a frontend and start using it with ready content.

Run the database seeder and you're done

-php artisan db:seed

Note: It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

-php artisan migrate:refresh
