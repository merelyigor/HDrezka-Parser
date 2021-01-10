<?php
/**
 *
 * CSV file input
 * ---------------------------------------------------------------------------------------------------------------------
 */

define('ENABLE_SCRIPT', true); // true to make the script work and false to disable the script
include 'helper.php';
include 'parser.php';


$temp = 'http://hdrezka.tv';
$temp = 'https://rezka.ag';
$temp = 'https://hdrezka.sh';
$temp = 'https://hdrezka.website';

$parser_films_url = "$temp/films";


function get_films_data($url, $args)
{
    /**---- main function of starting and working with csv ---**/
    # connect the csv file to the specified path
    $main = new parser();
    $html = new simple_html_dom();
    $helper = new Help();
    $content_html = $helper->hack_https_content($url, true);
    $html = $html->load($content_html);


    if (!empty($args[1]) && intval($args[1]) > 0)
        $max_num_pages = intval($args[1]);
    else
        $max_num_pages = $helper->last_max_page_pagination_hack($html->find('div.b-navigation')[0]->plaintext);


    if ($max_num_pages === 0) {
        $main->save_raw_films_data($url, true);
    } elseif ($max_num_pages > 0) {
        $i = 0;
        for ($I = 1; $I <= $max_num_pages; $I++) {
            if ($i == 0) {
                $starttime = date("H:i:s");
            }
            $msg = "RUN ======> ( " . $i . " ) operations for time (" . date("H:i:s") . ")
            ++++ the first operation time ==> ($starttime) ++++";
            echo $msg . PHP_EOL;
            $main->save_raw_films_data($url . "/page/{$I}/", false);
            $i++;
        }
    }
}

(ENABLE_SCRIPT) ? get_films_data($parser_films_url, $argv) : null; # checking script startup or shutdown