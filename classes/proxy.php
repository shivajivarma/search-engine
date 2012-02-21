<?php 
// Define the default, system-wide context. 
$r_default_context = stream_context_get_default 
    ( 
    array 
        ( 
        'http' => array 
            ( // All HTTP requests are passed through the local NTLM proxy server on port 8080. 
            'proxy' => 'tcp://192.168.23.32:3128', 
            'request_fulluri' => True, 
            ), 
        ) 
    ); 

// Though we said system wide, some extensions need a little coaxing. 
libxml_set_streams_context($r_default_context); 
?>
