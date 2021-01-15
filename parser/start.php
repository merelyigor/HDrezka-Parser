<?php
/**
 * Точка входа и начальная настройка скрипта
 * -------------------------------------------------------------------------------------
 */
########################################################################################
# Настройка окна скрипта + php ini + настройка вывода ошибок, но не предупреждений
########################################################################################
$start_using_script_memory_global = memory_get_peak_usage();
error_reporting(E_ERROR | E_PARSE);
ini_set('memory_limit', '8192M');
ini_set('max_execution_time', '9999');
ini_set('default_socket_timeout', '100000');
########################################################################################
########################################################################################
# Объявление суперглобальных переменной
# Глобальные пути к файлам или папкам скрипта
########################################################################################
# На сколко примерно мегабайт розбивать выходящий файл с данними после парсинга - СТРОГО ПИСАТЬ ТОЛЬКО ЦИФРУ !!!
$max_uot_put_file_size_megabyte = 10;
# Глобальная папка скрипта
$path_repo_global = preg_replace('/parser/', '', __DIR__);
# Глобальная папка для временных файлов работы скрипта
$path_repo_raw_data_global = $path_repo_global . 'RAW-DATA';
# Глобальная папка с готовыми файлами выходящими после парсинга
$path_repo_output_data_global = $path_repo_global . 'OUTPUT-DATA';
$path_repo_output_films_folder_global = $path_repo_output_data_global . '/FILMS';
$path_repo_output_serials_folder_global = $path_repo_output_data_global . '/SERIALS';
# Глобальная папка картинок фильмов которые были обработаны и спаршены
$path_repo_images_data_global = $path_repo_global . 'images/images-films/';

########################################################################################
# Глобальные юзер агенты
# https://developers.whatismybrowser.com/useragents/explore/
$user_agent_android_translate_global = 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1';
$user_agent_apple_mac_os_global = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36';
$user_agent_apple_windows10_global = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36';
$user_agent_apple_iphone_global = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148';
$user_agent_apple_ipad_global = 'Mozilla/5.0 (iPad; CPU OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148';
########################################################################################
# Глобальные пути к URL-slug для парсера
$films_slug_parser_global = 'films';
$serials_slug_parser_global = 'series';
########################################################################################
# Глобальные URLs HDrezka AJAX
$url_hdrezka_ajax_global = 'https://hdrezka.website/ajax/get_cdn_series/';
########################################################################################
########################################################################################
# Connecting dependencies and create variable + class
########################################################################################
require 'helper.php';
require 'parser.php';
$error_message_invalid_user = '
    Вы ввели какуюто хуйню

    повторите ппытку еще раз но читая что написано в окне скрипта!

';
########################################################################################
########################################################################################
/**
 * Начать выполнение настроек скрипта, используя консольный ввод
 * ---------------------------------------------------------------------------------------------------------------------
 */
# экстра остановка скрипта и выход из парсера ❌
if (isset($argv[1]))
    if ($argv[1] == 'stop')
        Helper::stop();

#▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
########################################################################################
# Выбор домена для работы парсера
########################################################################################
Helper::bash_escapeshellarg("printf \e[8;30;120t");
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░ Выберете с какого домена парсить ?: ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 1) hdrezka.website ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 2) rezka.ag ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 3) hdrezka.sh ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 4) hdrezka.tv ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';

$site_domain_global = intval(readline("ВВОД: "));
if ($site_domain_global == 1)
    $site_domain_global = 'https://hdrezka.website';
else if ($site_domain_global == 2)
    $site_domain_global = 'https://rezka.ag';
else if ($site_domain_global == 3)
    $site_domain_global = 'https://hdrezka.sh';
else if ($site_domain_global == 4)
    $site_domain_global = 'http://hdrezka.tv';
else
    Helper::error_print($error_message_invalid_user);


#▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
########################################################################################
# Выбор варианта парсинга с прокси или без него
########################################################################################
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Использовать Tor Proxy (127.0.0.1:9050) для парсинга? ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ У вас должен быть установлен tor прокси ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Для установки тора проследуйте по ссылке https://formulae.brew.sh/formula/tor ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Так же нужно установить services выполнив команду $ brew services ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Для проверки работы введите в терминале $ brew services start tor ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Далее перейите по http://127.0.0.1:9050 ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ (This is a SOCKs proxy, not an HTTP proxy) если видете данное сообщение все работает: ░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 1) Парсинг через Tor Proxy ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 2) Та ну нахуй тот Tor Proxy ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';

$proxy_type_readline = intval(readline("ВВОД: "));
$proxy_type_global = false;
if ($proxy_type_readline == 1) {
    $proxy_type_global = true;
    if (!Helper::tor_connect())
        Helper::tor_global_method(['action' => 'start-tor']);
} else if ($proxy_type_readline == 2)
    $proxy_type_global = false;
else
    Helper::error_print($error_message_invalid_user);


#▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
########################################################################################
# Оповещения в телеграмм
########################################################################################
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Оповещать в Telegram ? ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Оповещения будут приходить от бота в случае ошибок при работе скрита ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Так же прийдет оповещения о том что скрипт завершил парсинг и пришелет данные о завершении ░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 1) Включить оповещения ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 2) Не включать! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';

$telegram_send_status_readline = intval(readline("ВВОД: "));
$telegram_send_status_global = false;
if ($telegram_send_status_readline == 1)
    $telegram_send_status_global = true;
else if ($telegram_send_status_readline == 2)
    $telegram_send_status_global = false;
else
    Helper::error_print($error_message_invalid_user);


#▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
########################################################################################
# При выборе количества страниц пагинации для разбора
# на одной странице будет около 36 фильмов, если это не последняя страница пагинации.
########################################################################################
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Парсить все страницы или только определенное количество ?: ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ 0) Парсим все нахуй! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ [1-∞]) Парсим только несколько (ну типа вводим цыфру сколько парсить страниц) ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';

$count_pagination_readline = intval(readline("ВВОД: "));
$count_pagination_global = false;
if ($count_pagination_readline != 0)
    $count_pagination_global = $count_pagination_readline;
else if ($count_pagination_readline == 0)
    $count_pagination_global = 0;
else
    Helper::error_print($error_message_invalid_user);


#▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
########################################################################################
# Выбор типа парсинга
########################################################################################
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Выбор парсера: ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ 1) Парсинг фильмов ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ 2) Парсинг сериалов ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';

$parser_type = readline("ВВОД: ");
if ($parser_type == 1) {
    # Глобальный файл где временно хранятся урлы для работы парсера
    $path_repo_raw_data_urls_csv_global = $path_repo_raw_data_global . '/films-temporal-urls.csv';
    if (file_exists($path_repo_raw_data_urls_csv_global))
        unlink($path_repo_raw_data_urls_csv_global);
    require 'film/pars_films_urls_by_pagination.php';
} else if ($parser_type == 2) {
    # Глобальный файл где временно хранятся урлы для работы парсера
    $path_repo_raw_data_urls_csv_global = $path_repo_raw_data_global . '/serials-temporal-urls.csv';
//    if (file_exists($path_repo_raw_data_urls_csv_global))
//        unlink($path_repo_raw_data_urls_csv_global);
    require 'serials/pars_serials_urls_by_pagination.php';
} else
    Helper::error_print($error_message_invalid_user);