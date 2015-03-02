<?php

function layout_std_menu($active_page) {

	function is_active($active_page, $page) {
		if ($page == $active_page) {
			return 'active';
		} else {
			return 'active-no';
		}
	}

	function gen_li ($url, $text) {
		return '<li><a href="'.option('base_uri').$url.'">'.$text.'</a></li>';
	}

	$paths = api_get_path(false);

	?>
	<!-- Static navbar -->
	<div class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo option('base_uri').'/'; ?>">ZoUI</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="<?php echo is_active($active_page, 'home'); ?>"><a href="<?php echo option('base_uri').'/'; ?>"><span class="glyphicon glyphicon-home"></span> Home</a></li>
					<li class="dropdown <?php echo is_active($active_page, 'medias'); ?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-film"></span> Multimedia <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li class="dropdown-header">Movies</li>
							<?php 
								foreach ($paths[JSON_MOVIES] as $item) {
									echo gen_li('/media/films'.$item, explode("/", $item)[count(explode("/", $item)) - 1]);
								}
							?>
							<li class="divider"></li>
							<li class="dropdown-header">Music</li>
							<?php 
								foreach ($paths[JSON_MUSICS] as $item) {
									echo gen_li('/media/music'.$item, explode("/", $item)[count(explode("/", $item)) - 1]);
								}
							?>
							<li class="divider"></li>
							<li class="dropdown-header">Series</li>
							<?php 
								foreach ($paths[JSON_TV_SHOWS] as $item) {
									echo gen_li('/media/series'.$item, explode("/", $item)[count(explode("/", $item)) - 1]);
								}
							?>
						</ul>
					</li>

					<li class="dropdown <?php echo is_active($active_page, 'autres'); ?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-list"></span> Autres <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo option('base_uri').'/launch/app'; ?>"><span class="glyphicon glyphicon-phone"></span> Mobile App</a></li>
						</ul>
					</li>

					<li class="dropdown <?php echo is_active($active_page, 'tests'); ?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-wrench"></span> Tests <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li class="dropdown-header">Framework</li>
							<li><a href="<?php echo option('base_uri').'/tests'; ?>">Variables</a></li>
							<li><a href="<?php echo option('base_uri').'/api/ui'; ?>">API</a></li>
							<li class="dropdown-header">System</li>
							<li><a href="<?php echo option('base_uri').'/system/infos'; ?>">Infos</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="https://github.com/liorzoue/Zo">Fork me <span class="glyphicon glyphicon-share"></span></a></li>
				</ul>
			</div><!--/.nav-collapse -->
		</div><!--/.container-fluid -->
	</div>
	<?php
}

function layout_std_header($vars) {
	?>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="../../assets/ico/favicon.ico">


	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

	<!-- Custom styles for this template -->
	<link href="<?php echo option('base_uri').'/'; ?>public/css/navbar.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css"></style>

    <?php
}

function layout_std_scripts($vars) {
	?>
	    <!-- Bootstrap core JavaScript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

		<script src="<?php echo option('base_uri').'/'; ?>public/js/script.js"></script>
		<script src="<?php echo option('base_uri').'/'; ?>public/js/jsonview.js"></script>
    <?php
}

function layout_std($vars) {
	extract($vars);

	if(!isset($title)) {
		$title = 'An UI for Zo';
	}
	?> 

	<html lang="en">
	<head>
		<?php layout_std_header(); ?>
		<title>ZoUI - <?php echo $title; ?></title>
	</head>
	<body>
		<div class="container">

			<?php
				layout_std_menu($active_page);
				echo $content;
			?>

		</div> <!-- /container -->

		<?php layout_std_scripts(); ?>

	</body>
	</html>
	<!-- <?php print_r(benchmark()); ?> -->
	<?
}

