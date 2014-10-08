<?php
    
    /*
     API Key for omdbapi.com
     
     This API provide infos for movies
     */
    $api_key = 'c12925627273820b58a9ffb4e5f4f3a0';
    $api_url = 'http://private-c689d-themoviedb.apiary.io/3';
    
    $jdownloader['server'] = '192.168.1.120';
    $jdownloader['port'] = '10025';
    
    /*
     webserver files path
     */
    $absolute_path = '/var/www';
    
    /*
     Movies folders
     relative from $absolute_path
     */
    $movies = array('/films/720p', '/films/1080p', '/films/1080p 3D', '/films/DVD-rip/_Non Classé', '/films/DVD-rip/de Funès');
    
    $movies_titles = array('720p', '1080p', '1080p3D', 'DVDripNC', 'DVDripFunes');
    
    /*
     TV shows folders
     relative from $absolute_path
     */
    $tv_shows = array('/series');
    /*
     Music folders
     relative from $absolute_path
     */
    $music = array('/music');
    
    
    $user['invite'] = 'invite';
    $user['admin'] = 'liorzoue';
    
    
