<?php

/**
 * ParserHandler class
 * ---------------------------------------------------------------------------------------------------------------------
 */
class ParserHandler
{
#   Глобальные проверки информации
    private static function return_not_found($str)
    {
        return [null, $str];
    }

    private static function check_table_html($html)
    {
        $temp_film_info = $html->find('table.b-post__info')[0];
        if ($temp_film_info->innertext != '')
            return ['result' => true, 'table_html' => $temp_film_info];
        else
            return ['result' => false];
    }

#   Глобальные методы получения данных
    public static function get_title($html)
    {
        if ($html->find('div.b-post__title')[0]->plaintext != '')
            return Helper::check($html->find('div.b-post__title')[0]->find('h1', 0)->plaintext, 'div.b-post__title-219');
        else
            return self::return_not_found('div.b-post__title');
    }

    public static function get_orig_title($html)
    {
        if ($html->find('div.b-post__origtitle')[0]->plaintext != '')
            return Helper::check($html->find('div.b-post__origtitle')[0]->plaintext, 'div.b-post__origtitle-224');
        else
            return self::return_not_found('div.b-post__origtitle');
    }

    public static function get_imdb_rating($html)
    {
        $table_html = self::check_table_html($html);
        if ($table_html['result'])
            if ($table_html['table_html']->find('span.imdb')[0]->plaintext != '')
                return [
                    'rating' => Helper::check($table_html['table_html']->find('span.imdb')[0]->find('span', 0)->plaintext, 'span.imdb-229'),
                    'count' => Helper::check($table_html['table_html']->find('span.imdb')[0]->find('i', 0)->plaintext, 'span.imdb-229')
                ];
            else
                return self::return_not_found('span.imdb');
        else
            return self::return_not_found('table.b-post__info');
    }

    public static function get_kino_poisk_rating($html)
    {
        $table_html = self::check_table_html($html);
        if ($table_html['result'])
            if ($table_html['table_html']->find('span.kp')[0]->plaintext != '')
                return [
                    'rating' => Helper::check($table_html['table_html']->find('span.kp')[0]->find('span', 0)->plaintext, 'span.imdb-229'),
                    'count' => Helper::check($table_html['table_html']->find('span.kp')[0]->find('i', 0)->plaintext, 'span.imdb-229')
                ];
            else
                return self::return_not_found('span.kp');
        else
            return self::return_not_found('table.b-post__info');
    }

    public static function get_description_origin_ru($html)
    {
        if ($html->find('div.b-post__description_text')[0]->innertext != '') {
            $temp = preg_replace('/<br>|<br \/>|<\/br>|<\/ br>|\\n|\\r/', '', Helper::check($html->find('div.b-post__description_text')[0]->innertext, '238'));
            return !empty($temp) ? trim(strip_tags($temp)) : null;
        } else
            return self::return_not_found('div.b-post__description_text');
    }

    public static function get_urls_video_preg_match($html, $search_and_parsing_html = false)
    {
        static $arr = null;
        if ($search_and_parsing_html)
            $str = $html->find('body')[0]->innertext;
        else
            $str = $html;

        if ($str != '') {
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
                preg_match($pattern[$i]['pattern'], $str, $match);
                $tmp1 = (!empty($match[1])) ? trim($match[1]) : "NOT Video --{$pattern[$i]['name']}--";
                $tmp2 = (!empty($match[2])) ? trim($match[2]) : "NOT Video --{$pattern[$i]['name']}--";
                $arr['urls'][$pattern[$i]['name']][0] = str_replace('\\', '', $tmp1);
                $arr['urls'][$pattern[$i]['name']][1] = str_replace('\\', '', $tmp2);
            }
        } else
            return self::return_not_found('not default url in page');

        return $arr['urls'];
    }

