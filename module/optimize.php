<?php

/**
 * Class WPTurbo_Optimize
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Optimize extends WPTurbo_Base
{
    public $cdn = null;
    public $lazyload = null;
    public $preload = null;
    public $script = null;
    public static $instance = null;

    public static function init()
    {
        if(self::$instance){
            return self::$instance;
        }
        $obj = new self();
        //$opt = self::cnf();
        if (is_admin()) {
            add_action('wp_ajax_wpturbo', [$obj, 'wpturbo_ajax']);
        }
        $obj->cdn = new WPTurbo_Cdn();
        $obj->preload = new WPTurbo_Preload();
        $obj->lazyload = new WPTurbo_Lazyload();
        $obj->script = new WPTurbo_Script();

        self::$instance = $obj;
    }


    public function wpturbo_ajax()
    {
        $op = trim(self::param('op'));
        if(!$op){
            return;
        }
        $arrow = [
            'cdn_update', 'cdn_setting'
        ];
        if(!in_array($op, $arrow)){
            return;
        }
        if(!current_user_can('manage_options')){
            self::ajax_resp(['code' => 1, 'desc' => 'deny']);
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(self::param('_ajax_nonce')), 'wp_ajax_wpturbo')) {
            self::ajax_resp(['code'=>1,'desc'=>'illegal']);
            return;
        }
        switch ($op)
        {
            case 'cdn_update':

                $ret = ['code'=>1];
                do{

                    $opt = $this->sanitize_text_array(self::param('opt', []),['js_custom_head','js_custom_body','js_custom_footer']);

                    if(empty($opt) || !is_array($opt)){
                        $ret['desc'] = 'illegal';
                        break;
                    }
                    foreach(['js_custom_head','js_custom_body','js_custom_footer'] as $f){
                        if(isset($opt[$f])){
                            $opt[$f] = stripslashes($opt[$f]);
                        }
                    }

                    update_option( 'wpturbo_cdn', $opt );

                    $ret['code'] = 0;
                    $ret['desc'] = 'success';
                }while(0);
                self::ajax_resp($ret);

                break;
            case 'cdn_setting':
                $ret = [];
                $ret['opt'] = self::cnf();
                $ret['code'] = 0;
                $ret['desc'] = 'success';
                self::ajax_resp($ret);

                break;



        }
    }

    public static function def()
    {
        $default_conf = array(
            //cdn
            'switch' => '0',
            'cdn_url' => '',
            'cdn_include' => '',
            'cdn_exclude' => '',
            //load
            'preload_instant_page' => '0',
            'preload_dns_prefetch' => '',
            'preload_preconnect' => [],
            'preload_critical_image' => '0',
            'preload_resource' => [],

            //lazy
            'lazy_load_image'=>'0',
            'lazy_skip_image_num'=>'5',
            'lazy_frame_video'=>'0',
            'youtube_preview_thumbnails'=>'0',
            'lazy_exclude'=>'',
            'lazy_viewpoint'=>'',
            'lazy_new_dom'=>'0',
            'lazy_fix_image'=>'0',
            'lazy_image_fade_in'=>'0',

            //js
            'js_clean' => '0',
            'js_defer' => '0',
            'js_defer_jquery' => '0',
            'js_defer_exclude' => '',
            'js_delay' => '0',
            'js_delay_type' => '2',//1=>全部JS,2=>指定JS,
            'js_delay_exclude' => '',
            'js_delay_time' => '',
            'js_custom_head' => '',
            'js_custom_body' => '',
            'js_custom_footer' => '',
        );
        return $default_conf;
    }

    //sve

    public static function cnf($key=null, $default=null){
        static $_option = array();
        if(!$_option){
            $_option = get_option('wpturbo_cdn');
            if(!$_option || !is_array($_option)){
                $_option = [];
            }
            $default_conf = self::def();

            foreach ($default_conf as $k=>$v){
                if(!isset($_option[$k]))$_option[$k] = $v;
            }
            foreach (['preload_preconnect','preload_resource'] as $k){
                if(!is_array($_option[$k])){
                    $_option[$k] = [];
                }
            }

        }

        if(null === $key){
            return $_option;
        }

        if(isset($_option[$key])){
            return $_option[$key];
        }

        return $default;
    }


}