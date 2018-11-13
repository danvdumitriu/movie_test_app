Movie Test App
==============

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