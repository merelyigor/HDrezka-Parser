<?php
/**
 * helper functional and class
 * ---------------------------------------------------------------------------------------------------------------------
 */
require_once "library/simple_html_dom/simple_html_dom.php";

function dd($var_dump)
{
    echo '===============================================================================================================================================' . PHP_EOL;
    var_dump($var_dump);
    echo '===============================================================================================================================================' . PHP_EOL;
    exit;
}

function dd_($var_dump)
{
    echo '===============================================================================================================================================' . PHP_EOL;
    var_dump($var_dump);
    echo '===============================================================================================================================================' . PHP_EOL;
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
    public static function start_micro_time()
    {
        self::$start = microtime(true);
    }

    /**
     * Difference between current timestamp and self::$ start
     * @return float
     */
    public static function finish_micro_time()
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
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░███████╗██╗██╗░░░░░███╗░░░███╗░██████╗░░██████╗░░█████╗░██████╗░░██████╗███████╗██████╗░░░░░░░░░░░░░░░░░░
░░░░░░░██╔════╝██║██║░░░░░████╗░████║██╔════╝░░██╔══██╗██╔══██╗██╔══██╗██╔════╝██╔════╝██╔══██╗░░░░░░░░░░░░░░░░░
░░░░░░░█████╗░░██║██║░░░░░██╔████╔██║╚█████╗░░░██████╔╝███████║██████╔╝╚█████╗░█████╗░░██████╔╝░░░░░░░░░░░░░░░░░
░░░░░░░██╔══╝░░██║██║░░░░░██║╚██╔╝██║░╚═══██╗░░██╔═══╝░██╔══██║██╔══██╗░╚═══██╗██╔══╝░░██╔══██╗░░░░░░░░░░░░░░░░░
░░░░░░░██║░░░░░██║███████╗██║░╚═╝░██║██████╔╝░░██║░░░░░██║░░██║██║░░██║██████╔╝███████╗██║░░██║░░░░░░░░░░░░░░░░░
░░░░░░░╚═╝░░░░░╚═╝╚══════╝╚═╝░░░░░╚═╝╚═════╝░░░╚═╝░░░░░╚═╝░░╚═╝╚═╝░░╚═╝╚═════╝░╚══════╝╚═╝░░╚═╝░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██╗░░██╗██████╗░██████╗░███████╗███████╗██╗░░██╗░█████╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██║░░██║██╔══██╗██╔══██╗██╔════╝╚════██║██║░██╔╝██╔══██╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░███████║██║░░██║██████╔╝█████╗░░░░███╔═╝█████═╝░███████║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██╔══██║██║░░██║██╔══██╗██╔══╝░░██╔══╝░░██╔═██╗░██╔══██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░██║░░██║██████╔╝██║░░██║███████╗███████╗██║░╚██╗██║░░██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░╚═╝░░╚═╝╚═════╝░╚═╝░░╚═╝╚══════╝╚══════╝╚═╝░░╚═╝╚═╝░░╚═╝░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░';
        if ($return)
            return $main_header;
        else {
            self::clear();
            echo $main_header . PHP_EOL;
        }
    }

    public static function successfully_header_print($text_after_header = '')
    {
        $successfully_header = '
✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅
░░░█████╗░░█████╗░███╗░░░███╗██████╗░██╗░░░░░███████╗████████╗███████╗██████╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░██╔══██╗██╔══██╗████╗░████║██╔══██╗██║░░░░░██╔════╝╚══██╔══╝██╔════╝██╔══██╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░██║░░╚═╝██║░░██║██╔████╔██║██████╔╝██║░░░░░█████╗░░░░░██║░░░█████╗░░██║░░██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░██║░░██╗██║░░██║██║╚██╔╝██║██╔═══╝░██║░░░░░██╔══╝░░░░░██║░░░██╔══╝░░██║░░██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░╚█████╔╝╚█████╔╝██║░╚═╝░██║██║░░░░░███████╗███████╗░░░██║░░░███████╗██████╔╝░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░╚════╝░░╚════╝░╚═╝░░░░░╚═╝╚═╝░░░░░╚══════╝╚══════╝░░░╚═╝░░░╚══════╝╚═════╝░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░██████╗██╗░░░██╗░█████╗░░█████╗░███████╗░██████╗░██████╗███████╗██╗░░░██╗██╗░░░░░██╗░░░░░██╗░░░██╗░░░░░░░░░░░
░░██╔════╝██║░░░██║██╔══██╗██╔══██╗██╔════╝██╔════╝██╔════╝██╔════╝██║░░░██║██║░░░░░██║░░░░░╚██╗░██╔╝░░░░░░░░░░░
░░╚█████╗░██║░░░██║██║░░╚═╝██║░░╚═╝█████╗░░╚█████╗░╚█████╗░█████╗░░██║░░░██║██║░░░░░██║░░░░░░╚████╔╝░░░░░░░░░░░░
░░░╚═══██╗██║░░░██║██║░░██╗██║░░██╗██╔══╝░░░╚═══██╗░╚═══██╗██╔══╝░░██║░░░██║██║░░░░░██║░░░░░░░╚██╔╝░░░░░░░░░░░░░
░░██████╔╝╚██████╔╝╚█████╔╝╚█████╔╝███████╗██████╔╝██████╔╝██║░░░░░╚██████╔╝███████╗███████╗░░░██║░░░░░░░░░░░░░░
░░╚═════╝░░╚═════╝░░╚════╝░░╚════╝░╚══════╝╚═════╝░╚═════╝░╚═╝░░░░░░╚═════╝░╚══════╝╚══════╝░░░╚═╝░░░░░░░░░░░░░░
🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥';
        self::clear();
        echo $successfully_header . $text_after_header;
    }

    public static function error_print($error = '', $return = false)
    {
        $header = '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌
░░░░░░░░░░░░░░░░░░░░░░░░░░██╗░░███████╗██████╗░██████╗░░█████╗░██████╗░░░██╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░██║░░██╔════╝██╔══██╗██╔══██╗██╔══██╗██╔══██╗░░██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░██║░░█████╗░░██████╔╝██████╔╝██║░░██║██████╔╝░░██║░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░╚═╝░░██╔══╝░░██╔══██╗██╔══██╗██║░░██║██╔══██╗░░╚═╝░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░██╗░░███████╗██║░░██║██║░░██║╚█████╔╝██║░░██║░░██╗░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░╚═╝░░╚══════╝╚═╝░░╚═╝╚═╝░░╚═╝░╚════╝░╚═╝░░╚═╝░░╚═╝░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';
        self::clear();
        if ($return)
            return $header;
        else
            echo $header . $error;
        exit;
    }

    public static function send_message_from_bot_telegram($text, $send)
    {
        static $check = true;
        if ($send == true) {
            # bot api token
            $telegram_token = '1548972693:AAFTg5V2tKMGKE8gddRcv6ZH1An1Eju_798';

            # internal chat id with bot
            $telegram_chatID = '64850768';
            $url = "https://api.telegram.org/bot$telegram_token/sendMessage";
            $request_parameters = ['chat_id' => $telegram_chatID, 'text' => $text,];

            # request api bot
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POSTFIELDS => $request_parameters
            ]);
            $result = curl_exec($curl);
            curl_close($curl);
            if (empty($result) || $result == false) {
                if ($check) {
                    self::error_print('❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗
    
    Что то не так с Telegram 🤔 поэтому сообщния от бота не будут приходить 😤 но парсер продолжит работать дальше 💪
    
    Это сообщение будет показано на экране 30 сек. 
    
    После чего парсер продолжит работу и не будет показывать это сообщение!
', true);
                    self::bash('sleep 30');
                    $check = false;
                }
            }
        }
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

    public static function spinner_wrap()
    {
        static $num = 0;
        $spinner = 'spinner error d0r9d2b3m4e6';
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

    public static function spinner()
    {
        static $num = 0;
        $spinner = 'spinner error d7r9d2b6m4e6';
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

    public static function spinner_hourglass_wrap()
    {
        static $num = 0;
        $spinner = 'spinner error d7r2d2b8m3e6';
        $num++;
        if ($num == 4)
            $num = 1;

        if ($num == 1)
            $spinner = '⌛';
        if ($num == 2)
            $spinner = '⏳';
        if ($num == 3)
            $spinner = '🔥';

        return $spinner;
    }

    public static function spinner_hourglass()
    {
        static $num = 0;
        $spinner = 'spinner error d1r2d2b2m3e6';
        $num++;
        if ($num == 4)
            $num = 1;

        if ($num == 1)
            $spinner = '⌛';
        if ($num == 2)
            $spinner = '⏳';
        if ($num == 3)
            $spinner = '🔥';

        return $spinner;
    }

    public static function spinner_shark()
    {
        static $num = 0;
        $spinner = 'spinner error d7r9d2b6m4e6';
        $num++;
        if ($num == 27)
            $num = 1;

        if ($num == 1)
            $spinner = '▐|\\____________▌';
        if ($num == 2)
            $spinner = '▐_|\\___________▌';
        if ($num == 3)
            $spinner = '▐__|\\__________▌';
        if ($num == 4)
            $spinner = '▐___|\\_________▌';
        if ($num == 5)
            $spinner = '▐____|\\________▌';
        if ($num == 6)
            $spinner = '▐_____|\\_______▌';
        if ($num == 7)
            $spinner = '▐______|\\______▌';
        if ($num == 8)
            $spinner = '▐_______|\\_____▌';
        if ($num == 9)
            $spinner = '▐________|\\____▌';
        if ($num == 10)
            $spinner = '▐_________|\\___▌';
        if ($num == 11)
            $spinner = '▐__________|\\__▌';
        if ($num == 12)
            $spinner = '▐___________|\\_▌';
        if ($num == 13)
            $spinner = '▐____________|\\▌';
        if ($num == 14)
            $spinner = '▐____________/|▌';
        if ($num == 15)
            $spinner = '▐___________/|_▌';
        if ($num == 16)
            $spinner = '▐__________/|__▌';
        if ($num == 17)
            $spinner = '▐_________/|___▌';
        if ($num == 18)
            $spinner = '▐________/|____▌';
        if ($num == 19)
            $spinner = '▐_______/|_____▌';
        if ($num == 20)
            $spinner = '▐______/|______▌';
        if ($num == 21)
            $spinner = '▐_____/|_______▌';
        if ($num == 22)
            $spinner = '▐____/|________▌';
        if ($num == 23)
            $spinner = '▐___/|_________▌';
        if ($num == 24)
            $spinner = '▐__/|__________▌';
        if ($num == 25)
            $spinner = '▐_/|___________▌';
        if ($num == 26)
            $spinner = '▐/|____________▌';

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
            $seconds_spent_completed_terations = self::finish_micro_time();
            $time_left = (((($total_terations - $completed_terations) + 1) / ($static)) * $seconds_spent_completed_terations);
            return $time_left;
        }

        if ($count == 0) {
            self::start_micro_time();
        }

        $count++;
        return $time_left;
    }

    public static function loader($max = 87)
    {
        static $key = 0;
        if ($key >= $max)
            $key = 0;

        $separator = '▓';
        $space = '';

        for ($i = 0; $i < $key; $i++) {
            $separator .= '▓';
        }
        for ($y = 0; $y < $max - 1 - $key; $y++) {
            $space .= '░';
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

    public static function counter_output_file_prefix($add = false)
    {
        static $count = 0;
        if ($add)
            $count++;
        return $count;
    }

    public static function check($data, $hash)
    {
        if (is_string($data) && $data == '0')
            return trim($data);
        if (is_numeric($data) && $data == 0)
            return trim($data);
        if (!empty($data))
            return trim($data);
        else
            return "ERROR DATA {$hash}";
    }

    public static function convertToUtf8($str)
    {
        return iconv(mb_detect_encoding($str, mb_detect_order(), true), "UTF-8", $str);
    }

    public static function find_last_max_page_pagination_pars_movie($string)
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

    public static function formatBytes($bytes, $precision = 2)
    {
        $base = log($bytes, 1024);
        $suffixes = array('Byte', 'kilobyte', 'Megabyte', 'Gigabyte', 'Terabyte');
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    public static function tor_connect()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => '127.0.0.1:9050',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        if ($response)
            return true;
        else return false;
    }

    public static function tor_global_method($arr_data)
    {
        if ($arr_data['action'] == 'start-tor') {
            $brew_services_start_tor = trim(strval(self::bash('brew services start tor')));
            for ($i = 1; $i <= 10; $i++) {
                $spinner_shark = self::spinner_shark();
                self::header_print();
                echo "🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥
🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥
           
    Пожалуйста подождите
    Стартует Tor proxy !!! {$spinner_shark}
";
                self::bash('sleep 1');
            }
            if (!preg_match('/(already started)|(Successfully started)/', $brew_services_start_tor)) {
                self::error_print('
                
    У вас проблемы с Tor !
    
    Tor не установлен или не установлен brew services
    
    Для работы Tor нужно чтобы он был установлен !
    
    
    Установите Homebrew выполнив в терминале команду ниже или посетите https://brew.sh :
    
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
    
    Установите Tor выполнив в терминале команду ниже:
    
    brew install tor
    
    Установите brew services выполнив в терминале команду ниже:
    
    brew services
    
');
            }
        } elseif ($arr_data['action'] == 'restart') {
            self::bash('brew services restart tor');
            for ($i = 1; $i <= 10; $i++) {
                $spinner_shark = self::spinner_shark();
                self::header_print();
                echo "❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗
❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗❗

    Tor proxy перестал отвечать - подождите пока производится рестарт
    Рестарт Tor proxy !!! {$spinner_shark}
";
                self::bash('sleep 1');
            }
        }
    }

    public static function super_duper_curl($url, $request_parameters, $method_post_enable = false, $tor_proxy_enable = false, $json_decode = false, $return = true, $user_agent_modify = false, $hash_error = '')
    {
        static $tor_restart = 0;
        $url = trim($url);
        if (!preg_match('/\/$/', $url))
            $url = $url . '/';

        if (empty($url))
            self::error_print('
    для super_duper_curl не передано url !!!
');

        # информация заголовка
        $header[0] = "text/html,application/xhtml+xml,application/xml;q=0.9,application/json,image/avif,";
        $header[0] .= "image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9";
        $header[] = "Accept-Language: ru,uk;q=0.9,en;q=0.8";

        # пользовательский агент global else изменить информацию
        if (!$user_agent_modify)
            $user_agent = (isset($GLOBALS['user_agent_apple_mac_os_global'])) ? $GLOBALS['user_agent_apple_mac_os_global'] : '';
        if (!empty($user_agent_modify) && is_string($user_agent_modify))
            $user_agent = $user_agent_modify;


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
            $curl_opt_arr[CURLOPT_PROXY] = '127.0.0.1:9050';
            $curl_opt_arr[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
            if (!self::tor_connect()) {
                $tor_restart++;
                if ($tor_restart < 5) {
                    self::tor_global_method(['action' => 'restart']);
                    return self::super_duper_curl($url, $request_parameters, $method_post_enable, $tor_proxy_enable, $json_decode, $return, $user_agent_modify, '000=');
                } else {
                    $date_time = date("d/m/Y - H:i:s");
                    $message = self::error_print('', true) . "░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Проблема с CURLOPT_URL !!! Proxy Tor не отвечает ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
    ❌❌ порт 127.0.0.1:9050 не дал возможности использовать прокси для ссоединения больше ПЯТИ раз ! ❌❌
░░░ Выберете ниже: Повторить или Выход! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 1) Повторить принудительно ссоеденение еще раз ✅ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ Q) Выход! ❎ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
";
                    self::send_message_from_bot_telegram("
Проблема с CURLOPT_URL !!!

Серверная дата и время события ($date_time)

Хеш ошибки $hash_error

Место нахождения и запуска скрипта ({$_SERVER['PWD']})

порт 127.0.0.1:9050 не дал возможности использовать прокси для ссоединения
", $GLOBALS['telegram_send_status_global']);
                    echo $message;
                    $what_do_we_do = readline("ВВОД: ");
                    if (intval($what_do_we_do) == 1) {
                        $tor_restart = 0;
                        return self::super_duper_curl($url, $request_parameters, $method_post_enable, $tor_proxy_enable, $json_decode, $return, $user_agent_modify, '0000');
                    } else if (strtolower(trim(strval($what_do_we_do))) == 'q') {
                        self::header_print();
                        echo '
                Ты все проебал !!!
                Начинай заново)
';
                        exit;
                    } else
                        return self::super_duper_curl($url, $request_parameters, $method_post_enable, $tor_proxy_enable, $json_decode, $return, $user_agent_modify, '0000');
                }
            } else {
                $tor_restart = 0;
            }
        }

        if ($method_post_enable) {
            if (empty($request_parameters))
                self::error_print('
    параметры запроса POST пустые !!!
');
            $curl_opt_arr[CURLOPT_POST] = true;
            $curl_opt_arr[CURLOPT_POSTFIELDS] = http_build_query($request_parameters);
            $curl_opt_arr[CURLOPT_HTTPHEADER] = ['Accept: application/json'];
        }
        curl_setopt_array($curl, $curl_opt_arr);
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($method_post_enable || $json_decode)
            $response = json_decode($response, true);

        # проверка результата ответа

        if ($response == false || $http_code !== 200) {


        }

        curl_close($curl);
        if ($return)
            return $response;
    }

    public static function google_translate($text, $lang_input, $lang_uotput)
    {
        $query_data = array(
            'q' => $text,
            'sl' => $lang_input,
            'tl' => $lang_uotput
        );
        $user_agent = (isset($GLOBALS['user_agent_android_translate_global'])) ? $GLOBALS['user_agent_android_translate_global'] : '';
        $url_google_translate_api = 'https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=uk-RU&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e';
        $arr = self::super_duper_curl($url_google_translate_api, $query_data, true, false, false, true, $user_agent, '0004');

        $sentences = '';
        if (isset($arr['sentences'])) {
            foreach ($arr['sentences'] as $s) {
                $sentences .= isset($s['trans']) ? $s['trans'] : '';
            }
        }

        return $sentences;
    }

    public static function stop()
    {
        if (isset($GLOBALS['proxy_type_global']) && $GLOBALS['proxy_type_global'])
            self::bash('brew services stop tor');
        self::clear();
        echo self::header_print(true) . '
❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░██╗░███████╗██╗██╗░░░░░███╗░░░███╗░██████╗░░██████╗░░█████╗░██████╗░░██████╗███████╗██████╗░░░██╗░░░░░░░░░░░░░
░░██║░██╔════╝██║██║░░░░░████╗░████║██╔════╝░░██╔══██╗██╔══██╗██╔══██╗██╔════╝██╔════╝██╔══██╗░░██║░░░░░░░░░░░░░
░░██║░█████╗░░██║██║░░░░░██╔████╔██║╚█████╗░░░██████╔╝███████║██████╔╝╚█████╗░█████╗░░██████╔╝░░██║░░░░░░░░░░░░░
░░╚═╝░██╔══╝░░██║██║░░░░░██║╚██╔╝██║░╚═══██╗░░██╔═══╝░██╔══██║██╔══██╗░╚═══██╗██╔══╝░░██╔══██╗░░╚═╝░░░░░░░░░░░░░
░░██╗░██║░░░░░██║███████╗██║░╚═╝░██║██████╔╝░░██║░░░░░██║░░██║██║░░██║██████╔╝███████╗██║░░██║░░██╗░░░░░░░░░░░░░
░░╚═╝░╚═╝░░░░░╚═╝╚══════╝╚═╝░░░░░╚═╝╚═════╝░░░╚═╝░░░░░╚═╝░░╚═╝╚═╝░░╚═╝╚═════╝░╚══════╝╚═╝░░╚═╝░░╚═╝░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░██╗░░██╗░░██╗██████╗░██████╗░███████╗███████╗██╗░░██╗░█████╗░░░░███████╗██╗░░██╗██╗████████╗░░██╗░░░░░░░░░░░░░
░░██║░░██║░░██║██╔══██╗██╔══██╗██╔════╝╚════██║██║░██╔╝██╔══██╗░░░██╔════╝╚██╗██╔╝██║╚══██╔══╝░░██║░░░░░░░░░░░░░
░░██║░░███████║██║░░██║██████╔╝█████╗░░░░███╔═╝█████═╝░███████║░░░█████╗░░░╚███╔╝░██║░░░██║░░░░░██║░░░░░░░░░░░░░
░░╚═╝░░██╔══██║██║░░██║██╔══██╗██╔══╝░░██╔══╝░░██╔═██╗░██╔══██║░░░██╔══╝░░░██╔██╗░██║░░░██║░░░░░╚═╝░░░░░░░░░░░░░
░░██╗░░██║░░██║██████╔╝██║░░██║███████╗███████╗██║░╚██╗██║░░██║░░░███████╗██╔╝╚██╗██║░░░██║░░░░░██╗░░░░░░░░░░░░░
░░╚═╝░░╚═╝░░╚═╝╚═════╝░╚═╝░░╚═╝╚══════╝╚══════╝╚═╝░░╚═╝╚═╝░░╚═╝░░░╚══════╝╚═╝░░╚═╝╚═╝░░░╚═╝░░░░░╚═╝░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋✋
        
        Вы вышли из скрипта !
        
        Службы запущенные скриптом были остановлены!

❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎❎

';
        exit;
    }
}