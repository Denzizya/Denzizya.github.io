<?php
	$access_token = '801924054:AAH08aS1a5wnpSfKJw2syGIN_UBUfC-8Kmc';
	$url_telegram = 'https://api.telegram.org/bot' . $access_token;
    
    /*Сообщение*/
    $param = file_get_contents('php://input');
	$output = json_decode($param, TRUE);
	$chat_id = $output['message']['chat']['id'];
	$message = $output['message']['text'];

	/*Ответ с кнопок*/
	$callback_query = $output['callback_query'];
	$data = $callback_query['data'];
	$chat_id_in = $callback_query['message']['chat']['id'];
	$message_id = $callback_query['message']['message_id'];
	
	$group_chat_id = $output['channel_post']['chat']['id'];
	$group_message = $output['channel_post']['text'];

	$mes_welcom = "Добро пожаловать в управление бойлером.";
	
	function sendMessage($url, $id, $text, $markup=null){
	        $group_message_id = file_get_contents($url . '/sendMessage?chat_id=' . $id . '&text=' . $text . '&reply_markup=' . $markup, TRUE);
            return json_decode($group_message_id, TRUE)["result"]["message_id"];
	}
	
	function editMessage($url, $id, $id_mes, $text, $markup=null){
	    file_get_contents($url . '/editMessageText?chat_id=' . $id . '&message_id=' . $id_mes . '&text=' . $text . '&reply_markup=' . $markup);
	}
	
	function createButtonInline($do){
	    /*Кнопки под сообщением*/
	    $inline_button1 = array("text"=>$do, "callback_data"=>"Вкл/Выкл");
		$inline_button2 = array("text"=>"Отмена","callback_data"=>"Cancel");
		$inline_keyboard = [[$inline_button1,$inline_button2]];
		$keyboard=array("inline_keyboard"=>$inline_keyboard);
		return json_encode($keyboard);
	}

	function createButton($text){
		/*Кнопки под чатом.*/
		$keyboard = array(
			'keyboard' => array(
				array($text)
			),
			'resize_keyboard'=>True
		);
		return json_encode($keyboard); 
	}
	
	function getBoler($url, $id, $group_message_id=false, $metod=true){
	    
	    $url_server = 'http://fedan.ddns.ukrtel.net/home/tegramBot.php';
	    $pas_server = 30082007;
	    $mess_error = 'Сервис не доступен!';
	    
	    if($metod){
    	    $response = file_get_contents($url_server . '?PASS=' . $pas_server);
    		if($response == ''){
    		    if($group_message_id){
    		        editMessage($url, $id, $group_message_id, $mess_error);
    		    }else{
    		        sendMessage($url, $id, $mess_error);
    		    }
    			return false;
    		}
    		return $response;
	    }
	    
	    $response = file_get_contents($url_server . '?PASS=' . $pas_server . '&REPLACE');
		if($response == ''){
    		    if($group_message_id){
    		        editMessage($url, $id, $group_message_id, $mess_error);
    		    }else{
    		        sendMessage($url, $id, $mess_error);
    		    }
    		    return false;
		}
		return $response;
	}
	
	if($group_message == "Бойлер"){
	    $group_message_id = sendMessage($url_telegram, $group_chat_id, $mes_welcom);
	    $status = getBoler($url_telegram, $group_chat_id, $group_message_id);
	    if(!$status){exit();}
	    if(strrpos($status, 'Включен')){$do = "Выключить?";}else{$do = "Включить?";}
		$replyMarkup = createButtonInline($do);
        editMessage($url_telegram, $group_chat_id, $group_message_id, 'Бойлер: ' . $status, $replyMarkup);
		exit();
	}
	
	if($message == "/start"){
        $replyMarkup = createButton("Статус");
	    sendMessage($url_telegram, $chat_id, $mes_welcom, $replyMarkup);
		exit();
	}
	
	if($message == "Статус"){
		$status = getBoler($url_telegram, $chat_id);
	    if(!$status){exit();}
	    if(strrpos($status, 'Включен')){$do = "Выключить?";}else{$do = "Включить?";}
		$replyMarkup = createButtonInline($do);
		sendMessage($url_telegram, $chat_id, 'Бойлер: ' . $status, $replyMarkup);
		exit();
	}
	
	if($data == "Вкл/Выкл"){
	    $status = getBoler($url_telegram, $chat_id_in, false, false);
		if(!$status){exit();}
		editMessage($url_telegram, $chat_id_in, $message_id, 'Бойлер: ' . $status);
		exit();
	}

	if($data == "Cancel"){
	    file_get_contents($url_telegram . '/editMessageText?chat_id=' . $chat_id_in . '&message_id=' . $message_id . '&text=OK');
		exit();
	}
	
	file_get_contents($url_telegram . '/sendMessage?chat_id=' . $chat_id . '&text=Не знаю что сказать.');
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Приветствую</title>
</head>
<body>
	<div>Приветствую на github</div>
</body>
</html>