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
        elseif (($type == "leave")) {
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
        $aymMessage = substr($text, 0, 5);
 //       $aymMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($aymMessage);
 //       $response = $bot->replyMessage($event->replyToken, $aymMessageBuilder);

       if ($aymMessage == "reg1:"){
            $keyname1 = substr($text, 5);
    //        $regMessage = "$keynameが登録されました"
    //        $regMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($regMessage);
     //       $response = $bot->replyMessage($event->replyToken, $regMessageBuilder);
            $file = 'keyname1.txt';
            //$current = file_get_contents($file);
            //$current .= "$keyname";
            file_put_contents($file, $keyname1);
            $data = file_get_contents('keyname1.txt', true);
            $dataMessage = "'$data'を「鍵1」として登録しました";
            $dataMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($dataMessage);
            $response = $bot->replyMessage($event->replyToken, $dataMessageBuilder);
      //      file_put_contents("keyname.php", $regMessage);
        }

        else if ($aymMessage == "reg2:"){
            $keyname2 = substr($text, 5);
            $file = 'keyname2.txt';
            file_put_contents($file, $keyname2);
            $data = file_get_contents('keyname2.txt', true);
            $dataMessage = "'$data'を「鍵2」として登録しました";
            $dataMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($dataMessage);
            $response = $bot->replyMessage($event->replyToken, $dataMessageBuilder);
        }

        else if ($aymMessage == "reg3:"){
            $keyname3 = substr($text, 5);
            $file = 'keyname3.txt';
            file_put_contents($file, $keyname3);
            $data = file_get_contents('keyname3.txt', true);
            $dataMessage = "'$data'を「鍵3」として登録しました";
            $dataMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($dataMessage);
            $response = $bot->replyMessage($event->replyToken, $dataMessageBuilder);
        }

        //else{
            

//            $regMessage = "no";
//            $regMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($regMessage);
//            $response = $bot->replyMessage($event->replyToken, $regMessageBuilder);
        

///////メッセージタイプが文字列の場合////////////
        switch ($text){

            case 'ヘルプ':
            $replyMessage = "以下のコマンドが使用可能です。\n\n「鍵の登録」:施錠の確認を行いたい鍵を登録します。\n\n「鍵の確認」：登録されている鍵を確認します。\n\n「施錠確認」:登録されている鍵の施錠確認を開始します。\n\n「施錠状況」:登録されている鍵の状態を表示します。";
            break;

            case '鍵の登録':
            $registerMessage = "先頭に「reg(鍵番号):」と付けて鍵の名前を入力してください。\n現在3つまで登録可能です。\n例)→「reg1:玄関」";
            $registerMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($registerMessage);
            $response = $bot->replyMessage($event->replyToken, $registerMessageBuilder);
            break;

            case '鍵の確認':
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            $checkMessage = "現在以下の鍵が登録されています\n鍵1:"."$data1"."\n鍵2:"."$data2"."\n鍵3:"."$data3";
            $checkMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($checkMessage);
            $response = $bot->replyMessage($event->replyToken, $checkMessageBuilder);
            break;

            case '施錠確認':
                $data1 = file_get_contents('keyname1.txt', true);
                $data2 = file_get_contents('keyname2.txt', true);
                $data3 = file_get_contents('keyname3.txt', true);
      //          $alternativeText = "施錠確認中";
      //          $action1 = new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder ("施錠", "lock");
      //          $action2 = new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder ("解錠", "unlock");
      //          $text = "状態を選択してください";
                replyConfirmTemplate($bot,$event->replyToken, "施錠確認","施錠確認中", new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder ("施錠", "lock"), new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder ("解錠", "unlock"));

                
            break;

            case '施錠状況':
            $keyMessage = "現在の施錠状況です";
            $keyMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($keyMessage);
            $response = $bot->replyMessage($event->replyToken, $keyMessageBuilder);


            break;

/*           case 'test':

            break;
*/

            default:
            $etcMessage = "使い方を見るには以下のコマンドを入力してください。\n「ヘルプ」";
            $etcMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($etcMessage);
            $response = $bot->replyMessage($event->replyToken, $etcMessageBuilder);

        }//switch

    //    }//else(text == reg以外)
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

//confirmテンプレート
function replyConfirmTemplate($bot, $replyToken, $alternativeText, $text, $actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray, $value);
  }
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($alternativeText, new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder ($text, $actionArray));
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

return 0;