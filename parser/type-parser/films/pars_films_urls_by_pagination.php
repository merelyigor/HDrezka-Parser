<?php
/**
 * Parser films url by pagination
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!isset($GLOBALS['proxy_type_global']))
    $proxy_type_global = false;

$parser_films_url = "{$site_domain_global}/{$films_slug_parser_global}";

# можно отключить получение и парсинг урлов фильмов и работать с уже готовым имеющимся файлом (true) просто поменять true на false
(true) ? $result_global_end_script_pars_films_url = (new Parser)->get_media_urls_by_pagination_page($parser_films_url) : exit('no permission to run the script!');

require_once 'pars_one_film_data_by_urls_csv.php';