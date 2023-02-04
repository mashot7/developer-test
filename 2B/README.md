**[2B](https://github.com/Exove/developer-test#2b-create-an-sql-query)** <br>
``SELECT CONCAT(first_name, ' ', last_name) as name,
COALESCE(GROUP_CONCAT(number SEPARATOR ', '), 'n/a') as numbers
FROM people
LEFT JOIN phones ON people.id = phones.user_id
GROUP BY name
ORDER BY numbers DESC;``
