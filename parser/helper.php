<?php
/**
 * helper functional and class
 * ---------------------------------------------------------------------------------------------------------------------
 */
require_once "{$GLOBALS['path_repo_global']}/library/simplehtmldom_1_9_1/simple_html_dom.php";

function dd($var_dump, $die = false)
{
    echo '<pre style="color: #850085">';
    var_dump($var_dump);
    echo '</pre>';
    if ($die) {
        die();
    }
    exit();
}

function dd_($var_dump)
{
    echo '====================================================================' . PHP_EOL;
    var_dump($var_dump);
    echo '====================================================================' . PHP_EOL;
}

class Helper
{
    /**
     * @var float start time of script execution
     */
    private static $start = .0;

    /**
     * Start of execution
     */
    public static function start()
    {
        self::$start = microtime(true);
    }

    /**
     * Difference between current timestamp and self::$ start
     * @return float
     */
    public static function finish()
    {
        return microtime(true) - self::$start;
    }

    public static function bash($command)
    {
        $shell_return = system($command);
        return $shell_return;
    }

    public static function bash_escapeshellarg($command)
    {
        $bash_return = system(escapeshellarg($command));
        return $bash_return;
    }

    public static function clear()
    {
        self::bash('clear;');
    }

    public static function header_print($return = false)
    {
        $main_header = '
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░███████╗██╗██╗░░░░░███╗░░░███╗░██████╗░░██████╗░░█████╗░██████╗░░██████╗███████╗██████╗░░░░░░░
░░░░░░░██╔════╝██║██║░░░░░████╗░████║██╔════╝░░██╔══██╗██╔══██╗██╔══██╗██╔════╝██╔════╝██╔══██╗░░░░░░
░░░░░░░█████╗░░██║██║░░░░░██╔████╔██║╚█████╗░░░██████╔╝███████║██████╔╝╚█████╗░█████╗░░██████╔╝░░░░░░
░░░░░░░██╔══╝░░██║██║░░░░░██║╚██╔╝██║░╚═══██╗░░██╔═══╝░██╔══██║██╔══██╗░╚═══██╗██╔══╝░░██╔══██╗░░░░░░
░░░░░░░██║░░░░░██║███████╗██║░╚═╝░██║██████╔╝░░██║░░░░░██║░░██║██║░░██║██████╔╝███████╗██║░░██║░░░░░░
░░░░░░░╚═╝░░░░░╚═╝╚══════╝╚═╝░░░░░╚═╝╚═════╝░░░╚═╝░░░░░╚═╝░░╚═╝╚═╝░░╚═╝╚═════╝░╚══════╝╚═╝░░╚═╝░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██╗░░██╗██████╗░██████╗░███████╗███████╗██╗░░██╗░█████╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██║░░██║██╔══██╗██╔══██╗██╔════╝╚════██║██║░██╔╝██╔══██╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░███████║██║░░██║██████╔╝█████╗░░░░███╔═╝█████═╝░███████║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██╔══██║██║░░██║██╔══██╗██╔══╝░░██╔══╝░░██╔═██╗░██╔══██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██║░░██║██████╔╝██║░░██║███████╗███████╗██║░╚██╗██║░░██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░╚═╝░░╚═╝╚═════╝░╚═╝░░╚═╝╚══════╝╚══════╝╚═╝░░╚═╝╚═╝░░╚═╝░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░';
        if ($return)
            return $main_header;
        else {
            self::clear();
            echo $main_header . PHP_EOL;
        }
    }

    public static function send_message_from_bot_telegram($text)
    {
        # bot api token
        $telegram_token = '1548972693:AAFTg5V2tKMGKE8gddRcv6ZH1An1Eju_798';

        # internal chat id with bot
        $telegram_chatID = '64850768';

        # request api bot
        $curl = curl_init();
        curl_setopt_array($curl, [CURLOPT_URL => "https://api.telegram.org/bot$telegram_token/sendMessage",
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => array(
                'chat_id' => $telegram_chatID,
                'text' => $text,
            )]);
        curl_exec($curl);
    }

    public static function error_print($error = '', $return = false)
    {
        $header = '
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░██╗░░███████╗██████╗░██████╗░░█████╗░██████╗░░░██╗░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░██║░░██╔════╝██╔══██╗██╔══██╗██╔══██╗██╔══██╗░░██║░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░██║░░█████╗░░██████╔╝██████╔╝██║░░██║██████╔╝░░██║░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░╚═╝░░██╔══╝░░██╔══██╗██╔══██╗██║░░██║██╔══██╗░░╚═╝░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░██╗░░███████╗██║░░██║██║░░██║╚█████╔╝██║░░██║░░██╗░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░╚═╝░░╚══════╝╚═╝░░╚═╝╚═╝░░╚═╝░╚════╝░╚═╝░░╚═╝░░╚═╝░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';
        self::clear();
        if ($return)
            return $header;
        else
            echo $header . $error;
        exit();
    }

    public static function num_word($value, $words, $show = true)
    {
        $num = $value % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        $out = ($show) ? $value . ' ' : '';
        switch ($num) {
            case 1:
                $out .= $words[0];
                break;
            case 2:
            case 3:
            case 4:
                $out .= $words[1];
                break;
            default:
                $out .= $words[2];
                break;
        }

        return $out;
    }

