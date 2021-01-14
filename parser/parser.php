<?php

/**
 * Glob parser class
 * ---------------------------------------------------------------------------------------------------------------------
 */
class ParserHD
{
    private $html;

    public function __construct()
    {
        $this->html = new simple_html_dom();
    }
##############################################################################################################
# API private Methods
##############################################################################################################
    private function get_movie_poster_by_api_themoviedb($movie_name_ru, $movie_name_en)
    {
        $movie_name_ru = str_replace(' ', '%20', $movie_name_ru);
        $movie_name_en = str_replace(' ', '%20', $movie_name_en);
        if (empty($movie_name_en))
            $movie_name_en = $movie_name_ru;

        $path = 'https://www.themoviedb.org/t/p/w600_and_h900_bestv2';

        $url_api_ru = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=ru-RU&query=$movie_name_ru";
        $url_api_en = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=en-US&query=$movie_name_en";
        $result_API = Helper::super_duper_curl($url_api_ru, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0005');
        if (!$result_API['total_pages']) {
            $result_API = Helper::super_duper_curl($url_api_en, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0006');
            if (!$result_API['total_pages'])
                return null;
            $img_slug = (!empty($this->poster_api_check($result_API['results'])) ? $this->poster_api_check($result_API['results']) : null);
            $movie_desc = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', $result_API['results'][0]['overview']);
            $translate_desc = Helper::google_translate($movie_desc, 'en', 'ru');
            $arr['movie_desc'] = !empty($translate_desc) ? $translate_desc : null;
            $arr['movie_poster_slug'] = $img_slug;
            $arr['movie_poster_hash_name'] = preg_replace('/^(\/)|(\.jpg)/', '', $img_slug);
            $arr['movie_poster_file_name'] = preg_replace('/^(\/)/', '', $img_slug);
            $arr['movie_poster_url'] = $path . $img_slug;
        } else {
            $result_API_desc = Helper::super_duper_curl($url_api_en, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0007');
            if (!$result_API_desc['total_pages'])
                return null;
            $movie_desc = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', $result_API_desc['results'][0]['overview']);
            $translate_desc = Helper::google_translate($movie_desc, 'en', 'ru');
            $arr['movie_desc'] = !empty($translate_desc) ? $translate_desc : null;

            $result_API = Helper::super_duper_curl($url_api_ru, [], false, $GLOBALS['proxy_type_global'], true, true, false, '0008');
            if (!$result_API['total_pages'])
                return null;
            $img_slug = (!empty($this->poster_api_check($result_API['results'])) ? $this->poster_api_check($result_API['results']) : null);
            $arr['movie_poster_slug'] = $img_slug;
            $arr['movie_poster_hash_name'] = preg_replace('/^(\/)|(\.jpg)/', '', $img_slug);
            $arr['movie_poster_file_name'] = preg_replace('/^(\/)/', '', $img_slug);
            $arr['movie_poster_url'] = $path . $img_slug;
        }
        return $arr;
    }
##############################################################################################################
##############################################################################################################
# Pars Helper private Methods
##############################################################################################################
##############################################################################################################
    public function get_media_urls_by_pagination_page($url)
    {
        $content_html = Helper::super_duper_curl($url, [], false, $GLOBALS['proxy_type_global'], false, true, false, '0009');
        $html = $this->html->load($content_html);
        $result = '';
        $start_time = null;
        $this_time = null;

        (!empty($GLOBALS['count_pagination_global']) && intval($GLOBALS['count_pagination_global']) != 0) ?
            $max_num_pages = intval($GLOBALS['count_pagination_global']) :
            $max_num_pages = Helper::find_last_max_page_pagination_pars_films($html->find('div.b-navigation')[0]->plaintext);

        if ($max_num_pages >= 0) {
            $start_time_script = microtime(true);
            $page = 1;
            $movie = 0;
            for ($I = 1; $I <= $max_num_pages; $I++) {
                ($page == 1) ? $start_time = date("H:i:s") : $this_time = date("H:i:s");
                $sec = intval(round(microtime(true) - $start_time_script, 0));
                $how_much_is_left_until_the_end = Helper::how_much_time_is_left($max_num_pages, $page, $sec);
                $how_much_is_left_until_the_end_2 = Helper::how_much_time_is_left_2($max_num_pages, $page);
                $total_number_of_pagination_pages = Helper::num_word($max_num_pages, ['Страница пагинации', 'Страницы пагинации', 'Страниц пагинаций']);
                $counter_of_parsed = Helper::num_word($page, ['Страница пагинации', 'Страницы пагинации', 'Страниц пагинаций']);
                $spinner_hourglass = Helper::spinner_hourglass_wrap();
                $message = Helper::header_print(true) . "
                
    Всего найдено ✅ $total_number_of_pagination_pages ✅

    Уже спарсилось 🔥{$spinner_hourglass} $counter_of_parsed {$spinner_hourglass}🔥
            
    Время парсинга текущей страницы пагинации ➤ ⌚ $this_time ⌚

    Время парсинга первой страници пагинации ➤ ⌚ $start_time ⌚";

                # NEXT PARSER RUN
                $result = $this->parsing_data_by_fields_movie_page(
                    $url . "/page/{$I}/",
                    $message,
                    $page,
                    Helper::sec_to_time($how_much_is_left_until_the_end),
                    Helper::sec_to_time($how_much_is_left_until_the_end_2),
                    Helper::sec_to_time($sec)
                );

                $movie = $movie + $result['count_pars_films_temp'];
                $result['pagination'] = $page;
                $result['count_pars_films'] = $movie;
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

    private function poster_api_check($result_api)
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
            null;
    }

    private function save_movie_poster_img($arr)
    {
        if (is_array($arr) && !empty($arr['movie_poster_file_name'])) {
            $url = $arr['movie_poster_url'];
            $movie_poster_file_name = $arr['movie_poster_file_name'];
            $dir = $GLOBALS['path_repo_images_data_global'];
            $curl = curl_init($url);
            $fp = fopen("$dir{$movie_poster_file_name}", 'wb');
            curl_setopt($curl, CURLOPT_FILE, $fp);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_exec($curl);
            curl_close($curl);
            fclose($fp);
            return "$dir{$movie_poster_file_name}";
        } else {
            return null;
        }
    }

##############################################################################################################
##############################################################################################################
# Pars public Methods for films
##############################################################################################################
##############################################################################################################
    public function parsing_data_by_fields_movie_page($url, $pre_message, $pagination, $how_much_is_left_until_the_end, $how_much_is_left_until_the_end_2, $time_script_run)
    {
        $count_pars_movie = 0;
        $content_html = Helper::super_duper_curl($url, [], false, $GLOBALS['proxy_type_global'], false, true, false, '0010');
        $html = $this->html->load($content_html);
        if ($html->innertext != '' && count($html->find('div.b-content__inline_item-link'))) {
            $films_arr = $html->find('div.b-content__inline_item-link');
            $spinner = Helper::spinner();
            $spinner_hourglass = Helper::spinner_hourglass();
            $loader = Helper::loader();
            $pagination_text = Helper::num_word($pagination, ['Странице', 'Страницах', 'Страницах']);
            foreach ($films_arr as $key => $film) {
                $count_pars_movie = Helper::count_pars(!$key);

                $GLOBALS['count_pars_total_urls'] = $GLOBALS['count_pars_total_urls'] + 1;
                $parsed_urls_counter_text = Helper::num_word($GLOBALS['count_pars_total_urls'], ['Урл', 'Урла', 'Урлов']);
                $total_memory_text = Helper::formatBytes($GLOBALS['total_memory_bytes_global'], 3);
                Helper::clear();
                $message = "$pre_message

    {$spinner} {$loader} {$spinner}

    Счетчик количества спарсенных урлов ➤ ✅{$spinner_hourglass} $parsed_urls_counter_text {$spinner_hourglass}✅ на {$pagination_text} пагинации
            
    Время выполнения скрипта парсинга фильмов ➤ ⌚ {$time_script_run} ⌚
    
    Скрипт сожрал памяти ➤ ⚡ {$total_memory_text} ⚡

    {$spinner}  До конца выполнения скрипта осталось ➤ ⌚ {$how_much_is_left_until_the_end} ⌚  {$spinner}

    {$spinner}  До конца выполнения скрипта осталось ➤ ⌚ {$how_much_is_left_until_the_end_2} ⌚  {$spinner}
    ";
                echo $message;
                $temp_link = $film->find('a')[0];
                $film_url_slug = preg_replace('/http:\/\/hdrezka\.tv|https:\/\/hdrezka\.website|https:\/\/hdrezka\.sh|https:\/\/rezka\.ag/', '', $temp_link->href);
                $film_name = $temp_link->plaintext;
                $line_text = "$film_url_slug;$film_name \n";
                file_put_contents("{$GLOBALS['path_repo_raw_data_global']}/films-temporal-urls.csv", $line_text, FILE_APPEND);
            }
        }
        return [
            'time_script_run' => (!empty($time_script_run)) ? $time_script_run : 'менее секунды',
            'count_pars_films_temp' => intval($count_pars_movie),
        ];
    }

##############################################################################################################
    public function save_raw_one_films_data($url)
    {
        $arr = [];
        $content_html = Helper::super_duper_curl($url, [], false, $GLOBALS['proxy_type_global'], false, true, false, '0011');
        $html = $this->html->load($content_html);

        if ($html->innertext != '') {

            $temp_film_info = $html->find('table.b-post__info')[0];

            if ($html->find('div.b-post__title')[0]->plaintext != '')
                $arr['film_title'] = Helper::check($html->find('div.b-post__title')[0]->find('h1', 0)->plaintext, '219');
            else
                $arr['film_title'] = null;

            if ($html->find('div.b-post__origtitle')[0]->plaintext != '')
                $arr['film_orig_title'] = Helper::check($html->find('div.b-post__origtitle')[0]->plaintext, '224');
            else
                $arr['film_orig_title'] = null;

            if ($temp_film_info->find('span.imdb')[0]->plaintext != '')
                $arr['film_imdb_rating'] = Helper::check($temp_film_info->find('span.imdb')[0]->find('span', 0)->plaintext, '229');
            else
                $arr['film_imdb_rating'] = null;

            if ($temp_film_info->find('span.kp')[0]->plaintext != '')
                $arr['film_kino_poisk_rating'] = Helper::check($temp_film_info->find('span.kp')[0]->find('span', 0)->plaintext, '234');
            else
                $arr['film_kino_poisk_rating'] = null;

            $temp = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', Helper::check($html->find('div.b-post__description_text')[0]->innertext, '238'));
            $arr['film_desc_str'] = !empty($temp) ? $temp : null;

            foreach ($temp_film_info->find('tr') as $item) {
                if (preg_match('/Слоган/', $item->plaintext)) {
                    $temp = preg_replace('/&laquo;|&raquo;/', '', Helper::check($item->find('td', 1)->plaintext, '243'));
                    $arr['film_slogan_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/(Дата выхода)/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '247');
                    $arr['film_year_str'] = !empty($temp) ? $temp : null;
                    if (preg_match('/181[2-9]|18[2-9]\d|19\d\d|2\d{3}|30[0-3]\d|304[0-8]/', $arr['film_year_str'], $match)) {
                        $temp = intval($match[0]);
                        $arr['film_year_numb'] = !empty($temp) ? $temp : null;
                    }
                }
                if (preg_match('/Страна/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '255');
                    $arr['film_country_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Режиссер/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '260');
                        $arr['film_persons_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/Жанр/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '265');
                        $arr['film_genre_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/В качестве/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '269');
                    $arr['film_quality_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/В переводе/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '273');
                    $arr['film_translation_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Возраст/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '277');
                    $arr['film_age_check_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Время/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '281');
                    $arr['film_duration_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Из серии/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '286');
                        $arr['film_collection_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/В ролях/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '291');
                        $arr['film_actors_arr'][] = !empty($temp) ? $temp : null;
                    }
            }

            $arr['film_API_arr'] = $this->get_movie_poster_by_api_themoviedb($arr['film_title'], $arr['film_orig_title']);

            if (!empty($arr['film_API_arr']))
                $arr['film_img_locale_path'] = $this->save_movie_poster_img($arr['film_API_arr']);


            $arr['film_default_urls'] = Helper::get_urls_video_preg_match($html->find('body')[0]->innertext);

            if ($html->find('ul#translators-list')[0]->plaintext != '')
                foreach ($html->find('li.b-translator__item') as $key => $translator_item) {
                    $temp = [];
                    $temp['film_title'] = Helper::check($translator_item->plaintext, '334');
                    $temp['film_id'] = Helper::check($translator_item->attr['data-id'], '335');
                    $temp['film_translator_id'] = Helper::check($translator_item->attr['data-translator_id'], '335');
                    $temp['film_camrip'] = Helper::check($translator_item->attr['data-camrip'], '335');
                    $temp['film_ads'] = Helper::check($translator_item->attr['data-ads'], '335');
                    $temp['film_director'] = Helper::check($translator_item->attr['data-director'], '335');

                    $url_ajax = (isset($GLOBALS['url_hdrezka_ajax_global']))
                        ? $GLOBALS['url_hdrezka_ajax_global']
                        : Helper::error_print('url_hdrezka_ajax_global error f640fj2');

                    $request_parameters = [
                        'action' => 'get_movie',
                        'id' => $temp['film_id'],
                        'translator_id' => $temp['film_translator_id'],
                        'is_camrip' => $temp['film_camrip'],
                        'is_ads' => $temp['film_ads'],
                        'is_director' => $temp['film_director'],
                    ];

                    $arr['film_translation_arr'][$key] = !empty($temp) ? $temp : null;
                    $response = Helper::super_duper_curl($url_ajax, $request_parameters, true, $GLOBALS['proxy_type_global'], false, true, false, '0012');
                    if (!empty($response)) {
                        if (is_array($response))
                            $arr['film_translation_arr'][$key]['urls'] = Helper::get_urls_video_preg_match($response['url']);
                    }
                    unset($temp);
                }

            return (!empty($arr) && is_array($arr)) ? $arr : 'ERROR 107';
        }
    }
}