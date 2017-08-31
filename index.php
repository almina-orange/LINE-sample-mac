<?php
// all read library installed by composer
require_once __DIR__.'/vendor/autoload.php';

// instancing CurlHTTPClient using access token
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

// instancing LINEBot using CurlHTTPClient and secret
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

// get signature assigned LINE Messaging API to request
$signature = $_SERVER['HTTP_'.\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

// check correctness of signature. if correct, parse and storage into array
$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

// process rooply each events in array
foreach ($events as $event) {
  // collect multi messages
  // $replyText = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('TextMessage');
  // $replyImg = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder('https://'.$_SERVER['HTTP_HOST'].'/img/original.jpg', 'https://'.$_SERVER['HTTP_HOST'].'/img/preview.jpg');
  // $replyLoc = new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder('LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473);
  // $replySticker = new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 1);
  // replyMultiMessage($bot, $event->getReplyToken(), $replyText, $replyImg, $replyLoc, $replySticker);

  // reply message and proceed next event
  // replyTextMessage($bot, $event->getReplyToken(), 'TextMessage');

  // reply image and proceed next event
  // replyImageMessage($bot, $event->getReplyToken(), 'https://'.$_SERVER['HTTP_HOST'].'/img/original.jpg', 'https://'.$_SERVER['HTTP_HOST'].'/img/preview.jpg');

  // reply location and proceed next event
  // replyLocationMessage($bot, $event->getReplyToken(), 'LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473);

  // reply sticker and proceed newx event
  // replyStickerMessage($bot, $event->getReplyToken(), 1, 1);

  // reply video and proceed next event
  // replyVideoMessage($bot, $event->getReplyToken(), 'https://'.$_SERVER['HTTP_HOST'].'/video/sample.mp4', 'https://'.$_SERVER['HTTP_HOST'].'/video/sample_preview.jpg');

  // reply audio and proceed next event
  // replyAudioMessage($bot, $event->getReplyToken(), 'https://'.$_SERVER['HTTP_HOST'].'/audio/sample.m4a', 6000);

  // reply buttons template and proceed next event
  $action1 = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('TOMORROW WEATHER', 'tomorrow');
  $action2 = new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder('WEEKEND WEATHER', 'weekend');
  $action3 = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('PREVIEW WEB', 'http://google.jp');
  replyButtonsTemplate($bot, $event->getReplyToken(), 'Weather News: Sunny', 'https://'.$_SERVER['HTTP_HOST'].'/img/template.jpg', 'WEATHER NEWS', 'SUNNY', $action1, $action2, $action3);
}

/*===== function =====*/
/*=== basic contents ===*/
// text reply
function replyTextMessage($bot, $replyToken, $text) {
  // reply and get response
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

  // if response is storange
  if (!$response->isSucceeded()) {
    // output error
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}

// image reply
function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl) {
  // reply and get response
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));

  // if response is storange
  if (!$response->isSucceeded()) {
    // output error
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}

// location reply
function replyLocationMessage($bot, $replyToken, $title, $address, $lat, $lon) {
  // location, longitude, latitude
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title, $address, $lat, $lon));

  if (!$response->isSucceeded()) {
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}

// sticker reply
function replyStickerMessage($bot, $replyToken, $packageId, $stickerId) {
  // sticker
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId, $stickerId));

  if (!$response->isSucceeded()) {
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}

// video reply
function replyVideoMessage($bot, $replyToken, $originalContentUrl, $previewImageUrl) {
  // video
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\VideoMessageBuilder($originalContentUrl, $previewImageUrl));

  if (!$response->isSucceeded()) {
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}

// audio reply
function replyAudioMessage($bot, $replyToken, $originalContentUrl, $audioLength) {
  // audio
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\AudioMessageBuilder($originalContentUrl, $audioLength));

  if (!$response->isSucceeded()) {
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}

// multi message reply
function replyMultiMessage($bot, $replyToken, ...$msgs) {
  // instancing "MultiMessageBuilder"
  $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();

  // add all messages in builder
  foreach($msgs as $value) {
    $builder->add($value);
  }

  // multi message
  $response = $bot->replyMessage($replyToken, $builder);

  if (!$response->isSucceeded()) {
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}

/*=== rich text contents ===*/
// buttons template reply
function replyButtonsTemplate($bot, $replyToken, $alternativeText, $imageUrl, $title, $text, ...$actions) {
  // action array
  $actionArray = array();

  // add all actions
  foreach ($actions as $value) {
    array_push($actionArray, $value);
  }

  // instancing "ButtonTemplateBuilder" and "TemplateMessageBuilder"
  $buttonBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title, $text, $imageUrl, $actionArray);
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($alternativeText, $buttonBuilder);

  // button template
  $response = $bot->replyMessage($replyToken, $builder);

  if (!$response->isSucceeded()) {
    error_log('Failed! '.$response->getHTTPStatus.' '.$response->getRawBody());
  }
}
?>
