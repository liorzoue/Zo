<?php
    
    // API Key for omdbapi.com
    // This API provide infos for movies
    $api_key = 'your_api_key';
    $api_url = 'http://yourprivate_themoviedb.url/';
    
    // Your JDownloader config
    $jdownloader['server'] = 'your_jdownloader_server';
    $jdownloader['port'] = '10025'; // JDownloader remote port by default
    
    // File path
    $absolute_path = '/var/www';

    // Movies folders
    // relative from $absolute_path
    $movies = array('/movies/foo/', '/movies/bar');
    
    // Movies folders titles
    // relative from $absolute_path
    // Not used yet :)
    $movies_titles = array('Foo', 'Bar');

    // TV shows folders
    // relative from $absolute_path
    $tv_shows = array('/series');

    // Music folders
    // relative from $absolute_path
    $music = array('/music');
    
    // HTTP users and theirs roles
    // It will be better later :)
    $user['invite'] = 'invite';
    $user['admin'] = 'admin';
    
    
    
