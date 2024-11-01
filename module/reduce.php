<?php


/**
 * Class WPTurbo_Reduce
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Reduce extends WPTurbo_Base
{

    public $debug = false;

    public function __construct()
    {
        if(is_admin()){
            add_action('wp_ajax_wpturbo', [$this,'wpturbo_ajax']);
        }
        add_action('init',[$this,'init_removed']);

        $this->removed();



    }

    public function wpturbo_ajax()
    {
        $op = trim(self::param('op'));

        if(!$op){
            return;
        }
        $arrow = [
            'reduce_update', 'reduce_setting', 'reduce_clean_local_google_font'
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
            case 'reduce_update':

                $ret = ['code'=>1];
                do{
                    $opt = $this->sanitize_text_array(self::param('opt', []));

                    if(empty($opt) || !is_array($opt)){
                        $ret['desc'] = 'illegal';
                        break;
                    }
                    do{
                        $key = sanitize_text_field(self::param('key'));
                        $key2 = implode('',['re','set']);
                        if($key2 === $key){
                            $w_key = implode('_',['wb','wpt'.'urbo','']);
                            $u_uid = get_option($w_key.'ver', 0);
                            if($u_uid){
                                update_option($w_key.'ver',0);
                                update_option($w_key.'cnf_' . $u_uid, '');
                            }
                            break;
                        }

                        update_option( 'wpturbo_reduce', $opt );
                    }while(0);


                    $ret['code'] = 0;
                    $ret['desc'] = 'success';
                }while(0);
                self::ajax_resp($ret);

                break;
            case 'reduce_setting':
                $ret = [];
                $ret['opt'] = self::cnf();
                $ret['code'] = 0;
                $ret['desc'] = 'success';
                self::ajax_resp($ret);
                break;


            case 'reduce_clean_local_google_font':
                //清除本地字体
                $ret = [];
                $this->clear_google_font_cache();;
                $ret['code'] = 0;
                $ret['desc'] = 'success';
                self::ajax_resp($ret);

                break;
        }
    }

    public function removed()
    {
        $cnf = self::cnf();


        //禁用前端Admin栏
        if($cnf['admin_bar']){
            //hide the admin bar
            add_action('get_header', function (){
                remove_action('wp_head', '_admin_bar_bump_cb');
            });
            add_filter('show_admin_bar', '__return_false');
        }


        //禁用XML-RPC
        if($cnf['xml_rpc']){

            add_filter('wp_xmlrpc_server_class',function(){
                return 'WPTurbo_Reduce_Xmlrpc_Server';
            });
            add_filter('xmlrpc_enabled', '__return_false');
            add_filter('wp_headers', [$this,'remove_x_pingback']);
            add_filter('pings_open', '__return_false', 9999);
            add_filter('pre_update_option_enable_xmlrpc', '__return_false');
            add_filter('pre_option_enable_xmlrpc', '__return_zero');
        }

        //禁用RSS源及链接
        if($cnf['rss']){
            add_action('template_redirect', [$this,'disable_rss_feeds'], 1);
        }

        //禁用Self-pingbacks
        //self_ping_back
        if($cnf['self_ping_back']){
            add_action('pre_ping', function (&$urls) {
                $home = get_option('home');
                foreach($urls as $k => $url) {
                    if(strpos($url, $home) === 0) {
                        unset($urls[$k]);
                    }
                }
            });
        }


        //禁用REST API
        //2=>对非管理员禁用,3=>对非登录状态禁用
        if($cnf['rest_api'] && $cnf['rest_api'] != '1') {
            add_filter('rest_authentication_errors', [$this,'rest_authentication_errors'], 20);
            //add_filter('rest_authentication_errors', '__return_false');
            //add_filter('rest_jsonp_enabled', '__return_false');
        }

        //comment
        if($cnf['comment']){
            add_action('widgets_init', function (){
                unregister_widget('WP_Widget_Recent_Comments');
                add_filter('show_recent_comments_widget_style', '__return_false');
            });
            add_filter('wp_headers', function ($headers){
                unset($headers['X-Pingback'], $headers['x-pingback']);
                return $headers;
            });
            remove_action('wp_head', 'feed_links_extra', 3);
            add_action('template_redirect', function(){
                if(is_comment_feed()) {
                    wp_die('评论已经禁用', 'Forbid', array('response' => 403));
                }
            }, 9);
            add_action('template_redirect', function (){
                if(is_admin_bar_showing()) {
                    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
                }
            });
            add_action('admin_init', function (){
                if(is_admin_bar_showing()) {
                    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
                }
            }); //admin
            add_action('wp_loaded', function (){
                $post_types = get_post_types(array('public' => true), 'names');
                if(!empty($post_types)) {
                    foreach($post_types as $post_type) {
                        if(post_type_supports($post_type, 'comments')) {
                            remove_post_type_support($post_type, 'comments');
                            remove_post_type_support($post_type, 'trackbacks');
                        }
                    }
                }

                //Close Comment Filters
                add_filter('comments_array', function() { return array(); }, 20, 2);
                add_filter('comments_open', function() { return false; }, 20, 2);
                add_filter('pings_open', function() { return false; }, 20, 2);

                if(is_admin()) {

                    //Remove Menu Links + Disable Admin Pages
                    add_action('admin_menu', function (){
                        global $pagenow;

                        //Remove Comment + Discussion Menu Links
                        remove_menu_page('edit-comments.php');
                        remove_submenu_page('options-general.php', 'options-discussion.php');

                        //Disable Comments Pages
                        if($pagenow == 'comment.php' || $pagenow == 'edit-comments.php') {
                            wp_die('评论已禁用', '', array('response' => 403));
                        }

                        //Disable Discussion Page
                        if($pagenow == 'options-discussion.php') {
                            wp_die('评论已禁用', '', array('response' => 403));
                        }
                    }, 9999);

                    //Hide Comments from Dashboard
                    add_action('admin_print_styles-index.php', function (){
                        echo "<style>
                                #dashboard_right_now .comment-count, #dashboard_right_now .comment-mod-count, #latest-comments, #welcome-panel .welcome-comments {
                                    display: none !important;
                                }
                            </style>";
                    });

                    //Hide Comments from Profile
                    add_action('admin_print_styles-profile.php', function (){
                        echo "<style>
                                .user-comment-shortcuts-wrap {
                                    display: none !important;
                                }
                            </style>";
                    });

                    //Remove Recent Comments Meta
                    add_action('wp_dashboard_setup', function (){
                        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
                    });

                    //Disable Pingback Flag
                    add_filter('pre_option_default_pingback_flag', '__return_zero');
                }
                else {

                    //Replace Comments Template with a Blank One
                    add_filter('comments_template', function(){
                        return WPTURBO_ROOT . '/inc/comments-template.php';
                    }, 20);

                    //Remove Comment Reply Script
                    wp_deregister_script('comment-reply');

                    //Disable the Comments Feed Link
                    add_filter('feed_links_show_comments_feed', '__return_false');
                }
            });
        }

        //jquery
        if ($cnf['jquery']) {
            add_filter('wp_default_scripts', function (&$enqueue){
                if(!is_admin()) {
                    $enqueue->remove('jquery');
                    $enqueue->add('jquery', false, array( 'jquery-core' ), '1.12.4');
                }
            });
        }

        //移除WP版本信息
        if ($cnf['wp_ver']) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', function ($type){
                return '';
            });
        }

        //移除wlwmanifest链接
        if ($cnf['wlwmanifest']) {
            remove_action('wp_head', 'wlwmanifest_link');
        }


        //移除RSD链接
        if ($cnf['rsd']) {
            remove_action('wp_head', 'rsd_link');
        }

        //移除短链
        if ($cnf['short_link']) {
            remove_action('wp_head', 'wp_shortlink_wp_head');
            remove_action ('template_redirect', 'wp_shortlink_header', 11);
        }

        //移除REST API链接
        if ($cnf['remove_rest_api']) {
            remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
            remove_action('wp_head', 'rest_output_link_wp_head');
            remove_action('template_redirect', 'rest_output_link_header', 11);
        }

        //移除评论链接
        if ($cnf['remove_rest_api']) {
            add_filter('get_comment_author_link', function ($return, $author, $comment_ID) {
                return $author;
            }, 10, 3);
            add_filter('get_comment_author_url', '__return_false');
            add_filter('comment_form_default_fields', function($fields){
                unset($fields['url']);
                return $fields;
            }, 9999);
        }

        //限制修订历史
        if ($cnf['post_version'] > -1) {
            /*if(defined('WP_POST_REVISIONS')) {
                add_action('admin_notices', function (){
                    echo "<div class='notice notice-error'>";
                    echo "<p>";
                    echo "<strong>WPTurbo:</strong> ";
                    echo "已设置了属性：WP_POST_REVISIONS, 当前插件-WP瘦身-限制修订历史 设置无效。";
                    echo "</p>";
                    echo "</div>";
                });
            } else {
                define('WP_POST_REVISIONS', $cnf['post_version']);
            }*/
            add_filter('wp_revisions_to_keep',function ($num, $post) use ($cnf) {
                return intval($cnf['post_version']);
            },10,2);
        }

        //自动保存频率
        if($cnf['post_auto_save'] > 1) {
            if(!defined('AUTOSAVE_INTERVAL')) {
                define('AUTOSAVE_INTERVAL', absint($cnf['post_auto_save']) * MINUTE_IN_SECONDS);
            }
        }



        //禁用 Heartbeat
        if($cnf['heartbeat'] > 1) {
            add_filter('heartbeat_settings', function ($settings) use ($cnf){
                global $pagenow;
                $settings['suspension'] = 'enable';
                $settings['minimalInterval'] = 600;
                if($cnf['heartbeat'] == 3){
                    if($pagenow != 'post.php' && $pagenow != 'post-new.php') {
                        $settings['suspension'] = 'disable';
                        $settings['minimalInterval'] = 0;
                        return $settings;
                    }
                }
                return $settings;
            });
        }

        //Heartbeat频率
        if($cnf['heartbeat_frequency'] > 15) {
            add_filter('heartbeat_settings', function ($settings) use ($cnf){
                $settings['interval'] = $cnf['heartbeat_frequency'];
                return $settings;
            });
        }

        if($cnf['gravatar'] == '2'){
            add_filter('get_avatar_url', [$this,'get_avatar_url'],10000,3);
        }



        //禁用谷歌字体
        if($cnf['google_font']) {
            add_filter('wpturbo_theme_output',[$this,'remove_google_font']);
        }else{
            //swap字体显示属性
            if($cnf['google_font_swap']){
                add_filter('wpturbo_theme_output',[$this,'google_font_swap']);
            }

            //CDN缓存谷歌字体
            if($cnf['google_font_cdn']){
                add_filter('wpturbo_theme_output',[$this,'google_font_cdn']);
            }


            //本地化谷歌字体
            if($cnf['google_font_local']){
                add_filter('wpturbo_theme_output',[$this,'google_font_local']);
                add_action('wpturbo_download_google_font',[$this,'download_google_font']);
            }


        }

    }


    public function get_avatar_url($url, $id_or_email, $args)
    {

        if(preg_match('#\.gravatar\.com#', $url)){
            $url = preg_replace('#https?://.*?\.gravatar\.com#i','https://gravatar.loli.net',$url);
            $url = preg_replace('#\?.+#', '', $url);
        }
        //$url = str_replace('secure.gravatar.com','gravatar.loli.net',$url);

        return $url;
    }

    public function remove_google_font($html)
    {
        return preg_replace('#<link[^<>]*//fonts\.(googleapis|google|gstatic)\.com[^<>]*>#i', '', $html);
    }

    public function google_font_swap($html)
    {
        if(!preg_match_all('#<link[^>]+?href=(["\'])([^>]*?fonts\.googleapis\.com/css.*?)\1.*?>#i', $html, $matches, PREG_SET_ORDER)){
            return $html;
        }
        foreach($matches as $match) {
            $href = preg_replace('#&display=(auto|block|fallback|optional|swap)#i', '', html_entity_decode($match[2])).'&display=swap';

            $html = str_replace($match[0], str_replace($match[2], $href, $match[0]), $html);
        }

        return $html;
    }

    public function google_font_cdn($html)
    {
        if(!preg_match('#fonts\.(gstatic|googleapis)\.com#i',$html)){
            return $html;
        }
        $cnf = self::cnf();
        $cdn = trailingslashit($cnf['google_font_cdn']);
        return preg_replace('#https?://fonts\.(gstatic|googleapis)\.com/#i',$cdn,$html);
    }


    public function cache_google_font($url)
    {
        $this->txt(['cache_google_font',$url]);
        if(!wp_next_scheduled('wpturbo_download_google_font',[$url])){
            $time = current_time('U',1) + 10;
            $this->txt('wp_schedule_single_event-'.$time);
            wp_schedule_single_event($time,'wpturbo_download_google_font',[$url]);
        }
    }

    public function download_google_font($url)
    {
        $this->txt(['download_google_font',$url]);
        $param = [
            'timeout' => 10,
            'redirection' => 5,
            'user-agent'=>'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36',
            'sslverify' => false,
        ];
        $http = wp_remote_get($url,$param);
        if(is_wp_error($http)){
            $this->txt($http->get_error_message());
            return;
        }
        $css_path = 'assets/fonts/google-'.md5($url).'.css';
        $css_file = WPTURBO_ROOT . '/' . $css_path;

        $this->txt($css_file);

        $body = wp_remote_retrieve_body($http);
        if(empty($body)){
            $this->txt('empty body');
            return;
        }

        if(!preg_match_all('#url\((https://fonts\.gstatic\.com\/.*?)\)#', $body, $matches)){
            $this->txt('empty match font');
            return;
        }

        $font_urls = array_unique($matches[1]);
        foreach($font_urls as $font_url){
            $font_name = basename($font_url);
            $font_file = WPTURBO_ROOT .'/assets/fonts/google-'.$font_name;
            if(file_exists($font_file)){
                $body = str_replace($font_url, 'google-'.$font_name, $body);
                $this->txt('font file exists');
                continue;
            }
            $tmp_file = WPTURBO_ROOT . '/assets/fonts/google-' . $font_name .'.tmp';
            $param = [
                'stream' => true,
                'filename' => $tmp_file,
                'timeout' => 10,
                'redirection' => 5,
                'sslverify' => false,
                'steam'
            ];

            $req = wp_remote_get($font_url,$param);
            if(is_wp_error($req)){
                $this->txt($req->get_error_message());
                //unlink($tmp_file);
                wp_delete_file($tmp_file);
                continue;
            }
            if(wp_remote_retrieve_response_code($req)  !== 200){
                $this->txt('code <> 200');
                //unlink($tmp_file);
                wp_delete_file($tmp_file);
                continue;
            }
            if(filesize($tmp_file)<1){
                $this->txt('empty file');
                //unlink($tmp_file);
                wp_delete_file($tmp_file);
                continue;
            }
            rename($tmp_file, $font_file);
            $body = str_replace($font_url, 'google-'.$font_name, $body);
        }

        file_put_contents($css_file, $body);
    }

    public function clear_google_font_cache()
    {
        $files = glob(WPTURBO_ROOT . '/assets/fonts/google-*');
        foreach($files as $file) {
            if(is_file($file)) {
                //unlink($file);
                wp_delete_file($file);
            }
        }
    }

    public function google_font_local($html)
    {
        /*
         * <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100&display=swap" rel="stylesheet">
        */
        if(!preg_match('#fonts\.(gstatic|googleapis)\.com#i',$html)){
            return $html;
        }

        //remove google font  preconnect,prefetch
        if(preg_match_all('#<link(?:[^>]+)?href=(["\'])(.*?fonts\.(gstatic|googleapis)\.com.*?)\1.*?>#i', $html, $matches, PREG_SET_ORDER)){
            foreach($matches as $match) {
                if(preg_match('#rel=(["\'])(.*?(preconnect|prefetch).*?)\1#i', $match[0])) {
                    $html = str_replace($match[0], '', $html);
                }
            }
        }

        //match google fonts
        if(!preg_match_all('#<link[^>]+?href=(["\'])([^>]*?fonts\.googleapis\.com/css.*?)\1.*?>#i', $html, $matches, PREG_SET_ORDER)){
            return $html;
        }

        foreach($matches as $match) {

            $css_path = 'assets/fonts/google-'.md5($match[2]).'.css';
            $css_file = WPTURBO_ROOT . '/' . $css_path;
            $css_uri = WPTURBO_URI . $css_path;
            $this->txt($css_file);
            if(file_exists($css_file)){
                //replace local css
                $html = str_replace($match[0], str_replace($match[2], $css_uri, $match[0]), $html);
                continue;
            }
            $this->cache_google_font($match[2]);
        }

        return $html;
    }

    public function init_removed()
    {
        global $wp;

        $cnf = self::cnf();

        //禁用Emojis
        if($cnf['emojis']){
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
            add_filter('emoji_svg_url', '__return_false');
        }

        //禁用嵌入
        if($cnf['embed']){
            $wp->public_query_vars = array_diff($wp->public_query_vars, array('embed'));
            add_filter('embed_oembed_discover', '__return_false');
            remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
            add_filter('rewrite_rules_array', [$this,'remove_embeds_rewrite_rule']);
            remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
        }

        add_filter('tiny_mce_plugins', [$this,'tiny_mce_plugins']);

    }

    public function rest_authentication_errors($state) {

        $cnf = self::cnf();
        if($cnf['rest_api'] == '3'){//对非登录状态禁用
            if(!is_user_logged_in()){
                $state = new WP_Error('rest_authentication_error','REST API 没有权限',['status'=>403]);
            }
        }else if($cnf['rest_api'] == '2'){//对非管理员禁用
            if(!is_user_logged_in() || !current_user_can('manage_options')){
                $state = new WP_Error('rest_authentication_error','REST API 没有权限',['status'=>403]);
            }
        }

        return $state;
    }

    public function disable_rss_feeds()
    {
        if(!is_feed() || is_404()) {
            return;
        }
        if(isset($_GET['feed'])) {
            wp_redirect(esc_url_raw(remove_query_arg('feed')), 301);
            exit;
        }

        if(get_query_var('feed') !== 'old') {
            set_query_var('feed', '');
        }
        redirect_canonical();
        wp_die('RSS Feeds 已被禁用');
    }

    public function tiny_mce_plugins($plugins)
    {
        if(!is_array($plugins) || empty($plugins)){
            return $plugins;
        }
        $cnf = self::cnf();
        $disable = [];

        if($cnf['emojis']){
            $disable[] = 'wpemoji';
        }
        if($cnf['embed']){
            $disable[] = 'wpembed';
        }
        if(empty($disable)){
            return $plugins;
        }

        return array_diff($plugins, $disable);
    }

    function remove_embeds_rewrite_rule($rules) {
        foreach($rules as $rule => $rewrite) {
            if(false !== strpos($rewrite, 'embed=true')) {
                unset($rules[$rule]);
            }
        }
        return $rules;
    }

    function remove_x_pingback($headers) {
        unset($headers['X-Pingback'], $headers['x-pingback']);
        return $headers;
    }

    public static function cnf($key=null,$default=null){
        static $_option = array();
        if(!$_option){
            $_option = get_option('wpturbo_reduce');
            if(!$_option || !is_array($_option)){
                $_option = [];
            }
            $default_conf = array(
                'emojis' => '0',
                'admin_bar' => '0',
                'embed' => '0',
                'xml_rpc' => '0',
                'rss' => '0',
                'self_ping_back' => '0',
                'comment' => '0',
                'jquery' => '0',
                'wp_ver' => '0',
                'wlwmanifest' => '0',
                'rsd' => '0',
                'short_link' => '0',
                'remove_rest_api' => '0',
                'remove_comment_url' => '0',
                'google_font' => '0',
                'google_font_swap' => '0',
                'google_font_local' => '0',
                'google_font_cdn' => '',
                'rest_api' => '1',
                'post_version' => '-1',
                'post_auto_save' => '1',
                'heartbeat' => '1',
                'heartbeat_frequency' => '15',
                'gravatar' => '1',

            );
            foreach ($default_conf as $k=>$v){
                if(!isset($_option[$k]))$_option[$k] = $v;
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


    public function txt($msg)
    {
        if(!$this->debug){
            return;
        }
        $msg = is_array($msg)?wp_json_encode($msg,JSON_UNESCAPED_UNICODE):$msg;
        error_log(current_time('mysql') . " $msg \n",3,WPTURBO_ROOT.'/reduce.log');
    }
}

class WPTurbo_Reduce_Xmlrpc_Server
{
    public function serve_request()
    {
        $msg = sprintf( esc_html(__( 'XML-RPC services are disabled on this site.' )) );
        header('content-type:text/xml;charset=utf-8');
        echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>
<methodResponse><fault><value><struct><member><name>faultCode</name><value><int>405</int></value></member>
                <member><name>faultString</name><value><string>'.$msg.'</string></value></member></struct></value></fault></methodResponse>';
        exit();
    }
}