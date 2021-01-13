<?php
/**
 * Parser Films by urls
 * ---------------------------------------------------------------------------------------------------------------------
 */
function run_($line, $filename_and_hash)
{
    $parser_film_url = $GLOBALS['site_domain_global'] . $line[0];
    $result_serialize_arr = serialize((new ParserHD)->save_raw_one_films_data($parser_film_url));
    file_put_contents("{$GLOBALS['path_repo_output_data_global']}/$filename_and_hash", $result_serialize_arr . PHP_EOL, FILE_APPEND);
}

function main()
{
    $second_parser_data = [
        'pre_total_memory_bytes_global' => null,
        'time_script_run' => null,
        'count_pars_films' => null,
        'pagination' => null,
    ];
    $start_time = null;
    $this_time = null;
    $second_parser_data = (!empty($GLOBALS['result_global_end_script_pars_films_url']) ? $GLOBALS['result_global_end_script_pars_films_url'] : null);
    ($second_parser_data['pagination'] == 0) ? $second_parser_data['pagination'] = 1 : null;
    $filename_and_hash = 'FILMS_DATA_BY' . date("d-m-Y_H:i:s") . '.txt';

    $file = fopen("{$GLOBALS['path_repo_raw_data_films_urls_csv_global']}", 'r');

    if (!file_exists($GLOBALS['path_repo_raw_data_films_urls_csv_global'])) {
        Helper::error_print("
    какая то хуйня - нет файла с урлами фильмов в папке {$GLOBALS['path_repo_raw_data_films_urls_csv_global']}
    нужно розбераться так как парсер на предыдущем этапе его создавал и писал туда урлы )) короче пиздец
        
");
    } else {
        if ($file) {
            $i = 0;
            $start_time_script = microtime(true);
            while (($line = fgetcsv($file, 0, ';')) !== false) {
                ($i == 0) ? $start_time = date("H:i:s") : $this_time = date("H:i:s");
                $count_pars_movie = Helper::count_pars((!$i));
                $sec = intval(round(microtime(true) - $start_time_script, 0));
                $how_much_is_left_until_the_end = Helper::how_much_time_is_left($second_parser_data['count_pars_films'], $count_pars_movie, $sec);
                $how_much_is_left_until_the_end_text = Helper::sec_to_time($how_much_is_left_until_the_end);
                $how_much_is_left_until_the_end_2 = Helper::how_much_time_is_left_2($second_parser_data['count_pars_films'], $count_pars_movie);
                $how_much_is_left_until_the_end_2_text = Helper::sec_to_time($how_much_is_left_until_the_end_2);
                $spinner = Helper::spinner();
                $spinner_hourglass = Helper::spinner_hourglass();
                $loader = Helper::loader(($second_parser_data['count_pars_films'] < 90) ? $second_parser_data['count_pars_films'] : 90);
                $pages_were_parsed_text = Helper::num_word($second_parser_data['pagination'], ['Страница', 'Страницы', 'Страниц']);
                $spent_time_parsing_pages_text = Helper::num_word($second_parser_data['pagination'], ['Страницы', 'Страниц', 'Страниц']);
                $parsed_urls_for_movies = Helper::num_word($second_parser_data['count_pars_films'], ['урл', 'Урла', 'Урлов']);
                $count_of_the_number_of_currently_parsed_movies = Helper::num_word($count_pars_movie, ['Фильм', 'Фильма', 'Фильмов']);
                $total_memory_text = Helper::formatBytes($GLOBALS['total_memory_bytes_global'], 3);
                $pre_total_memory_text = Helper::formatBytes($second_parser_data['pre_total_memory_bytes_global'], 3);
                $execution_time_of_the_whole_script = Helper::sec_to_time($sec);

                Helper::clear();
                $message = Helper::header_print(true) . "

    Спарсилось ➤ ✅ $pages_were_parsed_text ✅
    
    Время потраченное на парсинг ➤ ✅ $spent_time_parsing_pages_text ✅ пагинаций ⌚ {$second_parser_data['time_script_run']} ⌚
    
    Всего спарсилось ➤ ✅ $parsed_urls_for_movies ✅ на фильмы
    
    Все урлы с пагинаций в файле ➤ 🚀{$GLOBALS['path_repo_raw_data_films_urls_csv_global']}🚀
    
    Скрипт парсинга урлов по пагинациям сожрал памяти ➤ ⚡ {$pre_total_memory_text} ⚡
    
    {$spinner} {$loader} {$spinner}
    
    Счетчик количества спарсенных фильмов ➤ 🔥{$spinner_hourglass} $count_of_the_number_of_currently_parsed_movies {$spinner_hourglass}🔥 из $spent_time_parsing_pages_text пагинации
                
    Время парсинга текущего фильма ➤ ⌚ $this_time ⌚
    
    Время парсинга первого фильма ➤ ⌚ $start_time ⌚
    
    Время выполнения скрипта парсинга фильмов ➤ ⌚ $execution_time_of_the_whole_script ⌚
    
    Скрипт сожрал памяти ➤ ⚡ {$total_memory_text} ⚡
    
    {$spinner}  До конца выполнения скрипта осталось ==> ⌚ $how_much_is_left_until_the_end_text ⌚  {$spinner}
    
    {$spinner}  До конца выполнения скрипта осталось ==> ⌚ $how_much_is_left_until_the_end_2_text ⌚  {$spinner}
    ";
                echo $message;
                run_($line, $filename_and_hash);
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

    $total_memory_text = Helper::formatBytes(memory_get_peak_usage() - $GLOBALS['base_memory_global'], 3);
    Helper::clear();
    echo "
✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅
░█████╗░░█████╗░███╗░░░███╗██████╗░██╗░░░░░███████╗████████╗███████╗██████╗░░░░░░░░░░░░░░░░░░░░░░░░░
██╔══██╗██╔══██╗████╗░████║██╔══██╗██║░░░░░██╔════╝╚══██╔══╝██╔════╝██╔══██╗░░░░░░░░░░░░░░░░░░░░░░░░
██║░░╚═╝██║░░██║██╔████╔██║██████╔╝██║░░░░░█████╗░░░░░██║░░░█████╗░░██║░░██║░░░░░░░░░░░░░░░░░░░░░░░░
██║░░██╗██║░░██║██║╚██╔╝██║██╔═══╝░██║░░░░░██╔══╝░░░░░██║░░░██╔══╝░░██║░░██║░░░░░░░░░░░░░░░░░░░░░░░░
╚█████╔╝╚█████╔╝██║░╚═╝░██║██║░░░░░███████╗███████╗░░░██║░░░███████╗██████╔╝░░░░░░░░░░░░░░░░░░░░░░░░
░╚════╝░░╚════╝░╚═╝░░░░░╚═╝╚═╝░░░░░╚══════╝╚══════╝░░░╚═╝░░░╚══════╝╚═════╝░░░░░░░░░░░░░░░░░░░░░░░░░
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
░██████╗██╗░░░██╗░█████╗░░█████╗░███████╗░██████╗░██████╗███████╗██╗░░░██╗██╗░░░░░██╗░░░░░██╗░░░██╗░
██╔════╝██║░░░██║██╔══██╗██╔══██╗██╔════╝██╔════╝██╔════╝██╔════╝██║░░░██║██║░░░░░██║░░░░░╚██╗░██╔╝░
╚█████╗░██║░░░██║██║░░╚═╝██║░░╚═╝█████╗░░╚█████╗░╚█████╗░█████╗░░██║░░░██║██║░░░░░██║░░░░░░╚████╔╝░░
░╚═══██╗██║░░░██║██║░░██╗██║░░██╗██╔══╝░░░╚═══██╗░╚═══██╗██╔══╝░░██║░░░██║██║░░░░░██║░░░░░░░╚██╔╝░░░
██████╔╝╚██████╔╝╚█████╔╝╚█████╔╝███████╗██████╔╝██████╔╝██║░░░░░╚██████╔╝███████╗███████╗░░░██║░░░░
╚═════╝░░╚═════╝░░╚════╝░░╚════╝░╚══════╝╚═════╝░╚═════╝░╚═╝░░░░░░╚═════╝░╚══════╝╚══════╝░░░╚═╝░░░░
✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅
✅✅ Все нахуй! скрипт отработал смотрим что там блять вышло) 
✅✅ Файл с фильмами и их данными тут /RAW-DATA/$filename_and_hash 
✅✅ Скриптом было сьедено $total_memory_text
✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅
";
    exit;
}

(true) ? main() : exit('no permission to run the script!');