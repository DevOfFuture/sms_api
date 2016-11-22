<?php

// get the API to Work
include("Api.php");

$app = new Api();
$app = $app->getResult($app->default['url'], $app->prepareRequest( $app->default ));
echo $app;
