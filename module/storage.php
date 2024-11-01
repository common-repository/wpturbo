<?php

/**
 * Class WPTurbo_Storage
 * @author quaniz
 * Author URI: https://www.wbolt.com
 */

class WPTurbo_Storage extends WPTurbo_Base
{

    public $debug = false;

    public function __construct()
    {
        if(is_admin()){
            add_action('wp_ajax_wpturbo', [$this,'wpturbo_ajax']);
        }
        $opt = self::cnf();
        if($opt['switch']){
            add_filter('wp_generate_attachment_metadata',[$this,'generate_attachment_metadata'],100,3);
            add_action( 'delete_attachment', [$this,'delete_attachment'],10,2 );
            $oss_domain = $this->oss_domain();
            if($oss_domain && ($opt['oss_mode'] == 'server' || $opt['oss_mode'] == 'server_adv')){// && $opt['domain']
                add_filter('wp_get_attachment_metadata',[$this,'get_attachment_metadata'],900,2);
                add_filter('wp_calculate_image_srcset',[$this,'calculate_image_srcset'],20,5);
                add_filter('wp_get_attachment_url',[$this,'get_attachment_url'],20,2);
                //add_filter('wp_get_attachment_image_src',[$this,'get_attachment_image_src'],20,4);
                //add_filter('image_get_intermediate_size',[$this,'get_intermediate_size'],20,3);
                //add_filter('image_downsize',[$this,'image_downsize'],20,2);
                /*add_filter('wp_calculate_image_srcset_meta',function ($image_meta, $size_array, $image_src, $attachment_id){
                    print_r($image_meta);
                    return $image_meta;
                },20,4);*/
            }
        }
        //add_filter('the_content',[$this,'the_content'],10);
    }

    public function the_content($content)
    {
        $opt = self::cnf();
        if($opt['switch']){
            return $content;
        }
        //oss domain
        $oss_domain = $this->oss_domain();
        if(!$oss_domain){
            return $content;
        }
        $content = preg_replace('#https?://'.preg_quote($oss_domain).'/#i',home_url('/'),$content);
        //print_r([$opt['domain']]);
        /*if(preg_match_all("#<img.+?src=('|\")(.+?".preg_quote($opt['domain']).".+?)\\1.+?>#is",$content,$match)){
            //print_r($match);
            foreach($match[2] as $src){
                $src_new = preg_replace('#https?://'.preg_quote($opt['domain']).'/#i',home_url('/wp-content/uploads/'),$src);
                $src_new = preg_replace('#\?.+#','',$src_new);
                $content = str_replace($src,$src_new,$content);
            }
            //$content = preg_replace('#https?://'.preg_quote($opt['domain']).'/#i',home_url('/wp-content/uploads/'),$content);
            //$content = preg_replace('#\?x-oss-[^\s\'"]+#i','',$content);
        }*/

        return $content;
    }

    public function get_intermediate_size($data, $post_id, $size)
    {
        //print_r($data);
        return $data;
    }

    public function get_attachment_image_src($image, $attachment_id, $size, $icon)
    {
        $oss = get_post_meta($attachment_id,'wb-oss',1);
        if(!$oss){
            return $image;
        }

        //print_r($image);

        return $image;
    }

    public function get_attachment_url($url,$post_id)
    {
        //print_r([$url]);
        $oss = get_post_meta($post_id,'wb-oss',true);
        if(!$oss){
            return $url;
        }

        $file = get_post_meta( $post_id, '_wp_attached_file', true );
        if(!$file){
            return $url;
        }
        $base_path = preg_replace('#https?://[^/]+/#','',$url);

        //$opt = self::cnf();
        $new = 'http://';
        if(is_ssl()){
            $new = 'https://';
        }
        $oss_domain = $this->oss_domain();
        return $new.trailingslashit( $oss_domain).$base_path;
        //$new .= trailingslashit( $opt['domain']).'/'.$file;
        //print_r([$new]);
        //return $new;
    }

