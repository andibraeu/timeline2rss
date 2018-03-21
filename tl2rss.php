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

$fb = new Facebook\Facebook([
	  'app_id'     => $app_id,
	  'app_secret' => $app_secret,
	  'default_graph_version' => 'v2.12',
	        ]);

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
		} elseif (strlen($message) <= $titleLength) {
				$title = $message . "...";
				$content = $message;
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
    $response = $fb->get('/'.$page_id.'/posts',$app_id."|".$app_secret);
    $posts = $response->getGraphEdge();//->getPropertyAsArray('data');
    foreach ($posts as $dings) {
        $type = $dings->getProperty('type');
				$message = $dings->getProperty('message');
        if (empty($message)) {
            continue;
        }
        $id = $dings->getProperty('id');
        $title = splitMessage($message, "title");
	$content = splitMessage($message, "content");
	$content=$dings->getProperty('message');
        $createdAt = $dings->getProperty('created_time')->format('U');
//        $link = $dings->getProperty('link');
//        $picture = $dings->getProperty('picture');
        $item = new Item();
        $item
            ->title($title)
            ->url("https://facebook.com/$id")
            ->pubDate($createdAt)
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
