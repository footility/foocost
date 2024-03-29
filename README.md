# FooCost for Laravel

## Overview

**FooCost** is a Laravel package designed to assist developers in estimating the development costs of an application by analyzing its database and source code. It offers automated cost estimates based on the number of tables and fields in the database, incorporating configurable parameters such as development time and hourly rates.

## Features

- Automated analysis of Laravel's database to estimate development costs.
- Easy configuration via `.env` file and/or a publishable configuration file.
- Support for Laravel 10 and above.

## Installation

To install FooCost into an existing Laravel project, follow these steps:

1. **Require the Package**

   Use Composer to add FooCost to your Laravel project:

    ```bash
    composer require footility/foocost
    ```

2. **Publish Configuration (Optional)**

   After installing the package, you can publish the configuration file to customize the default settings such as hourly rate and minutes per field:

    ```bash
    php artisan vendor:publish --provider="Footility\FooCost\FooCostServiceProvider"
    ```

   This command will copy the configuration file to `config/foocost.php` in your project, where you can modify it as needed.

## Usage

After installation, FooCost is ready to use. Access the cost estimation by visiting the `/foo/cost` route in your Laravel project.

For more details on how to configure and use FooCost, refer to the published configuration file or online documentation.

## Contributing

Interested in contributing to FooCost? Great! We are open to improvements and new ideas. Feel free to fork the repository, make changes, and submit a pull request.

## License

The FooCost package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
