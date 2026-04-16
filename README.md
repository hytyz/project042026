the following are required:
- php 8.3+
- composer
- node.js 20+
- pnpm

run in the terminal:
`composer install`

then generate the app key:
`php artisan key:generate`

to enable ai generation, generate the api key from openrouter:
1. create a free account at https://openrouter.ai/
2. create an api key

a .env file needs to be created:
```
APP_NAME=polinotes
APP_ENV=local
# APP_KEY is where you put the key you generated wtih artisan
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
# if OPENROUTER_API_KEY is left blank, flashcard generation has a fallback
OPENROUTER_API_KEY=
```

create and (optionally) seed the db:
`php artisan migrate`
`php artisan db:seed`

install js dependencies:
`pnpm install`

run the app:
`composer dev`
