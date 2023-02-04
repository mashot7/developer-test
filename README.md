**[1D](https://github.com/Exove/developer-test#1d-create-a-module-in-drupal)**
<br/>
Custom module for Drupal that shows the contents of an RSS feed.

RSS Feed URL can be provided via URL query parameter like in example below:

``http://localhost:32788/link-list?rss-url=https://techcrunch.com/feed``

If it's not provided, then will be shown RSS Feed content from the URL provided in the configuration.
<br/>
<br/>
**[2B](https://github.com/Exove/developer-test#2b-create-an-sql-query)**
<br>
``SELECT CONCAT(first_name, ' ', last_name) as name,
COALESCE(GROUP_CONCAT(number SEPARATOR ', '), 'n/a') as numbers
FROM people
LEFT JOIN phones ON people.id = phones.user_id
GROUP BY name
ORDER BY numbers DESC;``
<br/>
<br/>
**[3A](https://github.com/Exove/developer-test#3a-get-data-and-save-it-locally)**
<br>
Fetch data from a product API ([URL](https://github.com/Exove/developer-test/blob/main/material/products.json)) and save it into an SQL database locally.

Defines 4 tables: categories, products, product_categories, and variations;

If there are changes in API response data, then updates the updated fields in database.

Variations are not being updated as they don't have id(id from API response).