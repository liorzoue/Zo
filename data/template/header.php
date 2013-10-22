<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="/data/ico/favicon.png">

    <title>Millenium Falcon</title>

    <!-- Bootstrap core CSS -->
    <link href="/data/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/data/css/sticky-footer-navbar.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="/data/js/html5shiv.js"></script>
      <script src="/data/js/respond.min.js"></script>
    <![endif]-->
  <style type="text/css">
	.opa {
		opacity: 0.7;
	}
	
	.opa > div {
		opacity: 1;
	}
  </style>
  </head>

  <body style="">

    <!-- Wrap all page content here -->
	<div id="wrap">

	<!-- Fixed navbar -->
	<div class="navbar navbar-default navbar-fixed-top">
	<div class="container">
	  <div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="/">Millenium Falcon</a>
	  </div>
	  <div class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
		  <li ><a href="/">Home</a></li>
		  <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Repertoires <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li class="dropdown-header">Medias</li>
				<li><a href="#films" onclick="scan_the_dir('/var/www/films');">Films</a></li>
				<li><a href="#series" onclick="scan_the_dir('/var/www/series');">Séries</a></li>
				<li><a href="#musique" onclick="scan_the_dir('/var/www/musique');">Musique</a></li>
				<li class="divider"></li>
				<li class="dropdown-header">Fichiers divers</li>
				<li><a href="#softs" onclick="scan_the_dir('/var/www/softs');">Logiciels</a></li>
			</ul>
		  </li>
		  <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Outils <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li class="dropdown-header">Medias</li>
				<li><a href="/playlist">Playlist Generator</a></li>
				<li class="divider"></li>
				<li class="dropdown-header">Administration</li>
				<li><a href="/control">Controle</a></li>
			</ul>
		  </li>
		</ul>
	  </div><!--/.nav-collapse -->
	</div>
	</div>

	<!-- Begin page content -->
	<div class="container">