Movie Test App
==============

Requirements:
The application must be a kind of frontend + cache for movie information services. So, it will have 3 functionalities:

1. main page - displaying movies that are now in the "top 10"
2. search - from the main page, search box, enter the name of a movie or the ID on IMDB and display the movies that correspond to the search (if it is imdb ID it will jump directly to the detail page of the movie)
3. movie details page - will display details about the respective movie: Name, poster picture (if any), movie description (plot), list of actors

Technologies to use:
- Backend: PHP with / without framework. If it has a framework, you can choose between Laravel, Symfony and Zend
- Frontend: HTML5, CSS3, JS + Jquery
- Database: MySQL

To bring the data, you can use any online service that provides this data, such as https://developers.themoviedb.org/3, http://www.omdbapi.com/ and even scraping directly from IMDB.com. One or more services can be used, depending on the technical solution chosen to obtain the necessary information (for example omdbapi does not provide the images).

All data is stored in the database, including photos. If the data exists in the database, it is not brought again from the net. The exception is the "Top 10" list, which should have an expiration date of one day. You can also cache search results (by movie name, so that online services are not called again).

All information rendered by the frontend must be dynamic (AJAX calls are made, data is displayed depending on the response received from the backend. HTML + may exist on disk, it does not have to be a one page app, but the information does not have to be rendered by the backend.


Live demo at: [http://movie_test_app.wingover.ro/](http://movie_test_app.wingover.ro/)

Apache user/pass: dan / dan2018!


Installation / Usage
--------------------

Run the following commands in a shell:

```
git clone git@github.com:danvdumitriu/movie_test_app.git
cd movie_test_app
composer install
npm install
cp .env.example .env
php artisan key:generate

```

Create a new mysql database.
Edit .env file and fill in the mysql connection params. 

Then run:

```
php artisan migrate
chmod -R 777 storage/
npm run build
```
