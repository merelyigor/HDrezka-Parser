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

        $path = 'https://www.themoviedb.org/t/p/w600_and_h900_bestv2';

        $url_api_ru = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=ru-RU&query=$movie_name_ru";
        $url_api_en = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=en-US&query=$movie_name_en";
        $result_API = Helper::curl_API_films_themoviedb($url_api_ru);
        if (!$result_API['total_pages']) {
            $result_API = Helper::curl_API_films_themoviedb($url_api_en);
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
            $result_API_desc = Helper::curl_API_films_themoviedb($url_api_en);
            if (!$result_API_desc['total_pages'])
                return null;
            $movie_desc = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', $result_API_desc['results'][0]['overview']);
            $translate_desc = Helper::google_translate($movie_desc, 'en', 'ru');
            $arr['movie_desc'] = !empty($translate_desc) ? $translate_desc : null;

            $result_API = Helper::curl_API_films_themoviedb($url_api_ru);
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
        $content_html = Helper::hack_curl_https_content($url, $GLOBALS['proxy_type_global']);
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
                $message = Helper::header_print(true) . "
                
    Всего найдено $total_number_of_pagination_pages

    Уже спарсилось $counter_of_parsed
            
    Время парсинга текущей страницы пагинации ==> ($this_time)

    Время парсинга первой страници пагинации ==> ($start_time)";

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
        $content_html = Helper::hack_curl_https_content($url, $GLOBALS['proxy_type_global']);
        $html = $this->html->load($content_html);
        if ($html->innertext != '' && count($html->find('div.b-content__inline_item-link'))) {
            $films_arr = $html->find('div.b-content__inline_item-link');
            $spinner = Helper::spinner();
            $loader = Helper::loader();
            $pagination_text = Helper::num_word($pagination, ['Странице', 'Страницах', 'Страницах']);
            foreach ($films_arr as $key => $film) {
                $count_pars_movie = Helper::count_pars(!$key);

                $GLOBALS['count_pars_total_urls'] = $GLOBALS['count_pars_total_urls'] + 1;
                $parsed_urls_counter_text = Helper::num_word($GLOBALS['count_pars_total_urls'], ['Урл', 'Урла', 'Урлов']);
                Helper::clear();
                $message = "$pre_message

    {$spinner} {$loader} {$spinner}

    Счетчик количества спарсенных урлов ==> $parsed_urls_counter_text на {$pagination_text} пагинации
            
    Время выполнения скрипта парсинга фильмов ==> ( {$time_script_run} )

    {$spinner}  До конца выполнения скрипта осталось ==> ( {$how_much_is_left_until_the_end} )  {$spinner}

    {$spinner}  До конца выполнения скрипта осталось ==> ( {$how_much_is_left_until_the_end_2} )  {$spinner}
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
        $content_html = Helper::hack_curl_https_content($url, $GLOBALS['proxy_type_global']);
        $html = $this->html->load($content_html);

        if ($html->innertext != '') {

            $temp_film_info = $html->find('table.b-post__info')[0];

            if ($html->find('div.b-post__title')[0]->plaintext != '')
                $arr['film_title'] = Helper::check($html->find('div.b-post__title')[0]->find('h1', 0)->plaintext, '54');
            else
                $arr['film_title'] = null;

            if ($html->find('div.b-post__origtitle')[0]->plaintext != '')
                $arr['film_orig_title'] = Helper::check($html->find('div.b-post__origtitle')[0]->plaintext, '54');
            else
                $arr['film_orig_title'] = null;

            if ($temp_film_info->find('span.imdb')[0]->plaintext != '')
                $arr['film_imdb_rating'] = Helper::check($temp_film_info->find('span.imdb')[0]->find('span', 0)->plaintext, '55');
            else
                $arr['film_imdb_rating'] = null;

            if ($temp_film_info->find('span.kp')[0]->plaintext != '')
                $arr['film_kino_poisk_rating'] = Helper::check($temp_film_info->find('span.kp')[0]->find('span', 0)->plaintext, '55');
            else
                $arr['film_kino_poisk_rating'] = null;

            $temp = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', Helper::check($html->find('div.b-post__description_text')[0]->innertext, '75'));
            $arr['film_desc_str'] = !empty($temp) ? $temp : null;

            foreach ($temp_film_info->find('tr') as $item) {
                if (preg_match('/Слоган/', $item->plaintext)) {
                    $temp = preg_replace('/&laquo;|&raquo;/', '', Helper::check($item->find('td', 1)->plaintext, '56'));
                    $arr['film_slogan_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/(Дата выхода)/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '57');
                    $arr['film_year_str'] = !empty($temp) ? $temp : null;
                    if (preg_match('/181[2-9]|18[2-9]\d|19\d\d|2\d{3}|30[0-3]\d|304[0-8]/', $arr['film_year_str'], $match)) {
                        $temp = intval($match[0]);
                        $arr['film_year_numb'] = !empty($temp) ? $temp : null;
                    }
                }
                if (preg_match('/Страна/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '58');
                    $arr['film_country_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Режиссер/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '60');
                        $arr['film_persons_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/Жанр/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '63');
                        $arr['film_genre_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/В качестве/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '65');
                    $arr['film_quality_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/В переводе/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '66');
                    $arr['film_translation_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Возраст/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '67');
                    $arr['film_age_check_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Время/', $item->plaintext)) {
                    $temp = Helper::check($item->find('td', 1)->plaintext, '67');
                    $arr['film_duration_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Из серии/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '70');
                        $arr['film_collection_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/В ролях/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = Helper::check($value->plaintext, '73');
                        $arr['film_actors_arr'][] = !empty($temp) ? $temp : null;
                    }
            }

            $pattern = [
                0 => [
                    'pattern' => '/\[360p](https?.*?\.mp4):hls:manifest\.m3u8 or (https?.*?\.mp4)/',
                    'name' => '360p',
                ],
                1 => [
                    'pattern' => '/\[480p](https?.*?\.mp4):hls:manifest\.m3u8 or (https?.*?\.mp4)/',
                    'name' => '480p',
                ],
                2 => [
                    'pattern' => '/\[720p](https?.*?\.mp4):hls:manifest\.m3u8 or (https?.*?\.mp4)/',
                    'name' => '720p',
                ],
                3 => [
                    'pattern' => '/\[1080p](https?.*?\.mp4):hls:manifest\.m3u8 or (https?.*?\.mp4)/',
                    'name' => '1080p',
                ],
                4 => [
                    'pattern' => '/\[1080p Ultra](https?.*?\.mp4):hls:manifest\.m3u8 or (https?.*?\.mp4)/',
                    'name' => '1080p Ultra',
                ],
            ];

            for ($i = 0; $i < 5; $i++) {
                preg_match($pattern[$i]['pattern'], $html->find('body')[0]->innertext, $match);
                $tmp1 = (!empty($match[1])) ? trim($match[1]) : "NOT Video {$pattern[$i]['name']}";
                $tmp2 = (!empty($match[2])) ? trim($match[2]) : "NOT Video {$pattern[$i]['name']}";
                $arr['film_video_arr'][$pattern[$i]['name']][0] = str_replace('\\', '', $tmp1);
                $arr['film_video_arr'][$pattern[$i]['name']][1] = str_replace('\\', '', $tmp2);
            }

            $arr['film_API_arr'] = $this->get_movie_poster_by_api_themoviedb($arr['film_title'], $arr['film_orig_title']);

            if (!empty($arr['film_API_arr']))
                $arr['film_img_locale_path'] = $this->save_movie_poster_img($arr['film_API_arr']);

            return (!empty($arr) && is_array($arr)) ? $arr : 'ERROR 107';
        }
    }
}