<?php
/**
 * Parser serials url by pagination
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!isset($proxy_type_global))
    $proxy_type_global = false;

$parser_serials_url = "{$site_domain_global}/{$serials_slug_parser_global}";

# можно отключить получение и парсинг урлов фильмов и работать с уже готовым имеющимся файлом (true) просто поменять true на false
(true) ? $result_global_end_script_pars_serials_url = (new Parser)->get_media_urls_by_pagination_page($parser_serials_url) : exit('no permission to run the script!');

require_once 'pars_one_serial_data_by_urls_csv.php';