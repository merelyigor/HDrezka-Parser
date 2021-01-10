<?php
/**
 *
 * CSV file input
 * ---------------------------------------------------------------------------------------------------------------------
 */

define('ENABLE_SCRIPT', true); // true to make the script work and false to disable the script

function run_($line)
{
    var_dump(unserialize($line));
//    exit();
}

function main($args)
{
    /**---- main function of starting and working with csv ---**/
    # connect the csv file to the specified path
    $csv_file = fopen("{$args[1]}", 'r');

    if (!isset($args[1])) {
        $msg = 'input file not exist ERROR !!! argument(1)
        specify the file in the second line after the 
        function is launched as an argument 
        (the path to the file relative to the current script directory)';
        echo $msg . PHP_EOL;
        exit($msg);
    } else {
        if ($csv_file) {
            // I form an array from a csv file
            $i = 0;
            while (($line = fgets($csv_file)) !== false) {
                if ($i == 0) {
                    $starttime = date("H:i:s");
                }
                /**---- logic is running ---**/
                run_($line);

                $msg = "RUN ======> ( " . $i . " ) operations for time (" . date("H:i:s") . ")
                ++++ the first operation time ==> ($starttime) ++++";
                echo $msg . PHP_EOL;
                $i++;
            }
            fclose($csv_file);
        } else {
            $msg = "input file not found ERROR !!! does not exist on the path argument(1) ({$args[1]})";
            echo $msg . PHP_EOL;
            exit($msg);
        }
    }
    exit('The script completed successfully - the end of the logging of this script execution'); # end of script execution
}

(ENABLE_SCRIPT) ? main($argv) : null; # checking script startup or shutdown