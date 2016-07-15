# Exercise Log
Minimal logging software to keep track of my exercises, and workouts.

-----

### Configuration
Create a file called "server_variables.php" inside the lib directory and paste this in:

```php
<?php

// will force TLS on every page if set to true
define('FORCE_TLS', FALSE);

// database connection credentials
define('MYSQL_HOST', '');
define('MYSQL_USER', '');
define('MYSQL_PASS', '');
define('MYSQL_DBNAME', 'exercise_log');
?>

```

And then create a file called "server-variables.js" inside the js directory and paste this in:

```javascript
// this should point to the directory this code resides on your server
// ex: if this index.html is at "mywebsite.com/myexerciselog/index.html" this should be '/myexerciselog'
var RELATIVE_ROOT_DIR = '';

```

### Contributions
I'll accept contributions so long as they are in the spirit of the program, and they make sense.
