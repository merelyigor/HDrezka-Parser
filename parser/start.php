<?php
/**
 * Entry point and initial script setup
 * ---------------------------------------------------------------------------------------------------------------------
 */
########################################################################################
# Configuring the script window + php ini + configuring the output of errors but not warnings
########################################################################################
error_reporting(E_ERROR | E_PARSE);
ini_set('memory_limit', '8192M');
ini_set('max_execution_time', '9000');
ini_set('default_socket_timeout', '100000');

########################################################################################
# Declaring super globals variable
########################################################################################
# File or folder paths
$path_repo_global = preg_replace('/parser/', '', __DIR__);
$path_repo_raw_data_global = $path_repo_global . 'RAW-DATA';
$path_repo_output_data_global = $path_repo_global . 'OUTPUT-DATA';
$path_repo_images_data_global = $path_repo_global . 'images/images-films/';
$path_repo_raw_data_films_urls_csv_global = $path_repo_raw_data_global . '/films-temporal-urls.csv';
$path_repo_raw_data_serials_urls_csv_global = $path_repo_raw_data_global . '/serials-temporal-urls.csv';

# paths to url-slug for the parser
$films_slug_parser_global = 'films';
$serials_slug_parser_global = 'series';

########################################################################################
# Connecting dependencies and create variable + class
########################################################################################
require 'helper.php';
require 'parser.php';
$error_message_invalid_user = '
    Вы ввели какуюто хуйню

    повторите ппытку еще раз но читая что написано в окне скрипта!

';


/**
 * Start executing script settings using console input
 * ---------------------------------------------------------------------------------------------------------------------
 */
########################################################################################
# Choosing a domain for the parser to work
########################################################################################
Helper::bash_escapeshellarg("printf \e[8;30;120t");
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░ Выберете с какого домена парсить ?: ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 1) hdrezka.website ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 2) rezka.ag ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 3) hdrezka.sh ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 4) hdrezka.tv ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
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
########################################################################################
########################################################################################
# Choosing a parsing option with or without a proxy
########################################################################################
########################################################################################
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Использовать Tor Proxy (127.0.0.1:9150) для парсинга? ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ У вас должен быть открыт браузер Tor в данный момент (можно его просто свернуть) ░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ по данному адресу должно открываться сообщение http://127.0.0.1:9150/ ░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ (This is a SOCKs proxy, not an HTTP proxy) если видете данное сообщение все работает: ░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 1) Парсинг через Tor Proxy ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░ 2) Та ну нахуй тот Tor Proxy ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';
$proxy_type_readline = intval(readline("ВВОД: "));
var_dump($proxy_type_readline);
$proxy_type_global = false;
if ($proxy_type_readline == 1)
    $proxy_type_global = true;
else if ($proxy_type_readline == 2)
    $proxy_type_global = false;
else
    Helper::error_print($error_message_invalid_user);
########################################################################################
########################################################################################
# Selecting the number of pagination pages for parsing, there are about 36 films on one page if this is not the last pagination page
########################################################################################
########################################################################################
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Парсить все страницы или только определенное количество ?: ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ 0) Парсим все нахуй! ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ [1-∞]) Парсим только несколько (ну типа вводим цыфру сколько парсить страниц) ░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';
$count_pagination_readline = intval(readline("ВВОД: "));
$count_pagination_global = false;
if ($count_pagination_readline != 0)
    $count_pagination_global = $count_pagination_readline;
else if ($count_pagination_readline == 0)
    $count_pagination_global = 0;
else
    Helper::error_print($error_message_invalid_user);
########################################################################################
########################################################################################
# Choosing a parsing type
########################################################################################
########################################################################################
echo Helper::header_print() . '░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ Выбор парсера: ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ 1) Парсинг фильмов ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░ 2) Парсинг сериалов ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
';
$parser_type = readline("ВВОД: ");
if ($parser_type == 1) {
    if (file_exists($path_repo_raw_data_films_urls_csv_global))
        unlink($path_repo_raw_data_films_urls_csv_global);
    require 'film/pars_films_urls_by_pagination.php';
} else if ($parser_type == 2) {
    if (file_exists($path_repo_raw_data_serials_urls_csv_global))
        unlink($path_repo_raw_data_serials_urls_csv_global);
    require 'serials/pars_serials_urls_by_pagination.php';
} else
    Helper::error_print($error_message_invalid_user);