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
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Item;

require 'vendor/autoload.php';

$app_id = "";
$app_secret = "";
$page_id = '';

$channelTitle = "Weimarnetz Facebook News";
$channelDesc = "Wir veröffentlichen hier unsere Neuigkeiten aus unserer Facebook-Timeline";
$channelUrl = "http://www.weimarnetz.de";
$channelLanguage = "de-DE";
$channelCopyRight = "2014, Weimarnetz";

FacebookSession::setDefaultApplication($app_id, $app_secret);

$session = new FacebookSession($app_id."|".$app_secret);

/**
 * @param $message
 * @param $returnItem
 * @param int $titleLength
 * @return mixed
 */
function splitMessage($message, $returnItem, $titleLength=80) {
    $messageArray = explode("\n", $message, 2);
    if (count($messageArray) > 1 ) {
        $title = $messageArray[0];
        $content = $messageArray[1];
    } else {
        $title = splitMessage(wordwrap($message, $titleLength), "title");
        $content = str_replace($title, "...", $message);
        $title .= "...";
    }
    if ($returnItem == "title") {
        return $title;
    } else {
        return $content;
    }
}

$feed = new Feed();

$channel = new Channel();
$channel
    ->title($channelTitle)
    ->description($channelDesc)
    ->url($channelUrl)
    ->language($channelLanguage)
    ->copyright($channelCopyRight)
    ->pubDate(date("U"))
    ->lastBuildDate(date("U"))
    ->appendTo($feed);

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
        $id = $dings->getProperty('id');
        $title = splitMessage($message, "title");
        $content = splitMessage($message, "content");
        $createdAt = date("U",strtotime($dings->getProperty('created_time')));
        $updatedAt = strtotime($dings->getProperty('updated_time'));
        $link = $dings->getProperty('link');
        $picture = $dings->getProperty('picture');
        //print("$title: <br/>" . $content . "<br/><br/>");
        $item = new Item();
        $item
            ->title($title)
            ->url("https://facebook.com/$id")
            ->pubDate($updatedAt)
            ->guid("https://facebook.com/$id", true)
            ->description($content)
            ->appendTo($channel);
    }
} catch (FacebookRequestException $ex) {
    echo $ex->getMessage();
} catch (\Exception $ex) {
    echo $ex->getMessage();
}

// set the header type
header("Content-type: text/xml");
echo $feed->render();

?>