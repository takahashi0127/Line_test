<?php
require_once __DIR__ . '/vendor/autoload.php';

error_log("start");

// POSTを受け取る
$postData = file_get_contents('php://input');
error_log($postData);

// jeson化
$json = json_decode($postData);
$events = $json->events;
error_log(var_export($events[0], true));

// ChannelAccessTokenとChannelSecret設定
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('LineMessageAPIChannelAccessToken'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('LineMessageAPIChannelSecret')]);




//追記部分
$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];



//$events = $bot->parseEventRequest(file_get_contents('php://input'),$signature);

//foreach ($events as $event) {
//    if (($event instanceof \LINE\LINEBot\Event\BeaconDetectionEvent)) {
//        $type = $json_object->{"events"}[0]->{"beacon"}->{"type"};
//        if ($type === "enter") {
//            $message = "おかえりなさい";
//        }elseif (($type === "leave")) {
//            $message = "行ってらっしゃい";
//        }
//        $body = <<<EOD {$message}!! EOD;
//        replyTextMessage($bot, $event->getReplyToken(), $body);
//        exit;
//    }
//}

foreach ($events as $event) {
    if(!empty($event->beacon)) {
        $type = $event->beacon->type; //enter or leave
        if($type == "enter"){
            $replyMessage = "おかえりなさい";
        }
        else if (($type === "leave")) {
            $replyMessage = "行ってらっしゃい";
        }
    }
    // イベントタイプがmessage以外はスルー
    elseif ($event->type != "message"){
        return;
    }
    $replyMessage = null;
    // メッセージタイプが文字列の場合
    if ($event->message->type == "text") { //テキストメッセージの場合
        if ($event->message->text == "ヘルプ"){
            $replyMessage = "1：鍵の登録"."\n"."2：施錠確認開始"."\n"."3：施錠状況確認";

            if ($event->message->text == "1"){
                $replyMessage = "鍵の名前を入力してください";
                /*if ($event->message->type == "text"){
	    	  $keyname = $event->message->text;
                  $replyMessage = "OK";
                }
             }
            else{
		return;
	    }
        }*/

    }

        else if ($event->message->text != "ありがとう"){
            $replyMessage = $event->message->text;
        }
    }
    //文字列以外は無視
    else {
        return;
    }
}


// メッセージ作成
$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($replyMessage);

// メッセージ送信
$response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
var_export($response, true);
error_log(var_export($response,true));
return;