function layout_home($vars) {
	extract($vars);

	if(!isset($title)) {
		$title = 'An UI for Zo';
	}

	$paths = api_get_path(false);

	?> 

	<html lang="en">
	<head>
		<?php layout_std_header(); ?>
		<title>ZoUI - <?php echo $title; ?></title>
	</head>
	<body>
		<div class="container">

			<?php layout_std_menu($active_page); ?>

			<!-- Main component for a primary marketing message or call to action -->
			<div class="jumbotron">
				<h3>Zo UI</h3>
				<p>This is an web platform for Zo API.</p>
				<p>
					<a class="btn btn-lg btn-primary" href="<?php echo option('base_uri').'/launch/app'; ?>" role="button"><span class="glyphicon glyphicon-phone"></span> Launch mobile app</a>
				</p>
				<p>
					<?php echo $content; ?>
				</p>
			</div>

			<div class="row">
				<?php
				for ($i=0; $i < 3; $i++) { 
					switch ($i) {
						case 1:
							$data = JSON_MUSICS;
							$data_title = 'Music';
							break;

						case 2:
							$data = JSON_TV_SHOWS;
							$data_title = 'TV shows';
							break;

						case 0:
						default:
							$data = JSON_MOVIES;
							$data_title = 'Movies';
							break;
					}

					?>
					<div class="col-md-4">
						<div class="thumbnail">
							<div class="well well-sm">
								<?php echo $data_title; ?>
							</div>
							<ul class="nav nav-pills nav-stacked">
								<?php 
								foreach ($paths[$data] as $item) {
									$ar = utils_scandir('/var/www/'.$item);
									?>
									<li>
										<a href="<?php echo option('base_uri').'/media'.$item; ?>">
											<span class="badge pull-right"><?php echo count($ar); ?></span>
											<?php echo explode("/", $item)[count(explode("/", $item)) - 1]; ?>
										</a>
									</li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>

					<?php
				}
				?>

			</div>


		</div> <!-- /container -->

		<?php layout_std_scripts(); ?>

	</body>
	</html>
	<!-- <?php print_r(benchmark()); ?> -->
	<?
}

function layout_movies($vars) {
	extract($vars);

	if(!isset($title)) {
		$title = 'An UI for Zo';
	}

	if(!isset($folder)) {
		$title = 'Movies';
	}
	?> 

	<html lang="en">
	<head>
		<?php layout_std_header(); ?>
		<style>
			.list-movies li.list-group-item:hover {
				background-color: rgb(245, 245, 245);
			}
			
		</style>
		<title>ZoUI - <?php echo $title; ?></title>
	</head>
	<body>
		<div class="container">

			<!-- Modal -->
			<div class="modal fade" id="modalMovie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel">Modal title</h4>
						</div>
						<div class="modal-body">
							...
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary">OK</button>
						</div>
					</div>
				</div>
			</div>
			<?php layout_std_menu($active_page); ?>

			<!-- Main component for a primary marketing message or call to action -->
			<h3><?php echo $folder ?></h3>
			<hr>
			<div id="filelist" class="panel panel-default">
				<div class="panel-heading">
					<div class="btn-toolbar" role="toolbar">
						<div class="btn-group">
							<button type="button" class="btn btn-default">Button 1</button>
							<button type="button" class="btn btn-default">Button 2</button>
							<button type="button" class="btn btn-default">Button 3</button>
						</div>
					</div>
				</div>
				
				<ul class="list-group list-movies">
					<?php
					foreach ($list as $item) {
						?>
						<li class="list-group-item">
							<?php echo utils_extractfilename($item); ?>
							<small style="color: gainsboro;"><small>
								<?php echo $item; ?>
							</small></small>

							<div class="btn-group pull-right">
								<?php 
									if (utils_extractvo($item)) {
										?>
										<button type="button" class="btn btn-default btn-xs" disabled="disabled">VO</button>
										<?php
									}
									if (utils_extractdts($item)) {
										?>
										<button type="button" class="btn btn-default btn-xs" disabled="disabled">DTS</button>
										<?php
									}
								?>
								<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalMovie"><span class="glyphicon glyphicon-info-sign"></span> Infos</button>
								<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-play"></span> Play</button>
							</div>
							
						</li>

						<?php
					}
					?>
				</ul>
			</div>

		</div> <!-- /container -->

		<?php layout_std_scripts(); ?>

	</body>
	</html>
	<!-- <?php print_r(benchmark()); ?> -->
	<?
}

function layout_system($vars) {
	extract($vars);

	if(!isset($title)) {
		$title = 'An UI for Zo';
	}
	?> 

	<html lang="en">
	<head>
		<?php layout_std_header(); ?>
		<title>ZoUI - <?php echo $title; ?></title>
	</head>
	<body>
		<div class="container">

			<?php layout_std_menu($active_page); ?>

			<div class="row">
				<div class="col-md-4">
					<div class="thumbnail">
						<div class="col-md-11 col-md-offset-1">
							<h5>Generic infos</h5>
						</div>
						<dl class="dl-horizontal offset-top">
							<dt>CPU</dt>
							<dd><?php echo $system_infos['sysinfos']['cpu']['cpu']; ?></dd>

							<dt>Frequency</dt>
							<dd><?php echo $system_infos['sysinfos']['cpu']['frequency']; ?></dd>

							<dt>Load</dt>
							<dd><?php echo $system_infos['sysinfos']['cpu']['load']; ?></dd>

							<dt>Temperature</dt>
							<dd><?php echo $system_infos['sysinfos']['cpu']['temperature']; ?>°C</dd>

							<dt>Uptime</dt>
							<dd><?php echo $system_infos['sysinfos']['uptime']; ?></dd>
						</dl>
					</div>	
				</div>

				<div class="col-md-4">
					<div class="thumbnail">
						<div class="col-md-11 col-md-offset-1">
							<h5>Memory</h5>
						</div>
						<dl class="dl-horizontal offset-top">
							<dt>Free</dt>
							<dd><?php echo $system_infos['sysinfos']['memory']['free']; ?></dd>

							<dt>Total</dt>
							<dd><?php echo $system_infos['sysinfos']['memory']['total']; ?></dd>

							<dt>Used</dt>
							<dd><?php echo $system_infos['sysinfos']['memory']['used']; ?></dd>
						</dl>
					</div>	
				</div>

				<div class="col-md-4">
					<div class="thumbnail">
						<div class="col-md-11 col-md-offset-1">
							<h5>Swap</h5>
						</div>
						<dl class="dl-horizontal offset-top">
							<dt>Free</dt>
							<dd><?php echo $system_infos['sysinfos']['swap']['free']; ?></dd>

							<dt>Total</dt>
							<dd><?php echo $system_infos['sysinfos']['swap']['total']; ?></dd>

							<dt>Used</dt>
							<dd><?php echo $system_infos['sysinfos']['swap']['used']; ?></dd>
						</dl>
					</div>	
				</div>
			</div>

			<div class="row">
				<div class="col-md-8">
					<div class="thumbnail">
						<div class="row">
							<div class="col-md-11 col-md-offset-1">
								<h5>Disks usage</h5>
							</div>
						</div>
						<dl class="dl-horizontal offset-top">
							
								<?php
								foreach ($system_infos['sysinfos']['disks'] as $item) {
									try {
										if (isset($item['drive'])) {


											$percent_used = 100 * floatval($item['used']) / floatval($item['sizes']);
											$color = 'success';

											if ($percent_used > 70) {
												$color = 'warning';
											}

											if ($percent_used > 90) {
												$color = 'danger';
											}

											$percent_used = ''.round($percent_used, 2);

											?>
											<dt>Drive</dt>
											<dd><?php echo $item['drive']; ?></dd>
											<dt>Mount</dt>
											<dd><?php echo $item['mount']; ?></dd>
											<dt>Typex</dt>
											<dd><?php echo $item['typex']; ?></dd>
											<dt>Used</dt>
											<dd>
												<?php echo utils_toreadablesize($item['used'].'000'); ?> /
												<?php echo utils_toreadablesize($item['sizes'].'000'); ?>
												(<?php echo utils_toreadablesize($item['avail'].'000'); ?> avail.)
											</dd>
											<dt>Percent usage</dt>
											<dd>
												<div class="row">
													<div class="col-md-10">
														<div class="progress">
															<div class="progress-bar progress-bar-<?php echo $color; ?>" role="progressbar" aria-valuenow="<?php echo $percent_used; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percent_used; ?>%;">
																<?php echo $percent_used; ?>%
															</div>
														</div>
													</div>
												</div>
											</dd>
											<?php
										}
									} catch (Exception $e) {
										
									}
								}
								?>
						</dl>
					</div>
				</div>
			</div>

		</div> <!-- /container -->

		<?php layout_std_scripts(); ?>

	</body>
	</html>
	<!-- <?php print_r(benchmark()); ?> -->
	<?
}


function layout_json($vars) {
    return $vars;
}