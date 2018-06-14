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

            case 'ヘルプ':
                $replyMessage = "数字を入力してください\n1：鍵の登録\n2：施錠確認開始\n3：施錠状況確認";

                switch ($event->message->text){
                    case '1':
                    $replyMessage = "鍵の名前を入力してください";
                    break;

                    case '2':
                    $replyMessage = "施錠確認を開始します";
                    break;

                    case '3':
                    $replyMessage = "現在の施錠状況です";
                    break;
                }

            break;

            case 'たかせ':
                $replyMessage = "川上";
                break;

            case 'かいせい':
                $replyMessage = "真野";
                break;

            case 'みなみ':
                $replyMessage = "パン♡";
                break;

            default:
            $replyMessage = "以下のコマンドのみ使用できます。\n「ヘルプ」";

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