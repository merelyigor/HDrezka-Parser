<?php
/**
 * Parser Serials by urls
 * ---------------------------------------------------------------------------------------------------------------------
 * @param $parser_serial_url
 * @param $filename_and_hash
 */
function run_parser_save_one_serial_data($parser_serial_url, $date_time_folder_name)
{
    # парсинг одного сериала старт *******************>
    $result_serialize_arr = serialize((new Parser)->parse_raw_one_serials_data($parser_serial_url));
    # парсинг одного сериала старт *******************>

    if (!file_exists("{$GLOBALS['path_repo_output_serials_folder_global']}/$date_time_folder_name")) {
        mkdir("{$GLOBALS['path_repo_output_serials_folder_global']}/$date_time_folder_name", 0777, true);
    }
    $path_file_serials_hash_folder = "{$GLOBALS['path_repo_output_serials_folder_global']}/$date_time_folder_name/SERIALS_DATA_BY";

    $prefix = Helper::counter_output_file_prefix();
    $path_save_output_file = "{$path_file_serials_hash_folder}__$prefix.txt";
    if (file_exists($path_save_output_file)) {
        $file_size = trim(strval(shell_exec("wc -c $path_save_output_file")));
        $file_size_bytes = trim(str_replace($path_save_output_file, '', $file_size));
        $file_size_mb = intval(round((($file_size_bytes / 1024) / 1024), 0));
        if ($file_size_mb >= $GLOBALS['max_uot_put_file_size_megabyte']) {
            $prefix = Helper::counter_output_file_prefix(true);
            $path_save_output_file = "{$path_file_serials_hash_folder}__$prefix.txt";
        }
    }

    file_put_contents($path_save_output_file, $result_serialize_arr . PHP_EOL, FILE_APPEND);
}

