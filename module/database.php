<?php


/**
 * Class WPTurbo_Storage
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Database extends WPTurbo_Base
{
    public function __construct()
    {
        $conf = self::cnf();

        if($conf['schedule'] == 'off'){
            //wp_clear_scheduled_hook('wpturbo_optimize_database');
        }else{
            add_action('wpturbo_optimize_database',array($this,'cron_optimize_database'));
            if(!wp_next_scheduled('wpturbo_optimize_database')){

                $schedule = $conf['schedule'];
                if($conf['schedule'] == 'month'){
                    $schedule = 'daily';
                }
                $time = strtotime(current_time('Y-m-d 03:00:00',1)) + 10;
                wp_schedule_event($time, $schedule, 'wpturbo_optimize_database');
            }
        }

        if(is_admin()){
            add_action('wp_ajax_wpturbo', [$this,'wpturbo_ajax']);
        }

    }

    public function cron_optimize_database()
    {

        $opt = self::cnf();
        if($opt['schedule'] == 'off'){
            return;
        }
        if($opt['schedule'] == 'month'){
            if(current_time('d') != '01'){
                return;
            }
        }
        $this->database_optimize($opt);
    }

    public function wpturbo_ajax()
    {
        $op = trim(self::param('op'));
        if(!$op){
            return;
        }
        $arrow = [
            'database_optimize', 'database_update', 'database_setting'
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
            case 'database_optimize':
                $ret = [];
                $opt = $this->sanitize_text_array(self::param('opt', []));
                $this->database_optimize($opt);
                $ret['data'] = $this->database_state();
                $ret['code'] = 0;
                $ret['desc'] = 'success';
                self::ajax_resp($ret);
                break;
            case 'database_update':

                $ret = ['code'=>1];
                do{
                    $opt = $this->sanitize_text_array(self::param('opt', []));

                    if(empty($opt) || !is_array($opt)){
                        $ret['desc'] = 'illegal';
                        break;
                    }
                    $old = self::cnf();
                    if($old['schedule'] != 'off' && $opt['schedule'] == 'off'){
                        wp_clear_scheduled_hook('wpturbo_optimize_database');
                    }
                    update_option( 'wpturbo_database', $opt );

                    $ret['code'] = 0;
                    $ret['desc'] = 'success';
                }while(0);
                self::ajax_resp($ret);
                break;
            case 'database_setting':
                $ret = [];
                $ret['opt'] = self::cnf();
                $ret['data'] = $this->database_state();
                $ret['code'] = 0;
                $ret['desc'] = 'success';
                self::ajax_resp($ret);
                break;
        }
    }


    public function database_optimize($opt)
    {
       // global $wpdb;

        $db = self::db();
        $conf = self::def();
        foreach($conf as $k=>$v){
            $conf[$k] = $opt[$k] ?? $v;
        }


        if($conf['post_revisions']){
            $id_list = $db->get_col("SELECT ID FROM $db->posts WHERE post_type = 'revision'");
            if($id_list) foreach($id_list as $id) {
                wp_delete_post_revision(intval($id));
            }
        }
        if($conf['post_auto_drafts']){
            $id_list = $db->get_col("SELECT ID FROM $db->posts WHERE post_status = 'auto-draft'");
            if($id_list) foreach($id_list as $id) {
                wp_delete_post(intval($id), true);
            }
        }

        if($conf['trashed_posts']){
            $id_list = $db->get_col("SELECT ID FROM $db->posts WHERE post_status = 'trash'");
            if($id_list) foreach($id_list as $id) {
                wp_delete_post(intval($id), true);
            }
        }
        if($conf['spam_comments']){
            $id_list = $db->get_col("SELECT comment_ID FROM $db->comments WHERE comment_approved = 'spam'");
            if($id_list) foreach($id_list as $id) {
                wp_delete_comment(intval($id), true);
            }
        }

        if($conf['spam_comments']){
            $id_list = $db->get_col("SELECT comment_ID FROM $db->comments WHERE (comment_approved = 'trash' OR comment_approved = 'post-trashed')");
            if($id_list) foreach($id_list as $id) {
                wp_delete_comment(intval($id), true);
            }
        }

        if($conf['expired_transients']){
            $list = $db->get_col("SELECT option_name FROM $db->options WHERE option_name REGEXP '^_transient_timeout' AND option_value < ".time());
            if($list) foreach($list as $key) {
                delete_transient(str_replace('_transient_timeout_', '', $key));
            }
        }

        if($conf['all_transients']){
            $list = $db->get_col("SELECT option_name FROM $db->options WHERE option_name REGEXP '^_transient_' ");
            if($list) foreach($list as $key) {
                delete_transient(str_replace('_transient_', '', $key));
            }
            $list = $db->get_col("SELECT option_name FROM $db->options WHERE option_name REGEXP '^_site_transient_' ");
            if($list) foreach($list as $key) {
                delete_site_transient(str_replace('_site_transient_', '', $key));
            }
        }

        if($conf['optimize']){
            $list = $db->get_results("SELECT table_name, data_free FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' and Engine <> 'InnoDB' and data_free > 0");
            if($list) foreach($list as $r) {
                $db->query("OPTIMIZE TABLE $r->table_name");
            }
        }


    }

    public function database_state()
    {
        // global $wpdb;

        $db = self::db();
        $data = [];

        $data['post_revisions'] = $db->get_var("SELECT COUNT(ID) FROM $db->posts WHERE post_type = 'revision'");
        $data['post_auto_drafts'] = $db->get_var("SELECT COUNT(ID) FROM $db->posts WHERE post_status = 'auto-draft'");
        $data['trashed_posts'] = $db->get_var("SELECT COUNT(ID) FROM $db->posts WHERE post_status = 'trash'");
        $data['spam_comments'] = $db->get_var("SELECT COUNT(comment_ID) FROM $db->comments WHERE comment_approved = 'spam'");
        $data['trashed_comments'] = $db->get_var("SELECT COUNT(comment_ID) FROM $db->comments WHERE (comment_approved = 'trash' OR comment_approved = 'post-trashed')");
        $data['expired_transients'] = $db->get_var("SELECT COUNT(option_name) FROM $db->options WHERE `option_name` REGEXP '^_transient_timeout' AND option_value < ".time());
        $data['all_transients'] = $db->get_var("SELECT COUNT(option_id) FROM $db->options WHERE `option_name` REGEXP '^_transient_' OR `option_name` REGEXP '^_site_transient_'");
        $data['optimize'] = $db->get_var("SELECT COUNT(table_name) FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' and Engine <> 'InnoDB' and data_free > 0");
        return $data;
    }

    public static function def()
    {
        $default_conf = array(
            'post_revisions' => '0',
            'post_auto_drafts' => '0',
            'trashed_posts' => '0',
            'spam_comments' => '0',
            'trashed_comments' => '0',
            'expired_transients' => '0',
            'all_transients' => '0',
            'optimize' => '0',
            'schedule' => 'off',
        );
        return $default_conf;
    }

    public static function cnf($key=null,$default=null){
        static $_option = array();
        if(!$_option){
            $_option = get_option('wpturbo_database');
            if(!$_option || !is_array($_option)){
                $_option = [];
            }
            $default_conf = self::def();

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

}
