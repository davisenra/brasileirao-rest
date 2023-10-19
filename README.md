# Brasileirão REST

## Description

Welcome to the documentation for the Campeonato Brasileiro Série REST API, which provides access to data from the 2003 season up to the 2022 season, covering 8025 matches.

Data was provided by [Campeonato Brasileiro
@Kaggle](https://www.kaggle.com/datasets/adaoduque/campeonato-brasileiro-de-futebol/) as a CSV file. The file was parsed and stored in a MySQL database. Both the data model and the data itself can be found inside the `/data` folder.

## Motivation and goals

It's common to greenfield projects without any prior data. As a challenge, I decided to wrap existing data into a REST API, as a big football fan this Campeonato Brasileiro dataset was the perfect fit.

## Model

![Model](/data/model.png)

![API docs](/data/api-docs.png)

## Features

### Application

- RESTful endpoints
- Integration tests
- API documentation
- Authentication

### Stack

- PHP 8.2
- Laravel 10
- MySQL
- Docker

## Instructions to run

**WIP: Not yet dockerized**

<details>
<summary>Docker</summary>
The easiest way to run the project is using Docker. You can build the image and run the container with the following commands:

```bash
# Clone the repository
git clone https://github.com/davisenra/brasileirao-rest.git
cd brasileirao-rest

# Build the image and run the container
docker compose build
docker compose up -d
docker compose exec php bash

# Install dependencies
cd api
composer install

# Prepare application and the database
php artisan app:key generate
php artisan migrate:fresh

# Import data from CSV file
php artisan app:import-from-csv ../data/campeonato-brasileiro-dataset.csv

# Run tests
php artisan test
```
</details>