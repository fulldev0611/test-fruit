# test-fruit
Test fruit task using symfony

Fruit Scraper Project
This project is a PHP-based web application (symfony application) that scrapes fruit data from https://fruityvice.com/#1, saves them into a database, send an email if new values are going to be added and allows users to filter and add fruits to/remove from their favorites (up to 10 items)

Installation:
- Clone the repository: git clone https://github.com/your-username/fruit-scraper.git
- Install dependencies: composer install
- Create a MySQL database and update the DATABASE_URL variable in the .env file.
- Create/update database schema : php bin/console doctrine:schema:update --force
- Start the development server: symfony server:start

Usage:

Scraping Fruits
- To scrape fruits and save them into the database, run the following command:
   php bin/console app:fruit-scraper

Viewing Fruits
- To view all fruits, go to the following URL: http://localhost:8000/ there is a menu which can help you show favorits too.
- There is a pagination.

Filters
You can filter fruits by name and family using the search form at the top of the page, you can reset the filters.

Favorites
- To view your favorite fruits, go to the following URL: http://localhost:8000/favorites
- In this page you can see details of the fruits you decided to put in favorites section.
- To add a fruit to your favorites, click the "Add to Favorites" button on a fruit's row.
- You can remove them by clicking the "Remove from favorites" button


Nutrition Facts
- The nutrition facts of all favorite fruits are displayed at the bottom of the favorites page.

Running Tests
- To run unit tests, use the following command:
