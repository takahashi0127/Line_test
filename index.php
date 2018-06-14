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

/*foreach ($events as $event) {
    if (($event instanceof \LINE\LINEBot\Event\BeaconDetectionEvent)) {
        $type = $json_object->{"events"}[0]->{"beacon"}->{"type"};
        if ($type === "enter") {
            $message = "おかえりなさい";
        }elseif (($type === "leave")) {
            $message = "行ってらっしゃい";
        }
        $body = <<<EOD {$message}!! EOD;
        replyTextMessage($bot, $event->getReplyToken(), $body);
        exit;
    }
}*/

foreach ($events as $event) {


/////////////////////////ビーコンイベント///////////////////////////////////

    if(!empty($event->beacon)) {
        $type = $event->beacon->type; //enter or leave
        if($type == "enter"){
            $replyMessage = "おかえりなさい\n戸締りの確認をしましょう";
        }
        else if (($type === "leave")) {
            $replyMessage = "行ってらっしゃい\n鍵は閉めましたか？";
        }
    }
////////////////////////////////////////////////////////////////////////////

    else if ($event->message->type == "text"){


    //$replyMessage = null;

///////メッセージタイプが文字列の場合////////////
        switch ($event->message->text){

            case 'たかせ':
                $replyMessage = "みおな";
                break;

            case 'かいせい':
                $replyMessage = "ななせ";
                break;

            case 'あゆむ':
                $replyMessage = "みなみ";
                break;

            case 'みなみ':
                $replyMessage = "パン♡";
                break;

            case 'みおな':
                $replyMessage = "嫌なヤツ嫌なヤツ嫌なヤツ！";
                break;

            case 'ななせ':
                $replyMessage = "告白の2度聞きは、禁止やで♡";
                break;

            case 'ヘルプ':
                $replyMessage = "以下のコマンドが使用可能です。\n「鍵の登録」:施錠の確認を行いたい鍵を登録します。\n「施錠確認」:登録されている鍵の施錠確認を開始します。\n「施錠状況」:登録されている鍵の状態を表示します。";
                break;

            case '':
                $helpMessage = "10秒以内に数字を入力してください\n1：鍵の登録\n2：施錠確認開始\n3：施錠状況確認";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($helpMessage);
                $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);

                //sleep(10);
                require_once __DIR__ . '/vendor/autoload.php';
                $postData = file_get_contents('php://input');
                $json = json_decode($postData);
                $helpevents = $json->helpevents;

                foreach ($helpevents as $event) {
                    switch ($event->message->text){

                        case '1':
                        $registerMessage = "鍵の名前を入力してください";
                        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($registerMessage);
                        $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
                        break;

                        case '2':
                        $checkMessage = "施錠確認を開始します";
                        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($checkMessage);
                        $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
                        break;

                        case '3':
                        $keyMessage = "現在の施錠状況です";
                        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($keyMessage);
                        $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
                        break;

                        default:
                        $etcMessage = "数字を入力してください";
                        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($etcMessage);
                        $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
                    }//switch(text)
                }//foreach

            break;

            default:
            $replyMessage = "使い方を見るには以下のコマンドを入力してください。\n「ヘルプ」";

        }//switch
    }//elseif(text)

/*    else if ($event->message->text != "ヘルプ"){
        $replyMessage = $event->message->text;
        //return;
    }
*/

//イベントタイプがmessage以外はスルー
    else {
        return;
    }
/////////////////////////////////////////////////



}//foreach


// メッセージ作成
$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($replyMessage);

// メッセージ送信
$response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
//var_export($response, true);
error_log(var_export($response,true));
return 0;