function main()
{
    $start_time = null;
    $this_time = null;
    $second_parser_data = (!empty($GLOBALS['result_global_end_script_pars_serials_url']) ? $GLOBALS['result_global_end_script_pars_serials_url'] : null);
    ($second_parser_data['pagination'] == 0) ? $second_parser_data['pagination'] = 1 : null;
    $date_time_folder_name = date("d-m-Y_H-i-s");

    $file = fopen("{$GLOBALS['path_repo_raw_data_urls_csv_global']}", 'r');
    if (!file_exists($GLOBALS['path_repo_raw_data_urls_csv_global'])) {
        Helper::error_print("
    какая то хуйня - нет файла с урлами сериалов в папке {$GLOBALS['path_repo_raw_data_urls_csv_global']}
    нужно розбераться так как парсер на предыдущем этапе его создавал и писал туда урлы )) короче пиздец
        
");
    } else {
        if ($file) {
            $i = 0;
            $start_time_script = microtime(true);
            while (($line = fgetcsv($file, 0, ';')) !== false) {
                $parser_serial_url = $GLOBALS['site_domain_global'] . $line[0];
                ($i == 0) ? $start_time = date("H:i:s") : $this_time = date("H:i:s");
                $count_pars_movie = Helper::count_pars((!$i));
                $sec = intval(round(microtime(true) - $start_time_script, 0));
                $how_much_is_left_until_the_end = Helper::how_much_time_is_left($second_parser_data['count_pars_serials'], $count_pars_movie, $sec);
                $how_much_is_left_until_the_end_text = Helper::sec_to_time($how_much_is_left_until_the_end);
                $how_much_is_left_until_the_end_2 = Helper::how_much_time_is_left_2($second_parser_data['count_pars_serials'], $count_pars_movie);
                $how_much_is_left_until_the_end_2_text = Helper::sec_to_time($how_much_is_left_until_the_end_2);
                $spinner = Helper::spinner();
                $spinner_shark = Helper::spinner_shark();
                $spinner_hourglass = Helper::spinner_hourglass();
                $loader = Helper::loader(($second_parser_data['count_pars_serials'] < 90) ? $second_parser_data['count_pars_serials'] : 90);
                $pages_were_parsed_text = Helper::num_word($second_parser_data['pagination'], ['Страница', 'Страницы', 'Страниц']);
                $spent_time_parsing_pages_text = Helper::num_word($second_parser_data['pagination'], ['Страницы', 'Страниц', 'Страниц']);
                $parsed_urls_for_movies = Helper::num_word($second_parser_data['count_pars_serials'], ['урл', 'Урла', 'Урлов']);
                $count_of_the_number_of_currently_parsed_movies = Helper::num_word($count_pars_movie, ['Сериал', 'Сериала', 'Сериалов']);
                $total_memory_text = Helper::formatBytes($GLOBALS['total_memory_bytes_global'], 3);
                $pre_total_memory_text = Helper::formatBytes($second_parser_data['pre_total_memory_bytes_global'], 3);
                $execution_time_of_the_whole_script = Helper::sec_to_time($sec);

                Helper::clear();
                $message = Helper::header_print(true) . "
▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂▂
∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷

    Спарсилось ➤ ✅ $pages_were_parsed_text ✅
    
    Время потраченное на парсинг ➤ ✅ $spent_time_parsing_pages_text ✅ пагинаций ⌚ {$second_parser_data['time_script_run']} ⌚
    
    Всего спарсилось ➤ ✅ $parsed_urls_for_movies ✅ на сериалы
    
    Все урлы на спарсенные сериалы в файле ➤ 🚀({$GLOBALS['path_repo_raw_data_global']}/{$GLOBALS['file_name_temporal_urls_global']})🚀
    
    Скрипт парсинга урлов по пагинациям сожрал памяти ➤ ⚡ {$pre_total_memory_text} ⚡
    
    {$spinner} {$loader} {$spinner}
    
████████████████████████████████████████████████████████████████████████████████████████████████████████████████
    
    Текущий URL в цыкле ⟾  ({$parser_serial_url})
    
    Счетчик количества спарсенных сериалов ➤ ✅🔥{$spinner_hourglass} ({$count_of_the_number_of_currently_parsed_movies} из {$second_parser_data['count_pars_serials']}) {$spinner_shark}
                
    Время парсинга текущего сериала ➤ ⌚ $this_time ⌚
    
    Время парсинга первого сериала ➤ ⌚ $start_time ⌚
    
    Время выполнения скрипта парсинга сериалов ➤ ⌚ $execution_time_of_the_whole_script ⌚
    
    Скрипт сожрал памяти ➤ ⚡ {$total_memory_text} ⚡
    
    {$spinner}  До конца выполнения скрипта осталось ==> ⌚ $how_much_is_left_until_the_end_text ⌚  {$spinner}
    
    {$spinner}  До конца выполнения скрипта осталось ==> ⌚ $how_much_is_left_until_the_end_2_text ⌚  {$spinner}
    
∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷∷
▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔
";
                echo $message;
                # парсинг  сохранение одного сериала старт *******************>
                run_parser_save_one_serial_data($parser_serial_url, $date_time_folder_name);
                # парсинг  сохранение одного сериала стоп *******************>
                $i++;
                $GLOBALS['total_memory_bytes_global'] = memory_get_peak_usage() - $GLOBALS['base_memory_global'];
            }
            fclose($file);
        } else {
            Helper::error_print('
    какая то хуйня с файлом я хз нужно смотреть скрипт на строке с ошибкой 3f84s562f82sj73j45w6
            
            ');
        }
    }

    $total_memory_text = Helper::formatBytes(memory_get_usage(true), 3);
    $total_time_run_all_script = Helper::sec_to_time(($sec + $second_parser_data['second_time_total_sec']));
    if ($GLOBALS['proxy_type_global'])
        Helper::bash('brew services stop tor');

    $successfully_message = "
🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥
Все нахуй! скрипт отработал смотрим что там блять вышло)

Спарсилось ➤ ✅ $pages_were_parsed_text ✅
    
Время потраченное на парсинг ➤ ✅ $spent_time_parsing_pages_text ✅ пагинаций ⌚ {$second_parser_data['time_script_run']} ⌚
    
Всего спарсилось ➤ ✅ $parsed_urls_for_movies ✅ на сериалы
    
Все урлы на спарсенные сериалы в файле ➤ 🚀({$GLOBALS['path_repo_raw_data_global']}/{$GLOBALS['file_name_temporal_urls_global']})🚀
    
    
    
Время парсинга последнего сериала ➤ ⌚ $this_time ⌚
    
Время парсинга первого сериала ➤ ⌚ $start_time ⌚
    
Время выполнения скрипта парсинга сериалов ➤ ⌚ $execution_time_of_the_whole_script ⌚
  


Файлы с данными всех спарсенных сериалов тут ➤ 🚀({$GLOBALS['folder_name_films_global']}/{$date_time_folder_name})🚀

Время потраченное на работу всего скрипта ➤ 🚀⌚ $total_time_run_all_script ⌚🚀
    
Всего PHP было сьедено ➤ ⚡ {$total_memory_text} ⚡

";

    Helper::send_message_from_bot_telegram($successfully_message, $GLOBALS['telegram_send_status_global']);
    Helper::successfully_header_print($successfully_message);
    exit;
}

(true) ? main() : exit('no permission to run the script!');