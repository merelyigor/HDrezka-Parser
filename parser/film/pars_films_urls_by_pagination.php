<?php
/**
 * Parser films url by pagination
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!isset($GLOBALS['proxy_type_global']))
    $proxy_type_global = false;

$parser_films_url = "$site_domain_global/{$GLOBALS['films_slug_parser_global']}";

(true) ? $result_global_end_script_pars_films_url = (new ParserHD)->get_media_urls_by_pagination_page($parser_films_url) : exit('no permission to run the script!');

require 'pars_one_film_data_by_urls_csv.php';