# Laravel API with Docker Sail

This is a **Laravel API** running inside **Docker Sail**, providing an easy-to-setup development environment.

## Installation & Setup

- Clone this repository
```
git https://github.com/mpouras/research-works.git
```

- Enter the folder
```
cd research-works
```

- Install vendor files for docker
```
docker run --rm --interactive --tty -v $(pwd):/app composer install
```

- Create the .env file
```
cp .env.example .env
```

- Install Sail and run the app
```
./vendor/bin/sail up
```

### Run the application
```
./vendor/bin/sail up
```

- Run database migrations
```
./vendor/bin/sail artisan migrate
```

- Generate artisan key for .env file
```
php artisan key:generate
```
