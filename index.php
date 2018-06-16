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
$filename = "keyname.txt";
$sample = "aaa";


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

//var_dump( file_put_contents($filename, $sample) );
/////////////////////////ビーコンイベント///////////////////////////////////
  //  $beaconevent = $event->beacon->type; //enter or leave

    if (!empty($event->beacon)) {
        $type = $event->beacon->type; //enter or leave


        if ($type == "enter"){
            $replyMessage = "おかえりなさい\n戸締りの確認をしましょう";
    //        $replyMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($replyMessage);
    //        $response = $bot->replyMessage($event->replyToken, $replyMessageBuilder);
        }
        elseif (($type === "leave")) {
            $replyMessage = "行ってらっしゃい\n鍵は閉めましたか？";
    //        $replyMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($replyMessage);
    //        $response = $bot->replyMessage($event->replyToken, $replyMessageBuilder);
        }

    }
////////////////////////////////////////////////////////////////////////////







/////////////////////////テキストイベント////////////////////////////////////////////////////////////////////////////////
    else if ($event->message->type == "text"){

    //$replyMessage = null;
        $text = $event->message->text;
     //   $replyMessage = $event->message->text;
        $aymMessage = substr($text, 0, 4);
 //       $aymMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($aymMessage);
 //       $response = $bot->replyMessage($event->replyToken, $aymMessageBuilder);

       if ($aymMessage == "reg:"){
            $regMessage = substr($text, 4);
            $regMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($regMessage);
            $response = $bot->replyMessage($event->replyToken, $regMessageBuilder);
      //      file_put_contents("keyname.php", $regMessage);
        }

        else{
            

//            $regMessage = "no";
//            $regMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($regMessage);
//            $response = $bot->replyMessage($event->replyToken, $regMessageBuilder);
        

///////メッセージタイプが文字列の場合////////////
        switch ($text){

            case 'ヘルプ':
            $replyMessage = "以下のコマンドが使用可能です。\n\n「鍵の登録」:施錠の確認を行いたい鍵を登録します。\n\n「施錠確認」:登録されている鍵の施錠確認を開始します。\n\n「施錠状況」:登録されている鍵の状態を表示します。";
            break;

            case '鍵の登録':
            $registerMessage = "先頭に「reg:」と付けて鍵の名前を入力してください";
            $registerMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($registerMessage);
            $response = $bot->replyMessage($event->replyToken, $registerMessageBuilder);
            break;

            case '施錠確認':
            $checkMessage = "施錠確認を開始します";
            $checkMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($checkMessage);
            $response = $bot->replyMessage($event->replyToken, $checkMessageBuilder);
            break;

            case '施錠状況':
            $keyMessage = "現在の施錠状況です";
            $keyMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($keyMessage);
            $response = $bot->replyMessage($event->replyToken, $keyMessageBuilder);
            break;

            case 'test':
            if (is_writable($filename)) {
                
                $dataMessage = require_once __DIR__ . '/keyname.php';
                $dataMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($dataMessage);
                $response = $bot->replyMessage($event->replyToken, $dataMessageBuilder);
//                include 'keyname.php';
//                file_put_contents($filename, "aaa");
 //               if (!empty($filename)){

//                }else{
//                $else = "からっぺ";
 //               $elseMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($else);
//                $response = $bot->replyMessage($event->replyToken, $elseMessageBuilder);

            }
            else{
                $testMessage = "書き込み不能";
                $testMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($testMessage);
                $response = $bot->replyMessage($event->replyToken, $testMessageBuilder);
            }
            break;

            default:
            $etcMessage = "使い方を見るには以下のコマンドを入力してください。\n「ヘルプ」";
            $etcMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($etcMessage);
            $response = $bot->replyMessage($event->replyToken, $etcMessageBuilder);

        }//switch

        }//else(text == reg以外)
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



}//foreach




// メッセージ作成
$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($replyMessage);

// メッセージ送信
$response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
//var_export($response, true);
error_log(var_export($response,true));


return 0;