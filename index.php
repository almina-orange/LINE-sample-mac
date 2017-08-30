<?php
// all read library installed by composer
require_once __DIR__ . '/vendor/autoload.php';

// instancing CurlHTTPClient using access token
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

// instancing LINEBot using CurlHTTPClient and secret
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

// get signature assigned LINE Messaging API to request
$signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

// check correctness of signature. if correct, parse and storage into array
$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

// process rooply each events in array
foreach ($events as $event) {
  // reply message and next event
  replyTextMessage($bot, $event->getReplyToken(), 'TextMessage');
}

// text reply
function replyTextMessage($bot, $replyToken, $text) {
  // reply and get response
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

  // if response is storange
  if (!$response->isSucceeded()) {
    // output error
    error_log('Failed! ' . $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}
?>
