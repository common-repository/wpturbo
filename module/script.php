<?php

/**
 * Class WPTurbo_Script
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Script extends WPTurbo_Base
{
    public function __construct()
    {
        if (!is_admin()) {
            $opt = self::cnf();

            //js管理

            add_action('wp', [$this, 'js_init']);

            if ($opt['js_custom_head']) {
                add_action('wp_head', [$this, 'js_custom_head']);
            }
            if ($opt['js_custom_body']) {
                add_action('wp_body_open', [$this, 'js_custom_body']);
            }
            if ($opt['js_custom_footer']) {
                add_action('wp_footer', [$this, 'js_custom_footer']);
            }
        }
    }

    public function js_init()
    {

        $cnf = self::cnf();

        if ($cnf['js_defer'] || $cnf['js_delay']) {

            //actions + filters
            add_filter('wpturbo_theme_output', [$this, 'optimize_js'], 200);
            do {
                if (!$cnf['js_delay'] || is_admin() || is_embed() || is_feed() || is_customize_preview()) {
                    break;
                }
                if ($this->is_dynamic_request()) {
                    break;
                }
                add_action('wp_footer', [$this, 'print_delay_js'], 10000);
            } while (0);
        }
    }

    public function print_delay_js()
    {
        $cnf = self::cnf();

        $timeout = $cnf['js_delay_time'];

        if ($cnf['js_delay']) {

            if ($cnf['js_delay_type'] == 2) { //指定
                echo '<script type="text/javascript" id="wpturob-delayed-scripts-js">' . (!empty($timeout) ? 'const wbDelayTimer = setTimeout(pmLoadDelayedScripts,' . $timeout . '*1000);' : '') . 'const wpturboUserInteractions=["keydown","mousemove","wheel","touchmove","touchstart","touchend"];wpturboUserInteractions.forEach(function(event){window.addEventListener(event,pmTriggerDelayedScripts,{passive:!0})});function pmTriggerDelayedScripts(){pmLoadDelayedScripts();' . (!empty($timeout) ? 'clearTimeout(wbDelayTimer);' : '') . 'wpturboUserInteractions.forEach(function(event){window.removeEventListener(event, pmTriggerDelayedScripts,{passive:!0});});}function pmLoadDelayedScripts(){document.querySelectorAll("script[data-pmdelayedscript]").forEach(function(elem){elem.setAttribute("src",elem.getAttribute("data-pmdelayedscript"));});}</script>';
            } else { //全部
                echo '<script type="text/javascript" id="wpturob-delayed-scripts-js">' . (!empty($timeout) ? 'const pmDelayTimer=setTimeout(pmTriggerDOMListener,' . $timeout . '*1000),' : '') . 'pmUserInteractions=["keydown","mousemove","wheel","touchmove","touchstart","touchend","touchcancel","touchforcechange"],pmDelayedScripts={normal:[],defer:[],async:[]},jQueriesArray=[];var pmDOMLoaded=!1;function pmTriggerDOMListener(){' . (!empty($timeout) ? 'clearTimeout(pmDelayTimer),' : '') . 'pmUserInteractions.forEach(function(e){window.removeEventListener(e,pmTriggerDOMListener,{passive:!0})}),"loading"===document.readyState?document.addEventListener("DOMContentLoaded",pmTriggerDelayedScripts):pmTriggerDelayedScripts()}async function pmTriggerDelayedScripts(){pmDelayEventListeners(),pmDelayJQueryReady(),pmProcessDocumentWrite(),pmSortDelayedScripts(),pmPreloadDelayedScripts(),await pmLoadDelayedScripts(pmDelayedScripts.normal),await pmLoadDelayedScripts(pmDelayedScripts.defer),await pmLoadDelayedScripts(pmDelayedScripts.async),await pmTriggerEventListeners()}function pmDelayEventListeners(){let e={};function t(t,n){function r(n){return e[t].delayedEvents.indexOf(n)>=0?"wpturbo-"+n:n}e[t]||(e[t]={originalFunctions:{add:t.addEventListener,remove:t.removeEventListener},delayedEvents:[]},t.addEventListener=function(){arguments[0]=r(arguments[0]),e[t].originalFunctions.add.apply(t,arguments)},t.removeEventListener=function(){arguments[0]=r(arguments[0]),e[t].originalFunctions.remove.apply(t,arguments)}),e[t].delayedEvents.push(n)}function n(e,t){const n=e[t];Object.defineProperty(e,t,{get:n||function(){},set:function(n){e["wpturbo"+t]=n}})}t(document,"DOMContentLoaded"),t(window,"DOMContentLoaded"),t(window,"load"),t(window,"pageshow"),t(document,"readystatechange"),n(document,"onreadystatechange"),n(window,"onload"),n(window,"onpageshow")}function pmDelayJQueryReady(){let e=window.jQuery;Object.defineProperty(window,"jQuery",{get:()=>e,set(t){if(t&&t.fn&&!jQueriesArray.includes(t)){t.fn.ready=t.fn.init.prototype.ready=function(e){pmDOMLoaded?e.bind(document)(t):document.addEventListener("wpturbo-DOMContentLoaded",function(){e.bind(document)(t)})};const e=t.fn.on;t.fn.on=t.fn.init.prototype.on=function(){if(this[0]===window){function t(e){return e.split(" ").map(e=>"load"===e||0===e.indexOf("load.")?"wpturbo-jquery-load":e).join(" ")}"string"==typeof arguments[0]||arguments[0]instanceof String?arguments[0]=t(arguments[0]):"object"==typeof arguments[0]&&Object.keys(arguments[0]).forEach(function(e){delete Object.assign(arguments[0],{[t(e)]:arguments[0][e]})[e]})}return e.apply(this,arguments),this},jQueriesArray.push(t)}e=t}})}function pmProcessDocumentWrite(){const e=new Map;document.write=document.writeln=function(t){var n=document.currentScript,r=document.createRange();let a=e.get(n);void 0===a&&(a=n.nextSibling,e.set(n,a));var o=document.createDocumentFragment();r.setStart(o,0),o.appendChild(r.createContextualFragment(t)),n.parentElement.insertBefore(o,a)}}function pmSortDelayedScripts(){document.querySelectorAll("script[type=pmdelayedscript]").forEach(function(e){e.hasAttribute("src")?e.hasAttribute("defer")&&!1!==e.defer?pmDelayedScripts.defer.push(e):e.hasAttribute("async")&&!1!==e.async?pmDelayedScripts.async.push(e):pmDelayedScripts.normal.push(e):pmDelayedScripts.normal.push(e)})}function pmPreloadDelayedScripts(){var e=document.createDocumentFragment();[...pmDelayedScripts.normal,...pmDelayedScripts.defer,...pmDelayedScripts.async].forEach(function(t){var n=t.getAttribute("src");if(n){var r=document.createElement("link");r.href=n,r.rel="preload",r.as="script",e.appendChild(r)}}),document.head.appendChild(e)}async function pmLoadDelayedScripts(e){var t=e.shift();return t?(await pmReplaceScript(t),pmLoadDelayedScripts(e)):Promise.resolve()}async function pmReplaceScript(e){return await pmNextFrame(),new Promise(function(t){const n=document.createElement("script");[...e.attributes].forEach(function(e){let t=e.nodeName;"type"!==t&&("data-type"===t&&(t="type"),n.setAttribute(t,e.nodeValue))}),e.hasAttribute("src")?(n.addEventListener("load",t),n.addEventListener("error",t)):(n.text=e.text,t()),e.parentNode.replaceChild(n,e)})}async function pmTriggerEventListeners(){pmDOMLoaded=!0,await pmNextFrame(),document.dispatchEvent(new Event("wpturbo-DOMContentLoaded")),await pmNextFrame(),window.dispatchEvent(new Event("wpturbo-DOMContentLoaded")),await pmNextFrame(),document.dispatchEvent(new Event("wpturbo-readystatechange")),await pmNextFrame(),document.wpturboonreadystatechange&&document.wpturboonreadystatechange(),await pmNextFrame(),window.dispatchEvent(new Event("wpturbo-load")),await pmNextFrame(),window.wpturboonload&&window.wpturboonload(),await pmNextFrame(),jQueriesArray.forEach(function(e){e(window).trigger("wpturbo-jquery-load")}),window.dispatchEvent(new Event("wpturbo-pageshow")),await pmNextFrame(),window.wpturboonpageshow&&window.wpturboonpageshow()}async function pmNextFrame(){return new Promise(function(e){requestAnimationFrame(e)})}pmUserInteractions.forEach(function(e){window.addEventListener(e,pmTriggerDOMListener,{passive:!0})});</script>';
            }
        }
    }

    public function optimize_js($html)
    {

        //strip comments before search
        $html_no_comments = preg_replace('/<!--(.*)-->/Uis', '', $html);

        //match all script tags
        preg_match_all('#(<script\s?([^>]+)?\/?>)(.*?)<\/script>#is', $html_no_comments, $matches);

        //no script tags found
        if (!isset($matches[0])) {
            return $html;
        }

        $cnf = self::cnf();


        $is_js_defer = $cnf['js_defer'];

        //build js exlusions array
        $js_exclusions = array();

        if ($is_js_defer) {

            //add jquery if needed
            if (!$cnf['js_defer_jquery']) {
                array_push($js_exclusions, 'jquery(?:\.min)?.js');
            }

            //add extra user exclusions
            if ($cnf['js_defer_exclude']) {
                $rules = explode("\n", $cnf['js_defer_exclude']);
                foreach ($rules as $line) {
                    $line = trim($line);
                    if (!$line) continue;
                    array_push($js_exclusions, preg_quote($line));
                }
            }

            //convert exlusions to string for regex
            $js_exclusions = implode('|', $js_exclusions);
        }

        $delay_rule = [];
        if ($cnf['js_delay'] && $cnf['js_delay_exclude']) {
            $exclude_rule = explode("\n", $cnf['js_delay_exclude']);
            foreach ($exclude_rule as $s) {
                $s = trim($s);
                if (!$s) continue;
                $delay_rule[] = $s;
            }
        }

        //loop through scripts
        foreach ($matches[0] as $i => $tag) {

            $atts_array = !empty($matches[2][$i]) ? $this->get_atts_array($matches[2][$i]) : array();

            //skip if type is not javascript
            if (isset($atts_array['type']) && stripos($atts_array['type'], 'javascript') == false) {
                continue;
            }

            //delay javascript
            if ($cnf['js_delay']) {

                $delay_flag = false;

                /*if($cnf['js_delay_type'] == 2){

                }else{

                }*/
                if ($cnf['js_delay_type'] == 2) { //指定js

                    if ($delay_rule) foreach ($delay_rule as $delayed_script) {
                        if (strpos($tag, $delayed_script) !== false) {
                            $delay_flag = true;
                            if (!empty($atts_array['src'])) {
                                $atts_array['data-wbdelayedscript'] = $atts_array['src'];
                                unset($atts_array['src']);
                            } else {
                                $atts_array['data-wbdelayedscript'] = "data:text/javascript;base64," . base64_encode($matches[3][$i]);
                            }
                        }
                    }
                } else { //全部js

                    $excluded_scripts = array_merge(array(
                        'wpturbo-delayed-scripts-js',
                        'lazyload',
                        'lazyLoadInstance',
                        'lazysizes'
                    ), $delay_rule);
                    $matched = false;
                    if ($excluded_scripts) foreach ($excluded_scripts as $excluded_script) {
                        if (strpos($tag, $excluded_script) !== false) {
                            $matched = true;
                            break;
                        }
                    }
                    if ($matched) {
                        continue;
                    }

                    $delay_flag = true;

                    if (!empty($atts_array['type'])) {
                        $atts_array['data-wpturbo-type'] = $atts_array['type'];
                    }

                    $atts_array['type'] = 'wbdelayedscript';
                }

                if ($delay_flag) {

                    $atts_array['data-cfasync'] = "false";
                    $atts_array['data-no-optimize'] = "1";
                    $atts_array['data-no-defer'] = "1";
                    $atts_array['data-no-minify'] = "1";



                    $delayed_atts_string = $this->get_atts_string($atts_array);
                    $delayed_tag = sprintf('<script %1$s>', $delayed_atts_string) . ($cnf['js_delay_type'] == 1 ? $matches[3][$i] : '') . '</script>';

                    //replace new full tag in html
                    $html = str_replace($tag, $delayed_tag, $html);

                    continue;
                }
            }

            //defer javascript
            if ($is_js_defer) {

                //src is not set
                if (empty($atts_array['src'])) {
                    continue;
                }

                //check if src is excluded
                if (!empty($js_exclusions) && preg_match('#(' . $js_exclusions . ')#i', $atts_array['src'])) {
                    continue;
                }

                //skip if there is already an async
                if (stripos($matches[2][$i], 'async') !== false) {
                    continue;
                }

                //skip if there is already a defer
                if (stripos($matches[2][$i], 'defer') !== false) {
                    continue;
                }

                //add defer to opening tag
                $deferred_tag_open = str_replace('>', ' defer>', $matches[1][$i]);

                //replace new open tag in original full tag
                $deferred_tag = str_replace($matches[1][$i], $deferred_tag_open, $tag);

                //replace new full tag in html
                $html = str_replace($tag, $deferred_tag, $html);
            }
        }

        return $html;
    }

    public function get_atts_array($atts_string)
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

    function get_atts_string($atts_array)
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


    public function js_custom_head()
    {
        $opt = self::cnf();
        if ($opt['js_custom_head']) {
            echo $opt['js_custom_head'] . "\n";
        }
    }

    public function js_custom_body()
    {
        $opt = self::cnf();
        if ($opt['js_custom_body']) {
            echo $opt['js_custom_body'] . "\n";
        }
    }

    public function js_custom_footer()
    {
        $opt = self::cnf();
        if ($opt['js_custom_footer']) {
            echo $opt['js_custom_footer'] . "\n";
        }
    }

    public static function cnf($key = null, $default = null)
    {
        return WPTurbo_Optimize::cnf($key, $default);
    }

    public function is_dynamic_request()
    {
        if ((defined('REST_REQUEST') && REST_REQUEST) || (function_exists('wp_is_json_request') && wp_is_json_request()) || wp_doing_ajax() || wp_doing_cron()) {
            return true;
        }

        return false;
    }
}
