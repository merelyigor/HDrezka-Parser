<?php
require_once 'parserhandler.php';

/**
 * Parser class
 * ---------------------------------------------------------------------------------------------------------------------
 */
class Parser
{
#   Экземпляр simple_html_dom класса
    private $SimpleHtmlDom;

    function __construct()
    {
#       Экземпляр simple_html_dom класса
        $this->SimpleHtmlDom = new simple_html_dom;
    }
##############################################################################################################
# API private Methods

//  получение постера записи от внешнего API themoviedb

    private function get_movie_description_by_api_themoviedb($movie_name_ru, $movie_name_en)
    {
        $movie_name_ru = str_replace(' ', '%20', $movie_name_ru);
        $movie_name_en = str_replace(' ', '%20', $movie_name_en);
        $description_result_str = null;
        if (empty($movie_name_en))
            $movie_name_en = $movie_name_ru;

        $url_api_ru = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=ru-RU&query=$movie_name_ru";
        $url_api_en = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=en-US&query=$movie_name_en";
        $result_API_en = Helper::super_duper_curl($url_api_en, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0005');
        if ($result_API_en['total_pages']) {
            $movie_description = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n|\\r/', '', $result_API_en['results'][0]['overview']);
            $translate_description = Helper::google_translate($movie_description, 'en', 'ru');
            $description_result_str = !empty($translate_description) ? trim(strip_tags($translate_description)) : null;
        } else {
            $result_API_ru = Helper::super_duper_curl($url_api_ru, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0007');
            if ($result_API_ru['total_pages']) {
                $movie_description = preg_replace('/ <br>|<br> |<br>|<br \/>|\\n|\\r/', '', $result_API_ru['results'][0]['overview']);
                $translate_description = Helper::google_translate($movie_description, 'en', 'ru');
                $description_result_str = !empty($translate_description) ? trim(strip_tags($translate_description)) : null;
            }
        }
        return $description_result_str;
    }

    private function get_movie_poster_api_themoviedb_arr($movie_name_ru, $movie_name_en)
    {
        $movie_name_ru = str_replace(' ', '%20', $movie_name_ru);
        $movie_name_en = str_replace(' ', '%20', $movie_name_en);
        if (empty($movie_name_en))
            $movie_name_en = $movie_name_ru;

        $path = 'https://www.themoviedb.org/t/p/w600_and_h900_bestv2';
        $arr = null;
        $url_api_ru = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=ru-RU&query=$movie_name_ru";
        $url_api_en = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=en-US&query=$movie_name_en";

        $result_API_ru = Helper::super_duper_curl($url_api_ru, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0008');
        if ($result_API_ru['total_pages']) {
            $img_slug = $this->get_poster_path_exist_check($result_API_ru['results']);
            $arr['movie_poster_slug'] = $img_slug;
            $arr['movie_poster_hash_name'] = preg_replace('/^(\/)|(\.jpg)/', '', $img_slug);
            $arr['movie_poster_file_name'] = preg_replace('/^(\/)/', '', $img_slug);
            $arr['movie_poster_url'] = $path . $img_slug;
        } else {
            $result_API_en = Helper::super_duper_curl($url_api_en, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0005');
            if ($result_API_en['total_pages']) {
                $img_slug = $this->get_poster_path_exist_check($result_API_en['results']);
                $arr['movie_poster_slug'] = $img_slug;
                $arr['movie_poster_hash_name'] = preg_replace('/^(\/)|(\.jpg)/', '', $img_slug);
                $arr['movie_poster_file_name'] = preg_replace('/^(\/)/', '', $img_slug);
                $arr['movie_poster_url'] = $path . $img_slug;
            }
        }

        if (!empty($arr) && is_array($arr)) {
            $arr['img_locale_path'] = $this->save_movie_poster_img($arr);
        }
        return $arr;
    }

##############################################################################################################
# Pars Helper private Methods

//  проверка наличия постера записи от внешнего API
    private function get_poster_path_exist_check($result_api)
    {
        if (is_array($result_api) && !empty($result_api))
            foreach ($result_api as $item)
                foreach ($item as $key => $value) {
                    if ($key == 'poster_path')
                        if (!empty($value))
                            return $value;
                        else
                            break;
                }
        else
            return null;
    }

//  сохранение постера записи
    private function save_movie_poster_img($arr)
    {
        if (isset($arr['movie_poster_file_name']) && !empty($arr['movie_poster_file_name'])) {
            $url = $arr['movie_poster_url'];
            $movie_poster_file_name = $arr['movie_poster_file_name'];
            $dir = $GLOBALS['path_repo_images_data_global'];
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $curl = curl_init($url);
            $fp = fopen("$dir{$movie_poster_file_name}", 'wb');
            curl_setopt($curl, CURLOPT_FILE, $fp);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_exec($curl);
            curl_close($curl);
            fclose($fp);
            return "{$GLOBALS['img_path_global']}{$movie_poster_file_name}";
        } else {
            return null;
        }
    }

##############################################################################################################
# Глобальные парсеры урлов на странице одной пагинации

//  парсер урлов на странице одной пагинации
    private function parser_urls_on_the_page_of_one_pagination($url, $pre_message, $pagination, $how_much_is_left_until_the_end, $how_much_is_left_until_the_end_2, $time_script_run)
    {
        $count_pars_movie = 0;
        $content_html = Helper::super_duper_curl($url, [], false, $GLOBALS['proxy_type_global'], false, true, false, '0010');
        $html = $this->SimpleHtmlDom->load($content_html);
        if ($html->innertext != '' && count($html->find('div.b-content__inline_item-link'))) {
            $movies_arr = $html->find('div.b-content__inline_item-link');
            $spinner = Helper::spinner();
            $spinner_hourglass = Helper::spinner_hourglass();
            $loader = Helper::loader();
            $pagination_text = Helper::num_word($pagination, ['Странице', 'Страницах', 'Страницах']);
            foreach ($movies_arr as $key => $movie) {
                $count_pars_movie = Helper::count_pars(!$key);

                $GLOBALS['count_pars_total_urls'] = $GLOBALS['count_pars_total_urls'] + 1;
                $parsed_urls_counter_text = Helper::num_word($GLOBALS['count_pars_total_urls'], ['Урл', 'Урла', 'Урлов']);
                $total_memory_text = Helper::formatBytes($GLOBALS['total_memory_bytes_global'], 3);
                Helper::clear();
                $message = "$pre_message

    {$spinner} {$loader} {$spinner}

    Счетчик количества спарсенных урлов ➤ ✅{$spinner_hourglass} $parsed_urls_counter_text {$spinner_hourglass}✅ на {$pagination_text} пагинации
    
▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂
∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷
            
    Время выполнения скрипта парсинга Урлов ➤ ⌚ {$time_script_run} ⌚
    
    Скрипт сожрал памяти ➤ ⚡ {$total_memory_text} ⚡

    {$spinner}  До конца выполнения скрипта осталось ➤ ⌚ {$how_much_is_left_until_the_end} ⌚  {$spinner}

    {$spinner}  До конца выполнения скрипта осталось ➤ ⌚ {$how_much_is_left_until_the_end_2} ⌚  {$spinner}
    
∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷
▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔
";
                echo $message;
                $temp_link = $movie->find('a')[0];
                $movie_url_slug = preg_replace('/http:\/\/hdrezka\.tv|https:\/\/hdrezka\.website|https:\/\/hdrezka\.sh|https:\/\/rezka\.ag/', '', $temp_link->href);
                $movie_name = $temp_link->plaintext;
                $line_text = "$movie_url_slug;$movie_name \n";
                if (!file_exists($GLOBALS['path_repo_raw_data_global'])) {
                    mkdir($GLOBALS['path_repo_raw_data_global'], 0777, true);
                }
                file_put_contents("{$GLOBALS['path_repo_raw_data_urls_csv_global']}", $line_text, FILE_APPEND);
            }
        }
        return [
            'time_script_run' => (!empty($time_script_run)) ? $time_script_run : 'менее секунды',
            'count_pars_movie_temp' => intval($count_pars_movie),
        ];
    }

//  парсер урлов на странице пагинации
    public function get_media_urls_by_pagination_page($url)
    {
        $content_html = Helper::super_duper_curl($url, [], false, $GLOBALS['proxy_type_global'], false, true, false, '0009');
        $html = $this->SimpleHtmlDom->load($content_html);
        $result = '';
        $start_time = null;
        $this_time = null;

        (!empty($GLOBALS['count_pagination_global']) && intval($GLOBALS['count_pagination_global']) != 0) ?
            $max_num_pages = intval($GLOBALS['count_pagination_global']) :
            $max_num_pages = Helper::find_last_max_page_pagination_pars_movie($html->find('div.b-navigation')[0]->plaintext);

        if ($max_num_pages >= 0) {
            $start_time_script = microtime(true);
            $page = 1;
            $movie = 0;
            for ($processed_page = 1; $processed_page <= $max_num_pages; $processed_page++) {
                ($page == 1) ? $start_time = date("H:i:s") : $this_time = date("H:i:s");
                $sec = intval(round(microtime(true) - $start_time_script, 0));
                $how_much_is_left_until_the_end = Helper::how_much_time_is_left($max_num_pages, $page, $sec);
                $how_much_is_left_until_the_end_2 = Helper::how_much_time_is_left_2($max_num_pages, $page);
                $total_number_of_pagination_pages = Helper::num_word($max_num_pages, ['Страница пагинации', 'Страницы пагинации', 'Страниц пагинаций']);
                $counter_of_parsed = Helper::num_word($page, ['Страница пагинации', 'Страницы пагинации', 'Страниц пагинаций']);
                $spinner_hourglass = Helper::spinner_hourglass_wrap();
                $spinner_shark = Helper::spinner_shark();
                $message = Helper::header_print(true) . "
▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂
∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷

    Текущий URL в цыкле ⟾  ({$url})
                
    Всего найдено ✅ $total_number_of_pagination_pages ✅

    Уже спарсилось 🔥{$spinner_hourglass} $counter_of_parsed {$spinner_shark}🔥
            
    Время парсинга текущей страницы пагинации ➤ ⌚ $this_time ⌚

    Время парсинга первой страници пагинации ➤ ⌚ $start_time ⌚";

                # NEXT PARSER RUN
                $result = $this->parser_urls_on_the_page_of_one_pagination(
                    $url . "/page/{$processed_page}/",
                    $message,
                    $page,
                    Helper::sec_to_time($how_much_is_left_until_the_end),
                    Helper::sec_to_time($how_much_is_left_until_the_end_2),
                    Helper::sec_to_time($sec)
                );

                $movie = $movie + $result['count_pars_movie_temp'];
                $result['pagination'] = $page;
                $result['count_pars_movie'] = $movie;
                $result['second_time_total_sec'] = $sec;
                $page++;
                $GLOBALS['total_memory_bytes_global'] = memory_get_peak_usage() - $GLOBALS['base_memory_global'];
                $result['pre_total_memory_bytes_global'] = $GLOBALS['total_memory_bytes_global'];
            }
            return $result;
        } else
            Helper::error_print('
    что то пошло не так - ошибка в стрепте 3sa245s79f62
    
');
    }

##############################################################################################################
# Глобальные парсеры отдельных страниц

    public function parse_raw_one_films_data($url)
    {
        $arr = [];
        $content_html = Helper::super_duper_curl($url, [], false, $GLOBALS['proxy_type_global'], false, true, false, '0011');
        $html = $this->SimpleHtmlDom->load($content_html);

        if ($html->innertext != '') {

            $arr['film_title'] = ParserHandler::get_title($html);

            $arr['film_orig_title'] = ParserHandler::get_orig_title($html);

            $arr['film_imdb_rating'] = ParserHandler::get_imdb_rating($html);

            $arr['film_kino_poisk_rating'] = ParserHandler::get_kino_poisk_rating($html);

            $arr['film_information'] = ParserHandler::get_movie_information_array($html);

            $arr['film_description_origin_ru'] = ParserHandler::get_description_origin_ru($html);

            $arr['film_description_by_api_themoviedb'] = $this->get_movie_description_by_api_themoviedb($arr['film_title'], $arr['film_orig_title']);

            $arr['film_poster_arr'] = $this->get_movie_poster_api_themoviedb_arr($arr['film_title'], $arr['film_orig_title']);

            $arr['film_default_urls'] = ParserHandler::get_urls_video_preg_match($html, true);

            $arr['film_translation_arr'] = ParserHandler::get_movie_translators_list_array($html);

            return (!empty($arr) && is_array($arr)) ? $arr : 'ERROR 107';
        }
        return Helper::error_print('ERROR 361');
    }

    public function parse_raw_one_serials_data($url)
    {
        $arr = [];
        $content_html = Helper::super_duper_curl($url, [], false, $GLOBALS['proxy_type_global'], false, true, false, '0011');
        $html = $this->SimpleHtmlDom->load($content_html);

        if ($html->innertext != '') {

            $arr['serial_title'] = ParserHandler::get_title($html);

            $arr['serial_orig_title'] = ParserHandler::get_orig_title($html);

            $arr['serial_imdb_rating'] = ParserHandler::get_imdb_rating($html);

            $arr['serial_kino_poisk_rating'] = ParserHandler::get_kino_poisk_rating($html);

            $arr['serial_information'] = ParserHandler::get_movie_information_array($html);

            dd($arr);

            $arr['serial_description_origin_ru'] = ParserHandler::get_description_origin_ru($html);

            $arr['serial_description_by_api_themoviedb'] = $this->get_movie_description_by_api_themoviedb($arr['film_title'], $arr['film_orig_title']);

            $arr['serial_poster_arr'] = $this->get_movie_poster_api_themoviedb_arr($arr['film_title'], $arr['film_orig_title']);

            $arr['serial_default_urls'] = ParserHandler::get_urls_video_preg_match($html, true);

            $arr['serial_translation_arr'] = ParserHandler::get_movie_translators_list_array($html);

            return (!empty($arr) && is_array($arr)) ? $arr : 'ERROR 107';
        }
        return Helper::error_print('ERROR 497');
    }
}