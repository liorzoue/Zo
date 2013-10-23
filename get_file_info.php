<?php 
	$q = trim($_GET['q']);
	$api_key = 'your_api_key';
	function get_movie_data($title) {
		$title = preg_replace('/( -| \(|\.).*$/', '', $title);
		$title = str_replace(' ', '+', trim($title));
		$json=file_get_contents('http://private-c689d-themoviedb.apiary.io/3/search/movie?api_key='.$api_key.'&query='.$title);
		
		return $json;
	}
	
	function get_music_data($title) {
	
		return '{ "test":"test" }';
	}
	
	$ext = pathinfo($q, PATHINFO_EXTENSION);
	
	switch ($ext) {
		case 'mkv':
		case 'avi':
		case 'mp4':
			echo get_movie_data($q);
			break;
		case 'mp3':
		case 'm4a':
			echo get_music_data($q);
			break;
		default:
			echo 'Test 1;Data inside.<br />'.$q;
	}
?>