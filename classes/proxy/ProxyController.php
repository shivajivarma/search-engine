<?php

include_once(dirname(__DIR__) . '\constants.php');

class ProxyController
{


    function __construct()
    {
    }

    function getSettings()
    {
        $response = new stdClass();

        $response->proxy = PROXY;
        $response->proxyIP = PROXY_IP;
        $response->proxyPort = PROXY_PORT;


        header('Content-type: text/json');
        echo json_encode($response);
    }


    function setSettings()
    {
        if( isset($_GET['proxy']) && isset($_GET['proxyIP']) && isset($_GET['proxyPort'])){

            $source='../classes/constants.php';
            $target='out.txt';

            $file=fopen($source, 'r') or exit("Unable to open file!");
            $th=fopen($target, 'w');

            while(!feof($file))
            {
                $str = fgets($file);
                if (strpos($str, "'PROXY'")!==false) {
                    $str = "define('PROXY',".$_GET['proxy'].");\n";
                }
                elseif (strpos($str, "'PROXY_IP'")!==false) {
                    $str = "define('PROXY_IP','".$_GET['proxyIP']."');\n";
                }
                elseif (strpos($str, "'PROXY_PORT'")!==false) {
                    $str = "define('PROXY_PORT','".$_GET['proxyPort']."');\n";
                }

                fwrite($th, $str);

            }
            fclose($file);
            fclose($th);

            // delete old source file
            if(unlink($source));
            // rename target file to source file
            rename($target, $source);

            $response = true;
            header('Content-type: text/json');
            echo json_encode($response);
        }
    }







}

?>
