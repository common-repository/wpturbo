<?php

/**
 * Class WPTurbo_Base
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Base
{
    public static function db()
    {
        static $db = null;
        if($db){
            return $db;
        }
        $db = $GLOBALS['wpdb'];
        if($db instanceof wpdb){
            return $db;
        }
        return $db;
    }

    public static function param($key, $default = '', $type = 'p'){
        if('p' === $type){
            if(isset($_POST[$key])){
                return $_POST[$key];
            }
            return $default;
        } else if ('g' === $type){
            if(isset($_GET[$key])){
                return $_GET[$key];
            }
            return $default;
        }
        if(isset($_POST[$key])){
            return $_POST[$key];
        }
        if(isset($_GET[$key])){
            return $_GET[$key];
        }
        return $default;
    }

    public static function ajax_resp($ret)
    {
        header('content-type:text/json;charset=utf-8');
        echo wp_json_encode($ret);
        exit();
    }

    public function sanitize_text_array($v,$skip_key = []){

        if(is_array($v))foreach($v as $sk=>$sv){
            if($skip_key && in_array($sk,$skip_key)){
                continue;
            }
            if(is_array($sv)){
                $v[$sk] = $this->sanitize_text_array($sv,$skip_key);
            }else if(is_string($sv)){
                $v[$sk] = sanitize_text_field($sv);
            }
        }else if(is_string($v)){
            $v = sanitize_text_field($v);
        }
        return $v;
    }
}