# Приватные методы получения данных о записи внутри цыкла html таблицы
    private static function get_best_list_arr($table_item_html)
    {
        static $arr = null;
        if (preg_match('/Входит в списки/', $table_item_html->plaintext)) {
            if (preg_match('/(\:)|(\()/', trim(strip_tags($table_item_html->innertext)), $match1)) {
                if (count($match1) > 0) {
                    $html_best_list = explode(':', trim(strip_tags($table_item_html->innertext)));
                    if (isset($html_best_list[1])) {
                        $html_best_list = explode(')', trim(strip_tags($html_best_list[1])));
                        if (!empty($html_best_list) && is_array($html_best_list))
                            foreach ($html_best_list as $html_best_item) {
                                if (preg_match('/[\(](\d*)/', trim($html_best_item), $match2)) {
                                    if (preg_match('/(^.*) [\(]/', trim($html_best_item), $match3)) {
                                        $temp = Helper::check($match3[1], '3982');
                                        $arr[] = [
                                            'name' => (!empty($temp) ? $temp : null),
                                            'count' => (!empty($match2[1]) ? intval($match2[1]) : 99999),
                                        ];
                                    }
                                }
                            }
                    }
                }
            } else {
                foreach ($table_item_html->find('a') as $value) {
                    $temp = Helper::check($value->plaintext, '3988');
                    $arr[] = [
                        'name' => (!empty($temp) ? $temp : null),
                        'count' => 99999,
                    ];
                }
            }
        }
        return $arr;
    }

    private static function get_slogan_str($table_item_html)
    {
        static $str = null;
        if (preg_match('/Слоган/', $table_item_html->plaintext)) {
            $temp = preg_replace('/&laquo;|&raquo;/', '', Helper::check($table_item_html->find('td', 1)->plaintext, '243'));
            $str = !empty($temp) ? $temp : null;
        }
        return $str;
    }

    private static function get_year_arr($table_item_html)
    {
        static $arr = null;
        if (preg_match('/(Дата выхода)/', $table_item_html->plaintext)) {
            $temp = Helper::check($table_item_html->find('td', 1)->plaintext, '247');
            $arr['year_str'] = !empty($temp) ? $temp : null;
            if (preg_match('/([1]\d{3})|([2]\d{3})|([3]\d{3})/', $arr['year_str'], $match)) {
                $temp = intval($match[0]);
                $arr['year_numb'] = !empty($temp) ? $temp : null;
            }
        }
        return $arr;
    }

    private static function get_country_any($table_item_html)
    {
        static $any = null;
        if (preg_match('/Страна/', $table_item_html->plaintext)) {
            $temp_str = $table_item_html->find('td', 1)->plaintext;
            if (preg_match('/([,])/', $temp_str)) {
                $temp_country_arr = explode(',', trim($temp_str));
                $country_arr = [];
                foreach ($temp_country_arr as $country_str) {
                    $country_arr[] = trim($country_str);
                }
                $any = (!empty($country_arr) && is_array($country_arr)) ? $country_arr : null;
            } else {
                $temp = Helper::check($table_item_html->find('td', 1)->plaintext, '255');
                $any[] = !empty($temp) ? trim($temp) : null;
            }
        }
        return $any;
    }

    private static function get_persons_arr($table_item_html)
    {
        static $arr = null;
        if (preg_match('/Режиссер/', $table_item_html->plaintext))
            foreach ($table_item_html->find('a') as $value) {
                $temp = Helper::check($value->plaintext, '260');
                $arr[] = !empty($temp) ? $temp : null;
            }
        return $arr;
    }

    private static function get_genre_arr($table_item_html)
    {
        static $arr = null;
        if (preg_match('/Жанр/', $table_item_html->plaintext))
            foreach ($table_item_html->find('a') as $value) {
                $temp = Helper::check($value->plaintext, '265');
                $arr[] = !empty($temp) ? $temp : null;
            }
        return $arr;
    }

    private static function get_quality_str($table_item_html)
    {
        static $str = null;
        if (preg_match('/В качестве/', $table_item_html->plaintext)) {
            $temp = Helper::check($table_item_html->find('td', 1)->plaintext, '269');
            $str = !empty($temp) ? $temp : null;
        }
        return $str;
    }

    private static function get_translation_str($table_item_html)
    {
        static $str = null;
        if (preg_match('/В переводе/', $table_item_html->plaintext)) {
            $temp = Helper::check($table_item_html->find('td', 1)->plaintext, '273');
            $str = !empty($temp) ? $temp : null;
        }
        return $str;
    }

    private static function get_age_str($table_item_html)
    {
        static $str = null;
        if (preg_match('/Возраст/', $table_item_html->plaintext)) {
            $temp = Helper::check($table_item_html->find('td', 1)->plaintext, '277');
            $str = !empty($temp) ? $temp : null;
        }
        return $str;
    }

    private static function get_duration_str($table_item_html)
    {
        static $str = null;
        if (preg_match('/Время/', $table_item_html->plaintext)) {
            $temp = Helper::check($table_item_html->find('td', 1)->plaintext, '281');
            $str = !empty($temp) ? $temp : null;
        }
        return $str;
    }

    private static function get_collection_arr($table_item_html)
    {
        static $arr = null;
        if (preg_match('/Из серии/', $table_item_html->plaintext))
            foreach ($table_item_html->find('a') as $value) {
                $temp = Helper::check($value->plaintext, '286');
                $arr[] = !empty($temp) ? $temp : null;
            }
        return $arr;
    }

    private static function get_actors_arr($table_item_html)
    {
        static $arr = null;
        if (preg_match('/В ролях/', $table_item_html->plaintext))
            foreach ($table_item_html->find('a') as $value) {
                $temp = Helper::check($value->plaintext, '291');
                $arr[] = !empty($temp) ? $temp : null;
            }
        return $arr;
    }

    private static function get_serials_urls_arr($serial_id, $translator_id, $season_counter, $translator_title, $series_counter)
    {
        $return_series_urls = null;
        $url_ajax = (isset($GLOBALS['url_hdrezka_ajax_global']))
            ? "{$GLOBALS['site_domain_global']}/{$GLOBALS['url_hdrezka_ajax_global']}/"
            : Helper::error_print('url_hdrezka_ajax_global error f54d0fx2');

        $request_parameters = [
            'action' => 'get_stream',
            'id' => $serial_id,
            'translator_id' => $translator_id,
            'season' => $season_counter,
            'episode' => $series_counter,
        ];

        Helper::show_info_serials_parsing($translator_title, $season_counter, $series_counter);
        $response = Helper::super_duper_curl($url_ajax, $request_parameters, true, $GLOBALS['proxy_type_global'], false, true, false, '0026');

        if ($response != false && $response['success']) {
            $return_series_urls = self::get_urls_video_preg_match($response['url']);
        }
        return $return_series_urls;
    }

    private static function get_serials_arr($serial_id, $translator_id, $season_counter, $translator_title, $season)
    {
        $return_series = null;
        foreach ($season->find('li') as $series_counter => $series) {
            ++$series_counter;
            Helper::show_info_serials_parsing($translator_title, $season_counter, $series_counter);
            $series_urls = self::get_serials_urls_arr($serial_id, $translator_id, $season_counter, $translator_title, $series_counter);
            $return_series[$series_counter] = [
                'name' => $series->plaintext,
                'urls' => $series_urls
            ];
        }
        return $return_series;
    }

    private static function get_seasons_and_serials_count_arr($serial_id, $translator_id, $translator_title)
    {
        $return_seasons = null;
        $url_ajax = (isset($GLOBALS['url_hdrezka_ajax_global']))
            ? "{$GLOBALS['site_domain_global']}/{$GLOBALS['url_hdrezka_ajax_global']}/"
            : Helper::error_print('url_hdrezka_ajax_global error f614dn0fj2');

        $request_parameters = [
            'action' => 'get_episodes',
            'id' => $serial_id,
            'translator_id' => $translator_id,
        ];

        $response = Helper::super_duper_curl($url_ajax, $request_parameters, true, $GLOBALS['proxy_type_global'], false, true, false, '0026');

        if ($response != false && $response['success']) {
            $episodes = (new simple_html_dom())->load($response['episodes']);

            foreach ($episodes->find('ul') as $season_counter => $season) {
                ++$season_counter;
                Helper::show_info_serials_parsing($translator_title, $season_counter);
                $serials = self::get_serials_arr($serial_id, $translator_id, $season_counter, $translator_title, $season);
                $return_seasons[$season_counter] = [
                    'season' => "Сезон {$season_counter}",
                    'serials' => $serials
                ];
            }
        }
        return $return_seasons;
    }

