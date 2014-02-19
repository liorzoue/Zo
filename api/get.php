<?php
	
	/* 
		---------------------------------------------
		API Zo V.0.1 
		---------------------------------------------
		
		---------------------------------------------
		by E. Liorzou
		http://github.com/liorzoue/Zo
		---------------------------------------------
		
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
			[Restart from scratch]
			[Add] Basic configuration
			[Add] Infos
	
	*/
	
	require_once('config.php');
    // useful getID3 lib
	require_once('lib/getid3/getid3.php');
    
	$app_name = 'Zo';
	
	$app_author = 'E. Liorzou';
	$app_website = 'http://github.com/liorzoue/Zo';
	
	$version_main = 0;
	$version_revision = 2;
	$version_patch = 1;
	
	$include_subfolders = true;
	    
	$username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
    $url_from = $_SERVER['HTTP_HOST'];
    
    $is_invite = true;
    $is_admin = false;
    
    if($username != $user['invite']) { $is_invite = false; }
    if($username == $user['admin']) { $is_admin = true; }
    
	$what = $_GET['what'];
	$q = $_GET['q'];
	$ui = $_GET['ui'];
	
    
    const API_VERSION = 'version'; 
    const API_NAME = 'application_name';
    const API_PATHS = 'path';
    
    const CMD_INFOS = 'infos';
    const CMD_API_TEST = 'api_test';
    const CMD_MOVIES = 'movies';
    const CMD_TV_SHOWS = 'tvshows';
    
    const JSON_PATH = 'path';
    
    const JSON_CPU = 'cpu';
    const JSON_TEMPERATURE = 'temperature';
    const JSON_FREQUENCY = 'frequency';
    const JSON_LOAD = 'load';
    
    const JSON_ERROR = 'error';
    const JSON_DISK = 'disk';
    
    try {
        
        // Check PATHs
        sort($movies);
        sort($music);
        
        // Retourne une erreur
        function error($q) {
            $r[JSON_ERROR]['what'] = 'param_undefined';
            $r[JSON_ERROR]['about'] = $q;
            return $r;
        }
        
        // Return command list
        function get_command_list() {
            $cmd = array();
            
            array_push($cmd, CMD_INFOS);
            array_push($cmd, CMD_API_TEST);
            array_push($cmd, CMD_MOVIES);
            array_push($cmd, CMD_TV_SHOWS);
            array_push($cmd, 'movie_info');
            array_push($cmd, 'tvshows_infos');
            array_push($cmd, 'fileinfo');
            array_push($cmd, 'commands');
            
            return $cmd;
        }
        
        // Return system infos
        function get_sysinfo() {
            function NumberWithCommas($in) {
                return number_format($in);
            }
            
            function  WriteToStdOut($text) {
                $stdout = fopen('php://stdout','w') or die($php_errormsg);
                fputs($stdout, "\n" . $text);
            }
            
            $current_time = exec("date +'%d %b %Y<br />%T %Z'");
            $frequency = NumberWithCommas(exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000);
            $processor = str_replace("-compatible processor", "", explode(": ", exec("cat /proc/cpuinfo | grep Processor"))[1]);
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
            for ($i=0; $i < 1; $i++)
            {
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
            for ($i = 0; $i < count($meminfo); $i++)
            {
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
            exec("df -T -l -BM -x tmpfs -x devtmpfs -x rootfs", $diskfree);
            $count = 1;
            while ($count < sizeof($diskfree))
            {
                list($drive[$count], $typex[$count], $size[$count], $used[$count], $avail[$count], $percent[$count], $mount[$count]) = split(" +", $diskfree[$count]);
                $percent_part[$count] = str_replace( "%", "", $percent[$count]);
                $count++;
            }
            
            $data_arr['mem']['total'] = $total_mem;
            $data_arr['mem']['free'] = $free_mem;
            $data_arr['mem']['used'] = $used_mem;
            
            $data_arr['uptime'] = $uptime;
            
            $data_arr[JSON_CPU][JSON_FREQUENCY] = $frequency.'MHz';
            $data_arr[JSON_CPU][JSON_TEMPERATURE] = $cpu_temperature;
            $data_arr[JSON_CPU][JSON_LOAD] = $cpuload;
            
            $data_arr[JSON_DISK]['count'] = $count;
            $data_arr[JSON_DISK]['drive'] = $drive;
            $data_arr[JSON_DISK]['sizes'] = $size;
            $data_arr[JSON_DISK]['used'] = $used;
            $data_arr[JSON_DISK]['avail'] = $avail;
            $data_arr[JSON_DISK]['drive_pct'] = $percent_part;
            $data_arr[JSON_DISK]['typex'] = $typex;
            $data_arr[JSON_DISK]['mount'] = $mount;
            
                    
            return $data_arr;
        }
        
        // Retourne les informations spécifiques à l'application
        function infos($q) {
            global $app_name, $app_author, $app_website, $version_main, $version_revision, $version_patch, $api_key, $api_url, $url_from, $absolute_path, $movies, $music, $tv_shows, $include_subfolders, $username, $password, $is_admin, $is_invite;
             
            $r = array();
            // echo '"'.$q.'"<br>';
            
            if (strlen($q)>2) {
                $arr = preg_split ("/[\s,]+/", $q);
            } else {
                $arr = array('sysinfos', API_NAME, API_VERSION, 'author', 'website', 'api_key', 'api_url', 'subfolders', API_PATHS, 'auth', 'commands', 'url');
            }
            
            sort($arr);
            
            // echo $arr.'<br>'.'<br>';
            foreach ($arr as $item) {
                // echo $item.'<br>';
                switch ($item) {
                    case 'system':
                    case 'sys':
                    case 'sysinfo':
                    case 'sysinfos':
                        $r['sysinfo'] = get_sysinfo();
                        break;
                    case 'author':
                        $r['author'] = $app_author;
                        break;
                        
                    case 'website':
                        $r['website'] = $app_website;
                        break;
                        
                    case API_NAME:
                        $r[API_NAME] = $app_name;
                        break;
                        
                    case API_VERSION:
                        $r[API_VERSION] = 'v.'.$version_main.'.'.$version_revision.'.'.$version_patch;
                        break;
                        
                    case 'subfolders':
                        $r[JSON_PATH]['subfolder_included'] = $include_subfolders;
                        break;
                        
                    case 'movie_path':
                        $r[JSON_PATH]['movies'] = $movies;
                        break;
                            
                    case 'tvshows_path':
                        $r[JSON_PATH][CMD_TV_SHOWS] = $tv_shows;
                        break;
                        
                    case 'music_path':
                        $r[JSON_PATH]['music'] = $music;
                        break;
                        
                    case API_PATHS:
                        $r[JSON_PATH]['subfolder_included'] = $include_subfolders;
                        $r[JSON_PATH]['movies'] = $movies;
                        $r[JSON_PATH]['music'] = $music;
                        $r[JSON_PATH][CMD_TV_SHOWS] = $tv_shows;
                        break;
                        
                    case 'api_key':
                        if($is_admin) {
                            $r['api_key'] = $api_key;
                        }
                        break;
                        
                    case 'api_url': 
                        $r['apiUrl'] = $api_url;
                        break;
                        
                    case 'commands': 
                        $r['commands'] = get_command_list();
                        break;
                                 
                    case 'auth':
                        $r['auth']['username'] = $username;
                        $r['auth']['password'] = $password;
                        break;
                        
                    case 'url':
                        $r['url'] = $url_from;
                        break;
                        
                    case '':
                    default:
                        $r[JSON_ERROR] = true;
                        break;
                }
            }
            
            return $r;
        }
        
        // Return list of tv shows
        function get_tvshows ($q) {
            global $api_key, $api_url, $absolute_path, $tv_shows, $include_subfolders;
            $tv_shows_nb = count($tv_shows);
            
            function get_tv_shows_list($dir) {
                $files = scandir($dir);
                $r = array();
                foreach($files as $f) {
                    // pas de fichiers commencant par un '.'
                    if(substr($f, 0, 1) != '.') { $r[] = $f; }
                }
                return $r;
            }            
            
            for($i=0; $i<$tv_shows_nb; $i++) {
                $r[CMD_TV_SHOWS]['list'][$i] = get_tv_shows_list($absolute_path.$tv_shows[$i]);
                $r[CMD_TV_SHOWS][JSON_PATH][$i] = $tv_shows[$i];
            }
            
            return $r;
        }
        
        // Return infos linked to tv shows
        function get_tvshows_infos($q) {
            global $api_key;
            
            $tvListArray = get_tvshows('');
            
            $arr = preg_split("/[\s]+/", $q);
            $id = $arr[0];
            $fileName = $tvListArray[CMD_TV_SHOWS][$id];
            
            
            function gmd($api_key, $title) {
                global $api_url;
                $title = preg_replace('/( -| \(|\.).*$/', '', $title);
                $title = str_replace(' ', '+', trim($title));
                
                $url = $api_url.'/search/tv?api_key='.$api_key.'&query='.$title;

                $json=file_get_contents($url);
                $json_string = stripslashes($json);
                $data = json_decode($json_string, true);
                //echo $data;
                return $data;
            }
            
            $r['fileName'] = $fileName;
            $r['results'] = gmd($api_key, $fileName);
            
            return $r;
        }
        
        // Retourne les informations liées aux films
        function movies($q) {
            global $api_key, $api_url, $absolute_path, $movies, $include_subfolders;
            
            function get_movie_list($what) {
                global $movies, $absolute_path;
                $movies_nb = count($movies);
                $r = array();
                
                function movie_det($dir) {
                    $files = scandir($dir);
                    $r = array();
                    foreach($files as $f) {
                        // pas de fichiers commencant par un '.'
                        if(substr($f, 0, 1) != '.') { $r[] = $f; }
                    }
                    return $r;
                }
                
                $what = intval($what);
                
                if($what == 0) {
                    for($i=0; $i<$movies_nb; $i++) {
                        $path = $absolute_path.$movies[$i];
                        $r['movies'][$i] = movie_det($path);
                        $r[JSON_PATH][$i] = $path;
                    }
                } else {
                    $path = $absolute_path.$movies[$what-1];
                    $r['movies'] = movie_det($path);
                    $r[JSON_PATH] = $path;
                }
                $r['path_id'] = $what-1;
                return $r;
            }
            
            function get_movie_detail($where, $who) {
                global $movies, $absolute_path;
                $who = preg_replace('/\.\./', ' ', $who);
                $path = $absolute_path.$movies[intval($where)-1].'/'.$who;
                
                // Chrono 
                $starttime = microtime(true);
                
                /*****************************/
                $getID3 = new getID3;
                $r = $getID3->analyze($path);
                
                
                echo $r;
                
                //$r = getid3_lib::CopyTagsToComments($r);
                /*****************************/
                
                
                
                // Chrono
                $endtime = microtime(true);
                $r['chrono_time'] = number_format($endtime - $starttime, 3);
                
                return $r;
            }
            
            $r = array();
            $arr = preg_split("/[\s]+/", $q);

            switch ($arr[0]) {
                case 'list':
                case '':
                    $r = get_movie_list($arr[1]);
                    break;
                
                case 'detail':
                    $r = get_movie_detail($arr[1], $arr[2]);
                    break;
                    
                default:
                    $r = error($arr[0]);
                    break;
            }
            
            return $r;
        }
        
        //
        function movie_info($q) {
            global $api_key, $api_url, $absolute_path, $movies, $include_subfolders, $url_from;
            $movieListArray = movies('list');
            
            $arr = preg_split("/[\s]+/", $q);
            $cat = $arr[0];
            $id = $arr[1];
            $fileName = $movieListArray['movies'][$cat][$id];
            
            $matches = preg_split("/(\(|-)/", $fileName);
            $lookupName = substr($matches[0], 0, -1);        
            $lookupName = str_replace('\\u00e0', 'a', $lookupName);
            $lookupName = str_replace('\\u00e9', 'e', $lookupName);
            
            function conf($api_key) {
                global $api_url;
                $url = $api_url.'/configuration?api_key='.$api_key;

                $json=file_get_contents($url);
                $json_string = stripslashes($json);
                $data = json_decode($json_string, true);
                //echo $data;
                return $data;
            }
            
            function gmd($api_key, $title) {
                global $api_url;
                $title = preg_replace('/( -| \(|\.).*$/', '', $title);
                $title = str_replace(' ', '+', trim($title));
                
                $url = $api_url.'/search/movie?api_key='.$api_key.'&query='.$title;

                $json=file_get_contents($url);
                $json_string = stripslashes($json);
                $data = json_decode($json_string, true);
                //echo $data;
                return $data;
            }
            
            function gmd_det($api_key, $id) {
                global $api_url;
                $url = $api_url.'/movie/'.$id.'?api_key='.$api_key;

                $json=file_get_contents($url);
                $json_string = stripslashes($json);
                $data = json_decode($json_string, true);
                //echo $data;
                return $data;
            }
            
            $r['cat'] = $cat;
            $r['id'] = $id;
            $r['apiKey'] = $api_key;
            $r['apiUrl'] = $api_url;
            $r[JSON_PATH] = $movieListArray[JSON_PATH][$cat];
            $r['config'] = conf($api_key);
            $r['results'] = gmd($api_key, $lookupName);
            $r['movieDetail'] = gmd_det($api_key, $r['results']['results'][0]['id']);
            $r['lookupName'] = $lookupName;
            $r['fileName'] = $movieListArray['movies'][$cat][$id];
            $r['fileUrl'] = 'http://'.$url_from.str_replace('/var/www', '', $movieListArray[JSON_PATH][$cat]).'/'.$movieListArray['movies'][$cat][$id]; 
            
            return $r;
        }
        
        // FileInfo
        function fileinfo($filename) {
            // Chrono 
            $starttime = microtime(true);
            
            /*****************************/
            $getID3 = new getID3;
            $r = $getID3->analyze($filename);
            /*****************************/

            // Chrono
            $endtime = microtime(true);
            $r['chrono_time'] = number_format($endtime - $starttime, 3);
            $r['query'] = $filename;
            
            return $r;
        }
        
        
        switch ($what) {
            case 'info':
            case CMD_INFOS:
            case 'about':
                $r = infos($q);
                break;
                
            case CMD_API_TEST:
                $r['test'] = 'ok';
                break;
                
            case CMD_MOVIES:
                $r = movies($q);
                break;
                
            case CMD_TV_SHOWS:
                $r = get_tvshows($q);
                break;
                
            case 'tvshows_infos':
                $r = get_tvshows_infos($q);
                break;
                
            case 'movie_info':
                $r = movie_info($q);
                break;
                
            case 'fileinfo':
                $r = fileinfo($q);
                break;
                
            case 'commands':
                $r['commands'] = get_command_list();
                break;
                
            default:
                $r = error($what);
                break;				
        }
        
    }
    catch (Exception $e) {
         $r[JSON_ERROR]['message'] = $e->getMessage();
         $r[JSON_ERROR]['code'] = $e->getCode();
         $r[JSON_ERROR]['file'] = $e->getFile();
         $r[JSON_ERROR]['line'] = $e->getLine();
         $r[JSON_ERROR]['trace'] = $e->getTrace();
         $r[JSON_ERROR]['previous'] = $e->getPrevious();

    }
    
	header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
	echo json_encode($r, JSON_UNESCAPED_SLASHES);
	
?>