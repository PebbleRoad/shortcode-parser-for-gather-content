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

/**
 * Convert content to UTF 8
 * @param  [type] $source          [description]
 * @param  [type] $target_encoding [description]
 * @return [type]                  [description]
 */
function convert_to ( $source, $target_encoding )
{
    
    $encoding = mb_detect_encoding( $source, "auto" );
       
    $target = str_replace( "?", "[question_mark]", $source );
       
    $target = mb_convert_encoding( $target, $target_encoding, $encoding);
           
    $target = str_replace( "?", "", $target );
           
    $target = str_replace( "[question_mark]", "?", $target );

    return $target;
}


/**
 * Parse content
 */

function parseContent($content){

    $content = convert_to($content, 'UTF-8');
    
    $content = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $content);

    $content = preg_replace('/(?:\^A-|\^A|\^a)/', '', $content);

    /* Adds class name to table */

    $tablePattern = '/\<table/';

    $content = preg_replace($tablePattern, '<table class="table table--bordered"',$content);

    /* Removes inline styles */

    $content = stripStyles($content);

    /* Optional if you have a link table */

    //$content = replaceLinks($content);
    

    // Regex: http://regex101.com/r/bP2aY2

    $pattern = '/(?(?=[<p>])<p>|)(?:\n\t|)\s*(\[.*?(?<=\]))\s*(?(?=<\/\S>)<\/\S>|)/';

    $content = preg_replace($pattern, '$1', $content);

    return do_shortcode($content);

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
    $text = preg_replace('/\’/i', "'", $text);
    $text = preg_replace('/&nbsp;/i', "'", $text);

    return $text;

}

/**
 * Get all files from JSON Directory
 */
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

                $title = $page->name;

                $id = $page->id;

                foreach($config as $c){                    

                    if($c->label == 'Content'){

                        $elements = $c->elements;

                        foreach($elements as $e){
                            
                            if($e->label == "Summary"){
                                
                                $summary = strip_tags($e->value);

                            }
                            
                            if($e->label == "Body" && $e->value != ""){
                                
                                $content = '<h1>'.$title.'</h1><p class="text--lead">'.replaceLinks($summary).'</p>'.parseContent($e->value);

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
        
    }
}



?>
