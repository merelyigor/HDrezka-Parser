<?php
system('clear');


function super_duper_curl($url, $request_parameters, $method_post_enable = false, $tor_proxy_enable = false)
{
//    if (empty($url))
//        Helper::error_print('
//    для super_duper_curl не передано url !!!
//');
    # header info
    $header[0] = "text/html,application/xhtml+xml,application/xml;q=0.9,application/json,image/avif,";
    $header[0] .= "image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9";
    $header[] = "Accept-Language: ru,uk;q=0.9,en;q=0.8";

    # user agent info
    $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36';

    # curl init
    $curl = curl_init();
    $curl_opt_arr[CURLOPT_URL] = $url;
    $curl_opt_arr[CURLOPT_REFERER] = $url;
    $curl_opt_arr[CURLOPT_TIMEOUT] = 100;
    $curl_opt_arr[CURLOPT_COOKIEJAR] = 'cookie.txt';
    $curl_opt_arr[CURLOPT_COOKIEFILE] = 'cookie.txt';
    $curl_opt_arr[CURLOPT_HTTPHEADER] = $header;
    $curl_opt_arr[CURLOPT_USERAGENT] = $user_agent;


    $curl_opt_arr[CURLOPT_ENCODING] = true;
    $curl_opt_arr[CURLOPT_HTTPGET] = true;
    $curl_opt_arr[CURLOPT_FOLLOWLOCATION] = true;
    $curl_opt_arr[CURLOPT_RETURNTRANSFER] = true;
    $curl_opt_arr[CURLOPT_AUTOREFERER] = true;
    $curl_opt_arr[CURLOPT_FAILONERROR] = true;

    $curl_opt_arr[CURLOPT_HEADER] = false;
    $curl_opt_arr[CURLOPT_SSL_VERIFYPEER] = false;
    $curl_opt_arr[CURLOPT_SSL_VERIFYHOST] = false;

    if ($tor_proxy_enable) {
        $curl_opt_arr[CURLOPT_PROXY] = '127.0.0.1:905q0';
        $curl_opt_arr[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
    }

    if ($method_post_enable) {
//        if (empty($request_parameters))
//            Helper::error_print('
//    параметры запроса POST пустые !!!
//');
        $curl_opt_arr[CURLOPT_POST] = true;
        $curl_opt_arr[CURLOPT_POSTFIELDS] = http_build_query($request_parameters);
        $curl_opt_arr[CURLOPT_HTTPHEADER] = ['Accept: application/json'];
    }

    curl_setopt_array($curl, $curl_opt_arr);
    $response = curl_exec($curl);
//    if ($content == false) {
//        $date_time = date("d/m/Y - H:i:s");
//        $message = self::error_print('', true) . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//░░░ Проблема с CURLOPT_URL !!! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//░░░ порт 127.0.0.1:9050 не дал возможности использовать прокси для ссоединения ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//░░░ Выберете ниже: Повторить или Выход! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//░░░░░░░ что угодно) Повторить ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//░░░░░░░ 0) Выход! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
//';
//        Helper::send_message_from_bot_telegram("
//Проблема с CURLOPT_URL !!!
//
//Серверная дата и время события ($date_time)
//
//Место нахождения и запуска скрипта ({$_SERVER['PWD']})
//
//порт 127.0.0.1:9050 не дал возможности использовать прокси для ссоединения
//");
//        echo $message;
//        $what_do_we_do = readline("ВВОД: ");
//        if ($what_do_we_do == 1) {
//            return self::tor_proxy_9150_curl($url);
//        } else if ($what_do_we_do == 0) {
//            Helper::header_print();
//            echo '
//                Ты все проебал !!!
//                Начинай заново)
//                ';
//            exit;
//        } else
//            return self::tor_proxy_9150_curl($url);
//    }
    curl_close($curl);
    $response = json_decode($response, true);
    return $response;
}

$url = 'https://hdrezka.website/ajax/get_cdn_series/';
$request_parameters = [
    'action' => 'get_movie',
    'id' => 968,
    'translator_id' => 80,
    'is_camrip' => 0,
    'is_ads' => 0,
    'is_director' => 1,
];

$res = super_duper_curl($url, $request_parameters, true, true);



