

# imdbMovieFilter


## Description

SPA made with laravel and VueJs.

User can use title/genre/language form to filter movies from IMDB using [OMDb API](http://www.omdbapi.com/), save results and search log into database and view them in respective tables.

## Project setup

Copy .env.example file to .env on the root folder.

Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.

```
npm install

composer install

php artisan key:generate

php artisan migrate

npm run watch

php artisan serve
```

##Project preview

![Preview GIFt](https://media.giphy.com/media/FqdnqUjBjpt9fuFMRR/giphy.gif)

##ToDo Improvements
- Data filtering and and queries should refactored and improved and made with working .attach method. 
- Adding tests.






