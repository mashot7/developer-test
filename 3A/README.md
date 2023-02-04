**[3A](https://github.com/Exove/developer-test#3a-get-data-and-save-it-locally)**
<br>
Fetch data from a product API ([URL](https://github.com/Exove/developer-test/blob/main/material/products.json)) and save it into an SQL database locally.

Defines 4 tables: categories, products, product_categories, and variations;

If there are changes in API response data, then updates the updated fields in database.

Variations are not being updated as they don't have id(id from API response).