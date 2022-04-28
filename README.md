
# Invoice Management RESTful API
It is a RESTful API for Invoice Management. This system enables the users to manage all the sell invoices.
The system composed by:

- Customer management
- Category management
- Product management
- Invoice management

## Built With
- [Laravel](https://laravel.com/) The PHP Frameworkfor web application development
- [Laravel Passport](https://laravel.com/docs/8.x/passport) The full OAuth2 server implementation for your Laravel application.
- [MySQL](https://www.mysql.com/) The relational database management system (RDBMS).

## Installation

### Installation with Sail
Install docker on Mac, Windows or Linux [https://docs.docker.com/get-docker/](https://docs.docker.com/get-docker/)

For Linux you need to install docker compose separately here [https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/)

```bash
# Get the latest snapshot
$ git clone https://github.com/Adib128/invoice-management.git

# Change directory
$ cd invoice-management

# Rename the .env example file
$ mv .env.example .env

# Configure Sail as a Bash alias
$ alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# Start all of the Docker containers in the background
$ sail up -d

# Migrate database structure
$ sail php artisan migrate

# Seed database
$ sail php artisan db:seed

# Run the application
$ sail php artisan serve

```
Now if you go to http://0.0.0.0:80/api-documentation, you'll  find a detailed documentation of the API.

## Testing

To run test use the following command:

```bash
# Run application test
$ sail php artisan test

```