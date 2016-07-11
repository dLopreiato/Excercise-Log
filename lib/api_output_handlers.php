<?php
require_once('http_headers.php');

function apiExceptionHandler(Exception $e)
{
    header(API_RESPONSE_CONTENT);
    header(HTTP_INTERNAL_ERROR);
    echo json_encode(array('code' => $e->getCode(), 'message' => $e->getMessage()));
    die();
}

function apiDataHandler($object)
{
    $outputString = json_encode($object);
    header(API_RESPONSE_CONTENT);
    header(HTTP_OK);
    echo $outputString;
    die();
}
?>