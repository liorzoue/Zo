<?php
	function utils_no_output() { }

	function utils_json($arr) {
        return json_encode($arr, JSON_UNESCAPED_SLASHES);
	}

	function utils_xmlToArray($xml, $options = array()) {
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
                //*
                $attributeKey = $options['attributePrefix']
                . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                . $attributeName;
                /*/
                 $attributeKey = $attributeName; // */
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }
        
        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = utils_xmlToArray($childXml, $options);
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
        return array($xml->getName() => $propertiesArray);
    }

    function utils_scandir($dir) {
        $files = scandir($dir);
        $r = array();
        foreach($files as $f) {
            // pas de fichiers commencant par un '.'
            if(substr($f, 0, 1) != '.') { $r[] = $f; }
        }
        return $r;
    }

    function utils_cleanJsonString($data) {
        $data = trim($data);
        $data = preg_replace('!\s*//[^"]*\n!U', '\n', $data);
        $data = preg_replace('!/\*[^"]*\*/!U', '', $data);
        $data = !utils_startsWith('{', $data) ? '{'.$data : $data;
        $data = !utils_endsWith('}', $data) ? $data.'}' : $data;
        $data = preg_replace('!,(\s*[}\]])!U', '$1', $data);
        return $data;
    }

    function utils_startsWith($needle, $haystack) {
        return !strncmp($haystack, $needle, strlen($needle));
    }
    
    function utils_endsWith($needle, $haystack) {
        $length = strlen($needle);
        if ($length == 0)
            return true;
        return (substr($haystack, -$length) === $needle);
    }

    // Return system infos
    function utils_sysinfos() {
        
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

    function utils_extractfilename ($fileName) {
    	$matches = preg_split("/ (\(|-)/", $fileName);
        $lookupName = substr($matches[0], 0);
        $lookupName = str_replace('\\u00e0', 'a', $lookupName);
        $lookupName = str_replace('\\u00e9', 'e', $lookupName);
    	return $lookupName;
    }

    function utils_extractdts ($fileName) {
    	if (strpos($fileName,'DTS') !== false) {
    		return true;
    	}
    	return false;
    }

    function utils_extractvo ($fileName) {
    	if (strpos($fileName,'VO') !== false) {
    		return true;
    	}
    	if (strpos($fileName,'MULTI') !== false) {
    		return true;
    	}
    	return false;
    }

    function utils_toreadablesize($bytes) {
    	$bytes = floatval($bytes);

    	if ($bytes > 0)
    	{
    		$unit = intval(log($bytes, 1024));
    		$units = array('B', 'KB', 'MB', 'GB', 'TB');

    		if (array_key_exists($unit, $units) === true)
    		{
    			return round($bytes / pow(1024, $unit), 2).' '.$units[$unit];
    		}
    	}

    	return $bytes;
    }