## Setup

```bash
git clone git@github.com:gurgenandreasyan77/todo.git
#or 
git clone https://github.com/gurgenandreasyan77/todo.git
```

```bash
    cd todo
```

## Installation

Add this in **/etc/hosts**

```
127.0.0.1      todo.test
```

and run this in project

```bash
docker-compose up -d --build
```
```bash
make login
```
```bash
composer install
```
```bash
php artisan migrate
```
```bash
php artisan key:generate
```
```bash
php artisan storage:link
```

## Usage

open http://todo.test in your browser
