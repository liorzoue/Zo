<?php
	
	 function page_home () {
	 	layout('layout_home');
	    set('active_page', 'home');
	 	return html('');
	 }

	 function page_tests () {
	 	// if (!user_is_admin()) { return error_401(); }

	    set('title', 'Test');
	    set('active_page', 'tests');

	    $r  = '<div class="panel panel-default">';
	    $r .= '<div class="panel-heading">';
	    $r .= '<h3 class="panel-title">Option values</h3>';
	    $r .= '</div><div class="panel-body" style="overflow-y: scroll;">';

	    $r .= '<dl class="dl-horizontal"><dt>root_dir'           .'</dt><dd><code>'.option('root_dir'           ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>base_path'          .'</dt><dd><code>'.option('base_path'          ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>base_uri'           .'</dt><dd><code>'.option('base_uri'           ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>limonade_dir'       .'</dt><dd><code>'.option('limonade_dir'       ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>limonade_views_dir' .'</dt><dd><code>'.option('limonade_views_dir' ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>limonade_public_dir'.'</dt><dd><code>'.option('limonade_public_dir').'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>public_dir'         .'</dt><dd><code>'.option('public_dir'         ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>views_dir'          .'</dt><dd><code>'.option('views_dir'          ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>controllers_dir'    .'</dt><dd><code>'.option('controllers_dir'    ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>lib_dir'            .'</dt><dd><code>'.option('lib_dir'            ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>error_views_dir'    .'</dt><dd><code>'.option('error_views_dir'    ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>env'                .'</dt><dd><code>'.option('env'                ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>debug'              .'</dt><dd><code>'.option('debug'              ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>session'            .'</dt><dd><code>'.option('session'            ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>encoding'           .'</dt><dd><code>'.option('encoding'           ).'</code></dd></dl>';
	    $r .= '<dl class="dl-horizontal"><dt>x-sendfile'         .'</dt><dd><code>'.option('x-sendfile'         ).'</code></dd></dl>';

	    $r .= '</div></div>';

	    return html($r);
	 }

	 function page_launch_app () {
	 	set('title', 'Application Android');
	 	set('active_page', 'application');

	 	$r = '<div class="row">
	 	<div class="col-xs-6 col-md-3">
	 	<a href="'.option('base_path').'/launch/app/download" class="thumbnail well">
	 	<img src="'.option('base_path').'/public/img/zo-android-web.png" alt="ZoDroid">
	 	</a>
	 	</div>
	 	</div>';
	 	return html($r);
	 }


	 function page_films () {

	 	layout('layout_movies');

	 	$folder = params(0);
	 	if(!$folder) { $folder = 'default'; }

	 	$movies = utils_scandir('/var/www/films/'.$folder);
	 	
	 	$title = 'Movies - '.$folder;
	 	set('title', 		$title);
	    set('path', 		$folder);
	    set('list',			$movies);
	    set('active_page', 	'medias');
	    set('folder', 		explode("/", $folder)[count(explode("/", $folder)) - 1]);

	    return html($title);
	 }

	 function page_music () {

	    $folder = params(0);
	    if(!$folder) { $folder = 'default'; }

	    $title = 'Music - '.$folder;
	 	set('title', $title);
	    set('active_page', 'medias');


	    return html($title);
	 }

	 function page_series () {

	    $folder = params(0);
	    if(!$folder) { $folder = 'default'; }

		$title = 'Series - '.$folder;
	 	set('title', $title);
	    set('active_page', 'medias');

	    return html($title);
	 }

	 function page_system_infos () {
	 	layout('layout_system');

	 	set('title', 		'System infos');
	    set('active_page', 	'tests');
	    set('system_infos', api_get_sysinfos(false));

	    return html('System infos');
	 }