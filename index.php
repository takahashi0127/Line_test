﻿<?php
require_once __DIR__ . '/vendor/autoload.php';

error_log("start");

// POSTを受け取る
$postData = file_get_contents('php://input');
error_log($postData);

// jeson化
$json = json_decode($postData);
$event = $json->events[0];
error_log(var_export($event, true));

// ChannelAccessTokenとChannelSecret設定
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('LineMessageAPIChannelAccessToken'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('LineMessageAPIChannelSecret')]);




//追記部分//////////////////////////////////////////////////////////////////////////////////////
$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$events = $bot->parseEventRequest(file_get_contents('php://input'),$signature);

foreach ($event as $event) {
    if (($event instanceof \LINE\LINEBot\Event\BeaconDetectionEvent)) {
        $type = $json_object->{"events"}[0]->{"beacon"}->{"type"};
        if ($type === "enter") {
            $message = "おかえりなさい";
        }elseif (($type === "leave")) {
            $message = "行ってらっしゃい";
        }
        $body = <<<EOD
{$message}!!
EOD;
        replyTextMessage($bot, $event->getReplyToken(), $body);
        exit;
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////



// イベントタイプがmessage以外はスルー
if ($event->type != "message")
    return;

$replyMessage = null;
// メッセージタイプが文字列の場合
if ($event->message->type == "text") {
    if($event->message->text == "ありがとう"){
        $replyMessage = "どういたしまして";
    }
    else if ($event->message->text != "ありがとう"){
    $replyMessage = $event->message->text;
    }
}


//文字列以外は無視
else {
    return;
}

// メッセージ作成
$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($replyMessage);

// メッセージ送信
$response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
var_export($response, true);
error_log(var_export($response,true));
return;
