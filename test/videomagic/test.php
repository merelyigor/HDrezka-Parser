<?php
system('clear');


/*
 * Title:             PlayerJS Thumbnails & WebVTT Creator
 * URI:               https://playerjs.com/docs/q=thumbnailsphpwebvtt
 * Version:           1.0
 * Author:            Playerjs.com
 * Author URI:        https://playerjs.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       playerjs
 */

//-----------------------------------------
//  Settings (require FFmpeg and FFprobe)
//-----------------------------------------

$pjs_config = [];

$pjs_config['root'] = dirname(__FILE__);

$pjs_config['FFMPEG'] = 'ffmpeg';

$pjs_config['FFPROBE'] = 'ffprobe';

$pjs_config['WIDTH'] = 160;

$pjs_config['SIZE'] = '5x5';

$pjs_config['JPG_URL'] = '//site.com/video/thumbnails/jpg/';

$pjs_config['JPG_FOLDER'] = 'jpg';

//--------------------------------------------------------
//  Use - thumbnails.php?file=video.mp4&output=video.vtt
//--------------------------------------------------------

$_GET['file'] = 'https://stream.voidboost.cc/1/6/3/0/9/3/7dd425e02cb908d227a9f4513b0cb049:2021011419/2ll0z.mp4';
$_GET['output'] = 'vtt/video.vtt';


if (isset($_GET['file']) && isset($_GET['output'])) {

    if (true) {

        $pjs_config['FILE'] = $_GET['file'];

        $pjs_config['OUTPUT'] = $pjs_config['root'] . "/" . $_GET['output'];


        if (true) {

            $filename = explode('.', $_GET['file']);

            $jpg_location = $pjs_config['root'] . "/jpg/" . $filename[0];

            if (!file_exists($jpg_location)) {
                mkdir($jpg_location, 0777, true);
            }

            $info = shell_exec($pjs_config['FFPROBE'] . " -v error -select_streams v:0 -show_entries stream=duration,width,height -of csv=p=0 " . $pjs_config['FILE']);

            $info = explode(',', $info);

            if (count($info) == 3) {

                $output = [];

                $output['duration'] = $duration = $info[2];

                $interval = 2;

                $duration > 120 && $duration <= 600 ? $interval = 5 : '';

                $duration > 600 && $duration <= 1800 ? $interval = 10 : '';

                $duration > 1800 && $duration <= 3600 ? $interval = 20 : '';

                $duration > 3600 ? $interval = 30 : '';

                $output['aspect'] = $info[0] / $info[1];

                $output['width'] = $pjs_config['WIDTH'];

                $output['height'] = round($pjs_config['WIDTH'] / $output['aspect']);

                shell_exec($pjs_config['FFMPEG'] . " -i " . $pjs_config['FILE'] . " -vsync vfr -vf 'select=isnan(prev_selected_t)+gte(t-prev_selected_t\," . $interval . "),scale=" . $output['width'] . ":" . $output['height'] . ",tile=" . $pjs_config['SIZE'] . "' -qscale:v 3 " . $jpg_location . "/img%d.jpg");

                $size = explode('x', $pjs_config['SIZE']);

                $vtt = "WEBVTT";

                $counter = 0;

                $output['images'] = ceil(($duration / $interval) / ($size[0] * $size[1]));

                for ($jpg = 1; $jpg <= $output['images']; $jpg++) {

                    for ($col = 0; $col < $size[0]; $col++) {

                        for ($row = 0; $row < $size[1]; $row++) {

                            $vtt .= "\n" . gmdate("H:i:s", $counter * $interval) . " --> " . gmdate("H:i:s", ($counter + 1) * $interval) . "\n" . $pjs_config['JPG_URL'] . $filename[0] . "/img" . $jpg . ".jpg#xywh=" . ($row * $output['width']) . "," . ($col * $output['height']) . "," . $output['width'] . "," . $output['height'] . "";
                            $counter++;

                        }
                    }
                }

                //var_dump($output);

                echo(nl2br($vtt));

                file_put_contents($pjs_config['OUTPUT'], $vtt);

            } else {

                echo("Video info not found");

            }

        } else {

            echo("File " . $_GET['file'] . " not found");

        }
    } else {

        echo("FFmpeg not found");

    }
}