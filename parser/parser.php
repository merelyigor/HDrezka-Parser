<?php


class parser
{
    private function check($data, $hash)
    {
        if (!empty($data))
            return trim($data);
        else
            return "ERROR DATA {$hash}";
    }

    public function save_raw_films_data($url, $echo_execution = false)
    {
        $html = new simple_html_dom();
        $helper = new Help();
        $content_html = $helper->hack_https_content($url, true);
        $html = $html->load($content_html);
        if ($html->innertext != '' && count($html->find('div.b-content__inline_item-link'))) {
            $i = 0;
            foreach ($html->find('div.b-content__inline_item-link') as $film) {
                if ($i == 0) {
                    $starttime = date("H:i:s");
                }
                $line_text = '';
                $temp_link = $film->find('a')[0];
                $film_url_slug = preg_replace('/http:\/\/hdrezka\.tv|https:\/\/hdrezka\.website|https:\/\/hdrezka\.sh|https:\/\/rezka\.ag/', '', $temp_link->href);
                $film_name = $temp_link->plaintext;

                $line_text = "$film_url_slug;$film_name \n";
                $msg = "RUN ======> ( " . $i . " ) operations for time (" . date("H:i:s") . ")
                    ++++ the first operation time ==> ($starttime) ++++";
                if ($echo_execution)
                    echo $msg . PHP_EOL;
                file_put_contents('../RAW-DATA/films-urls-' . date("d.M.Y") . '.csv', $line_text, FILE_APPEND);
                $i++;
            }
        }
    }

    public function get_movie_poster($movie_name_ru, $movie_name_en)
    {
        $helper = new Help();
        $movie_name_ru = str_replace(' ', '%20', $movie_name_ru);
        $movie_name_en = str_replace(' ', '%20', $movie_name_en);

        $path = 'https://www.themoviedb.org/t/p/w600_and_h900_bestv2';

        $url_api_ru = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=ru-RU&query=$movie_name_ru";
        $url_api_en = "https://api.themoviedb.org/3/search/movie?api_key=39afda4f996c1aec7d5df75dab74bca0&language=en-US&query=$movie_name_en";
        $result_API = $helper->curl_API($url_api_ru);
        if (!$result_API['total_pages']) {
            $result_API = $helper->curl_API($url_api_en);
            $movie_desc = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', $result_API['results'][0]['overview']);
            $translate_desc = $helper->google_translate($movie_desc, 'en', 'ru');
            $arr['movie_desc'] = !empty($translate_desc) ? $translate_desc : null;
        } else {
            $result_API_desc = $helper->curl_API($url_api_en);
            $movie_desc = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', $result_API_desc['results'][0]['overview']);
            $translate_desc = $helper->google_translate($movie_desc, 'en', 'ru');
            $arr['movie_desc'] = !empty($translate_desc) ? $translate_desc : null;
        }

        $img_slug = $result_API['results'][0]['poster_path'];
        $arr['movie_poster_slug'] = $img_slug;
        $arr['movie_poster_hash_name'] = preg_replace('/^(\/)|(\.jpg)/', '', $img_slug);
        $arr['movie_poster_file_name'] = preg_replace('/^(\/)/', '', $img_slug);
        $arr['movie_poster_url'] = $path . $img_slug;

        return $arr;
    }

    function save_movie_poster_img($arr)
    {
        if (is_array($arr)) {
            $url = $arr['movie_poster_url'];
            $movie_poster_file_name = $arr['movie_poster_file_name'];
            $dir = '../images-all/images-films/';
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

    public function save_raw_one_films_data($url)
    {
        $html = new simple_html_dom();
        $helper = new Help();
        $content_html = $helper->hack_https_content($url, true);
        $html = $html->load($content_html);

        if ($html->innertext != '') {

            $temp_film_info = $html->find('table.b-post__info')[0];

            if ($html->find('div.b-post__title')[0]->plaintext != '')
                $arr['film_title'] = $this->check($html->find('div.b-post__title')[0]->find('h1', 0)->plaintext, '54');
            else
                $arr['film_title'] = null;

            if ($html->find('div.b-post__origtitle')[0]->plaintext != '')
                $arr['film_orig_title'] = $this->check($html->find('div.b-post__origtitle')[0]->plaintext, '54');
            else
                $arr['film_orig_title'] = null;

            if ($temp_film_info->find('span.imdb')[0]->plaintext != '')
                $arr['film_imdb_rating'] = $this->check($temp_film_info->find('span.imdb')[0]->find('span', 0)->plaintext, '55');
            else
                $arr['film_imdb_rating'] = null;

            if ($temp_film_info->find('span.kp')[0]->plaintext != '')
                $arr['film_kino_poisk_rating'] = $this->check($temp_film_info->find('span.kp')[0]->find('span', 0)->plaintext, '55');
            else
                $arr['film_kino_poisk_rating'] = null;

            $temp = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n/', '', $this->check($html->find('div.b-post__description_text')[0]->innertext, '75'));
            $arr['film_desc_str'] = !empty($temp) ? $temp : null;

            foreach ($temp_film_info->find('tr') as $item) {
                if (preg_match('/Слоган/', $item->plaintext)) {
                    $temp = preg_replace('/&laquo;|&raquo;/', '', $this->check($item->find('td', 1)->plaintext, '56'));
                    $arr['film_slogan_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/(Дата выхода)/', $item->plaintext)) {
                    $temp = $this->check($item->find('td', 1)->plaintext, '57');
                    $arr['film_year_str'] = !empty($temp) ? $temp : null;
                    if (preg_match('/181[2-9]|18[2-9]\d|19\d\d|2\d{3}|30[0-3]\d|304[0-8]/', $arr['film_year_str'], $match)) {
                        $temp = intval($match[0]);
                        $arr['film_year_numb'] = !empty($temp) ? $temp : null;
                    }
                }
                if (preg_match('/Страна/', $item->plaintext)) {
                    $temp = $this->check($item->find('td', 1)->plaintext, '58');
                    $arr['film_country_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Режиссер/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = $this->check($value->plaintext, '60');
                        $arr['film_persons_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/Жанр/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = $this->check($value->plaintext, '63');
                        $arr['film_genre_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/В качестве/', $item->plaintext)) {
                    $temp = $this->check($item->find('td', 1)->plaintext, '65');
                    $arr['film_quality_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/В переводе/', $item->plaintext)) {
                    $temp = $this->check($item->find('td', 1)->plaintext, '66');
                    $arr['film_translation_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Возраст/', $item->plaintext)) {
                    $temp = $this->check($item->find('td', 1)->plaintext, '67');
                    $arr['film_age_check_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Время/', $item->plaintext)) {
                    $temp = $this->check($item->find('td', 1)->plaintext, '67');
                    $arr['film_duration_str'] = !empty($temp) ? $temp : null;
                }
                if (preg_match('/Из серии/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = $this->check($value->plaintext, '70');
                        $arr['film_collection_arr'][] = !empty($temp) ? $temp : null;
                    }
                if (preg_match('/В ролях/', $item->plaintext))
                    foreach ($item->find('a') as $value) {
                        $temp = $this->check($value->plaintext, '73');
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

            $arr['film_API_arr'] = $this->get_movie_poster($arr['film_title'], $arr['film_orig_title']);

            if (!empty($arr['film_API_arr']['movie_poster_slug']))
                $arr['film_img_locale_path'] = $this->save_movie_poster_img($arr['film_API_arr']);

            return (!empty($arr) && is_array($arr)) ? $arr : 'ERROR 107';
        }
    }
}