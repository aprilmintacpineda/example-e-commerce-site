# example-e-commerce-site
###### Not intended for publication.

I originally made this as a task for an exam when I applied for a company based in Poland, they gave me the requirements and I made sure that I met those requirements. There's nothing fancy here, just a simple e-commerce site that runs on native PHP, JS, CSS, and HTML, which is one of the requirements they asked.

If you would like to see it, feel free to clone it. Once you have cloned it, create a database on your database platform, then change the `core/config.php`, then run the `core/generate_items.php` to generate the test data. Only run it once or you'll get multiple copies of the same data, the site is not going to misbehave but you'll see multiple copies of the same products, which wouldn't make sense in real world scenarios.

###### core/config.php

```php
$db = 'mysql';
$host = 'localhost';
$dbname = 'experiment_db';
$user = 'root';
$pass = 'password';
```