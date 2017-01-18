<?php 

/* AJAX check  */
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $data = json_decode($_POST["data"],true);
    $outputdata = array();

    foreach ($data as $val1) {
    	$outputdata += array(
    		$val1["name"]	=> htmlspecialchars($val1["value"])
    	);
    }

    mb_language('ja');
    mb_internal_encoding('UTF-8');

    $mailTo  = "xxxxxxxxxxx@gmail.com";
    $subject = mb_encode_mimeheader("フォームより送信されました");
    $from    = "From:".$outputdata["name"];


//送信メッセージ
$message = <<< EOD
以下の内容がフォームより送信されました。
────────────────────────────────────
[氏名]
{$outputdata["name"]}

[会社]
{$outputdata["company"]}

[電話番号]
{$outputdata["tel"]}

[メールアドレス]
{$outputdata["mail"]}

[メッセージ]
{$outputdata["message"]}
────────────────────────────────────
EOD;

    $message = mb_convert_encoding($message , "ISO-2022-JP", "auto");
    if(mail($mailTo, $subject, $message ,$from)){
        echo true;
    }else{
        echo false;
    }
}

die($content);

?>