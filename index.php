<?php
/*
Plugin Name: WPTurbo -WordPress性能优化插件
Plugin URI: http://wordpress.org/plugins/wpturbo/
Version: 2.0.3
Description: WPTurbo如其名，即WordPress的涡轮增压器，是一款专门针对WordPress开发的性能优化插件，效用包括WP瘦身，WP速度优化，数据库优化及对象存储等。
Author: 闪电博
Author URI: https://www.wbolt.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}
define('WPTURBO_ROOT',__DIR__);
define('WPTURBO_BASE',__FILE__);
define('WPTURBO_URI',plugin_dir_url(__FILE__));
define('WPTURBO_VERSION','2.0.3');


require_once __DIR__.'/module/base.php';
require_once __DIR__.'/module/turbo.php';
new WPTurbo();

require_once __DIR__.'/module/reduce.php';
new WPTurbo_Reduce();

require_once __DIR__.'/module/database.php';
new WPTurbo_Database();


require_once __DIR__.'/module/optimize.php';
require_once __DIR__.'/module/cdn.php';
require_once __DIR__.'/module/preload.php';
require_once __DIR__.'/module/lazyload.php';
require_once __DIR__.'/module/script.php';

WPTurbo_Optimize::init();

require_once __DIR__.'/module/storage.php';
new WPTurbo_Storage();


require_once __DIR__.'/module/theme.php';
new WPTurbo_Theme();


//add_filter('use_block_editor_for_post_type',function($is_user,$post_type){return false;},10,2);