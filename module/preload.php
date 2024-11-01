<?php


/**
 * Class WPTurbo_Preload
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Preload extends WPTurbo_Base
{

    public $preload = [];

    public function __construct()
    {
        if (!is_admin()) {
            $opt = self::cnf();
            if ($opt['preload_instant_page']) {
                add_action('wp_enqueue_scripts', [$this, 'enqueue_instant_page'], 1000);
                add_filter('script_loader_tag', [$this, 'instant_page_attribute'], 10, 2);
            }
            if ($opt['preload_dns_prefetch']) {
                add_action('wp_head', [$this, 'preload_dns_prefetch'], 1);
            }
            if ($opt['preload_preconnect']) {
                add_action('wp_head', [$this, 'preload_preconnect'], 1);
            }

            //preload critical images

            //preload
            if($opt['preload_resource'] || $opt['preload_critical_image']){
                add_action('wpturbo_theme_output', [$this, 'add_preloads'],50);
                if($opt['preload_critical_image']){
                    add_action('wpturbo_theme_output', [$this, 'prepare_critical_images'],51);
                }
            }

        }
    }



    //add preloads to html
    public function add_preloads($html) {
        $cnf = self::cnf();
        if($cnf['preload_critical_image']) {
            $this->add_critical_image_preloads($html);
        }

        if($cnf['preload_resource'] && is_array($cnf['preload_resource'])) {

            $mime_types = array(
                'svg'   => 'image/svg+xml',
                'ttf'   => 'font/ttf',
                'otf'   => 'font/otf',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'eot'   => 'application/vnd.ms-fontobject',
                'sfnt'  => 'font/sfnt'
            );
            $rules = $cnf['preload_resource'];

            foreach($rules as $line) {

                //device type check
                if(!empty($line['device'])) {
                    $device_type = wp_is_mobile() ? 'mobile' : 'desktop';
                    if($line['device'] != $device_type) {
                        continue;
                    }
                }

                //location check
                if($line['location']) {

                    $location_match = false;

                    $exploded_locations = explode(',', $line['location']);
                    $trimmed_locations = array_map('trim', $exploded_locations);

                    //single post exclusion
                    if(is_singular()) {
                        global $post;
                        if(in_array($post->ID, $trimmed_locations)) {
                            $location_match = true;
                        }
                    }
                    //posts page exclusion
                    elseif(is_home() && in_array('blog', $trimmed_locations)) {
                        $location_match = true;
                    }
                    elseif(is_archive()) {
                        //woocommerce shop check
                        /*if(function_exists('is_shop') && is_shop()) {
                            if(in_array(wc_get_page_id('shop'), $trimmed_locations)) {
                                $location_match = true;
                            }
                        }*/
                    }

                    if(!$location_match) {
                        continue;
                    }
                }

                $mime_type = "";

                if($line['type'] && $line['type'] == 'font') {
                    $path_info = pathinfo($line['url']);
                    $mime_type = !empty($path_info['extension']) && isset($mime_types[$path_info['extension']]) ? $mime_types[$path_info['extension']] : "";
                }

                //print script/style handle as preload
                if(!empty($line['type']) && in_array($line['type'], array('script', 'style'))) {
                    if(strpos($line['url'], '.') === false) {

                        global $wp_scripts;
                        global $wp_styles;

                        $scripts_arr = $line['type'] == 'script' ? $wp_scripts : $wp_styles;

                        if(!empty($scripts_arr)) {
                            $scripts_arr = $scripts_arr->registered;

                            if(array_key_exists($line['url'], $scripts_arr)) {

                                $url = $scripts_arr[$line['url']]->src;

                                $parsed_url = wp_parse_url($scripts_arr[$line['url']]->src);
                                if(empty($parsed_url['host'])) {
                                    $url = site_url($url);
                                }

                                $ver = $scripts_arr[$line['url']]->ver;

                                if(empty($ver) && preg_match('/wp-includes|wp-admin/i', $url)) {
                                    $ver = get_bloginfo('version');
                                }

                                $line['url'] = $url . (!empty($ver) ? '?ver=' . $ver : '');
                            }
                        }
                    }
                }

                $preload = "<link rel='preload' href='" . $line['url'] . "'" . (!empty($line['type']) ? " as='" . $line['type'] . "'" : "") . (!empty($mime_type) ? " type='" . $mime_type . "'" : "") . (!empty($line['cross']) ? " crossorigin" : "") . (!empty($line['type']) && $line['type'] == 'style' ? " onload=\"this.rel='stylesheet';this.removeAttribute('onload');\"" : "") . " />";

                if($line['type'] == 'image') {
                    array_unshift($this->preload, $preload);
                }
                else {
                    $this->preload[] = $preload;
                }
            }
        }

        if(!empty($this->preload)) {
            $preloads_string = "";
            foreach($this->preload as $preload) {
                $preloads_string.= $preload;
            }
            $pos = strpos($html, '</title>');
            if($pos !== false) {
                $html = substr_replace($html, '</title>' . $preloads_string, $pos, 8);
            }
        }

        return $html;
    }

    //add data attribute to critical images
    public function prepare_critical_images($html) {

        $cnf = self::cnf();
        if(!$cnf['preload_critical_image']){
            return $html;
        }

        //match all img tags
        if(!preg_match_all('#<img([^>]+?)\/?>#is', $html, $images, PREG_SET_ORDER)){
            return $html;
        }


        $exclusions = apply_filters('wpturbo_preload__critical_image_exclusions', array());

        $count = 0;

        //loop through images
        foreach($images as $image) {

            if($count >= $cnf['preload_critical_image']) {
                break;
            }

            if(strpos($image[0], 'secure.gravatar.com') !== false) {
                continue;
            }


            if(!empty($exclusions) && is_array($exclusions)) {
                $exclude = false;
                foreach($exclusions as $exclusion) {
                    if(strpos($image[0], $exclusion) !== false) {
                        $exclude = true;
                        break;
                    }
                }
                if($exclude){
                    continue;
                }
            }

            $count++;

            $new_image = str_replace('<img ', '<img data-wpturbo-preload="' . $count . '" ', $image[0]);
            $html = str_replace($image[0], $new_image, $html);
        }


        return $html;
    }

    public function add_critical_image_preloads(&$html) {

        //match preload picture > src tag
        preg_match_all('#<picture.+?data-wpturbo-preload="(.)".+?\/picture>#is', $html, $pictures, PREG_SET_ORDER);

        if(!empty($pictures)) {

            foreach($pictures as $picture) {

                preg_match('#<source([^>]+?image\/webp[^>]+?)\/?>#is', $picture[0], $source);

                if(!empty($source)) {

                    $this->generate_critical_image_preload($picture[1], $source[1]);
                    $new_picture = str_replace('data-wpturo-preload="' . $picture[1] . '"', '', $picture[0]);
                    $html = str_replace($picture[0], $new_picture, $html);
                }
            }
        }

        preg_match_all('#<img([^>]+?data-wpturo-preload="(.)"[^>]+?)\/?>#is', $html, $images, PREG_SET_ORDER);
        if(!empty($images)) {
            foreach($images as $image) {
                $this->generate_critical_image_preload($image[2], $image[1]);
            }
        }

        if(!empty($this->preloads)) {
            ksort($this->preloads);
        }
    }

    //generate preload link from att string
    public function generate_critical_image_preload($index, $att_string) {
        if(!empty($att_string)) {
            $atts = $this->get_atts_array($att_string);

            $src = $atts['data-src'] ?? $atts['src'] ?? '';

            $this->preloads[$index] = '<link rel="preload" href="' . $src . '" as="image"' .
                (!empty($atts['srcset']) ? ' imagesrcset="' . $atts['srcset'] . '"' : '') .
                (!empty($atts['sizes']) ? ' imagesizes="' . $atts['sizes'] . '"' : '') .
                ' />';
        }
    }


    public function enqueue_instant_page() {
        wp_register_script('wpturbo-instant-page', plugins_url('assets/js/instantpage.js', WPTURBO_BASE), array(), WPTURBO_VERSION, true);
        wp_enqueue_script('wpturbo-instant-page');
    }

    public function instant_page_attribute($tag, $handle) {
        if($handle !== 'wpturbo-instant-page') {
            return $tag;
        }
        return str_replace(' src', ' data-cfasync="false" data-no-optimize="1" src', $tag);
    }

    public function preload_dns_prefetch()
    {
        $opt = self::cnf();
        if(!$opt['preload_dns_prefetch']){
            return;
        }
        $dns_list = explode("\n", trim($opt['preload_dns_prefetch']));
        foreach($dns_list as $dns){
            $dns = trim($dns);
            if(!$dns)continue;
            if(!preg_match('#^https?://#',$dns) && !preg_match('#^//#',$dns)){
                $dns = '//'.$dns;
            }
            echo '<link rel="dns-prefetch" href="' . esc_attr($dns) . '" />' . "\n";
        }
    }

    public function preload_preconnect()
    {
        $opt = self::cnf();
        if(!$opt['preload_preconnect']){
            return;
        }
        if(!is_array($opt['preload_preconnect'])){
            return;
        }
        foreach($opt['preload_preconnect'] as $r){
            $url = trim($r['url']);
            if(!$url)continue;
            $cross = '';
            if($r['cross']){
                $cross = ' crossorigin="anonymous"';
            }
            if(!preg_match('#^https?://#',$url) && !preg_match('#^//#',$url)){
                $url = '//'.$url;
            }
            //crossorigin
            echo '<link rel="preconnect" href="' . esc_attr($url) . '"'.$cross.' />' . "\n";
        }
    }

    public function get_atts_array($atts_string) {

        if(!empty($atts_string)) {
            $atts_array = array_map(
                function(array $attribute) {
                    return $attribute['value'];
                },
                wp_kses_hair($atts_string, wp_allowed_protocols())
            );

            return $atts_array;
        }

        return false;
    }

    public static function cnf($key=null, $default=null)
    {
        return WPTurbo_Optimize::cnf($key, $default);
    }
}