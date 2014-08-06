<?php
include 'lib/shortcodes.api.php';
include 'lib/gathercontent.api.php';
include 'shortcodes.mapping.php';


$jsondir = dirname ( __FILE__ ) . '/output/json';
$output = dirname( __FILE__ ). '/output/html/body';

if (!file_exists(dirname ( __FILE__ ) . '/output/html')) {
    mkdir(dirname ( __FILE__ ) . '/output/html', 0777, true);
    mkdir(dirname ( __FILE__ ) . '/output/html/body', 0777, true);
}


function parseContent($content){

    // Regex: http://regex101.com/r/bP2aY2

    $pattern = '/(?(?=[<p>])<p>|)(?:\n\t|)\s*(\[[a-z|A-Z|-|\/|\\|\s|\"|\=]*\])\s*(?(?=<\/\S>)<\/\S>|)/';    

    $content = html_entity_decode($content);

    $replaced = preg_replace($pattern, '$1', $content);

    /* Adds class name to table */

    $tablePattern = '/\<table/';

    $replaced = preg_replace($tablePattern, '<table class="table table--bordered"',$replaced);

    /* Removes inline styles */

    $replaced = stripStyles($replaced);
    
    /* Does short code replace */

    return do_shortcode($replaced);

}

/**
 * Strip Style tags
 * Removes Inline styles
 */

function stripStyles($content){    

    /* Remove inline style */
    $text = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $content);

    /* Replace ” to " */

    $text = preg_replace('/\”/i', '"', $text);    

    return $text;

}



if (is_dir($jsondir)) {

    if ($dh = opendir($jsondir)) {
        while (($file = readdir($dh)) !== false) {
            if($file == "." || $file == ".."){continue;} 

            $filename = explode(".",$file); //seperate filename from extenstion
            $cnt = count($filename); $cnt--; $ext = $filename[$cnt]; //as above

            if(strtolower($ext) == 'json'){

                $content = file_get_contents($jsondir.'/'.$file);

                $page = json_decode($content);

                $config = json_decode(base64_decode($page->config));


                $id = $page->id;
                

                foreach($config as $c){

                    if($c->label == 'Content'){

                        $elements = $c->elements;

                        foreach($elements as $e){
                            
                            
                            if($e->label == "Body" && $e->value != ""){
                                
                                $content = parseContent($e->value);

                                $file = fopen($output.'/'.$id.'.html', "w");

                                fwrite($file, $content);

                                fclose($file);
                            }
                            
                        }
                    }
                }


            }
            
        }
        closedir($dh);

        echo  'done';
    }
}



?>