    public static function sec_to_time($secs)
    {
        $res = '';

        $days = floor($secs / 86400);
        $secs = $secs % 86400;
        if ($days)
            $res .= self::num_word($days, array('день', 'дня', 'дней')) . ', ';

        $hours = floor($secs / 3600);
        $secs = $secs % 3600;
        if ($hours)
            $res .= self::num_word($hours, array('час', 'часа', 'часов')) . ', ';

        $minutes = floor($secs / 60);
        $secs = $secs % 60;
        if ($minutes)
            $res .= self::num_word($minutes, array('минута', 'минуты', 'минут')) . ', ';

        if ($secs)
            $res .= self::num_word($secs, array('секунда', 'секунды', 'секунд'));

        return (!empty($res) ? $res : null);
    }

    public static function spinner()
    {
        static $num = 0;
        $spinner = 'spinner error d7r9d2b3m4e6';
        $num++;
        if ($num == 5)
            $num = 1;

        if ($num == 1)
            $spinner = '[/]';
        if ($num == 2)
            $spinner = '[-]';
        if ($num == 3)
            $spinner = '[\\]';
        if ($num == 4)
            $spinner = '[|]';

        return $spinner;
    }

    public static function how_much_time_is_left($total_terations, $completed_terations, $seconds_spent_completed_terations)
    {
        return round(((($total_terations - $completed_terations) / $completed_terations) * $seconds_spent_completed_terations), 0);
    }

    public static function how_much_time_is_left_2($total_terations, $completed_terations)
    {
        static $count = 0;
        static $time_left;
        $static = 5;
        if ($count == $static) {
            $count = 0;
            $seconds_spent_completed_terations = self::finish();
            $time_left = (((($total_terations - $completed_terations) + 1) / ($static)) * $seconds_spent_completed_terations);
            return $time_left;
        }

        if ($count == 0) {
            self::start();
        }

        $count++;
        return $time_left;
    }

    public static function loader($max = 87)
    {
        static $key = 0;
        if ($key >= $max)
            $key = 0;

        $separator = '░';
        $space = '';

        for ($i = 0; $i < $key; $i++) {
            $separator .= '░';
        }
        for ($y = 0; $y < $max - 1 - $key; $y++) {
            $space .= ' ';
        }
        $key++;
        return "[{$separator}{$space}]";
    }

    public static function count_pars($reset = false)
    {
        static $num = 0;
        if ($reset)
            $num = 0;
        $num++;
        return $num;
    }

    public static function check($data, $hash)
    {
        if (!empty($data))
            return trim($data);
        else
            return "ERROR DATA {$hash}";
    }

    public static function convertToUtf8($str)
    {
        return iconv(mb_detect_encoding($str, mb_detect_order(), true), "UTF-8", $str);
    }

    public static function find_last_max_page_pagination_pars_films($string)
    {
        preg_match('/ *(\d{1,999})$/', trim(preg_replace('/&nbsp;|&nbsp;|\.\.\.|  /', '', $string)), $m);
        $res = intval(trim($m[1]));
        if (!empty($res) && $res > 0)
            return $res;
        else
            self::error_print('
    какая то хуйня с пагинацией - парсер не нашел максимальное количество страниц пагинации
            
            ');
    }

    public static function tor_proxy_9150_curl($url)
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
        if ($content == false) {
            $date_time = date("d/m/Y - H:i:s");
            $message = self::error_print('', true) . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Проблема с CURLOPT_URL !!! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ порт 127.0.0.1:9150 не дал возможности использовать прокси для ссоединения ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Выберете ниже: Повторить или Выход! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ что угодно) Повторить ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 0) Выход! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';
            Helper::send_message_from_bot_telegram("
Проблема с CURLOPT_URL !!!
            
Серверная дата и время события ($date_time)

Место нахождения и запуска скрипта ({$_SERVER['PWD']})

порт 127.0.0.1:9150 не дал возможности использовать прокси для ссоединения
");
            echo $message;
            $what_do_we_do = readline("ВВОД: ");
            if ($what_do_we_do == 1) {
                return self::tor_proxy_9150_curl($url);
            } else if ($what_do_we_do == 0) {
                Helper::header_print();
                echo '
                Ты все проебал !!!
                Начинай заново)
                ';
                exit();
            } else
                return self::tor_proxy_9150_curl($url);
        }
        curl_close($curl);
        if (!$content)
            self::error_print('
    какая то хуйня - у вас Tor не работает или типа того - 
    короче прокси и тд не пашет поэтому curl_setopt послал нахуй
    попробуйте проверить прокси на http://127.0.0.1:9150/ 
    он должен работать и выдавать что то типа (This is a SOCKs proxy, not an HTTP proxy)
    ');
        return $content;
    }

    public static function hack_curl_https_content($url, $tor = false)
    {
        if ($tor) {
            return self::tor_proxy_9150_curl($url);
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
        if (!$content)
            self::error_print('
    какая то хуйня - у вас что то с интернетом или типа того - 
    короче curl_setopt послал нахуй ибо или забанили ip с которого запускается скрипт или инет не работает или я хз
    
    ');
        return $content;
    }

    public static function curl_API_films_themoviedb($url_api, $tor = false)
    {
        if ($tor) {
            return self::tor_proxy_9150_curl($url_api);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        return $response;
    }

    public static function google_translate($text, $lang_input, $lang_uotput)
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