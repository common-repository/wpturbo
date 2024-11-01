<?php

/**
 * Class WPTurbo_Cdn
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Cdn extends WPTurbo_Base
{
    public function __construct()
    {
        $opt = self::cnf();

        if($opt['switch'] && $opt['cdn_url']){
            add_filter('wpturbo_theme_output',[$this,'cnd_replace']);
        }

    }

    public static function cnf($key=null, $default=null)
    {
        return WPTurbo_Optimize::cnf($key, $default);
    }



    public function cnd_replace($html)
    {
        $opt = self::cnf();

        $siteURL  = '//' . ((!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : wp_parse_url(home_url(), PHP_URL_HOST));
        $escapedSiteURL = quotemeta($siteURL);
        $regExURL = '(https?:|)' . substr($escapedSiteURL, strpos($escapedSiteURL, '//'));

        //prep included directories
        $directories = 'wp\-content|wp\-includes';
        if($opt['cdn_include']) {
            $directoriesArray = array_map('trim', explode(',', $opt['cdn_include']));
            if(count($directoriesArray) > 0) {
                $directories = implode('|', array_map('quotemeta', array_filter($directoriesArray)));
            }
        }

        //rewrite urls in html
        $regEx = '#(?<=[(\"\'])(?:' . $regExURL . ')?/(?:((?:' . $directories . ')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';
        //print_r([]);
        // error_log(print_r([$regEx],true), 3, __DIR__.'/cdn.log');
        //base exclusions
        $exclusions = array('script-manager.js');

        //add user exclusions
        if($opt['cdn_exclude']) {
            $exclusions_user = array_map('trim', explode(',', $opt['cdn_exclude']));
            $exclusions = array_merge($exclusions, $exclusions_user);
        }

        //set cdn url
        $cdnURL = $opt['cdn_url'];

        //replace urls
        $html = preg_replace_callback($regEx, function($url) use ($siteURL, $cdnURL, $exclusions, $directories) {
            //check for exclusions
            foreach($exclusions as $exclusion) {
                if(!empty($exclusion) && stristr($url[0], $exclusion) != false) {
                    return $url[0];
                }
            }
            if($directories && !preg_match('#'.$directories.'#is',$url[0])){
                return $url[0];
            }

            //replace url with no scheme
            if(strpos($url[0], '//') === 0) {
                return str_replace($siteURL, $cdnURL, $url[0]);
            }

            //replace non relative site url
            if(strstr($url[0], $siteURL)) {
                return str_replace(array('http:' . $siteURL, 'https:' . $siteURL), $cdnURL, $url[0]);
            }

            //replace relative url
            return $cdnURL . $url[0];

        }, $html);

        return $html;
    }


}