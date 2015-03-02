<?php
    
    /*
     API Key for omdbapi.com
     
     This API provide infos for movies
     */
    $api_key = 'your_api_key';
    $api_url = 'http://private-url-themoviedb.apiary.io/3';
    
    $jdownloader['server'] = 'your_jdownloader_server';
    $jdownloader['port'] = '10025';
    

    $bdd['host'] = 'localhost';
    $bdd['dbname'] = 'zo-bdd';
    $bdd['username'] = 'zo-bdd-user';
    $bdd['password'] = 'zo-bdd-user-password';
    
    /*
     webserver files path
     */
    $absolute_path = '/var/www';
    
    /*
     Movies folders
     relative from $absolute_path
     */
    $movies = array('/movies/foo/', '/movies/bar');
    
    $movies_titles = array('Foo', 'Bar');
    
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
    $user['admin'] = 'admin';