#   Глобальный метод получения массива информации о записи
    public static function get_movie_information_array($html)
    {
        $table_html = self::check_table_html($html);
        $return_arr = [];

        if ($table_html['result']) {
            foreach ($table_html['table_html']->find('tr') as $table_item_html) {

                $return_arr['best_list_arr'] = self::get_best_list_arr($table_item_html);

                $return_arr['slogan_str'] = self::get_slogan_str($table_item_html);

                $return_arr['year_arr'] = self::get_year_arr($table_item_html);

                $return_arr['country_arr'] = self::get_country_any($table_item_html);

                $return_arr['persons_arr'] = self::get_persons_arr($table_item_html);

                $return_arr['genre_arr'] = self::get_genre_arr($table_item_html);

                $return_arr['quality_str'] = self::get_quality_str($table_item_html);

                $return_arr['translation_str'] = self::get_translation_str($table_item_html);

                $return_arr['age_str'] = self::get_age_str($table_item_html);

                $return_arr['duration_str'] = self::get_duration_str($table_item_html);

                $return_arr['collection_arr'] = self::get_collection_arr($table_item_html);

                $return_arr['actors_arr'] = self::get_actors_arr($table_item_html);
            }
            return $return_arr;
        } else
            return self::return_not_found('table.b-post__info');
    }

