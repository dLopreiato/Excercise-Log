<?php

/* Content Headers */
// header for api responses
define('API_RESPONSE_CONTENT', 'Content-Type: text/plain; charset=utf-8');


/* Status codes */
// response for good input
define('HTTP_OK', 'HTTP/1.1 200 OK');

// error for bad input
define('HTTP_BAD_REQUEST', 'HTTP/1.1 400 Bad Request');

// error for room in progress
define('HTTP_FORBIDDEN', 'HTTP/1.1 403 Forbidden');

// error for object with this id does not exist
define('HTTP_NOT_FOUND', 'HTTP/1.1 404 Not Found');

// error for someone who is already in a room
define('HTTP_CONFLICT', 'HTTP/1.1 409 Conflict');

// error for a room which did exist but is now gone
define('HTTP_GONE', 'HTTP/1.1 410 Gone');

// error for something went wrong on the inside
define('HTTP_INTERNAL_ERROR', 'HTTP/1.1 500 Internal Server Error');

?>
