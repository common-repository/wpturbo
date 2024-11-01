<?php


class WPTurbo_Theme
{
    public function __construct()
    {
        if($this->is_cache_output()){
            add_action('template_redirect', [$this,'template_redirect'],1);
        }
    }


    public function is_cache_output()
    {


        return true;
    }

    public function template_redirect()
    {
        ob_start([$this,'output_callback']);
    }


    public function output_callback($html)
    {

        $html = apply_filters('wpturbo_theme_output',$html);

        //$html .= '<!-- wpturbo -->';

        return $html;
    }

}