#   Глобальный метод получения массива ссылок с переводами
    public static function get_film_translators_list_array($html)
    {
        $return_arr = [];

        if ($html->find('ul#translators-list')[0]->plaintext != '') {
            foreach ($html->find('li.b-translator__item') as $key => $translator_item) {
                $temp = [];
                $temp['film_title'] = Helper::check($translator_item->plaintext, '334');
                $temp['film_id'] = Helper::check($translator_item->attr['data-id'], '335');
                $temp['film_translator_id'] = Helper::check($translator_item->attr['data-translator_id'], '335');
                $temp['film_camrip'] = Helper::check($translator_item->attr['data-camrip'], '335');
                $temp['film_ads'] = Helper::check($translator_item->attr['data-ads'], '335');
                $temp['film_director'] = Helper::check($translator_item->attr['data-director'], '335');

                $url_ajax = (isset($GLOBALS['url_hdrezka_ajax_global']))
                    ? "{$GLOBALS['site_domain_global']}/{$GLOBALS['url_hdrezka_ajax_global']}/"
                    : Helper::error_print('url_hdrezka_ajax_global error f640fj2');

                $request_parameters = [
                    'action' => 'get_movie',
                    'id' => $temp['film_id'],
                    'translator_id' => $temp['film_translator_id'],
                    'is_camrip' => $temp['film_camrip'],
                    'is_ads' => $temp['film_ads'],
                    'is_director' => $temp['film_director'],
                ];

                $return_arr[$key] = !empty($temp) ? $temp : null;

                $response = Helper::super_duper_curl($url_ajax, $request_parameters, true, $GLOBALS['proxy_type_global'], false, true, false, '0012');
                if (!empty($response)) {
                    if (is_array($response))
                        $return_arr[$key]['urls'] = self::get_urls_video_preg_match($response['url'], false);
                }
                unset($temp);
            }
            return $return_arr;
        } else
            return self::return_not_found('ul#translators-list');
    }

#   Глобальный метод получения массива ссылок с переводами
    public static function get_serial_translators_list_array($html)
    {
        $return_arr = [];
        $url = $html->find('div.b-post')[0]->find('meta', 0)->attr['content'];
        if (preg_match('/[\/](\d{1,99})[-]/', $url, $temp_match)) {
            $return_arr['serial_id'] = intval($temp_match[1]);
        } else {
            $return_arr['serial_id'] = intval($html->find('a.show-trailer')[0]->attr['data-id']);
        }

        if ($html->find('ul#translators-list')[0]->plaintext != '') {
            foreach ($html->find('li.b-translator__item') as $translate_counter => $translator_item) {
                ++$translate_counter;
                $translator_id = intval($translator_item->attr['data-translator_id']);
                $translator_title = $translator_item->plaintext;
                Helper::show_info_serials_parsing($translator_title);
                $seasons = self::get_seasons_and_serials_count_arr($return_arr['serial_id'], $translator_id, $translator_title);
                $return_arr['translators'][++$translate_counter] = [
                    'translator_title' => $translator_title,
                    'translator_id' => $translator_id,
                    'seasons' => $seasons,
                ];
            }
            return $return_arr;
        } else {
            $translator_id = 8;
            $translator_title = 'default translator';
            Helper::show_info_serials_parsing($translator_title);
            $seasons = self::get_seasons_and_serials_count_arr($return_arr['serial_id'], $translator_id, $translator_title);
            $return_arr['translators'][] = [
                'translator_title' => $translator_title,
                'translator_id' => $translator_id,
                'seasons' => $seasons,
            ];
        }
        return self::return_not_found('ul#translators-list');
    }
}
















