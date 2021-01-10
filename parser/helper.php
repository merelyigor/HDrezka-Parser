<?php
system('clear');

ini_set('memory_limit', '8192M');
ini_set('max_execution_time', '9000');
ini_set('default_socket_timeout', '100000');

require_once "../library/simplehtmldom_1_9_1/simple_html_dom.php";

function dd($var_dump, $die = false)
{
    echo '<pre style="color: #850085">';
    var_dump($var_dump);
    echo '</pre>';
    if ($die) {
        wp_die();
    }
    exit();
}

class Help
{
    public function for_row($row, $count)
    {
        $temp = null;
        for ($j = 0; $j < $count; ++$j) {
            $temp[] = $row[$j];
        }
        return $temp;
    }

    public function convertToUtf8($str)
    {
        return iconv(mb_detect_encoding($str, mb_detect_order(), true), "UTF-8", $str);
    }

    public function getTextLink($text_link_obj)
    {
        $temp = str_get_html($text_link_obj);
        return $temp->plaintext;
    }

    public function last_max_page_pagination_hack($string)
    {
        preg_match('/ *(\d{1,999})$/', trim(preg_replace('/&nbsp;|&nbsp;|\.\.\.|  /', '', $string)), $m);
        $res = intval(trim($m[1]));
        if (!empty($res) && $res > 0)
            return $res;
        else
            return 0;
    }

    public function tor_proxy_9150_curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_ENCODING, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:9150');
        curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);


        $header[0] = "text/html,application/xhtml+xml,application/xml;q=0.9,application/json,image/avif,";
        $header[0] .= "image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9";
        $header[] = "Accept-Language: ru,uk;q=0.9,en;q=0.8";

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_REFERER, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, FALSE);

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

    public function hack_https_content($url, $tor = false)
    {
        if ($tor) {
            return $this->tor_proxy_9150_curl($url);
        }

        $header[0] = "text/html,application/xhtml+xml,application/xml;q=0.9,application/json,image/avif,";
        $header[0] .= "image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9";
        $header[] = "Accept-Language: ru,uk;q=0.9,en;q=0.8";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_REFERER, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, FALSE);

        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, TRUE);

        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie.txt');

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }

    public function curl_API($url_api, $tor = false)
    {
        if ($tor) {
            return $this->tor_proxy_9150_curl($url_api);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function google_translate($text, $lang_input, $lang_uotput)
    {
        $query_data = array(
            'client' => 'x',
            'q' => $text,
            'sl' => $lang_input,
            'tl' => $lang_uotput
        );
        $filename = 'http://translate.google.ru/translate_a/t';
        $options = array(
            'http' => array(
                'user_agent' => 'Mozilla/5.0 (Windows NT 6.0; rv:26.0) Gecko/20100101 Firefox/26.0',
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($query_data)
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($filename, false, $context);
        return json_decode($response);
    }
}