<?php


    include 'lib/shortcodes.api.php';
    include 'lib/gathercontent.api.php';    
    include 'config.php';
    include 'shortcodes.mapping.php';

    /**
     * Check for API URL, KEY and Project ID
     */
    
    try{
        if(!API_URL || !API_KEY || !PROJECT_ID){
            throw new Exception("Did you forget to add API url, key or project ID in config.php!");
        }
    }catch(Exception $e) {
        throw $e;
    }


    /**
     * Proceed !!
     */

    if (!file_exists(dirname ( __FILE__ ) . '/output/json')) {
        mkdir(dirname ( __FILE__ ) . '/output/json', 0777, true);
    }

    $path = dirname ( __FILE__ ) . '/output/json';

    $api = new PR_GatherContent();

    $response = $api->request('get_pages_by_project', array('id'=>PROJECT_ID));

    $data = $response;
        
    $obj = json_decode(json_encode($data));

    $pages = $obj->pages;

    foreach($pages as $page){

        $config = json_decode(base64_decode($page->config));
        

        $id = $page->id;
        
        $content = json_encode($page);

        $file = fopen($path.'/'.$id.'.json', "w");

        fwrite($file, $content);

        fclose($file);        

    };
    
    echo 'refreshed';
?>