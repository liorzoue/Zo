<?php
    
    /*
     ---------------------------------------------
     API Zo v 0.3.0
     ---------------------------------------------
     
     ---------------------------------------------
     by E. Liorzou
     http://github.com/liorzoue/Zo
     ---------------------------------------------
     
     beta-rev2 (17/02/2014)
     [Add] Limonade micro-framework (at 0.5.0)
     [Add] jDownloader basic support
     
     beta-rev1 (03/02/2014)
     [Add] Raspberry Pi system infos
     [Add] Get all infos from one movie
     [Add] Config file
     [Add] Constants for hard-coded string
     
     alpha-rev3 (21/01/2014)
     [Add] List all movies from all directories
     [Cosmetics] Put some hard-coded var on top
     
     alpha-rev2
     [Add] lists of movies in directories
     [Add] First getID3() use
     
     alpha-rev1
     [Add] Basic configuration
     [Add] Infos
     
     */
    
    require_once 'lib/limonade.php';
    require_once 'config.php';
    
    const API_NAME = 'Zo';
	const API_AUTHOR = 'E. Liorzou';
	const API_WEBSITE = 'http://github.com/liorzoue/Zo';
	const API_VERSION_MAIN = 0;
	const API_VERSION_REVISION = 3;
	const API_VERSION_PATCH = 0;
    
    const JSON_API_AUTHOR = 'author';
    const JSON_API_NAME = 'app_name';
    const JSON_API_WEBSITE = 'website';
    const JSON_API_VERSION = 'version';
    
    const JSON_CMD = 'commands';
    const JSON_MESSAGE = 'message';
    
    const JSON_NOAUTH = 'What are you trying to do my friend ?';
    
    const JSON_ERROR = 'error';
    const JSON_ERROR_ID = 'id';
    const JSON_ERROR_HTTP = 'type';
    
    const JSON_ABOUT = 'about';
    const JSON_MOVIES = 'movies';
    const JSON_TV_SHOWS = 'series';
    const JSON_MUSICS = 'musics';
    const JSON_PATH_ABS = 'absolute_path';
    const JSON_BENCHMARK = 'benchmark';
    const JSON_LIST = 'list';
    const JSON_FUNCTIONS = 'functions';
    
    const JSON_IS_ADMIN = 'admin';
    const JSON_IS_INVITE = 'invite';
    const JSON_LOAD = 'load';
    
    const JSON_SYSINFO = 'sysinfos';
    const JSON_UPTIME = 'uptime';
    const JSON_MEMORY = 'memory';
    const JSON_SWAP = 'swap';
    
    const JSON_FREE = 'free';
    const JSON_TOTAL = 'total';
    const JSON_USED = 'used';
    
    const JSON_DISK = 'disks';
    const JSON_CPU = 'cpu';
    const JSON_PROCESSOR = 'processor';
    const JSON_FREQUENCY = 'frequency';
    const JSON_TEMPERATURE = 'temperature';
    
    const JSON_DOWNLOAD = 'download';
    const JSON_URL = 'url';
    const JSON_SERVER = 'server';
    const JSON_OUTPUT = 'output';
    const JSON_SPEED = 'speed';
    
    const USER_ADMIN = 'admin';
    const USER_INVITE = 'invite';
    
    const API_URL_CAT = 'cat';
    const API_URL_ID = 'id';
    
    const JD_SERVER = 'server';
    const JD_PORT = 'port';
    
    $current_user = $_SERVER['PHP_AUTH_USER'];
    // $password = $_SERVER['PHP_AUTH_PW'];
    
    // format to json
    function my_json($arr) {
        return json_encode($arr, JSON_UNESCAPED_SLASHES);
    }
    
    // scan the dir passed
    function my_scandir($dir) {
        $files = scandir($dir);
        $r = array();
        foreach($files as $f) {
            // pas de fichiers commencant par un '.'
            if(substr($f, 0, 1) != '.') { $r[] = $f; }
        }
        return $r;
    }
    
    // Return system infos
    function my_sysinfos() {
        
        function NumberWithCommas($in) {
            return number_format($in);
        }
        
        function  WriteToStdOut($text) {
            $stdout = fopen('php://stdout','w') or die($php_errormsg);
            fputs($stdout, "\n" . $text);
        }
        
        $current_time = exec("date +'%d %b %Y<br />%T %Z'");
        $frequency = NumberWithCommas(exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000);
        /*
         processor	: 0
         model name	: ARMv6-compatible processor rev 7 (v6l)
         BogoMIPS	: 2.00
         Features	: swp half thumb fastmult vfp edsp java tls
         CPU implementer	: 0x41
         CPU architecture: 7
         CPU variant	: 0x0
         CPU part	: 0xb76
         CPU revision	: 7
         
         Hardware	: BCM2708
         Revision	: 000f
         Serial		: 0000000013cfd7f2
         */
        // get processor infos
        // TODO: Update
        $processor = str_replace("-compatible processor", "", explode(": ", exec("cat /proc/cpuinfo | grep processor"))[1]);
        $cpu_temperature = round(exec("cat /sys/class/thermal/thermal_zone0/temp ") / 1000, 1);
        //$RX = exec("ifconfig eth0 | grep 'RX bytes'| cut -d: -f2 | cut -d' ' -f1");
        //$TX = exec("ifconfig eth0 | grep 'TX bytes'| cut -d: -f3 | cut -d' ' -f1");
        list($system, $host, $kernel) = split(" ", exec("uname -a"), 4);
        
        //Uptime
        $uptime_array = explode(" ", exec("cat /proc/uptime"));
        $seconds = round($uptime_array[0], 0);
        $minutes = $seconds / 60;
        $hours = $minutes / 60;
        $days = floor($hours / 24);
        $hours = sprintf('%02d', floor($hours - ($days * 24)));
        $minutes = sprintf('%02d', floor($minutes - ($days * 24 * 60) - ($hours * 60)));
        if ($days == 0):
            $uptime = $hours . ":" .  $minutes . "";
        elseif($days == 1):
        $uptime = $days . " jour, " .  $hours . ":" .  $minutes . "";
        else:
            $uptime = $days . " jours, " .  $hours . ":" .  $minutes . "";
        endif;
        
        //CPU Usage
        $output1 = null;
        $output2 = null;
        //First sample
        exec("cat /proc/stat", $output1);
        //Sleep before second sample
        sleep(1);
        //Second sample
        exec("cat /proc/stat", $output2);
        $cpuload = 0;
        for ($i=0; $i < 1; $i++) {
            //First row
            $cpu_stat_1 = explode(" ", $output1[$i+1]);
            $cpu_stat_2 = explode(" ", $output2[$i+1]);
            //Init arrays
            $info1 = array("user"=>$cpu_stat_1[1], "nice"=>$cpu_stat_1[2], "system"=>$cpu_stat_1[3], "idle"=>$cpu_stat_1[4]);
            $info2 = array("user"=>$cpu_stat_2[1], "nice"=>$cpu_stat_2[2], "system"=>$cpu_stat_2[3], "idle"=>$cpu_stat_2[4]);
            $idlesum = $info2["idle"] - $info1["idle"] + $info2["system"] - $info1["system"];
            $sum1 = array_sum($info1);
            $sum2 = array_sum($info2);
            //Calculate the cpu usage as a percent
            $load = (1 - ($idlesum / ($sum2 - $sum1))) * 100;
            $cpuload += $load;
        }
        $cpuload = round($cpuload, 1); //One decimal place
        
        //Memory Utilisation
        $meminfo = file("/proc/meminfo");
        for ($i = 0; $i < count($meminfo); $i++) {
            list($item, $data) = split(":", $meminfo[$i], 2);
            $item = trim(chop($item));
            $data = intval(preg_replace("/[^0-9]/", "", trim(chop($data)))); //Remove non numeric characters
            switch($item)
            {
                case "MemTotal": $total_mem = $data; break;
                case "MemFree": $free_mem = $data; break;
                case "SwapTotal": $total_swap = $data; break;
                case "SwapFree": $free_swap = $data; break;
                case "Buffers": $buffer_mem = $data; break;
                case "Cached": $cache_mem = $data; break;
                default: break;
            }
        }
        
        $used_mem = $total_mem - $free_mem;
        $used_swap = $total_swap - $free_swap;
        $percent_free = round(($free_mem / $total_mem) * 100);
        $percent_used = round(($used_mem / $total_mem) * 100);
        $percent_swap = round((($total_swap - $free_swap ) / $total_swap) * 100);
        $percent_swap_free = round(($free_swap / $total_swap) * 100);
        $percent_buff = round(($buffer_mem / $total_mem) * 100);
        $percent_cach = round(($cache_mem / $total_mem) * 100);
        $used_mem = NumberWithCommas($used_mem);
        $used_swap = NumberWithCommas($used_swap);
        $total_mem = NumberWithCommas($total_mem);
        $free_mem = NumberWithCommas($free_mem);
        $total_swap = NumberWithCommas($total_swap);
        $free_swap = NumberWithCommas($free_swap);
        $buffer_mem = NumberWithCommas($buffer_mem);
        $cache_mem = NumberWithCommas($cache_mem);
        
        //Disk space check
        exec("df -T -l -x tmpfs -x devtmpfs -x rootfs", $diskfree);
        $count = 1;
        while ($count < sizeof($diskfree))
        {
            list($drive[$count], $typex[$count], $size[$count], $used[$count], $avail[$count], $percent[$count], $mount[$count]) = split(" +", $diskfree[$count]);
            $percent_part[$count] = str_replace( "%", "", $percent[$count]);
            $count++;
        }
        
        // Return array
        $data_arr[JSON_MEMORY][JSON_TOTAL] = $total_mem;
        $data_arr[JSON_MEMORY][JSON_FREE] = $free_mem;
        $data_arr[JSON_MEMORY][JSON_USED] = $used_mem;
        
        $data_arr[JSON_SWAP][JSON_TOTAL] = $total_swap;
        $data_arr[JSON_SWAP][JSON_FREE] = $free_swap;
        $data_arr[JSON_SWAP][JSON_USED] = $used_swap;
        
        $data_arr[JSON_UPTIME] = $uptime;
        
        $data_arr[JSON_CPU][JSON_FREQUENCY] = $frequency.'MHz';
        $data_arr[JSON_CPU][JSON_TEMPERATURE] = $cpu_temperature;
        $data_arr[JSON_CPU][JSON_LOAD] = $cpuload;
        $data_arr[JSON_CPU][JSON_CPU] = $processor;
        
        for($i=1; $i<$count; $i++) {
            $data_arr[JSON_DISK][$i]['drive'] = $drive[$i];
            $data_arr[JSON_DISK][$i]['sizes'] = $size[$i];
            $data_arr[JSON_DISK][$i][JSON_USED] = $used[$i];
            $data_arr[JSON_DISK][$i]['avail'] = $avail[$i];
            $data_arr[JSON_DISK][$i]['drive_pct'] = $percent_part[$i];
            $data_arr[JSON_DISK][$i]['typex'] = $typex[$i];
            $data_arr[JSON_DISK][$i]['mount'] = $mount[$i];
        }
        
        $data_arr[JSON_DISK]['count'] = $count-1;
        
        
        return $data_arr;
    }
    
    function my_layout_json() {
        return $vars;
    }
    
    function xmlToArray($xml, $options = array()) {
        $defaults = array(
                          'namespaceSeparator' => ':',//you may want this to be something other than a colon
                          'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
                          'alwaysArray' => array(),   //array of xml tag names which should always become arrays
                          'autoArray' => true,        //only create arrays for tags which appear more than once
                          'textContent' => '$',       //key used for the text content of elements
                          'autoText' => true,         //skip textContent key if node has no attributes or child nodes
                          'keySearch' => false,       //optional search and replace on tag and attribute names
                          'keyReplace' => false       //replace values for above search values (as passed to str_replace())
                          );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace
        
        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                    str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }
        
        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);
                
                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                    str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
                
                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                    in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                    ? array($childProperties) : $childProperties;
                } elseif (
                          is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                          === range(0, count($tagsArray[$childTagName]) - 1)
                          ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }
        
        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;
        
        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
        ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
        
        //return node as array
        return array(
                     $xml->getName() => $propertiesArray
                     );
    }
    
    function no_output() { }
    
    function before($route)
    {
        // header("X-LIM-route-function: ".$route['callback']);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        layout('my_layout_json');
    }
    
    dispatch('/', 'hello');
    dispatch('/about', 'get_api_infos');
    dispatch('/api', 'get_api_infos');
    
    dispatch('/commands', 'get_commands_list');
    dispatch('/benchmark', 'get_benchmark');
    dispatch('/no', 'no_output');
    dispatch('/system', 'get_sysinfos');
    
    
    dispatch('/functions', 'get_functions');
    dispatch('/functions/exec', 'get_functions');
    dispatch('/functions/exec/:func', 'run_this_function');
    
    dispatch('/path', 'get_path');
    dispatch('/path/absolute', 'get_path_absolute');
    dispatch('/path/movies', 'get_path_movies');
    dispatch('/path/series', 'get_path_series');
    dispatch('/path/musics', 'get_path_musics');
    
    dispatch('/movie/:'.API_URL_CAT.'/all', 'get_movie_list');
    dispatch('/movie/:'.API_URL_CAT.'/:'.API_URL_ID, 'get_movie_info');
    
    dispatch('/is/admin', 'is_admin');
    dispatch('/is/invite', 'is_invite');
    
    dispatch('/download/', 'get_download_infos');
    dispatch('/download/get/', 'get_download_infos');
    dispatch('/download/get/server', 'get_server_jdownloader');
    dispatch('/download/get/currentlist', 'get_download_currentlist');
    dispatch('/download/get/speed', 'get_download_speed');
    dispatch('/download/**', 'add_download_url');
    
    
    function hello () {
        $r[JSON_ABOUT] = get_api_infos(false);
        $r[JSON_CMD] = get_commands_list(false)[JSON_CMD];
        
        return my_json($r);
    }
    
    function get_api_infos($with_json = true)
    {
        
        $r[JSON_API_NAME] = API_NAME;
        $r[JSON_API_AUTHOR] = API_AUTHOR;
        $r[JSON_API_VERSION] = API_VERSION_MAIN.'.'.API_VERSION_REVISION.'.'.API_VERSION_PATCH;
        $r[JSON_API_WEBSITE] = API_WEBSITE;
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_commands_list($with_json = true)
    {
        
        $r[JSON_CMD][] = '/';
        $r[JSON_CMD][] = '/no';
        $r[JSON_CMD][] = '/about';
        $r[JSON_CMD][] = '/api';
        $r[JSON_CMD][] = '/commands';
        $r[JSON_CMD][] = '/benchmark';
        $r[JSON_CMD][] = '/system';
        
        $r[JSON_CMD][] = '/download/get/server';
        $r[JSON_CMD][] = '/download/get/currentlist';
        $r[JSON_CMD][] = '/download/get/speed';
        
        $r[JSON_CMD][] = '/path';
        $r[JSON_CMD][] = '/path/absolute';
        $r[JSON_CMD][] = '/path/movies';
        $r[JSON_CMD][] = '/path/musics';
        $r[JSON_CMD][] = '/path/series';
        
        $r[JSON_CMD][] = '/movie/:cat/all';
        $r[JSON_CMD][] = '/movie/:cat/:id';
        
        $r[JSON_CMD][] = '/is/admin';
        $r[JSON_CMD][] = '/is/invite';
        
        if(is_admin(false)) {
            $r[JSON_CMD][] = '/download/:url';
            
            $r[JSON_CMD][] = '/functions';
            $r[JSON_CMD][] = '/functions/exec';
            $r[JSON_CMD][] = '/functions/exec/:func';
        }
        
        sort($r[JSON_CMD]);
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_functions($with_json = true)
    {
        if(is_invite(false)) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'auth';
            $r[JSON_ERROR][JSON_MESSAGE] = JSON_NOAUTH;
            
            if ($with_json) { return my_json($r); }
            else { return $r; }
        }
        
        $r[JSON_FUNCTIONS] = get_defined_functions()['user'];
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function run_this_function($with_json = true)
    {
        if(is_invite(false)) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'auth';
            $r[JSON_ERROR][JSON_MESSAGE] = JSON_NOAUTH;
            
            if ($with_json) { return my_json($r); }
            else { return $r; }
        }
        
        
        $func = params('func');
        
        $r[JSON_OUTPUT] = $func(false);
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_benchmark($with_json = true)
    {
        
        $r = benchmark();
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_sysinfos($with_json = true)
    {
        
        $r[JSON_SYSINFO] = my_sysinfos();
        $r[JSON_BENCHMARK] = get_benchmark(false);
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    
    // ***** get paths functions ***** //
    function get_path($with_json = true)
    {
        global $absolute_path;
        
        $r[JSON_PATH_ABS] = $absolute_path;
        $r[JSON_MOVIES] = get_path_movies(false)[JSON_MOVIES];
        $r[JSON_TV_SHOWS] = get_path_series(false)[JSON_TV_SHOWS];
        $r[JSON_MUSICS] = get_path_musics(false)[JSON_MUSICS];
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_path_absolute($with_json = true)
    {
        global $absolute_path;
        
        $r[JSON_PATH_ABS] = $absolute_path;
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_path_movies($with_json = true)
    {
        global $movies;
        
        $r[JSON_MOVIES] = $movies;
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_path_series($with_json = true)
    {
        global $tv_shows;
        
        $r[JSON_TV_SHOWS] = $tv_shows;
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_path_musics($with_json = true)
    {
        global $music;
        
        $r[JSON_MUSICS] = $music;
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_movie_list($with_json = true)
    {
        $paths = get_path(false);
        $cat = params(API_URL_CAT);
        $i = 0;
        
        if ($cat == 0) { $cat = null; }
        // Read paths
        foreach ($paths[JSON_MOVIES] as $p) {
            $i++;
            if ($cat == null || $cat == $i) {
                $r[$p] = my_scandir($paths[JSON_PATH_ABS].$p);
            }
        }
        
        $r[API_URL_CAT] = $cat;
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_movie_info()
    {
        $paths = get_path(false);
        $cat = params(API_URL_CAT);
        $id = params(API_URL_ID);
        
        if ($cat == null || $id == null) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'url';
            $r[JSON_ERROR]['description'] = 'missing parameters';
            
            return my_json($r);
        }
        
        $r[API_URL_CAT] = $cat;
        $r[API_URL_ID] = $id;
        
        return my_json($r);
    }
    
    
    // ***** download url ***** //
    function get_server_jdownloader($with_json = true)
    {
        global $jdownloader;
        
        $server = $jdownloader[JD_SERVER];
        $port = $jdownloader[JD_PORT];
        
        $url = 'http://'.$server.':'.$port;
        
        $r[JSON_SERVER] = $url;
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function communicate_jdownloader($param = '/help', $isXml = false)
    {
        $url = get_server_jdownloader(false)[JSON_SERVER].$param;
        
        $out[JSON_URL] = $url;
        if ($isXml) {
            $out[JSON_OUTPUT] = xmlToArray(simplexml_load_file($url));
        } else {
            $out[JSON_OUTPUT] = file_get_contents($url);
        }
    
        return $out;
    }
    
    function add_download_url($with_json = true)
    {
        if(is_invite(false)) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'auth';
            $r[JSON_ERROR][JSON_MESSAGE] = JSON_NOAUTH;
            
            if ($with_json) { return my_json($r); }
            else { return $r; }
        }
        
        
        $url = params(0);
        $url = str_replace(":/", "://", $url);
        $url = str_replace(" ", "%20", $url);
        
        $url = '/action/add/links/grabber0/start1/'.$url;
        $out = communicate_jdownloader($url);
        
        $r[JSON_URL] = $url;
        $r[JSON_DOWNLOAD] = $out;
        $r[JSON_BENCHMARK] = get_benchmark(false);
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
        
    }
    
    function get_download_currentlist($with_json = true)
    {
        $out = communicate_jdownloader('/get/downloads/currentlist', true);
        
        $r[JSON_DOWNLOAD][JSON_LIST] = $out;
        $r[JSON_BENCHMARK] = get_benchmark(false);
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_download_speed($with_json = true)
    {
        $out = communicate_jdownloader('/get/speed');
        
        $r[JSON_DOWNLOAD][JSON_SPEED] = intval($out[JSON_OUTPUT]);
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    function get_download_infos($with_json = true)
    {
        $r[JSON_SPEED] = get_download_speed(false)[JSON_DOWNLOAD][JSON_SPEED];
        $r[JSON_LIST] = get_download_currentlist(false)[JSON_DOWNLOAD][JSON_LIST];
        
        if ($with_json) { return my_json($r); }
        else { return $r; }
    }
    
    
    // ***** is functions ***** //
    function is_admin($with_json = true)
    {
        global $user, $current_user;
        
        if($user[USER_ADMIN] == $current_user) { $is = true; }
        else { $is = false; }
        
        if ($with_json) {
            $r[JSON_IS_ADMIN] = $is;
            return my_json($r);
        } else {
            return $is;
        }
        
    }
    
    function is_invite($with_json = true)
    {
        global $user, $current_user;
        
        if($user[USER_INVITE] == $current_user) { $is = true; }
        else { $is = false; }
        
        if ($with_json) {
            $r[JSON_IS_INVITE] = $is;
            return my_json($r);
        } else {
            return $is;
        }
        
    }

    run();
    
    // EOF