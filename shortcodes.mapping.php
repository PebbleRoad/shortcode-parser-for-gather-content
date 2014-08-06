<?php
    
    function accordion_func($attrs, $content) {    
        return "\n<div class=\"ui-accordion\">".do_shortcode($content)."</div>";
    }

    /**
     * Accordion
     */

    function panel_func($attrs, $content){

        return '<div class="accordion__header">'.$attrs['title'].'</div><div class="accordion__content">'.$content.'</div>';
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

        return '<li>'.$title.'<ul>'.do_shortcode($content).'</ul></li>';
    }

    $count = 0;
    function passmap_step_task_func($attrs, $content){

        // Attributes
        extract( shortcode_atts(
            array(
                'title' => ''
            ), $attrs)
        );

        return '<li data-step="'.++$count.'" data-intro="'.$content.'">'.$title.'</li>';
    }
    /* -------------------------- */
    /* -------------------------- */


    function next_func($attrs, $content){
        
        return '<div class="section-next"><span class="next-title gamma">What\'s next</span><p><a href="documents-required.html"><em class="fa fa-angle-right"></em>'.$content.'</a></p></div>';
    }


    add_shortcode('accordion', 'accordion_func');
    add_shortcode('panel', 'panel_func');
    add_shortcode('highlight', 'highlight_func');
    add_shortcode('passmap', 'passmap_func');
    add_shortcode('step', 'passmap_step_func');
    add_shortcode('task', 'passmap_step_task_func');
    add_shortcode('next', 'next_func');
?>