<?php
    
    /**
     * Links
     */

    $links_json = file_get_contents(dirname ( __FILE__ ).'/linkmap.json');
    $links = json_decode($links_json);

    $links_json_external = file_get_contents(dirname ( __FILE__ ).'/linkmap.external.json');
    $links_external = json_decode($links_json_external);

        
    /**
     * Search in JSON     
     */

    /**
     * Get page link from ID
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    function getLink($id){

        global $links;
        $ex = explode('#', $id);

        $id = $ex[1]? $ex[0]: $id;
        $anc = $ex[1]? '#'.$ex[1]: '';


        
        foreach($links as $l){
            if($l->id == $id){                     
                return $l->url.$anc;
            }            
        }

        return false;

    }

    /**
     * Get page title from ID
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    function getTitle($id){

        global $links;
        
        foreach($links as $l){
            if($l->id == $id){
                return $l->title;
            }            
        }

        return false;

    }
    /**
     * Get page details from ID
     * @param  [type] $id   [description]
     * @param  [type] $item [description]
     * @return [type]       [description]
     */
    function getPageItem($id, $item){
        global $links;
        
        foreach($links as $l){
            if($l->id == $id){
                return $l->$item;
            }            
        }

        return false;
    }
    /**
     * Get immediate children of a page
     * @param  [type] $parent  [description]
     * @param  [type] $exclude [description]
     * @return [type]          [description]
     */
    function getChildren($parent, $exclude){
        global $links;

        $pages = array();
        
        foreach($links as $l){
            if($l->parent == $parent && !in_array($l->id, $exclude)){
                array_push($pages, $l);
            }            
        }

        return $pages;
    }

    /**
     * Recursively get all children of a page
     * @var array
     */
    $children_pages = array();

    function getAllChildrenRecursive($parent, $exclude){

        global $links, $children_pages;

        $children = getChildren($parent, $exclude);

        foreach($children as $c){

            array_push($children_pages, $c->id);

            getAllChildrenRecursive($c->id, $exclude);

        }

        return $children_pages;
    }

    /**
     * Get short title of a page
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    function getShortTitle($id){

        global $links;
        
        foreach($links as $l){
            if($l->id == $id){
                return $l->short_title;
            }            
        }

        return false;

    }

    function getExternalLink($page){
        
        global $links_external;

        $page = trim($page);
        
        foreach($links_external as $l){
            if($l->page == $page){
                return $l->link;
            }            
        }

        return false;

    }

    /**
     * Get page segment from ID
     * @param  [type] $segment [description]
     * @return [type]          [description]
     */
    function getPageIdFromSegment($segment){
        global $links;

        foreach($links as $l){
            if($l->url == $segment.'.html'){
                return $l->id;
            }            
        }

        return false;
    }

    /**
     * Get page title from segment
     * @param  [type] $segment [description]
     * @return [type]          [description]
     */
    function getPageTitleFromSegment($segment){

        $id = getPageIdFromSegment($segment);
        if($id){
            return getShortTitle($id);
        }

        return false;

    }

    /**
     * get page template from ID
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    function getTemplateType($id){

        global $links;

        foreach($links as $l){

            if($l->id == $id){
                return $l->template;
            }
        }

        return false;
    }

    function pr_sanitize($string) {
        //Lower case everything
        $string = strtolower($string);
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }


    function toggle_func($attrs, $content){
        // Attributes
        extract( shortcode_atts(
            array(
                'type' => 'body'
            ), $attrs)
        );

        $class = '';

        if($type == "header"){
            $class = "toggle-enhanced";
        }

        return "<div class=\"ui-toggle ".$class."\">".do_shortcode($content)."</div>";
    }
    
    function accordion_func($attrs, $content) {    
        return "<div class=\"ui-accordion\">".do_shortcode($content)."</div>";
    }

    /**
     * Accordion
     */

    function panel_func($attrs, $content){

        // Attributes
        extract( shortcode_atts(
            array(
                'title' => ''
            ), $attrs)
        );

        return '<h2 class="toggle__header" id="'.pr_sanitize($title).'"><span>'.$title.'</span><a href="#" class="toggle__link"><span>Show</span></a></h2><div class="toggle__content">'.do_shortcode($content).'</div>';
    }

    /**
     * Alerts
     * @param  [type] $attrs   [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    function highlight_func($attrs, $content){

        // Attributes
        extract( shortcode_atts(
            array(
                'type' => 'note'
            ), $attrs)
        );

        return "<div class=\"alert alert--".$type."\">".$content."</div>";

    }

    /**
     * PassMap
     */
    /* -------------------------- */
    /* -------------------------- */

    function passmap_func($attrs, $content){

        $content = preg_replace('/(\<br\s\/\>)/i', ' ', $content);
        return '<p><a class="btn btn--primary float--right js-tour hide-for-phone">Explain this map to me</a></p><ul class="pass-map">'.do_shortcode($content).'</ul>';
    }

    function passmap_step_func($attrs, $content){

        // Attributes
        extract( shortcode_atts(
            array(
                'title' => ''
            ), $attrs)
        );

        return '<li><span>'.$title.'</span><ul>'.do_shortcode($content).'</ul></li>';
    }

    $count = 0;    
    function passmap_step_task_func($attrs, $content){
        static $count=0;
        $count++;
        
        // Attributes
        extract( shortcode_atts(
            array(
                'title' => '',
                'link' => ''
            ), $attrs)
        );

        return '<li data-step="'.$count.'" data-intro="'.$content.'"><a href="'.getLink($link).'">'.$title.'</a></li>';
    }
    /* -------------------------- */
    /* -------------------------- */


    function next_func($attrs, $content){

        //global $links;

        // Attributes
        extract( shortcode_atts(
            array(
                'page' => ''
            ), $attrs)
        );
        
        return '<div class="section-next"><span class="next-title gamma">What\'s next</span><p><a href="'.getLink($page).'"><em class="fa fa-angle-right"></em>'.getShortTitle($page).'</a></p></div>';
    }


    function tabList_func($attrs, $content){

        // Attributes
        extract( shortcode_atts(
            array(
                'tabs' => ''
            ), $attrs)
        );

        if(isset($tabs)){

            $nav= '<nav>';
            $tabs_exploded = explode('|', $tabs);

            foreach($tabs_exploded as $t){
                $t = trim($t);
                $nav.='<a href="#tab-'.pr_sanitize($t).'" class="tab__handle">'.$t.'</a>';
                
            }

            $nav.='</nav>';
        }else{
            $nav = '';
        }




        return '<div class="ui-tabs">'.$nav.do_shortcode($content).'</div>';
    }
   

        /**
         * Individual tab
         */
        
        function tab_func($attrs, $content){
            
            // Attributes
            extract( shortcode_atts(
                array(
                    'title' => '',
                    'heading' => ''
                ), $attrs)
            );


            if($title == "eServices"){
                /* List block */
                $content = preg_replace('/\<ul\>/', '<ul class="list--block">', $content);

                /* remove line breaks */

                $content = preg_replace('/\<br \/\>/', '', $content);

                /* Add link */

                $content = preg_replace('/\<a\s{1}/', '<'.$heading.' class="delta"><a ', $content);
                $content = preg_replace('/\<\/a>/', '</'.$heading.'></a>', $content);

                /* Add para */

                $content = preg_replace('/\<\/a>(.*)\<\/li\>/', '<p>$1</p></li>', $content);

            }


            return '<div class="tab" id="tab-'.pr_sanitize($title).'">'.do_shortcode($content).'</div>';
        }

    /**
     * Form list
     */
    
    function formList_func($attrs, $content){

        return '<ul class="list--block">'.do_shortcode($content).'</ul>';
    }

        /* Form item */

        function formItem_func($attrs, $content){

            // Attributes
            extract( shortcode_atts(
                array(
                    'link' => '',
                    'name' => ''
                ), $attrs)
            );

            return '<li><div class="row"><div class="columns eight"><h3 class="delta">'.$name.'</h3><p>'.$content.'</p></div><div class="columns four"><a href="'.$link.'" class="btn btn--primary">Download</a></div></div></li>';

        }


    /**
     * Checkbox
     */
    
    
    $filter_c = 0;$filter_title='';

    function checkbox_func($attrs, $content){
        global $filter_count, $filter_c, $filter_title;
        
        $filter_c++; 

        
        extract( shortcode_atts(
            array(
                'id' => '',
                'label' => '',
                'checked' => false
            ), $attrs)
        );

        return ($filter_c == 1? '<div class="panel panel--criteria"><div class="panel__body">'.$filter_title.'<fieldset class="js-criteria">':'').'<label class="label-checkbox"><input type="checkbox" name="filter-'.$id.'" checked="'.($checked?"checked": "").'">'.$label.'</label>'.($filter_c == $filter_count? '</fieldset></div></div>':'');

    }

    /**
     * Filter
     */
    
    function filter_func($attrs, $content){
        global $filter_count, $filter_c, $filter_title;
        
        $filter_c = 0;
        
        preg_match_all('/(\[checkbox.*?(?<=\]))/', $content, $matches);

        preg_match('/(.*?(?=\[))/s', $content, $filter_title_match);

        $content = preg_replace('/(.*?(?=\[))/s', '',$content, 1);

        $filter_count = count($matches[1]);

        $filter_title = $filter_title_match[1];        

        return '<div class="ui-filter">'.do_shortcode($content).'</div>';

    }

    /**
     * Filter: filterContent
     */
    
    function filterContent_func($attrs, $content){

        extract( shortcode_atts(
            array(
                'id' => '',
                'type' => ''                
            ), $attrs)
        );

        $classnames = explode('|', $id);
        $class_array = array();
        
        foreach($classnames as &$c){

            //$c = 'filter-'.$c;

            $cex = explode('&amp;', $c);
            
            foreach($cex as &$x){
                $x = 'filter-'.$x;
            }
            array_push($class_array, implode('_', $cex));

        }
        
        $final_class = implode(' ', $class_array);

        if($type == "result"){

            /**
             * Seperate result
             */
            
            // Get the last sentence
            preg_match('/.*?(?<=\.)\s((?=\d+).*)/', $content, $result_match);
            
            if($result_match){

                preg_match('/(.*(?<=days|weeks))(.*)/', $result_match[1], $result_inner);
            }

            return '<div class="panel panel--answer filter-content '.$final_class.'"><div class="panel__body"><div class="row"><div class="columns five push--seven"><p><span class="beta text--callout">'.$result_inner[1].'</span>'.$result_inner[2].'</p></div><div class="columns seven pull--five">'.do_shortcode($content).'</div></div></div></div>';
        }
        elseif($type == "error"){

            return '<div class="filter-content filter-error alert alert--error">'.$content.'</div>';

        }
        else{

            return '<div class="filter-content '.$final_class.'">'.do_shortcode($content).'</div>';
        }
    }


    add_shortcode('toggle', 'toggle_func');
    add_shortcode('accordion', 'accordion_func');
    add_shortcode('panel', 'panel_func');
    add_shortcode('highlight', 'highlight_func');
    add_shortcode('passmap', 'passmap_func');
    add_shortcode('step', 'passmap_step_func');
    add_shortcode('task', 'passmap_step_task_func');
    add_shortcode('next', 'next_func');
    add_shortcode('tabList', 'tabList_func');
    add_shortcode('tab', 'tab_func');
    add_shortcode('formList', 'formList_func');
    add_shortcode('formItem', 'formItem_func');
    add_shortcode('filter', 'filter_func');
    add_shortcode('checkbox', 'checkbox_func');
    add_shortcode('filterContent', 'filterContent_func');
?>