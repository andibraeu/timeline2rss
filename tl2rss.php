<?php
/**
 * Created by PhpStorm.
 * User: andi
 * Date: 12.09.14
 * Time: 13:57
 */

use Facebook\FacebookSession;
use Facebook\GraphObject;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Argentum\FeedBundle\Feed\Feed;


require 'vendor/autoload.php';

$app_id = "";
$app_secret = "";
$page_id = '';

FacebookSession::setDefaultApplication($app_id, $app_secret);

$session = new FacebookSession($app_id."|".$app_secret);

/**
 * @param $message
 * @param $returnItem
 * @param int $titleLength
 * @return mixed
 */
function splitMessage($message, $returnItem, $titleLength=100) {
    $messageArray = explode("\n", $message, 2);
    if (count($messageArray) > 1 ) {
        $title = $messageArray[0];
        $content = $messageArray[1];
    } else {

    }
    return $message;
}

//$session = FacebookSession::newAppSession();

// Make a new request and execute it.
try {
    $posts = (new FacebookRequest($session, 'GET', '/'.$page_id.'/posts'))->execute()->getGraphObject(GraphObject::className())->getPropertyAsArray('data');
    //print_r($posts);
    foreach ($posts as $dings) {
        $type = $dings->getProperty('type');
        $message = $dings->getProperty('message');
        if (empty($message)) {
            continue;
        }
        $title = splitMessage($message, "title");
        $content = splitMessage($message, "content");
        $createdAt = $dings->getProperty('created_time');
        $updatedAt = $dings->getProperty('updated_time');
        $link = $dings->getProperty('link');
        $picture = $dings->getProperty('picture');
        print("$type: " . $message . "<br/>");
    }
} catch (FacebookRequestException $ex) {
    echo $ex->getMessage();
} catch (\Exception $ex) {
    echo $ex->getMessage();
}

?>