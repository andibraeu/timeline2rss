<?php
/**
 * Created by PhpStorm.
 * User: andi
 * Date: 11.09.14
 * Time: 12:19
 */

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\GraphPage;

require 'vendor/autoload.php';

$app_id = "";
$app_secret = "";
$page_id = '';

FacebookSession::setDefaultApplication($app_id, $app_secret);

$session = new FacebookSession($app_id."|".$app_secret);

//$session = FacebookSession::newAppSession();

// Make a new request and execute it.
try {
    $response = (new FacebookRequest($session, 'GET', '/'.$page_id.'/posts'))->execute();
    //echo $response->getRawResponse();
    $object = $response->getGraphObject();
    $tlArray = $object->getProperty('data');
    foreach($tlArray as $dings) {
        print_r($dings);
    }
} catch (FacebookRequestException $ex) {
    echo $ex->getMessage();
} catch (\Exception $ex) {
    echo $ex->getMessage();
}



?>