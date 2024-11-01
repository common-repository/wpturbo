<?php

/**
 * Class WPTurbo_Lazyload
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Lazyload extends WPTurbo_Base
{

    public function __construct()
    {
        add_action('wp', [$this, 'lazy_load_init']);
    }


    public static function cnf($key = null, $default = null)
    {
        return WPTurbo_Optimize::cnf($key, $default);
    }

    public function lazy_load_init()
    {

        if (is_admin() || is_embed() || is_feed() || is_customize_preview()) {
            return;
        }
        if (wp_doing_ajax() || wp_doing_cron()) {
            return;
        }
        if ((defined('REST_REQUEST') && REST_REQUEST) || (function_exists('wp_is_json_request') && wp_is_json_request())) {
            return;
        }

        $cnf = self::cnf();
        //lazy load image or iframes
        $is_lazy = $cnf['lazy_load_image'] || $cnf['lazy_frame_video'];
        if (!$is_lazy) {
            return;
        }
        if ($cnf['lazy_fix_image']) {
            add_filter('wpturbo_theme_output', [$this, 'image_dimensions'], 100);
        }
        add_action('wp_enqueue_scripts', [$this, 'enqueue_lazy_load']);
        //add_filter('template_redirect', [$this, 'lazy_load'], 2);
        add_filter('wpturbo_theme_output', [$this, 'lazy_load_buffer'], 110);
        add_action('wp_footer', [$this, 'print_lazy_load_js'], 10000);
        add_action('wp_head', [$this, 'print_lazy_load_css'], 10000);
        add_filter('wp_lazy_loading_enabled', '__return_false');
    }

    public function enqueue_lazy_load()
    {
        wp_register_script('wpturbo-lazy-load-js', plugins_url('assets/js/lazyload.min.js', WPTURBO_BASE), array(), WPTURBO_VERSION, true);
        wp_enqueue_script('wpturbo-lazy-load-js');
    }

    public function lazy_load()
    {
        //ob_start([$this,'lazy_load_buffer']);
    }

    public function lazy_load_buffer($html)
    {

        $cnf = self::cnf();

        //get clean html to use for buffer search
        $buffer = $this->lazy_load_clean_html($html);

        if ($cnf['lazy_load_image']) {
            $html = $this->lazy_load_images($html, $buffer);
            $html = $this->lazy_load_pictures($html, $buffer);
            $html = $this->lazy_load_background_images($html, $buffer);
        }

        if ($cnf['lazy_frame_video']) {
            $html = $this->lazy_load_iframes($html, $buffer);
            $html = $this->lazy_load_videos($html, $buffer);
        }
        return $html;
    }

    //remove unecessary bits from html for buffer searh
    public function lazy_load_clean_html($html)
    {
        //remove existing script tags
        $html = preg_replace('#<script\b(?:[^>]*)>(?:.+)?</script>#Umsi', '', $html);
        //remove existing noscript tags
        $html = preg_replace('#<noscript>(?:.+)</noscript>#Umsi', '', $html);
        return $html;
    }

    //lazy load img tags
    public function lazy_load_images($html, $buffer)
    {

        //match all img tags
        preg_match_all('#<img([^>]+?)/?>#is', $buffer, $images, PREG_SET_ORDER);

        if (empty($images)) {
            return $html;
        }

        $cnf = self::cnf();

        //return $images[3][0];


        $lazy_image_count = 0;
        $lazy_skip_image_num = $cnf['lazy_skip_image_num'] ?? 0;

        //remove any duplicate images
        $images = array_unique($images, SORT_REGULAR);

        //loop through images
        foreach ($images as $image) {

            $lazy_image_count++;

            if ($lazy_image_count <= $lazy_skip_image_num) {
                continue;
            }

            //prepare lazy load image
            $lazy_image = $this->lazy_load_image($image);

            //replace image in html
            $html = str_replace($image[0], $lazy_image, $html);
        }

        return $html;
    }

    //prep img tag for lazy loading
    public function lazy_load_image($image)
    {

        //if there are no attributes, return original match
        if (empty($image[1])) {
            return $image[0];
        }

        //get image attributes array
        $image_atts = $this->lazyload_get_atts_array($image[1]);
        if (empty($image_atts['src'])) {
            return $image[0];
        }
        //强制lazy load
        $force_rule = $this->get_lazyload_forced_atts();
        if (!$this->lazyload_excluded($image[1], $force_rule)) {
            if (!empty($image_atts['class']) && strpos($image_atts['class'], 'do-not-lazyload') !== false) {
                return $image[0];
            }
            $exclude_rule = $this->get_lazyload_exclude_rule();
            if ($this->lazyload_excluded($image[1], $exclude_rule)) {
                return $image[0];
            }
        }

        //add lazyload class
        $image_atts['class'] = (isset($image_atts['class']) ? $image_atts['class'] . ' ' : '') . 'wpturbo-lazy';

        //migrate src
        $image_atts['data-src'] = $image_atts['src'];

        //add placeholder src
        $width = !empty($image_atts['width']) ? $image_atts['width'] : 0;
        $height = !empty($image_atts['height']) ? $image_atts['height'] : 0;
        $image_atts['src'] = "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20" . $width . "%20" . $height . "'%3E%3C/svg%3E";

        //migrate srcset
        if (!empty($image_atts['srcset'])) {
            $image_atts['data-srcset'] = $image_atts['srcset'];
            unset($image_atts['srcset']);
        }

        //migrate sizes
        if (!empty($image_atts['sizes'])) {
            $image_atts['data-sizes'] = $image_atts['sizes'];
            unset($image_atts['sizes']);
        }

        //build lazy image attributes string
        $lazy_image_atts_string = $this->lazyload_get_atts_string($image_atts);

        //replace attributes
        $html = sprintf('<img %1$s />', $lazy_image_atts_string);

        //original noscript image
        $html .= "<noscript>" . $image[0] . "</noscript>";

        return $html;
    }

    public function lazyload_get_atts_array($atts_string)
    {

        if (!empty($atts_string)) {
            $atts_array = array_map(
                function (array $attribute) {
                    return $attribute['value'];
                },
                wp_kses_hair($atts_string, wp_allowed_protocols())
            );

            return $atts_array;
        }

        return false;
    }

    //check for excluded attributes in attributes string
    public function lazyload_excluded($string, $excluded)
    {
        if (!is_array($excluded)) {
            (array) $excluded;
        }

        if (empty($excluded)) {
            return false;
        }

        foreach ($excluded as $exclude) {
            if (strpos($string, $exclude) !== false) {
                return true;
            }
        }

        return false;
    }

    //get forced attributes
    public function get_lazyload_forced_atts()
    {
        static $rules = [];
        if ($rules) {
            return $rules;
        }
        $rules =  apply_filters('wpturo_lazyload_forced_rules', array());
        return $rules;
    }

    //get excluded attributes
    public function get_lazyload_exclude_rule()
    {
        static $rules = [];
        if ($rules) {
            return $rules;
        }
        //base exclusions
        $base_rules = array(
            'data-wpturbo-preload',
            'gform_ajax_frame'
        );

        $cnf = self::cnf();
        //get exclusions added from settings
        if (!empty($cnf['lazy_exclude']) && is_array($cnf['lazy_exclude'])) {
            $rules = array_unique(array_merge($base_rules, $cnf['lazy_exclude']));
        }

        $rules =  apply_filters('wpturo_lazyload_excluded_rules', $rules);
        return $rules;
    }


    public function lazyload_get_atts_string($atts_array)
    {

        if (!empty($atts_array)) {
            $assigned_atts_array = array_map(
                function ($name, $value) {
                    if ($value === '') {
                        return $name;
                    }
                    return sprintf('%s="%s"', $name, esc_attr($value));
                },
                array_keys($atts_array),
                $atts_array
            );
            $atts_string = implode(' ', $assigned_atts_array);

            return $atts_string;
        }

        return false;
    }


    //lazy load picture tags for webp
    public function lazy_load_pictures($html, $buffer)
    {

        //match all picture tags
        if (!preg_match_all('#<picture(.*)?>(.*)<\/picture>#isU', $buffer, $pictures, PREG_SET_ORDER)) {
            return $html;
        }

        $force_rule = $this->get_lazyload_forced_atts();
        $exclude_rule = $this->get_lazyload_exclude_rule();

        foreach ($pictures as $picture) {

            //get picture tag attributes
            $picture_atts = $this->lazyload_get_atts_array($picture[1]);
            if (!$this->lazyload_excluded($picture[1], $force_rule)) {
                //skip if no-lazy class is found
                if (!empty($picture_atts['class']) && strpos($picture_atts['class'], 'do-not-lazyload') !== false) {
                    continue;
                }

                //skip if exluded attribute was found
                if ($this->lazyload_excluded($picture[0], $exclude_rule)) {
                    continue;
                }
            }


            //match all source tags inside the picture
            if (!preg_match_all('#<source(\s.+)>#isU', $picture[2], $sources, PREG_SET_ORDER)) {
                continue;
            }

            //remove any duplicate sources
            $sources = array_unique($sources, SORT_REGULAR);

            foreach ($sources as $source) {

                //skip if exluded attribute was found
                if ($this->lazyload_excluded($source[1], $exclude_rule)) {
                    continue;
                }

                //create placeholder src
                $width = !empty($picture_atts['width']) ? $picture_atts['width'] : 0;
                $height = !empty($picture_atts['height']) ? $picture_atts['height'] : 0;
                $placeholder = "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20" . $width . "%20" . $height . "'%3E%3C/svg%3E";

                //migrate srcet
                $new_source = preg_replace('/([\s"\'])srcset/i', '${1}srcset="' . $placeholder . '" data-srcset', $source[0]);

                //migrate sizes
                $new_source = preg_replace('/([\s"\'])sizes/i', '${1}data-sizes', $new_source);

                //replace source in html
                $html = str_replace($source[0], $new_source, $html);
            }


            //match img tag inside the picture
            if (!preg_match('#<img([^>]+?)\/?>#isU', $picture[0], $image)) {
                continue;
            }

            //get lazy load image
            $lazy_image = $this->lazy_load_image($image);

            //replace image in html
            $html = str_replace($image[0], $lazy_image, $html);
        }

        return $html;
    }

    //lazy load background images
    public function lazy_load_background_images($html, $buffer)
    {

        //match all elements with inline styles
        //preg_match_all('#<(?<tag>div|figure|section|span|li)(\s+[^>]+[\'"\s]?style\s*=\s*[\'"].*?background-image.*?[\'"][^>]*)>#is', $buffer, $elements, PREG_SET_ORDER); //alternate to possibly filter some out that don't have background images???
        if (!preg_match_all('#<(?<tag>div|figure|section|span|li)(\s+[^>]*[\'"\s]?style\s*=\s*[\'"].*?[\'"][^>]*)>#is', $buffer, $elements, PREG_SET_ORDER)) {
            return $html;
        }

        $force_rule = $this->get_lazyload_forced_atts();
        $exclude_rule = $this->get_lazyload_exclude_rule();

        foreach ($elements as $element) {

            //get element tag attributes
            $element_atts = $this->lazyload_get_atts_array($element[2]);

            //dont check excluded if forced attribute was found
            if (!$this->lazyload_excluded($element[2], $force_rule)) {

                //skip if no-lazy class is found
                if (!empty($element_atts['class']) && strpos($element_atts['class'], 'no-lazy') !== false) {
                    continue;
                }

                //skip if exluded attribute was found
                if ($this->lazyload_excluded($element[2], $exclude_rule)) {
                    continue;
                }
            }

            //skip if no style attribute
            if (!isset($element_atts['style'])) {
                continue;
            }

            //match background-image in style string
            if (!preg_match('#background(-image)?\s*:\s*(\s*url\s*\((?<url>[^)]+)\))\s*;?#is', $element_atts['style'], $url)) {
                continue;
            }

            $url['url'] = trim($url['url'], '\'" ');

            //add lazyload class
            $element_atts['class'] = (!empty($element_atts['class']) ? $element_atts['class'] . ' ' : '') . 'wpturbo-lazy';

            //remove background image url from inline style attribute
            $element_atts['style'] = str_replace($url[0], '', $element_atts['style']);

            //migrate src
            $element_atts['data-bg'] = 'url(' . esc_attr($url['url']) . ')';

            //build lazy element attributes string
            $lazy_element_atts_string = $this->lazyload_get_atts_string($element_atts);

            //build lazy element
            $lazy_element = sprintf('<' . $element['tag'] . ' %1$s >', $lazy_element_atts_string);

            //replace element with placeholder
            $html = str_replace($element[0], $lazy_element, $html);

            unset($lazy_element);
        }

        return $html;
    }



    //lazy load iframes
    public function lazy_load_iframes($html, $buffer)
    {

        //match all iframes
        if (!preg_match_all('#<iframe(\s.+)>.*</iframe>#iUs', $buffer, $iframes, PREG_SET_ORDER)) {
            return $html;
        }

        $cnf = self::cnf();

        //remove any duplicates
        $iframes = array_unique($iframes, SORT_REGULAR);

        $force_rule = $this->get_lazyload_forced_atts();
        $exclude_rule = $this->get_lazyload_exclude_rule();

        foreach ($iframes as $iframe) {

            //get iframe attributes array
            $iframe_atts = $this->lazyload_get_atts_array($iframe[1]);

            //dont check excluded if forced attribute was found
            if (!$this->lazyload_excluded($iframe[1], $force_rule)) {

                //skip if exluded attribute was found
                if ($this->lazyload_excluded($iframe[1], $exclude_rule)) {
                    continue;
                }

                //skip if no-lazy class is found
                if (!empty($iframe_atts['class']) && strpos($iframe_atts['class'], 'do-not-lazyload') !== false) {
                    continue;
                }
            }

            //skip if no src is found
            if (empty($iframe_atts['src'])) {
                continue;
            }

            $iframe['src'] = trim($iframe_atts['src']);

            //try rendering youtube preview placeholder if we need to
            if (!empty($cnf['youtube_preview_thumbnails'])) {
                $iframe_lazyload = $this->lazy_load_youtube_iframe($iframe);
            }

            //default iframe placeholder
            if (empty($iframe_lazyload)) {

                $iframe_atts['class'] = (!empty($iframe_atts['class']) ? $iframe_atts['class'] . ' ' : '') . 'wpturbo-lazy';

                //migrate src
                $iframe_atts['data-src'] = $iframe_atts['src'];
                unset($iframe_atts['src']);

                //build lazy iframe attributes string
                $lazy_iframe_atts_string = $this->lazyload_get_atts_string($iframe_atts);

                //replace iframe attributes string
                $iframe_lazyload = str_replace($iframe[1], ' ' . $lazy_iframe_atts_string, $iframe[0]);

                //add noscript original iframe
                $iframe_lazyload .= '<noscript>' . $iframe[0] . '</noscript>';
            }

            //replace iframe with placeholder
            $html = str_replace($iframe[0], $iframe_lazyload, $html);

            unset($iframe_lazyload);
        }

        return $html;
    }

    //prep youtube iframe for lazy loading
    public function lazy_load_youtube_iframe($iframe)
    {

        if (!$iframe) {
            return false;
        }

        //attempt to get the id based on url
        $result = preg_match('#^(?:https?:)?(?://)?(?:www\.)?(?:youtu\.be|youtube\.com|youtube-nocookie\.com)/(?:embed/|v/|watch/?\?v=)?([\w-]{11})#iU', $iframe['src'], $matches);

        //return false if there is no usable id
        if (!$result || $matches[1] === 'videoseries') {
            return false;
        }

        $youtube_id = $matches[1];

        //parse iframe src url
        $query = wp_parse_url(htmlspecialchars_decode($iframe['src']), PHP_URL_QUERY);

        //clean up the url
        $parsed_url = wp_parse_url($iframe['src'], -1);
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '//';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $youtube_url = $scheme . $host . $path;

        //thumbnail resolutions
        $resolutions = array(
            'default'       => array(
                'width'  => 120,
                'height' => 90,
            ),
            'mqdefault'     => array(
                'width'  => 320,
                'height' => 180,
            ),
            'hqdefault'     => array(
                'width'  => 480,
                'height' => 360,
            ),
            'sddefault'     => array(
                'width'  => 640,
                'height' => 480,
            ),
            'maxresdefault' => array(
                'width'  => 1280,
                'height' => 720,
            )
        );

        //filter set resolution
        $resolution = apply_filters('wpturbo_lazyload_youtube_thumbnail_resolution', 'hqdefault');

        //finished youtube lazy output
        $youtube_lazyload = '<div class="wpturbo-lazy-youtube" data-src="' . esc_attr($youtube_url) . '" data-id="' . esc_attr($youtube_id) . '" data-query="' . esc_attr($query) . '" onclick="wpturboLazyLoadYouTube(this);">';
        $youtube_lazyload .= '<div>';
        $youtube_lazyload .= '<img class="wpturbo-lazy" src="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%20' . $resolutions[$resolution]['width'] . '%20' . $resolutions[$resolution]['height'] . '%3E%3C/svg%3E" data-src="https://i.ytimg.com/vi/' . esc_attr($youtube_id) . '/' . $resolution . '.jpg" alt="YouTube Video" width="' . $resolutions[$resolution]['width'] . '" height="' . $resolutions[$resolution]['height'] . '" data-pin-nopin="true">';
        $youtube_lazyload .= '<div class="play"></div>';
        $youtube_lazyload .= '</div>';
        $youtube_lazyload .= '</div>';
        $youtube_lazyload .= '<noscript>' . $iframe[0] . '</noscript>';

        return $youtube_lazyload;
    }

    //lazy load videos
    public function lazy_load_videos($html, $buffer)
    {

        //match all videos
        if (!preg_match_all('#<video(\s.+)>.*</video>#iUs', $buffer, $videos, PREG_SET_ORDER)) {
            return $html;
        }
        //get plugin options
        $cnf = self::cnf();

        //remove any duplicates
        $videos = array_unique($videos, SORT_REGULAR);

        $force_rule = $this->get_lazyload_forced_atts();
        $exclude_rule = $this->get_lazyload_exclude_rule();
        foreach ($videos as $video) {

            //get video attributes array
            $video_atts = $this->lazyload_get_atts_array($video[1]);

            //dont check excluded if forced attribute was found
            if (!$this->lazyload_excluded($video[1], $force_rule)) {

                //skip if exluded attribute was found
                if ($this->lazyload_excluded($video[1], $exclude_rule)) {
                    continue;
                }

                //skip if no-lazy class is found
                if (!empty($video_atts['class']) && strpos($video_atts['class'], 'do-not-lazyload') !== false) {
                    continue;
                }
            }

            //skip if no src is found
            if (empty($video_atts['src'])) {
                continue;
            }

            //add lazyload class
            $video_atts['class'] = (!empty($video_atts['class']) ? $video_atts['class'] . ' ' : '') . 'wpturob-lazy';

            //migrate src
            $video_atts['data-src'] = $video_atts['src'];
            unset($video_atts['src']);

            //build lazy video attributes string
            $lazy_video_atts_string = $this->lazyload_get_atts_string($video_atts);

            //replace video attributes string
            $video_lazyload  = str_replace($video[1], ' ' . $lazy_video_atts_string, $video[0]);

            //add noscript original video
            $video_lazyload .= '<noscript>' . $video[0] . '</noscript>';

            //replace video with placeholder
            $html = str_replace($video[0], $video_lazyload, $html);

            unset($video_lazyload);
        }

        return $html;
    }


    //initialize lazy load instance
    public function print_lazy_load_js()
    {

        $cnf = self::cnf();

        $viewpoint = apply_filters('wpturbo_lazyload_viewpoint', ($cnf['lazy_viewpoint'] ? $cnf['lazy_viewpoint'] : '0px'));

        $inline_js = '<script>';

        $inline_js .= 'document.addEventListener("DOMContentLoaded",function(){';

        //initialize lazy loader
        $inline_js .= 'var lazyLoadInstance=new LazyLoad({elements_selector:"img[data-src],.wpturbo-lazy",thresholds:"' . $viewpoint . ' 0px",callback_loaded:function(element){if(element.tagName==="IFRAME"){if(element.classList.contains("loaded")){if(typeof window.jQuery!="undefined"){if(jQuery.fn.fitVids){jQuery(element).parent().fitVids()}}}}}});';

        //dom monitoring
        if ($cnf['lazy_new_dom']) {
            $inline_js .= 'var target=document.querySelector("body");var observer=new MutationObserver(function(mutations){lazyLoadInstance.update()});var config={childList:!0,subtree:!0};observer.observe(target,config);';
        }

        $inline_js .= '});';

        //youtube thumbnails
        if ($cnf['lazy_frame_video'] && $cnf['youtube_preview_thumbnails']) {
            $inline_js .= 'function wpturboLazyLoadYouTube(e){var t=document.createElement("iframe"),r="ID?";r+=0===e.dataset.query.length?"":e.dataset.query+"&",r+="autoplay=1",t.setAttribute("src",r.replace("ID",e.dataset.src)),t.setAttribute("frameborder","0"),t.setAttribute("allowfullscreen","1"),t.setAttribute("allow","accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"),e.replaceChild(t,e.firstChild)}';
        }
        $inline_js .= '</script>';

        echo $inline_js;
    }

    //print lazy load styles
    public function print_lazy_load_css()
    {

        $cnf = self::cnf();

        //print noscript styles
        echo '<noscript><style>.wpturbo-lazy[data-src]{display:none !important;}</style></noscript>';

        $inline_styles = '';

        //youtube thumbnails
        if ($cnf['lazy_frame_video'] && $cnf['youtube_preview_thumbnails']) {
            $cover = plugins_url('assets/youtube.svg', WPTURBO_BASE);
            $inline_styles .= '.wpturbo-lazy-youtube{position:relative;width:100%;max-width:100%;height:0;padding-bottom:56.23%;overflow:hidden}.wpturbo-lazy-youtube img{position:absolute;top:0;right:0;bottom:0;left:0;display:block;width:100%;max-width:100%;height:auto;margin:auto;border:none;cursor:pointer;transition:.5s all;-webkit-transition:.5s all;-moz-transition:.5s all}.wpturbo-lazy-youtube img:hover{-webkit-filter:brightness(75%)}.wpturbo-lazy-youtube .play{position:absolute;top:50%;left:50%;width:68px;height:48px;margin-left:-34px;margin-top:-24px;background:url(' . $cover . ') no-repeat;background-position:center;background-size:cover;pointer-events:none}.wpturbo-lazy-youtube iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:99}';
            if (current_theme_supports('responsive-embeds')) {
                $inline_styles .= '.wp-has-aspect-ratio .wp-block-embed__wrapper{position:relative;}.wp-has-aspect-ratio .wpturbo-lazy-youtube{position:absolute;top:0;right:0;bottom:0;left:0;width:100%;height:100%;padding-bottom:0}';
            }
        }

        //fade in effect
        if ($cnf['lazy_image_fade_in']) {
            $inline_styles .= '.wpturbo-lazy:not(picture),.wpturbo-lazy img{opacity:0}.wpturbo-lazy.loaded,.wpturbo-lazy img.loaded,.wpturbo-lazy[data-was-processed=true]{opacity:1;transition:opacity ' . apply_filters('wpturbo_lazyload_image_fade_in_speed', 500) . 'ms}';
        }

        //print styles
        if ($inline_styles) {
            echo '<style>' . $inline_styles . '</style>';
        }
    }


    //fix images missing dimensions
    public function image_dimensions($html)
    {
        //match all img tags without width or height attributes
        if (!preg_match_all('#<img((?:[^>](?!(height|width)=[\'\"](?:\S+)[\'\"]))*+)>#is', $html, $images, PREG_SET_ORDER)) {
            return $html;
        }

        //remove any duplicate images
        $images = array_unique($images, SORT_REGULAR);

        //loop through images
        foreach ($images as $image) {

            //get image attributes array
            $image_atts = $this->lazyload_get_atts_array($image[1]);

            if (empty($image_atts['src'])) {
                continue;
            }

            //get image dimensions
            $dimensions = $this->get_dimensions_from_url($image_atts['src']);
            if (empty($dimensions)) {
                continue;
            }

            //remove any existing dimension attributes
            $new_image = preg_replace('/(height|width)=[\'"](?:\S+)*[\'"]/i', '', $image[0]);

            //add dimension attributes to img tag
            $new_image = preg_replace('/<\s*img/i', '<img width="' . $dimensions['width'] . '" height="' . $dimensions['height'] . '"', $new_image);
            //replace original img tag in html
            if (!empty($new_image)) {
                $html = str_replace($image[0], $new_image, $html);
            }
        }

        return $html;
    }

    public function get_dimensions_from_url($url)
    {
        //grab dimensions from file name if available
        if (preg_match('/(?:.+)-([0-9]+)x([0-9]+)\.(jpg|jpeg|png|gif|svg)$/', $url, $matches)) {
            return array('width' => $matches[1], 'height' => $matches[2]);
        }

        //get image path
        $image_path = ABSPATH . wp_parse_url($url)['path'];

        if (file_exists($image_path)) {

            //get dimensions from file
            $sizes = getimagesize($image_path);

            if (!empty($sizes)) {
                return array('width' => $sizes[0], 'height' => $sizes[1]);
            }
        }

        return false;
    }
}
