<?php
$token = '5225863895:AAEiw_GbpGBf_35W5j1pd9o6DGLLKWWVLv0'; // Token bot Telegram
$website = 'https://api.telegram.org/bot'.$token; //Conectividad con la API de Telegram

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];
$reply = $update['message']['reply_to_message']['text'];
$replay=explode(" ", $reply);

//Variables emoticonos
$emoteperiodico=" 📰 ";
$hola='🙋‍♂️';
$info='ℹ️';
$wtf='😓';

if(empty($reply)){
    switch($message){
        case '/start':
            $keyboard = array('keyboard' => 
            array(array(
                array('text' => '/noticias', 'callback_data' => "1"),
            ),
            array(
                array('text' => '/descripcion', 'callback_data' => "4")
            )), 'one_time_keyboard' => false, 'resize_keyboard' => true
        );
        file_get_contents('https://api.telegram.org/bot5225863895:AAEiw_GbpGBf_35W5j1pd9o6DGLLKWWVLv0/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&reply_markup='.json_encode($keyboard).'&text=Escoge que quieres hacer');
        break;
        case '/noticias':
            $response = $emoteperiodico.'Escoge la categoría de la noticia';
            sendMessage($chatId, $response, TRUE);
            break;
        case '/descripcion':
            $response = $hola.'Me llamo @NoticiasBot, mi objetivo es enseñarte las noticias de última hora de diferentes periódicos o fuentes disponibles.'.$info;
            sendMessage($chatId, $response, FALSE);
            break;
            default:
            $response = $wtf.'No entiendo que quieres decir';
            sendMessage($chatId, $response);
            break;
    }

}else{
    switch($message){
        case 'Nacional':
            getNoticias($chatId,1);
            break;
        case 'Internacional':
            getNoticias($chatId,2);
            break;
        case 'Deportes':
            getNoticias($chatId,3);
            break;
        case 'Cultura':
            getNoticias($chatId,4);
            break;
        case 'Sociedad':
            getNoticias($chatId,5);
            break;
        default:
        $response = 'No entiendo que quieres decir';
        sendMessage($chatId, $response, true);
        break;
    }
}

function sendMessage($chatId, $response, $reply){
    if($reply==TRUE){
        $reply_mark=array('force_reply' => TRUE);
        $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&reply_markup='.json_encode($reply_mark).'&text='.urlencode($response);
    }else{
        $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
        file_get_contents($url);
    }
}

function getNoticias($chatId, $categoria){
    $context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
    switch($categoria){
        case 1:
            $url = "https://www.europapress.es/rss/rss.aspx?ch=00066";
            break;
        case 2:
            $url = "https://www.europapress.es/rss/rss.aspx?ch=00069";
            break;
        case 3: 
            $url = "https://www.europapress.es/rss/rss.aspx?ch=00067";
        case 4:
            $url = "https://www.europapress.es/rss/rss.aspx?ch=00126";
            break;
        case 5:
            $url = "https://www.europapress.es/rss/rss.aspx?ch=00073";
    }

    $xmlstring = file_get_contents($url, false, $context);
    $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);

    for($i=0;$i<10;$i++){
        $titulos = $titulos."\n\n".$array['channel']['item'][0]['tittle'].$array['channel']['item'][$i]['link'];
    }
    sendMessage($chatId, $titulos, false);
}


//Intento de bot fallido, otra vez.
// if(empty($reply)){
//     switch($message) {
//         case '/start':
//             $response = 'Bienvenido. Mis servicios acaban de ser iniciados.';
//             sendMessage($chatId, $response);
//             break;
//         case '/descripcion':
//             $response = 'Me llamo @NoticiasBot, mi objetivo es enseñarte las noticias de última hora de diferentes periódicos o fuentes disponibles.';
//             sendMessage($chatId, $response);
//             break;
//         case '/hola':
//             $response = 'Hola! Estos días visualiza una gran semana, la mente crea lo que la mente cree!';
//             sendMessage($chatId, $response);
//             break;
//         case '/estado':
//             $response = 'Sigo operativo';
//             sendMessage($chatId, $response);
//             break;
//         case '/noticias':
//             $response = '¿Sobre qué categoría quieres ver la noticia?';
//             sendMessage($chatId, $response);
//         default:
//             $response = 'No entiendo que me quieres decir';
//             sendMessage($chatId, $response);
//             break;
//     }
// }else{
//     if($reply == '¿Sobre qué categoría quieres ver la noticia? | Nacional, Internacional, Deportes y/o Cultura.'){
//         $categoria = $message;
//         consultar_categoria($chatId, $categoria);
//     }
// }

// //Envía mensaje bot
// function sendMessage($chatId,$response,$reply_a){
//     if($reply_a == TRUE){
//         $reply_mark = array('force_reply' => TRUE);
//         $url = $GLOBALS['apitlg'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&reply_markup='.json_encode($reply_mark).'&text='.urlencode($response);
//     }else{
//         $url = $GLOBALS['apitlg'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
//     }
//     file_get_contents($url);
// }

//     $xmlstring = file_get_contents($url, false, $context);
//     $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
//     $json = json_encode($xml);
//     $array = json_decode($json, TRUE);

//     for($i=0; $i < 9; $i++){
//         $titulos = $titulos."\n\n".$array['channel']['item'][$i]['tittle']."<a href='".$array['channel']['item'][$i]['link']."'> +info</a>";
//     }
//     sendMessage($chatId, $titulos);
// }

// //Funcion consultar noticias
// function consultar_categoria($chatId, $categoria){
//     if($categoria == "Nacional"){
//         $urlcategoria = "https://www.europapress.es/rss/rss.aspx?ch=00066";
//     }elseif($categoria == "Internacional"){
//         $urlcategoria = "https://www.europapress.es/rss/rss.aspx?ch=00069";
//     }elseif($categoria == "Deportes"){
//         $urlcategoria = "https://www.europapress.es/rss/rss.aspx?ch=00067";
//     }elseif($categoria == "Cultura"){
//         $urlcategoria == "https://www.europapress.es/rss/rss.aspx?ch=00126";
//     }else{
//         echo "Error. Asegúrate de escribir la categoría con la inicial en mayúscula o la categoría de noticias que has introducido NO está disponible en este momento. Por favor, consulte otra categoría.";
//     }

//     $context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
//     $xmlstring = file_get_contents($urlcategoria, false, $context);
//     $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
//     $json = json_encode($xml);
//     $array = json_decode($json, TRUE);

//     for($i=0; $i < 9; $i++){
//         $titulos = $titulos."\n\n".$array['channel']['item'][$i]['tittle']."<a href='".$array['channel']['item'][$i]['link']."'> +info</a>";
//     }
//     sendMessage($chatId, $titulos);
// }

?>