<?php
system('clear');




function tor_proxy_9150_curl($url, $arr_)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_ENCODING, TRUE);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:9150');
    curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);


    $header[] = "application/json, text/javascript, */*; q=0.01";

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_REFERER, $url);

    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,
        http_build_query($arr_));

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);

    curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie.txt');

    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

$url = 'https://hdrezka.website/ajax/get_cdn_series/';

$ozvuchka_vasa = 56;

//for ($i = 0; $i <= 5000; $i++) {
//    $arr_ = [
//        'action' => 'get_episodes',
//        'episode' => 1,
//        'season' => 1,
//        'translator_id' => $i,
//        'id' => 23376,
//    ];
//    $res = tor_proxy_9150_curl($url, $arr_);
//    if (preg_match('/(\{\".*)/', $res, $m)) {
//        $arr = json_decode($m[0], true);
//        if ($arr['success'] == true) {
//            var_dump($i);
//            var_dump($arr);
//        }
//    }
//}




