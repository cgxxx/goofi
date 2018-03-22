<?php
header("Content-Type: text/css");

$servername=($_SERVER["HTTP_X_FORWARDED_HOST"]=='')?"{$_SERVER["SERVER_NAME"]}":"{$_SERVER["HTTP_X_FORWARDED_HOST"]}";
define('SERVER_NAME',$servername);

$font_folder='fonts';
$css_folder='font_faces';
$useragent=$_SERVER['HTTP_USER_AGENT'];

if (!empty($_GET['family']))
        {
        if (!is_dir($font_folder)) {mkdir($font_folder);}
        if (!is_dir($css_folder)) {mkdir($css_folder);}

        $family=strip_tags($_GET['family']);
        $css_filename=md5($useragent.$family).'.css';
        $css_file_url='http://fonts.googleapis.com/css?family='.urlencode($family);

        if (!is_file($css_folder."/".$css_filename))
                {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $css_file_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                $css = curl_exec ($ch);
                curl_close ($ch);

                preg_match_all("#font-family: '(?P<name>[^']+)[^^]*?url\((?P<url>[^\)]+)\)#", $css, $urls);
                foreach($urls['url'] as $nb=>$url)
                        {
                        $font_file=$urls['name'][$nb].basename($url);

                        if (!is_file($font_folder.'/'.$font_file))
                                {
                                $font=file_get_contents($url);
                                file_put_contents($font_folder.'/'.$font_file, $font);
                                }

                        $css=str_replace($url,"https://".SERVER_NAME."/".$font_folder.'/'.$font_file,$css);
                        }

                file_put_contents($css_folder."/".$css_filename,$css);
                }
        else
                $css=file_get_contents($css_folder."/".$css_filename);

        exit($css);
        }
?>
