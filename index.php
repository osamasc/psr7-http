<?php

use Phly\Http\ServerRequestFactory;

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$request = ServerRequestFactory::fromGlobals()->getRequestTarget();

echo "<pre>";

var_dump($request);