    public function calculate_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id)
    {
        //print_r([$sources, $size_array, $image_src, $image_meta, $attachment_id]);
        //print_r($sources);
        $oss = get_post_meta($attachment_id,'wb-oss',true);
        if(!$oss){
            return $sources;
        }
        if(empty($sources) || !is_array($sources)){
            return $sources;
        }
        //$opt = self::cnf();
        $oss_domain = $this->oss_domain();
        $new_base = 'http://';
        if(is_ssl()){
            $new_base = 'https://';
        }
        $new_base .= trailingslashit( $oss_domain);

        //$upload_dir    = wp_get_upload_dir();
        //$image_baseurl = trailingslashit( $upload_dir['baseurl'] );
        //print_r([$image_baseurl,$new_base]);
        foreach($sources as $k=>$r){
            $sources[$k]['url'] = preg_replace('#^https?://[^/]+/#',$new_base,$r['url']);
        }

        //print_r($sources);

        return $sources;
    }

    public function get_attachment_metadata($data,$attachment_id)
    {
        $oss = get_post_meta($attachment_id,'wb-oss',true);
        if(!$oss){
            return $data;
        }
        if(!isset($data['sizes']) || empty($data['sizes'])){
            return $data;
        }

        $file = $data['file'] ?? '';
        if(!$file){
            $file = get_post_meta($attachment_id,'_wp_attached_file',true);
        }
        if(preg_match('#\.pdf$#',$file)){
            return $data;
        }
        $opt = self::cnf();
        $vendor = $opt['vendor'];

        $name = basename($file);
        if(!isset($data['sizes'])){
            return $data;
        }

        foreach($data['sizes'] as $s=>$r){
            if(!isset($r['file'])){
                continue;
            }
            if(!preg_match('#\.(png|jpg|jpeg|webp|gif)$#i',$r['file'])){
                continue;
            }
            $rule = '';
            if(isset($r['width'],$r['height'])){
                if($vendor == 'aliyun'){
                    $rule = '?x-oss-process=image/resize,m_lfit';
                    if($r['width']>0){
                        $rule .= ',w_'.$r['width'];
                    }
                    if($r['height']>0){
                        $rule .= ',h_'.$r['height'];
                    }
                }else if($vendor == 'tencent'){
                    $rule = '?imageMogr2/thumbnail/';
                    if($r['width']>0){
                        $rule .= $r['width'];
                    }
                    if($r['height']>0){
                        $rule .= 'x'.$r['height'];
                    }else{
                        $rule .= 'x';
                    }
                }else if($vendor == 'huawei'){
                    $rule = '?x-image-process=image/resize,m_lfit';
                    if($r['width']>0){
                        $rule .= ',w_'.$r['width'];
                    }
                    if($r['height']>0){
                        $rule .= ',h_'.$r['height'];
                    }
                }else if($vendor == 'baidu'){
                    $rule = '?x-bce-process=image/resize,m_lfit';
                    if($r['width']>0){
                        $rule .= ',w_'.$r['width'];
                    }
                    if($r['height']>0){
                        $rule .= ',h_'.$r['height'];
                    }

                }
            }
            $data['sizes'][$s]['file'] = $name.''.$rule;
        }
        //print_r($data);

        return $data;
    }


    public function delete_attachment($post_id, $post)
    {
        $oss = get_post_meta($post_id,'wb-oss',1);
        if(!$oss){
            return;
        }
        try{
            do{
                $file = get_post_meta($post_id,'_wp_attached_file',true);
                if(!$file){
                    break;
                }
                $upload = wp_get_upload_dir();
                $baseurl = preg_replace('#https?://[^/]+/#','',$upload['baseurl'].'/');
                $this->delete($baseurl.$file);
                if(preg_match('#\.(png|jpg|jpeg|webp|gif)$#i',$file)){
                    break;
                }
                $base_path = dirname($file);
                $meta = get_post_meta($post_id,'_wp_attachment_metadata',true);
                if(isset($meta['sizes']))foreach($meta['sizes'] as $s=>$r){
                    if(!isset($r['file']))continue;
                    $this->delete($baseurl.$base_path.'/'.$r['file']);
                }
            }while(0);
        }catch (Exception $ex){

        }
    }


    public function generate_attachment_metadata($metadata, $attachment_id, $state)
    {
        if($state != 'create'){
            return $metadata;
        }

        try{
            do{
                //2022/06/filename
                $file = get_post_meta($attachment_id,'_wp_attached_file',true);
                if(!$file){
                    break;
                }
                $upload = wp_get_upload_dir();
                /*Array
                    (
                        [path] => /home/www/wordpress/wp-content/uploads/2022/03
                        [url] => http://domain.com/wp-content/uploads/2022/03
                        [subdir] => /2022/03
                        [basedir] => /home/www/wordpress/wp-content/uploads
                        [baseurl] => http://domain.com/wp-content/uploads
                        [error] =>
                    )*/
                $baseurl = preg_replace('#https?://[^/]+/#','',$upload['baseurl'].'/');
                $local_file = $upload['basedir'].'/'.$file;
                if(!file_exists($local_file)){
                    break;
                }
                $opt = self::cnf();

                $result = $this->upload($baseurl.$file,$local_file);
                $base_path = dirname($file);

                update_post_meta($attachment_id,'wb-oss','1');
                $sizes = $metadata['sizes'] ?? [];
                $folder = dirname($local_file);
                if(!preg_match('#\.(png|jpg|jpeg|webp|gif)$#i',$file)){
                    foreach($sizes as $r){
                        $thumb_file = $folder.'/'.$r['file'];
                        $this->upload($baseurl.$base_path.'/'.$r['file'],$thumb_file);
                    }
                }

                $del_local = $opt['oss_mode'] == 'server' && $opt['local'] == 'delete';

                if($opt['oss_mode'] == 'server_adv' || $del_local){
                    // unlink($local_file);
                    wp_delete_file($local_file);
                    copy(WPTURBO_ROOT.'/assets/empty.gif',$local_file);
                    foreach($sizes as $r){
                        $thumb_file = $folder.'/'.$r['file'];
                        // unlink($thumb_file);
                        wp_delete_file($thumb_file);
                    }
                }

            }while(0);

        }catch (Exception $ex){
            $this->txt_log($ex->getMessage());

        }

        return $metadata;

    }

    public function txt_log($msg)
    {
        if(!$this->debug){
            return;
        }
        error_log($msg."\n", 3, __DIR__.'/storage_run.log');
    }

    public function delete($path)
    {
        $opt = self::cnf();

        $client = $this->get_client();
        if(is_wp_error($client)){
            throw new Exception(esc_html($client->get_error_message()));
        }
        $vendor = $opt['vendor'];
        $oss = $opt[$vendor] ?? [];
        $result = null;
        switch ($vendor)
        {
            case 'aliyun':
                $result = $client->deleteObject($oss['bucket'], $path);
                break;
            case 'tencent':
                $result = $client->deleteObject(array(
                    'Bucket' => $oss['bucket'],
                    'Key' => $path
                ));
                break;
            case 'huawei':
                $result = $client->deleteObject(
                    [
                        'Bucket' => $oss['bucket'],
                        'Key' => $path
                    ]
                );

                break;
            case 'baidu':
                try{
                    $result = $client->deleteObject($oss['bucket'],$path);
                }catch (BaiduBce\Exception\BceServiceException $ex){

                }
                break;
        }


        return $result;
    }

    /**
     * 上传
     * @param $path
     * @param $local_file
     * @throws Exception
     */
    public function upload($path,$local_file)
    {
        $opt = self::cnf();

        $client = $this->get_client();
        if(is_wp_error($client)){
            throw new Exception(esc_html($client->get_error_message()));
        }
        $vendor = $opt['vendor'];
        $oss = $opt[$vendor] ?? [];
        $result = null;
        switch ($vendor)
        {
            case 'aliyun':
                $result = $client->uploadFile($oss['bucket'], $path, $local_file);
                break;
            case 'tencent':
                $result = $client->upload($oss['bucket'], $path, fopen($local_file, 'rb'));
                break;
            case 'huawei':
                $result = $client->putObject([
                    'Bucket' => $oss['bucket'],
                    'Key' => $path,
                    'SourceFile' => $local_file
                ]);
                break;
            case 'baidu':
                $result = $client->putObjectFromFile($oss['bucket'],$path,$local_file);
                break;
        }


        return $result;
    }

    public function exists($path)
    {
        $opt = self::cnf();

        $client = $this->get_client();
        if(is_wp_error($client)){
            throw new Exception(esc_html($client->get_error_message()));
        }
        $vendor = $opt['vendor'];
        $oss = $opt[$vendor] ?? [];
        $result = null;
        switch ($vendor)
        {
            case 'aliyun':
                $result = $client->doesObjectExist($oss['bucket'], $path);
                break;
            case 'tencent':
                $result = $client->doesObjectExist($oss['bucket'], $path);
                break;
            case 'huawei':
                try{
                    $result = $client->getObjectMetadata([
                        'Bucket' => $oss['bucket'],
                        'Key' => $path,
                    ]);
                    return true;
                }catch (Obs\ObsException $ex){
                    return false;
                }

                break;
            case 'baidu':
                try {
                    $result = $client->getObjectMetadata($oss['bucket'], $path);
                    return true;
                }catch(BaiduBce\Exception\BceServiceException $ex){
                    return false;
                }
                break;
        }


        return $result;
    }

    public function get_client()
    {
        static $instance = null;
        if($instance!=null){
            return $instance;
        }

        $opt = self::cnf();
        $vendor = $opt['vendor'];
        $oss = $opt[$vendor] ?? [];
        if(empty($oss)){
            return new WP_Error('500','没有找到OSS配置');
        }
        $empty = 0;
        foreach ($oss as $v){
            if(empty($v)){
                $empty = 1;
                break;
            }
        }
        if($empty){
            return new WP_Error('500','没有找到OSS配置');
        }

        require_once WPTURBO_ROOT.'/vendor/autoload.php';
        $client = null;
        switch ($vendor){
            case 'aliyun':

                try {
                    $client = new OSS\OssClient($oss['id'], $oss['key'], $oss['endpoint']);
                } catch (OSS\Core\OssException $e) {
                    return new WP_Error('500',$e->getMessage());
                }
                break;
            case 'tencent':

                $client = new Qcloud\Cos\Client(
                    [
                        'region' => $oss['endpoint'],
                        'schema' => is_ssl()?'https':'http', //协议头部，默认为http
                        'credentials'=> [
                            'secretId'  => $oss['id'] ,
                            'secretKey' => $oss['key']
                        ]
                    ]);
                break;
            case 'huawei':
                $client = Obs\ObsClient::factory ( [
                    'key' => $oss['id'],
                    'secret' => $oss['key'],
                    'endpoint' => $oss['endpoint'],
                    'socket_timeout' => 30,
                    'connect_timeout' => 10
                ] );
                break;

            case 'baidu':
                require_once WPTURBO_ROOT.'/vendor/BaiduBce.phar';
                $client = new BaiduBce\Services\Bos\BosClient(
                    [
                        'credentials' => [
                            'accessKeyId' => $oss['id'],
                            'secretAccessKey' => $oss['key'],
                            //'sessionToken' => 'your session token'
                        ],
                        'endpoint' => str_replace($oss['bucket'].'.','',$oss['endpoint']),
                        //'endpoint' => $oss['endpoint'],
                        //'stsEndpoint' => 'sts host',
                    ]
                );

                break;

        }
        if(!$client){
            $client = new WP_Error('500','OSS初始化错误');
        }else{
            $instance = $client;
        }

        return $client;
    }

    public function wpturbo_ajax()
    {
        $op = trim(self::param('op'));
        if(!$op){
            return;
        }
        $allow = [
            'storage_update', 'storage_setting', 'storage_upload_media_to_oss'
        ];
        if(!in_array($op, $allow)){
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
            case 'storage_update':

                $ret = ['code'=>1,'desc'=>'fail'];
                do{
                    $opt = $this->sanitize_text_array(self::param('opt', []));

                    if(empty($opt) || !is_array($opt)){
                        $ret['desc'] = 'illegal';
                        break;
                    }

                    if($opt['conf'] == 'file'){
                        $vendor = $opt['vendor'];
                        $oss = $opt[$vendor] ?? [];
                        if($oss){
                            $this->update_conf_file($oss);
                            $opt[$vendor]['id'] = '';
                            $opt[$vendor]['key'] = '';
                            $opt[$vendor]['bucket'] = '';
                            $opt[$vendor]['endpoint'] = '';
                        }
                    }
                    if($opt){
                        update_option( 'wpturbo_storage', $opt );
                    }

                    $ret['code'] = 0;
                    $ret['desc'] = 'success';
                }while(0);

                self::ajax_resp($ret);

                break;
            case 'storage_setting':
                $ret = [];
                $ret['opt'] = self::cnf();
                $ret['dir'] = wp_upload_dir();
                $ret['media_num'] = $this->media_count();
                $ret['code'] = 0;
                $ret['desc'] = 'success';
                self::ajax_resp($ret);

                break;
            case 'storage_upload_media_to_oss':

                do {
                    $total = intval(self::param('total'));
                    if($total < 1){
                        $ret = ['code'=>0,'data'=>0,'desc'=>'success'];
                        break;
                    }
                    $this->upload_media_to_oss(20);
                    $ret = ['code'=>0,'data'=>max($total - 20,0),'desc'=>'success'];

                }while(0);

                self::ajax_resp($ret);

                break;

        }
    }


    public function upload_media_to_oss($num)
    {
        // global $wpdb;
        $db = self::db();
        $sql = "SELECT ID
			FROM $db->posts  a
			WHERE  a.post_type = 'attachment' AND a.post_mime_type LIKE 'image/%'  AND ( 
  NOT EXISTS (SELECT 1 FROM $db->postmeta mt1 WHERE mt1.post_ID = a.ID AND mt1.meta_key = 'wb-oss' LIMIT 1)
)";
        $list = $db->get_col($sql." LIMIT $num");

        if($list)foreach($list as $ID){
            $meta = get_post_meta($ID,'_wp_attachment_metadata',true);
            if(!$meta)continue;
            $this->generate_attachment_metadata($meta,$ID,'create');
        }

    }

    public function media_count()
    {
        /// global $wpdb;

        $db = self::db();
        $sql = "SELECT COUNT(1)
			FROM $db->posts  a
			WHERE  a.post_type = 'attachment' AND a.post_mime_type LIKE 'image/%'  AND ( 
  NOT EXISTS (SELECT 1 FROM $db->postmeta mt1 WHERE mt1.post_ID = a.ID AND mt1.meta_key = 'wb-oss' LIMIT 1)
)";
        return $db->get_var($sql);

        /*
        $param = [
            'post_type'=>'attachment',
            'post_status'=>'any',
            'no_found_rows'=>true,
            'nopaging'=>true,
            'post_mime_type'=>'image/*',
            'meta_query'=>[
                ['key'=>'wb-oss','compare_key'=>'NOT EXISTS']
            ]
        ];
        query_posts($param);
        return 0;*/
    }

    public function update_conf_file($conf)
    {
        if(!file_exists(ABSPATH.'/wp-config.php')){
            return;
        }
        $content = file_get_contents(ABSPATH.'/wp-config.php');

        $content = preg_replace('#define\(\'WPTURBO_OSS.+;\s*#i','',$content);

        $s = "define('WP_DEBUG'";
        $row = preg_split('#define\(\'WP_DEBUG\'#',$content,2);
        if(count($row) != 2){
            $s = "\$table_prefix";
            $row = preg_split('#\$table_prefix#',$content,2);
        }
        if(!isset($row[1])){
            return;
        }

        $new = [];
        $oss_id = $conf['id'] ?? '';
        $oss_key = $conf['key'] ?? '';
        $oss_bucket = $conf['bucket'] ?? '';
        $oss_endpoint = $conf['endpoint'] ?? '';
        $new[] = $row[0].sprintf("define('WPTURBO_OSS_ID', '%s');",$oss_id);
        $new[] = sprintf("define('WPTURBO_OSS_KEY', '%s');",$oss_key);
        $new[] = sprintf("define('WPTURBO_OSS_BUCKET', '%s');",$oss_bucket);
        $new[] = sprintf("define('WPTURBO_OSS_ENDPOINT', '%s');",$oss_endpoint);
        $new[] = $s.$row[1];

        file_put_contents(ABSPATH.'/wp-config.php',implode("\n",$new));

    }

    public static function def()
    {
        $default_conf = array(
            'switch' => '0',
            'conf' => 'db',
            'vendor' => 'aliyun',
            'aliyun' => [
                'id' => '',
                'key' => '',
                'bucket' => '',
                'endpoint' => '',
            ],
            'tencent' => [
                'id' => '',
                'key' => '',
                'bucket' => '',
                'endpoint' => '',
            ],
            'upyun' => [
                'id' => '',
                'key' => '',
                'bucket' => '',
                'endpoint' => '',
            ],
            'huawei' => [
                'id' => '',
                'key' => '',
                'bucket' => '',
                'endpoint' => '',
            ],
            'baidu' => [
                'id' => '',
                'key' => '',
                'bucket' => '',
                'endpoint' => '',
            ],
            'oss_mode' => 'back',
            'local' => 'keep',
            'domain' => '',
        );
        return $default_conf;
    }

    private function oss_domain()
    {
        $cnf = self::cnf();
        $vendor = $cnf['vendor'] ?? 'aliyun';
        $oss = $cnf[$vendor] ?? $cnf['aliyun'];
        $domain =  $oss['domain'] ?? ($cnf['domain'] ?? '');
        if($domain){
            $domain = preg_replace('#^https?://#','',$domain);
            $domain = preg_replace('#/.+#','',$domain);
            $domain = trim($domain,'/');
        }
        return $domain;
    }

    //sve
    public static function cnf($key=null,$default=null){
        static $_option = array();
        if(!$_option){
            $_option = get_option('wpturbo_storage');
            if(!$_option || !is_array($_option)){
                $_option = [];
            }
            $default_conf = self::def();

            foreach ($default_conf as $k=>$v){
                if(!isset($_option[$k]))$_option[$k] = $v;
            }
            if($_option['conf'] == 'file'){
                $vendor = $_option['vendor'];
                $oss = $_option[$vendor] ?? [];
                if($oss){
                    $oss['id'] = defined('WPTURBO_OSS_ID') ? WPTURBO_OSS_ID : $oss['id'];
                    $oss['key'] = defined('WPTURBO_OSS_KEY') ? WPTURBO_OSS_KEY : $oss['key'];
                    $oss['bucket'] = defined('WPTURBO_OSS_BUCKET') ? WPTURBO_OSS_BUCKET : $oss['bucket'];
                    $oss['endpoint'] = defined('WPTURBO_OSS_ENDPOINT') ? WPTURBO_OSS_ENDPOINT : $oss['endpoint'];
                    $_option[$vendor] = $oss;
                }
            }
            $_option['local'] = 'keep';
            if($_option['oss_mode'] === 'server_adv'){
                $_option['local'] = 'delete';
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