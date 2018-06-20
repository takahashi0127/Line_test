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
            $file = 'keyname1.txt';

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
                $keys = file_get_contents('keyname1.txt', true)."/".file_get_contents('keyname2.txt', true)."/".file_get_contents('keyname3.txt', true);
                $keydata = explode("/", $keys);
                $repkeyMessage = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();


//-------------------------------------key[0]------------------------------------------------------------------------------------------------------------
                $confirmMessage0 = $keydata[0]."の状態を選択してください";
                //はい ボタン
                $yes_post0 = new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("施錠", "1I");
                //いいえボタン
                $no_post0 = new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("解錠", "1O");

                //Confirmテンプレート
                $confirm0 = new LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($confirmMessage0, [$yes_post0, $no_post0]);
                // Confirmメッセージを作る
                $repkey0 = new LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("施錠確認中", $confirm0);
                $repkeyMessage->add($repkey0);
//                $response0 = $bot->replyMessage($event->replyToken, $replyMessage0);


//-------------------------------------key[1]------------------------------------------------------------------------------------------------------------
                $confirmMessage1 = $keydata[1]."の状態を選択してください";
                //はい ボタン
                $yes_post1 = new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("施錠", "2I");
                //いいえボタン
                $no_post1 = new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("解錠", "2O");

                //Confirmテンプレート
                $confirm1 = new LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($confirmMessage1, [$yes_post1, $no_post1]);
                // Confirmメッセージを作る
                $repkey1 = new LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("施錠確認中", $confirm1);
                $repkeyMessage->add($repkey1);
//                $response1 = $bot->replyMessage($event->replyToken, $replyMessage1);


//-------------------------------------key[2]------------------------------------------------------------------------------------------------------------
                $confirmMessage2 = $keydata[2]."の状態を選択してください";
                //はい ボタン
                $yes_post2 = new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("施錠", "3I");
                //いいえボタン
                $no_post2 = new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("解錠", "3O");

                //Confirmテンプレート
                $confirm2 = new LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($confirmMessage2, [$yes_post2, $no_post2]);
                // Confirmメッセージを作る
                $repkey2 = new LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("施錠確認中", $confirm2);
                $repkeyMessage->add($repkey2);
//                $response2 = $bot->replyMessage($event->replyToken, $replyMessage2);

                //複数同時表示
                $response = $bot->replyMessage($event->replyToken, $repkeyMessage);

            break;

            case '施錠状況':
//            $keyMessage = "現在の施錠状況です";
//            $keyMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($keyMessage);
//            $response = $bot->replyMessage($event->replyToken, $keyMessageBuilder);
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            $conflock1 = file_get_contents('lock1.txt', true);
 //           $conflock2 = file_get_contents('lock2.txt', true);
 //           $conflock3 = file_get_contents('lock3.txt', true);


            $checkMessage = "現在の施錠状況です\n「".$data1."」:".$conflock1;
            $checkMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($checkMessage);
            $response = $bot->replyMessage($event->replyToken, $checkMessageBuilder);


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

    else if (!empty($event->postback->data)){//ボタンが押されたとき
        $action = $event->postback->data;

        switch ($action){
        case '1I':
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            file_put_contents('lock1.txt', "Lock");
            $button1 = $data1."→施錠";
            $situMessageBuilder1 = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($button1);
            $response = $bot->replyMessage($event->replyToken, $situMessageBuilder1);
        break;

        case '2I':
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            file_put_contents('lock2.txt', "Lock");
            $button2 = $data2."→施錠";
            $situMessageBuilder2 = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($button2);
            $response = $bot->replyMessage($event->replyToken, $situMessageBuilder2);
        break;

        case '3I':
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            file_put_contents('lock3.txt', "Lock");
            $button3 = $data3."→施錠";
            $situMessageBuilder3 = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($button3);
            $response = $bot->replyMessage($event->replyToken, $situMessageBuilder3);
        break;

        case '1O':
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            file_put_contents('lock1.txt', "Unlock");
            $button1 = $data1."→施錠";
            $situMessageBuilder1 = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($button1);
            $response = $bot->replyMessage($event->replyToken, $situMessageBuilder1);
        break;

        case '2O':
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            file_put_contents('lock2.txt', "Unlock");
            $button2 = $data2."→施錠";
            $situMessageBuilder2 = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($button2);
            $response = $bot->replyMessage($event->replyToken, $situMessageBuilder2);
        break;

        case '3O':
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            file_put_contents('lock3.txt', "Unlock");
            $button3 = $data3."→施錠";
            $situMessageBuilder3 = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($button3);
            $response = $bot->replyMessage($event->replyToken, $situMessageBuilder3);
        break;

        default:
        return;
        }//switch
    }//elseif
/*

        case I:
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            $lockmessage = $data1."：施錠";
            file_put_contents(lock1.txt, "Lock");

            $lockMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($lockMessage);
            $response = $bot->replyMessage($event->replyToken, $lockMessageBuilder);


        case O:
            $data1 = file_get_contents('keyname1.txt', true);
            $data2 = file_get_contents('keyname2.txt', true);
            $data3 = file_get_contents('keyname3.txt', true);
            $lockmessage = $data1."：施錠";
            file_put_contents(lock1.txt, "Unlock");

            $lockMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($lockMessage);
            $response = $bot->replyMessage($event->replyToken, $lockMessageBuilder);
        

        }//switch*/

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


function isPostback($event)

{

    if ($event->type == "postback") {

        return true;

    } else {

        return false;

    }

}

return 0;