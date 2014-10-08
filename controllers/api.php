<?php

	function api_hello () {
        $r[JSON_ABOUT] = api_get_infos(false);
        $r[JSON_CMD] = api_get_commands_list(false)[JSON_CMD];
        
        return utils_json($r);
    }

    function api_ui () {
    	layout('layout_std');
    	set('title', 'API UI');
	    set('active_page', 'tests');

    	return html('<pre>'.api_get_commands_list().'</pre>');
    }

    function api_get_config($with_json = true) {
    	$r[JSON_ABOUT] = api_get_infos(false);
    	$r[JSON_PATH] = api_get_path(false);

    	if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_get_infos($with_json = true) {
        
        $r[JSON_API_NAME] = API_NAME;
        $r[JSON_API_AUTHOR] = API_AUTHOR;
        $r[JSON_API_VERSION] = API_VERSION_MAIN.'.'.API_VERSION_REVISION.'.'.API_VERSION_PATCH;
        $r[JSON_API_WEBSITE] = API_WEBSITE;
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_get_commands_list($with_json = true) {
        
        $r[JSON_CMD][] = '/';
        $r[JSON_CMD][] = '/no';
        $r[JSON_CMD][] = '/about';
        $r[JSON_CMD][] = '/api';
        $r[JSON_CMD][] = '/api/ui';
        $r[JSON_CMD][] = '/commands';
        $r[JSON_CMD][] = '/configuration';
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
        
        if(api_is_admin(false)) {
            $r[JSON_CMD][] = '/download/:url';
            
            $r[JSON_CMD][] = '/functions';
            $r[JSON_CMD][] = '/functions/exec';
            $r[JSON_CMD][] = '/functions/exec/:func';
        }
        
        sort($r[JSON_CMD]);
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_get_functions($with_json = true) {
        if(is_invite(false)) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'auth';
            $r[JSON_ERROR][JSON_MESSAGE] = JSON_NOAUTH;
            
            if ($with_json) { return utils_json($r); }
            else { return $r; }
        }
        
        $r[JSON_FUNCTIONS] = get_defined_functions()['user'];
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_run_this_function($with_json = true) {
        if(is_invite(false)) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'auth';
            $r[JSON_ERROR][JSON_MESSAGE] = JSON_NOAUTH;
            
            if ($with_json) { return utils_json($r); }
            else { return $r; }
        }
        
        
        $func = params('func');
        
        $r[JSON_OUTPUT] = $func(false);
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_get_benchmark($with_json = true) {
        
        $r = benchmark();
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_get_sysinfos($with_json = true) {
        
        $r[JSON_SYSINFO] = utils_sysinfos();
        $r[JSON_BENCHMARK] = api_get_benchmark(false);
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }

	function api_get_path($with_json = true) {

        global $absolute_path;
        
        $r[JSON_PATH_ABS]	= $absolute_path;
        $r[JSON_MOVIES] 	= api_get_path_movies(false)[JSON_MOVIES];
        $r[JSON_TV_SHOWS] 	= api_get_path_series(false)[JSON_TV_SHOWS];
        $r[JSON_MUSICS] 	= api_get_path_musics(false)[JSON_MUSICS];
        
        if ($with_json) {
        	layout('layout_json');
        	return utils_json($r);
        }
        else { return $r; }
    }

    function api_get_path_absolute($with_json = true) {
        global $absolute_path;
        
        $r[JSON_PATH_ABS] = $absolute_path;
        
        if ($with_json) {
        	layout('layout_json');
        	return utils_json($r); }
        else { return $r; }
    }

    function api_get_path_movies($with_json = true) {
        global $movies;
        
        $r[JSON_MOVIES] = $movies;
        
        if ($with_json) {
        	layout('layout_json');
        	return utils_json($r); }
        else { return $r; }
    }

    function api_get_path_series($with_json = true) {
        global $tv_shows;
        
        $r[JSON_TV_SHOWS] = $tv_shows;
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }

    function api_get_path_musics($with_json = true) {
        global $music;
        
        $r[JSON_MUSICS] = $music;
        
        if ($with_json) {
        	layout('layout_json');
        	return utils_json($r); }
        else { return $r; }
    }

    function api_get_movie_list($with_json = true) {
        $paths = api_get_path(false);
        $cat = params(API_URL_CAT);
        if ($with_json == false) { $cat = null; }
        $i = 0;
        
        if ($cat == 0) { $cat = null; }
        // Read paths
        foreach ($paths[JSON_MOVIES] as $p) {
            $i++;
            if ($cat == null || $cat == $i) {
                $r[JSON_MOVIES][$i] = utils_scandir($paths[JSON_PATH_ABS].$p);
            }
        }
        
        $r[API_URL_CAT] = $cat;
        
        if ($with_json) {
        	layout('layout_json');
        	return utils_json($r); }
        else { return $r; }
    }

    function api_get_movie_info() {
        global $api_key, $api_url, $url_from;
        
        $paths = api_get_path(false);
        $cat = params(API_URL_CAT);
        $id = params(API_URL_ID);
        
        $path = api_get_path_movies(false)[JSON_MOVIES][intval($cat)-1];
        
        if ($cat == null || $id == null) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'url';
            $r[JSON_ERROR]['description'] = 'missing parameters';
            
            return utils_json($r);
        }
        
        // , $absolute_path, $movies, $include_subfolders
        $movieListArray = api_get_movie_list(false);
        
        // $arr = preg_split("/[\s]+/", $q);
        $fileName = $movieListArray[JSON_MOVIES][$cat][$id];
        
        $matches = preg_split("/(\(|-)/", $fileName);
        $lookupName = substr($matches[0], 0, -1);
        $lookupName = str_replace('\\u00e0', 'a', $lookupName);
        $lookupName = str_replace('\\u00e9', 'e', $lookupName);
        
        function conf($api_key) {
            global $api_url;
            $url = $api_url.'/configuration?api_key='.$api_key;
            
            $json=file_get_contents($url);
            $json_string = utils_cleanJsonString($json);
            $data = json_decode($json_string, true);
            
            return $data;
        }
        
        function gmd($api_key, $title) {
            global $api_url;
            $title = preg_replace('/( -| \(|\.).*$/', '', $title);
            $title = str_replace(' ', '+', trim($title));
            
            $url = $api_url.'/search/movie?api_key='.$api_key.'&query='.$title;
            
            $json=file_get_contents($url);
            $json_string = utils_cleanJsonString($json);
            $data = json_decode($json_string, true);
            $data['json_string'] = $json_string;
            
            return $data;
        }
        
        function gmd_det($api_key, $id) {
            global $api_url;
            $url = $api_url.'/movie/'.$id.'?api_key='.$api_key;
            
            $json = file_get_contents($url);
            $json_string = utils_cleanJsonString($json);
            $data = json_decode($json_string, true);
            
            return $data;
        }
        
        $r[API_URL_CAT] = $cat;
        $r[API_URL_ID] = $id;
        $r[JSON_PATH] = $path;
        $r['config'] = conf($api_key);
        $r[JSON_RESULT] = gmd($api_key, $lookupName)['results'][0];
        $r['movieDetail'] = gmd_det($api_key, $r[JSON_RESULT]['id']);
        $r['lookupName'] = $lookupName;
        $r['fileName'] = $movieListArray[JSON_MOVIES][$cat][$id];
        $r['fileUrl'] = 'http://'.$url_from.$path.'/'.$movieListArray['movies'][$cat][$id];
        
        $r[API_URL_CAT] = $cat;
        $r[API_URL_ID] = $id;
        

        layout('layout_json');
        	
        return utils_json($r);
    }

    function api_get_server_jdownloader($with_json = true) {
        global $jdownloader;
        
        $server = $jdownloader[JD_SERVER];
        $port = $jdownloader[JD_PORT];
        
        $url = 'http://'.$server.':'.$port;
        
        $r[JSON_SERVER] = $url;
        
        if ($with_json) {
        	layout('layout_json');
        	return utils_json($r); }
        else { return $r; }
    }
    
    function api_communicate_jdownloader($param = '/help', $isXml = false) {
        $url = api_get_server_jdownloader(false)[JSON_SERVER].$param;
        
        $out[JSON_URL] = $url;
        if ($isXml) {
            $out[JSON_OUTPUT] = utils_xmlToArray(simplexml_load_file($url));
        } else {
            $out[JSON_OUTPUT] = file_get_contents($url);
        }
        
        return $out;
    }
    
    function api_download_limit_no($with_json = true)
    {
    	$limit = 0;
        $out = api_communicate_jdownloader('/action/set/download/limit/'.$limit);
        
        $r[JSON_DOWNLOAD][JSON_MESSAGE] = 'download paused (limit:'.$limit.')';
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }

    function api_download_limit($with_json = true)
    {
        $limit = params(0);
        $out = api_communicate_jdownloader('/action/set/download/limit/'.$limit);
        
        $r[JSON_DOWNLOAD][JSON_MESSAGE] = 'download paused (limit:'.$limit.')';
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_add_download_url($with_json = true)
    {
        if(is_invite(false)) {
            $r[JSON_ERROR][JSON_ERROR_ID] = 'auth';
            $r[JSON_ERROR][JSON_MESSAGE] = JSON_NOAUTH;
            
            if ($with_json) { return utils_json($r); }
            else { return $r; }
        }
        
        
        $url = params(0);
        $url = str_replace(":/", "://", $url);
        $url = str_replace(" ", "%20", $url);
        
        $url = '/action/add/links/grabber0/start1/'.$url;
        $out = api_communicate_jdownloader($url);
        
        $r[JSON_URL] = $url;
        $r[JSON_DOWNLOAD] = $out;
        $r[JSON_BENCHMARK] = api_get_benchmark(false);
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }  
    }
    
    function api_get_download_currentlist($with_json = true)
    {
        $out = api_communicate_jdownloader('/get/downloads/currentlist', true);
        
        $r[JSON_DOWNLOAD][JSON_LIST] = $out;
        $r[JSON_BENCHMARK] = api_get_benchmark(false);
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_get_download_speed($with_json = true)
    {
        $out = api_communicate_jdownloader('/get/speed');
        
        $r[JSON_DOWNLOAD][JSON_SPEED] = intval($out[JSON_OUTPUT]);
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    function api_get_download_infos($with_json = true)
    {
        $r[JSON_SPEED] = api_get_download_speed(false)[JSON_DOWNLOAD][JSON_SPEED];
        $r[JSON_LIST] = api_get_download_currentlist(false)[JSON_DOWNLOAD][JSON_LIST];
        
        if ($with_json) { return utils_json($r); }
        else { return $r; }
    }
    
    
    // ***** is functions ***** //
    function api_is_admin($with_json = true)
    {
        global $user, $current_user;
        
        if($user[USER_ADMIN] == $current_user) { $is = true; }
        else { $is = false; }
        
        if ($with_json) {
            $r[JSON_IS_ADMIN] = $is;
            return utils_json($r);
        } else {
            return $is;
        }
        
    }
    
    function api_is_invite($with_json = true)
    {
        global $user, $current_user;
        
        if($user[USER_INVITE] == $current_user) { $is = true; }
        else { $is = false; }
        
        if ($with_json) {
            $r[JSON_IS_INVITE] = $is;
            return utils_json($r);
        } else {
            return $is;
        }
        
    }