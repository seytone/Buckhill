<p align="center"><a href="https://www.buckhill.co.uk/about" target="_blank"><img src="https://pbs.twimg.com/profile_images/1448253066185170950/-aKSNf1I_400x400.jpg" width="400"></a></p>

## The Project

This task involves demonstrating my backend skills by developing an API for a Pet Shop, aligning with various user stories that outline specific requirements. It forms a crucial segment of the selection process to join the Buckhill development team. Furthermore, it presents an excellent opportunity for me to showcase my ability to excel in such challenges while adhering to strict timelines.

## About Buckhill

Buckhill was born of a desire to help organisations achieve greater success by partnering with them to create an innovative digital environment. They are a highly motivated and proficient team of technology experts with considerable experience in solving real-world IT problems and implementing best-of-breed solutions.

## Instructions to run

To run this project, just follow the steps below:

- Clone the repo [https://github.com/seytone/Buckhill.git](https://github.com/seytone/Buckhill.git) to download to your local.
- Open a Linux or WSL2 terminal
- Run: git clone https://github.com/seytone/Buckhill.git PetShopAPI
- Run: cd PetShopAPI
- Run: cp .env.example .env
- Run: composer install
- Run: php artisan key:generate
- Run: php artisan migrate
- Run: php artisan db:seed
- Run: php artisan serve --host=localhost --port=8000

## Instructions to test

You can use Postman in order to test the API methods created for the task. So, just open Postman and import the Buckhill Backend-Task.postman_collection.json file located in the root folder, and run each endpoint. Or, just take a look at the out of the box Swagger documentation attached to the project by going to http://localhost:8000/api/documentation/v1.

Excecute Unit tests
- Run: php artisan test

Look for code error using PHPstan
- Run: ./vendor/bin/phpstan analyse

Analyze code quality using PHPinsights
- Run: php artisan insights

## Technical Specifications

This project was build using Laravel v10.22.0 and PHP v8.2.10
