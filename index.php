<?php

	/*
	 ---------------------------------------------
	 API Zo v 0.3.0
	 ---------------------------------------------
	 
	 ---------------------------------------------
	 by E. Liorzou
	 http://github.com/liorzoue/Zo
	 ---------------------------------------------
	 
	 beta-rev5 (11/03/2014)
	 [Update] Merge index.php files

	 beta-rev4 (21/02/2014)
	 [Add] Set jDownloader speed limit
	 
	 beta-rev3 (20/02/2014)
	 [Back] The come back of movies infos !
	 
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

	 const API_NAME 			= 'Zo';
	 const API_AUTHOR 			= 'E. Liorzou';
	 const API_WEBSITE 			= 'http://github.com/liorzoue/Zo';
	 const API_VERSION_MAIN 	= 0;
	 const API_VERSION_REVISION = 3;
	 const API_VERSION_PATCH 	= 0;
	 
	 const JSON_API_AUTHOR		= 'author';
	 const JSON_API_NAME 		= 'app_name';
	 const JSON_API_WEBSITE 	= 'website';
	 const JSON_API_VERSION 	= 'version';
	 
	 const JSON_CMD 			= 'commands';
	 const JSON_MESSAGE 		= 'message';
	 
	 const JSON_NOAUTH 			= 'What are you trying to do my friend ?';
	 
	 const JSON_ERROR 			= 'error';
	 const JSON_ERROR_ID 		= 'id';
	 const JSON_ERROR_HTTP 		= 'type';
	 const JSON_PATH 			= 'path';

	 const JSON_ABOUT 			= 'about';
	 const JSON_MOVIES 			= 'movies';
	 const JSON_TV_SHOWS 		= 'series';
	 const JSON_MUSICS 			= 'musics';
	 const JSON_PATH_ABS 		= 'absolute_path';
	 const JSON_BENCHMARK 		= 'benchmark';
	 const JSON_LIST 			= 'list';
	 const JSON_FUNCTIONS 		= 'functions';
	 
	 const JSON_IS_ADMIN 		= 'admin';
	 const JSON_IS_INVITE 		= 'invite';
	 const JSON_LOAD 			= 'load';
	 
	 const JSON_SYSINFO 		= 'sysinfos';
	 const JSON_UPTIME 			= 'uptime';
	 const JSON_MEMORY 			= 'memory';
	 const JSON_SWAP 			= 'swap';
	 
	 const JSON_FREE 			= 'free';
	 const JSON_TOTAL 			= 'total';
	 const JSON_USED 			= 'used';
	 
	 const JSON_DISK 			= 'disks';
	 const JSON_CPU 			= 'cpu';
	 const JSON_PROCESSOR 		= 'processor';
	 const JSON_FREQUENCY 		= 'frequency';
	 const JSON_TEMPERATURE 	= 'temperature';
	 
	 const JSON_DOWNLOAD 		= 'download';
	 const JSON_URL 			= 'url';
	 const JSON_SERVER 			= 'server';
	 const JSON_OUTPUT 			= 'output';
	 const JSON_SPEED 			= 'speed';
	 const JSON_RESULT 			= 'result';
	 
	 const USER_ADMIN 			= 'admin';
	 const USER_INVITE 			= 'invite';
	 
	 const API_URL_CAT 			= 'cat';
	 const API_URL_ID 			= 'id';
	 
	 const JD_SERVER 			= 'server';
	 const JD_PORT 				= 'port';
	 
	 function before($route)
	 {
	 	layout('layout_std');
	 }

	 dispatch('/', 'page_home');
	 dispatch('/tests', 'page_tests');
	 dispatch('/no', 'utils_no_output');

	 dispatch('/launch/app', 'page_launch_app');
	 dispatch('/launch/app/download', 'page_launch_app');

	 dispatch('/api/', 							'api_hello');
	 dispatch('/api/ui', 						'api_ui');
	 dispatch('/api/about', 					'api_get_infos');
	 dispatch('/api/api', 						'api_get_infos');
	 dispatch('/api/commands', 					'api_get_commands_list');
	 dispatch('/api/configuration', 			'api_get_config');
	 dispatch('/api/benchmark', 				'api_get_benchmark');
	 dispatch('/api/no', 						'utils_no_output');
	 dispatch('/api/system', 					'api_get_sysinfos');
	 dispatch('/api/functions', 				'api_get_functions');
	 dispatch('/api/functions/exec', 			'api_get_functions');
	 dispatch('/api/functions/exec/:func', 		'api_run_this_function');
	 dispatch('/api/path', 						'api_get_path');
	 dispatch('/api/path/absolute', 			'api_get_path_absolute');
	 dispatch('/api/path/movies', 				'api_get_path_movies');
	 dispatch('/api/path/series', 				'api_get_path_series');
	 dispatch('/api/path/musics', 				'api_get_path_musics');
	 dispatch('/api/movie/:cat/all', 			'api_get_movie_list');
	 dispatch('/api/movie/:cat/:id', 			'api_get_movie_info');
	 dispatch('/api/is/admin', 					'api_is_admin');
	 dispatch('/api/is/invite', 				'api_is_invite');
	 dispatch('/api/download/', 				'api_get_download_infos');
	 dispatch('/api/download/get/', 			'api_get_download_infos');
	 dispatch('/api/download/get/server', 		'api_get_server_jdownloader');
	 dispatch('/api/download/get/currentlist', 	'api_get_download_currentlist');
	 dispatch('/api/download/get/speed', 		'api_get_download_speed');
	 dispatch('/api/download/limit/no', 		'api_download_limit_no');
	 dispatch('/api/download/limit/**', 		'api_download_limit');
	 dispatch('/api/download/**', 				'api_add_download_url');

	 dispatch('/media/films/**',				'page_films');
	 dispatch('/media/music/**',				'page_music');
	 dispatch('/media/series/**',				'page_series');

	 dispatch('/system/infos',					'page_system_infos');

	 run();